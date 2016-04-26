<?php

function wpv_wpml_icl_current_language($lang) { // TODO check why is this needed: it just returns the default language when looking for the current language...
    global $sitepress;

    return $sitepress->get_default_language();
}

function wpml_content_fix_links_to_translated_content($body){
    global $wpdb, $sitepress, $sitepress_settings, $wp_taxonomies;

    global $WP_Views;
    $settings = $WP_Views->get_options();
    if (isset($settings['wpml_fix_urls'])) {
        $wpml_fix_urls = $settings['wpml_fix_urls'];
    } else {
        $wpml_fix_urls = true;
    }

    if (!$wpml_fix_urls) {
        return $body;
    }


    if (isset($sitepress)) {

        static $content_cache = array();

        $target_lang_code = $sitepress->get_current_language();

        $cache_code = md5($body . $target_lang_code);
        if (isset($content_cache[$cache_code])) {
            $body = $content_cache[$cache_code];
        } else {

            add_filter('icl_current_language', 'wpv_wpml_icl_current_language');
            remove_filter('option_rewrite_rules', array($sitepress, 'rewrite_rules_filter'));

            require_once ICL_PLUGIN_PATH . '/inc/absolute-links/absolute-links.class.php';
            $icl_abs_links = new AbsoluteLinks;

            $old_body = $body;
            $alp_broken_links = array();
            $body = $icl_abs_links->_process_generic_text($body, $alp_broken_links);

            // Restore the language as the above call can change the current language.
            $sitepress->switch_lang($target_lang_code);

            if ($body == '') {
                // Handle a problem with abs links occasionally return empty.
                $body = $old_body;
            }

            $new_body = $body;

            $base_url_parts = parse_url(get_option('home'));

            $links = wpml_content_get_link_paths($body);

            $all_links_fixed = 1;

            $pass_on_qvars = array();
            $pass_on_fragments = array();

            foreach($links as $link_idx => $link) {
                $path = $link[2];
                $url_parts = parse_url($path);

                if(isset($url_parts['fragment'])){
                    $pass_on_fragments[$link_idx] = $url_parts['fragment'];
                }

                if((!isset($url_parts['host']) or $base_url_parts['host'] == $url_parts['host']) and
                        (!isset($url_parts['scheme']) or $base_url_parts['scheme'] == $url_parts['scheme']) and
                        isset($url_parts['query'])) {
                    $query_parts = explode('&', $url_parts['query']);

                    foreach($query_parts as $query){
                        // find p=id or cat=id or tag=id queries
                        list($key, $value) = explode('=', $query);
                        $translations = NULL;
                        $is_tax = false;
                        if($key == 'p'){
                            $kind = 'post_' . $wpdb->get_var("SELECT post_type FROM {$wpdb->posts} WHERE ID='{$value}'");
                        } else if($key == "page_id"){
                            $kind = 'post_page';
                        } else if($key == 'cat' || $key == 'cat_ID'){
                            $is_tax = true;
                            $kind = 'tax_category';
                            $taxonomy = 'category';
                        } else if($key == 'tag'){
                            $is_tax = true;
                            $taxonomy = 'post_tag';
                            $kind = 'tax_' . $taxonomy;
                            $value = $wpdb->get_var("SELECT term_taxonomy_id FROM {$wpdb->terms} t
                                JOIN {$wpdb->term_taxonomy} x ON t.term_id = x.term_id WHERE x.taxonomy='{$taxonomy}' AND t.slug='{$value}'");
                        } else {
                            $found = false;
                            foreach($wp_taxonomies as $ktax => $tax){
                                if($tax->query_var && $key == $tax->query_var){
                                    $found = true;
                                    $is_tax = true;
                                    $kind = 'tax_' . $ktax;
                                    $value = $wpdb->get_var("
                                        SELECT term_taxonomy_id FROM {$wpdb->terms} t
                                            JOIN {$wpdb->term_taxonomy} x ON t.term_id = x.term_id WHERE x.taxonomy='{$ktax}' AND t.slug='{$value}'");
                                    $taxonomy = $ktax;
                                }
                            }
                            if(!$found){
                                $pass_on_qvars[$link_idx][] = $query;
                                continue;
                            }
                        }

                        $link_id = (int)$value;

                        if (!$link_id) {
                            continue;
                        }

                        $trid = $sitepress->get_element_trid($link_id, $kind);
                        if(!$trid){
                            continue;
                        }
                        if($trid !== NULL){
                            $translations = $sitepress->get_element_translations($trid, $kind);
                        }
                        if(isset($translations[$target_lang_code]) && $translations[$target_lang_code]->element_id != null){

                            // use the new translated id in the link path.

                            $translated_id = $translations[$target_lang_code]->element_id;

                            if($is_tax){ //if it's a tax, get the translated link based on the term slug (to avoid the need to convert from term_taxonomy_id to term_id)
                                $translated_id = $wpdb->get_var("SELECT slug FROM {$wpdb->terms} t JOIN {$wpdb->term_taxonomy} x ON t.term_id=x.term_id WHERE x.term_taxonomy_id=$translated_id");
                            }

                            // if absolute links is not on turn into WP permalinks
                            if(empty($GLOBALS['WPML_Sticky_Links'])){
                                ////////
                                if(preg_match('#^post_#', $kind)){
                                    $replace = get_permalink($translated_id);
                                }elseif(preg_match('#^tax_#', $kind)){
                                remove_filter('icl_current_language', 'wpv_wpml_icl_current_language');
                                    if(is_numeric($translated_id)) $translated_id = intval($translated_id);
                         //           $translated_id = 186;
                                    $replace = get_term_link($translated_id, $taxonomy);
                                    add_filter('icl_current_language', 'wpv_wpml_icl_current_language');
                                }
                                $new_link = str_replace($link[2], $replace, $link[0]);

                                $replace_link_arr[$link_idx] = array('from'=> $link[2], 'to'=>$replace);
                            }else{
                                $replace = $key . '=' . $translated_id;
                                $new_link = str_replace($query, $replace, $link[0]);

                                $replace_link_arr[$link_idx] = array('from'=> $query, 'to'=>$replace);
                            }

                            // replace the link in the body.
                            // $new_body = str_replace($link[0], $new_link, $new_body);
                            $all_links_arr[$link_idx] = array('from'=> $link[0], 'to'=>$new_link);
                            // done in the next loop

                        } else {
                            // translation not found for this.
                            $all_links_fixed = 0;
                        }
                    }
                }

            }

            if(!empty($replace_link_arr))
            foreach($replace_link_arr as $link_idx => $rep){
                $rep_to = $rep['to'];
                $fragment = '';

                // if sticky links is not ON, fix query parameters and fragments
                if(empty($GLOBALS['WPML_Sticky_Links'])){
                    if(!empty($pass_on_fragments[$link_idx])){
                        $fragment = '#' . $pass_on_fragments[$link_idx];
                    }
                    if(!empty($pass_on_qvars[$link_idx])){
                        $url_glue = (strpos($rep['to'], '?') === false) ? '?' : '&';
                        $rep_to = $rep['to'] . $url_glue . join('&', $pass_on_qvars[$link_idx]);
                    }
                }

                $all_links_arr[$link_idx]['to'] = str_replace($rep['to'], $rep_to . $fragment, $all_links_arr[$link_idx]['to']);

            }

            if(!empty($all_links_arr))
            foreach($all_links_arr as $link){
                $new_body = str_replace($link['from'], $link['to'], $new_body);
            }

            $body = $new_body;
            $content_cache[$cache_code] = $body;

            remove_filter('icl_current_language', 'wpv_wpml_icl_current_language');
            add_filter('option_rewrite_rules', array($sitepress, 'rewrite_rules_filter'));

        }
    }

    return $body;
}

function wpml_content_get_link_paths($body) {

    $regexp_links = array(
                        /*"/<a.*?href\s*=\s*([\"\']??)([^\"]*)[\"\']>(.*?)<\/a>/i",*/
                        "/<a[^>]*href\s*=\s*([\"\']??)([^\"^>]+)[\"\']??([^>]*)>/i",
                        );

    $links = array();

    foreach($regexp_links as $regexp) {
        if (preg_match_all($regexp, $body, $matches, PREG_SET_ORDER)) {
            foreach ($matches as $match) {
              $links[] = $match;
            }
        }
    }
    return $links;
}

// Add settings to the translation managment setup screen.

add_action('icl_tm_menu_mcsetup', 'wpv_wpml_settings');
function wpv_wpml_settings() {
    global $WP_Views;
    $settings = $WP_Views->get_options();
    if (isset($settings['wpml_fix_urls'])) {
        $wpml_fix_urls = $settings['wpml_fix_urls'];
    } else {
        $wpml_fix_urls = true;
    }

    wp_nonce_field('wpv_wpml_save_settings_nonce', 'wpv_wpml_save_settings_nonce');
    
    if(defined('ICL_SITEPRESS_VERSION')) {

	if ( version_compare( ICL_SITEPRESS_VERSION, '3.0' )  < 0 ) {
    
    ?>


    <?php
    /*
        This section should be display conditionally, only for WPML < 3.0
    */
    ?>
    <table class="widefat">
        <thead>
            <tr>
                <th><?php _e('Views', 'wpv-views'); ?></th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td style="border: none;">
                    <p>
                        <label>
                            <input id="wpv_wpml_fix_urls" type="checkbox" value="1" <?php if($wpml_fix_urls): ?>checked<?php endif; ?> />
                            <?php _e('Convert URLs to point to translated content in Views and Content Templates', 'wpv-views'); ?>
                        </label>
                    </p>
                </td>
            </tr>
            <tr>
                <td>
                    <input class="button-primary" type="button" value="<?php _e('Save', 'wpv-views'); ?>" onclick="wpv_wpml_save_view_settings(); return false;" />
                    <span id="icl_ajx_response_views_wpml" class="icl_ajx_response"><?php _e('Settings Saved', 'wpv-views'); ?></span>
                </td>
            </tr>
        </tbody>
    </table>

    <?php
    
    } else {
    
    /*
	This section should be display conditionally, only for WPML >= 3.0
	http://wpml-woocommerce.localhost/wp-admin/admin.php?page=wpml/menu/languages.php
    */
    
    ?>

    <div class="wpml-section">
        <div class="wpml-section-header">
            <h3>
                <?php _e('Views', 'wpv-views'); ?></th>
            </h3>
        </div>
        <div class="wpml-section-content">
            <p>
                <label>
                    <input id="wpv_wpml_fix_urls" type="checkbox" value="1" <?php if($wpml_fix_urls): ?>checked<?php endif; ?> />
                    <?php _e('Convert URLs to point to translated content in Views and Content Templates', 'wpv-views'); ?>
                </label>
            </p>
            <p class="buttons-wrap">
                <span id="icl_ajx_response_views_wpml" class="icl_ajx_response"><?php _e('Settings Saved', 'wpv-views'); ?></span>
                <input class="button-primary" type="button" value="<?php _e('Save', 'wpv-views'); ?>" onclick="wpv_wpml_save_view_settings(); return false;" />
            </p>
        </div>
    </div>
    
    <?php
    
	}
    }
    ?>

	<script type="text/javascript">
        function wpv_wpml_save_view_settings() {

            var data = {
                action : 'wpv_wpml_save_settings',
                wpv_wpml_fix_urls : jQuery('#wpv_wpml_fix_urls:checked').val(),
                wpv_nonce : jQuery('#wpv_wpml_save_settings_nonce').attr('value')

            };

            jQuery.ajaxSetup({async:false});
            jQuery.post(ajaxurl, data, function(response) {
                jQuery('#icl_ajx_response_views_wpml').show();
            });

        }
    </script>

    <?php
}

add_action('wp_ajax_wpv_wpml_save_settings', 'wpv_wpml_save_settings');
function wpv_wpml_save_settings() {
	if (wp_verify_nonce($_POST['wpv_nonce'], 'wpv_wpml_save_settings_nonce')) {
        global $WP_Views;
        $settings = $WP_Views->get_options();

        if (isset($_POST['wpv_wpml_fix_urls'])) {
            $settings['wpml_fix_urls'] = $_POST['wpv_wpml_fix_urls'];
        } else {
            $settings['wpml_fix_urls'] = false;
        }

        $WP_Views->save_options($settings);

    }

    die();
}

// Add the [wpml-string] shortcode to the allowed inner shortcodes, but only if the [wpml-string] shortcode itself exists

add_filter('wpv_custom_inner_shortcodes', 'wpv_wpml_string_in_custom_inner_shortcodes');

function wpv_wpml_string_in_custom_inner_shortcodes($custom_inner_shortcodes) {
	if ( function_exists( 'wpml_string_shortcode' ) ) {
		if ( !is_array( $custom_inner_shortcodes ) ) $custom_inner_shortcodes = array();
		$custom_inner_shortcodes[] = 'wpml-string';
		$custom_inner_shortcodes = array_unique( $custom_inner_shortcodes );
	}
	return $custom_inner_shortcodes;
}
