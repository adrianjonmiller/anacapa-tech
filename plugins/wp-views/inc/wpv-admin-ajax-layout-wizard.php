<?php

/*
* File for Layout Wizard AJAX calls
*/

// Layout Extra save callback function

add_action('wp_ajax_wpv_update_layout_extra', 'wpv_update_layout_extra_callback');

function wpv_update_layout_extra_callback() {
	$nonce = $_POST["wpnonce"];
	if (! wp_verify_nonce($nonce, 'wpv_view_layout_extra_nonce') ) die("Security check");
    
    // Save the wizard settings if they are there.
    if (isset($_POST['style'])) {
        $settings = get_post_meta($_POST["id"], '_wpv_layout_settings', true);
        $settings['style'] = $_POST['style'];
        $settings['insert_at'] = $_POST['insert_at'];
        $settings['table_cols'] = $_POST['table_cols'];
        $settings['include_field_names'] = $_POST['include_field_names'];
    
        $settings['fields'] = $_POST['fields'];        
        $settings['real_fields'] = $_POST['real_fields'];        
        
        update_post_meta($_POST["id"], '_wpv_layout_settings', $settings);
    }
    
	$changed = false;
	$changed_bis = false;
    //update_post_meta($_POST["id"], '_wpv_layout_settings', $settings);
    
    $view_layout_array = get_post_meta($_POST["id"], '_wpv_layout_settings', true);

    $previous_layout = $view_layout_array;
    
	$view_array = get_post_meta($_POST["id"], '_wpv_settings', true);

	if (!isset($view_layout_array['layout_meta_html']) || $_POST["layout_val"] != $view_layout_array['layout_meta_html']) {
   		$view_layout_array['layout_meta_html'] = $_POST["layout_val"];
		$changed = true;
	}
	if (!isset($view_array['layout_meta_html_css']) || $_POST["layout_css_val"] != $view_array['layout_meta_html_css']) {
		$view_array['layout_meta_html_css'] = $_POST["layout_css_val"];
		$changed_bis = true;
	}
	if (!isset($view_array['layout_meta_html_js']) || $_POST["layout_js_val"] != $view_array['layout_meta_html_js']) {
		$view_array['layout_meta_html_js'] = $_POST["layout_js_val"];
		$changed_bis = true;
	}
	if ($changed || $changed_bis) {
            
        // We need to pass the previous value for some reason.
        // Otherwise update_post_meta returns 0 because it thinks nothing has changed.
		$result = update_post_meta($_POST["id"], '_wpv_layout_settings', $view_layout_array, $previous_layout);
                
		$result_bis = update_post_meta($_POST["id"], '_wpv_settings', $view_array);
                
		echo ($result || $result_bis) ? $_POST["id"] : false;
	} else {
		echo $_POST["id"];
	}
	die();
}

/*function add_comments_to_translation( $content, $view_id )
{
	$names = array('none','one','more');
	
	$content = str_replace("%,", '', $content);
	
	$content = stripslashes( $content );
	
//	$context = get_post_field( 'post_name', $view_id );
	
	$control = array();
	
		
			if( preg_match("/\[wpv-post-comments-number .*?/",  $content )  )
			{
				
			
			preg_match_all( "/\\[wpv-post-comments-number\s*?none\s*?=\"(.*?)\s*?one\s*?=\"(.*?)\s*?more\s*?=\"(.*?)\"\s*?\\]/", $content, $matches );
			
			$len = sizeof($matches);
			
			if( $len > 1 )//if we have at least 2 matches
			{
				$matches = array_slice($matches, 1);
				
				$len = count($matches);
				
				for( $i=0;$i<$len;$i++)
				{
					icl_register_string( "wpv-views", 'Comments_'.$names[$i], str_replace('"', '', $matches[$i][0] ) );
				//	print "View ".$context." " . 'Comments_'.$names[$i] . "  " .str_replace('"', '', $matches[$i][0] ) ."\n";
				}
			}			
	}
}*/

// Layout Extra JS save callback function

add_action('wp_ajax_wpv_update_layout_extra_js', 'wpv_update_layout_extra_js_callback');

function wpv_update_layout_extra_js_callback() {
	$nonce = $_POST["wpnonce"];
	if (! wp_verify_nonce($nonce, 'wpv_view_layout_settings_extra_js_nonce') ) die("Security check");
	$view_array = get_post_meta($_POST["id"], '_wpv_layout_settings', true);
	if (isset($view_array['additional_js']) && $_POST["value"] == $view_array['additional_js']) {
		echo $_POST["id"];
		die();
	}
	$view_array['additional_js'] = $_POST["value"];
	$result = update_post_meta($_POST["id"], '_wpv_layout_settings', $view_array);
        echo $result ? $_POST["id"] : false;
        die();
}

// Content save callback function

add_action('wp_ajax_wpv_update_content', 'wpv_update_content_callback');

function wpv_update_content_callback() {
	$nonce = $_POST["wpnonce"];
	if (! wp_verify_nonce($nonce, 'wpv_view_content_nonce') ) die("Security check");
	$content_post = get_post($_POST["id"]);
	$content = $content_post->post_content;
	if ($_POST["content"] == $content) {
		echo $_POST["id"];
		die();
	}
	$this_post = array();
	$this_post['ID'] = $_POST["id"];
	$this_post['post_content'] = $_POST["content"];
	$result = wp_update_post( $this_post );
    echo $result ? $_POST["id"] : false;
    die();
}

/*
* Layout Wizard
*/

add_action('wp_ajax_wpv_layout_wizard', 'wpv_layout_wizard_callback');

function wpv_layout_wizard_callback() {
    ob_start();
    require_once( WPV_PATH . '/inc/redesign/templates/wpv-layout-edit-wizard.tpl.php' );
	$result = array('dialog' => ob_get_clean(),
                    'settings' => wpv_layout_wizard_load_settings()
                    );
    echo json_encode($result);
	die();
}

function wpv_layout_wizard_load_settings() {
    $settings = get_post_meta($_POST["view_id"], '_wpv_layout_settings', true);

    return $settings;    
}

add_action('wp_ajax_wpv_convert_layout_settings', 'wpv_layout_wizard_convert_settings');
function wpv_layout_wizard_convert_settings() {

    $settings = get_post_meta($_POST["view_id"], '_wpv_layout_settings', true);

    $settings['style'] = $_POST['layout_style'];
    $settings['insert_at'] = $_POST['insert_to_view'];
    $settings['table_cols'] = $_POST['numcol'];
    $settings['include_field_names'] = $_POST['inc_headers'];
    $settings['layout_meta_html'] = $_POST['layout_content'];

    $new_fields = array();
    foreach ($_POST['fields'] as $fields) {
        $new_fields[] = stripslashes($fields[1]);
    }
 
    // Compatibility
    $comp = array();
    $i = 0;
    foreach ($_POST['fields'] as $fields) {
        $comp["prefix_$i"] = ''; // 1
        $fields[1] = stripslashes($fields[1]);
        if (preg_match('/\[types.*?field=\"(.*?)\"/', $fields[1], $out)) {
            $comp["name_$i"] = 'types-field'; // 2
            $comp["types_field_name_$i"] = $out[1]; //3
            $comp["types_field_data_$i"] = $fields[1]; //4
        } else {
            $comp["name_$i"] = trim($fields[1], '[]'); // 2
            $comp["types_field_name_$i"] = ''; //3
            $comp["types_field_data_$i"] = ''; // 4
        }
        
        $comp["row_title_$i"] = $fields[3]; // 5
        $comp["suffix_$i"] = ''; //6
        $i++;
    }
    
    $settings['fields'] = $comp;
    $settings['real_fields'] = $new_fields;

    echo json_encode($settings);
    die();
}

add_action('wp_ajax_layout_wizard_add_field', 'wpv_layout_wizard_add_field');

function wpv_layout_wizard_add_field() { // TODO this might need localization TODO this is seriously broken
    if ( !isset($_POST["wpnonce"]) || ! wp_verify_nonce($_POST["wpnonce"], 'layout_wizard_nonce') ) die("Undefined Nonce.");

    global $WP_Views, $wpdb;
    $settings = $WP_Views->get_view_settings($_POST["view_id"]);

    $WP_Views->editor_addon = new Editor_addon('wpv-views',
            __('Insert Views Shortcodes', 'wpv-views'),
            WPV_URL . '/res/js/views_editor_plugin.js',
            WPV_URL . '/res/img/bw_icon16.png');

    if ((string)$settings["query_type"][0] == 'posts') {
        add_short_codes_to_js( array('body-view-templates','post', 'taxonomy', 'post-view', 'taxonomy-view', 'user-view'), $WP_Views->editor_addon );
    } else if ((string)$settings["query_type"][0]=='taxonomy') {
        add_short_codes_to_js( array('post-view', 'taxonomy-view', 'user-view'), $WP_Views->editor_addon );
    }
    else if ((string)$settings["query_type"][0]=='users') {
       
    }
    $fields_list = $WP_Views->editor_addon->get_fields_list();
    if ( empty($fields_list) ){
        $fields_list = array();
    }
    $fields_accos = array();

        // Show taxonomy fields only if we are in Taxonomy mode
    if ((string)$settings["query_type"][0]=='taxonomy') {
        $tax_fields = array();
        $tax_fields[__('Taxonomy title', 'wpv-views')] = 'wpv-taxonomy-title';
        $tax_fields[__('Taxonomy title with a link', 'wpv-views')] = 'wpv-taxonomy-link';
        $tax_fields[__('Taxonomy URL', 'wpv-views')] = 'wpv-taxonomy-url';
        $tax_fields[__('Taxonomy slug', 'wpv-views')] = 'wpv-taxonomy-slug';
        $tax_fields[__('Taxonomy description', 'wpv-views')] = 'wpv-taxonomy-description';
        $tax_fields[__('Taxonomy post count', 'wpv-views')] = 'wpv-taxonomy-post-count';
        foreach($tax_fields as $name => $value) {
            $fields_accos[__('Taxonomy fields', 'wpv-views')][] = array($name, $value);
        }
    }
    if ((string)$settings["query_type"][0]=='users') {
       $user_fields = array();
       $user_fields[__('User Email', 'wpv-views')] = 'wpv-user field="user_email"';
       $user_fields[__('User Login', 'wpv-views')] = 'wpv-user field="user_login"';
       $user_fields[__('First Name', 'wpv-views')] = 'wpv-user field="user_firstname"';
       $user_fields[__('Last Name', 'wpv-views')] = 'wpv-user field="user_lastname"';
       $user_fields[__('Nickname', 'wpv-views')] = 'wpv-user field="nickname"';
       $user_fields[__('Display Name', 'wpv-views')] = 'wpv-user field="display_name"';
       $user_fields[__('Description', 'wpv-views')] = 'wpv-user field="description"';
       $user_fields[__('Yahoo IM', 'wpv-views')] = 'wpv-user field="yim"';
       $user_fields[__('Jabber', 'wpv-views')] = 'wpv-user field="jabber"';
       $user_fields[__('AIM', 'wpv-views')] = 'wpv-user field="aim"';
       $user_fields[__('User Url', 'wpv-views')] = 'wpv-user field="user_url"';
       $user_fields[__('Registration Date', 'wpv-views')] = 'wpv-user field="user_registered"';
       $user_fields[__('User Status', 'wpv-views')] = 'wpv-user field="user_status"';
       $user_fields[__('User Spam Status', 'wpv-views')] =  'wpv-user field="spam"';
        foreach($user_fields as $name => $value) {
            $fields_accos[__('Basic', 'wpv-views')][] = array($name, $value);
        }
        $unused_field = array('comment_shortcuts','managenav-menuscolumnshidden','dismissed_wp_pointers','meta-box-order_dashboard','nav_menu_recently_edited',
            'primary_blog','rich_editing','source_domain','use_ssl','user_level','user-settings-time'
            ,'user-settings','dashboard_quick_press_last_post_id','capabilities','new_date','show_admin_bar_front','show_welcome_panel','show_highlight','admin_color'
            ,'language_pairs','first_name','last_name','name','nickname','description','yim','jabber','aim');
            $exclude_these_hidden_var = '/('.implode('|', $unused_field).')/';
        $meta_keys = get_user_meta_keys();
            $all_types_fields = get_option( 'wpcf-fields', array() );
            foreach ($meta_keys as $key) {
                $key_nicename = '';
                if ( function_exists('wpcf_init') ){
                    if (stripos($key, 'wpcf-') === 0) {
                        //
                    }
                    else {
                        if ( preg_match($exclude_these_hidden_var , $key) ){
                            continue;
                        }
                        $fields_accos[__('Users fields', 'wpv-views')][] = array($key, 'wpv-user field="'.$key.'"');    
                    }
                }
                else{
                    if ( preg_match($exclude_these_hidden_var , $key) ){
                            continue;
                    }
                    $fields_accos[__('Users fields', 'wpv-views')][] = array($key, 'wpv-user field="'.$key.'"');       
                }
                
            }
            
            if ( function_exists('wpcf_init') ){// TODO do the same for wpcf-fields for posts
                //Get types groups and fields
                $groups = wpcf_admin_fields_get_groups( 'wp-types-user-group' );            
                $user_id = wpcf_usermeta_get_user();
                $add = array();
                if ( !empty( $groups ) ) {
                    foreach ( $groups as $group_id => $group ) {
                        if ( empty( $group['is_active'] ) ) {
                            continue;
                        }
                        $fields = wpcf_admin_fields_get_fields_by_group( $group['id'],
                                'slug', true, false, true, 'wp-types-user-group',
                                'wpcf-usermeta' );
            
                        if ( !empty( $fields ) ) {
                            foreach ( $fields as $field_id => $field ) {
                                $add[] = $field['meta_key'];
                                $callback = 'wpcfFieldsEditorCallback(\'' . $field['id'] . '\', \'views-usermeta\', -1)';
                                /*$this->items[] = array($field['name'], 
                                  'types usermeta="'.$field['meta_key'].'"][/types',
                                  $group['name'],$callback);  */
                               $fields_accos[$group['name']][] = array($field['name'], 'types usermeta="'.$field['slug'].'"][/types');         
                              
                            }
                        }
                    }
                }

                //Get unused types fields
                $cf_types = wpcf_admin_fields_get_fields( true, true, false, 'wpcf-usermeta' );
                foreach ( $cf_types as $cf_id => $cf ) {
                     if ( !in_array( $cf['meta_key'], $add) ){
                         $callback = 'wpcfFieldsEditorCallback(\'' . $cf['id'] . '\', \'views-usermeta\', -1)';
                               /* $this->items[] = array($cf['name'], 
                                  'types usermeta="'.$cf['meta_key'].'"][/types',
                                  'Types fields',$callback); */
                         $fields_accos[__('Types fields', 'wpv-views')][] = array($cf['name'], 'types usermeta="'.$cf['slug'].'"][/types');  
                     }
                }
             }
             
             $view_available = $wpdb->get_results("SELECT ID, post_title FROM {$wpdb->posts} WHERE post_type='view' AND post_status in ('publish')");
             $fields_accos[__('Types fields', 'wpv-views')] = array();
		foreach($view_available as $view) {

			$view_settings = get_post_meta($view->ID, '_wpv_settings', true);
			if (isset($view_settings['query_type'][0]) && $view_settings['query_type'][0] == 'posts' && !$WP_Views->is_archive_view($view->ID)) {
			
				$fields_accos[__('Post View', 'wpv-views')][] = array($view->post_title,
					'wpv-view name="' . $view->post_title . '"'
				);
			}
		}
             
    }

    $content_templates = array( 'Content template' => array(array('None', 'wpv-post-body view_template="None"')) );
    if (function_exists('types_get_fields')) {
        $tmp = types_get_fields();
    } else {
        $tmp = array();
    }
    if (isset($tmp['fields'])) { $tmp = $tmp['fields']; }

    foreach ($fields_list as $items) {
        
        if (function_exists('wpcf_admin_fields_get_groups_by_field') &&
            (preg_match( '/-!-/', $items[2]) || preg_match('/wpcf-/', $items[0]) || preg_match('/\[types.*?field=\"(.*?)\"/', $items[0]) )) {
            
            if (preg_match('/\[types.*?field=\"(.*?)\"/', $items[0], $outp)) {
                $split = $outp[1];
            } else {
                $split = preg_replace('/wpcf-/', '', $items[0]);
            }
            
            //Field name, not a slug
            if ( isset( $tmp[$split]['name'] ) ) { // if: fix PHP Notice in the AJAX response when showing hidden fields
                $items[0] = $tmp[$split]['name'];
                $group = wpcf_admin_fields_get_groups_by_field( $tmp[$split]['id'] );
                foreach ($group as $id => $params) {
                    $group = $params['name'];
                }
            }
        } else {
            
            if ($items[2] == 'Field') {
                $items[2] = 'Custom fields';
            }
            
            $group = $items[2];
        }

        if ( $items[2] == __('Content template', 'wpv-views') ) {
        //    global $wpdb;
          //  $items[0] = $wpdb->get_var("SELECT post_title  FROM {$wpdb->posts} WHERE post_title = '{$items[0]}'");

            $content_templates['Content template'][] = array($items[0], $items[1]);
        }
        
        if ( $items[1] == 'wpml-string' ) { // Take out of the Layout Wizard the new wpml-string Translatable string shortcode added to V popups
		$group = '';
        }

        if (!empty($group)) {
            $fields_accos[$group][] = array($items[0], $items[1]);
        }

    } 
    
    
    
    if ((string)$settings["query_type"][0]=='posts') {
        // add taxonomies
        $assoc = array();
        $taxonomies = get_taxonomies(array(), 'objects');
        $exclude_tax_slugs = array();
	$exclude_tax_slugs = apply_filters( 'wpv_admin_exclude_tax_slugs', $exclude_tax_slugs );
        foreach($taxonomies as $tname => $tax) {
            if ( !in_array($tname, $exclude_tax_slugs ) && $tax->show_ui)
                $assoc['Taxonomies'][] = array($tax->label, 'wpv-post-taxonomy type="'.$tname.'" separator=", " format="link" show="name"');
        }

        // add user meta fields
        if (function_exists('wpcf_admin_post_add_usermeta_to_editor_js')) {
            $usermeta_fields_list = wpcf_admin_post_add_usermeta_to_editor_js( array() );
            foreach ($usermeta_fields_list as $group => $items) {
                foreach($items as $field) {
                    $assoc[$group][] = array($field[0], $field[1]);
                }
            }
        }
        
        // Add after the Basic fields.
        $fields_accos = array_slice($fields_accos, 0, 1, true) +
            $assoc +
            array_slice($fields_accos, 1, count($fields_accos)-1, true);
        
        
    }
    ob_start();
?>
<li id="layout-wizard-style_<?php echo ( isset($_POST['id']) ) ? $_POST['id'] : $count; ?>">
    <i class="icon-move js-layout-wizard-move-field"></i>
    <select name="layout-wizard-style" class="js-select2 js-layout-wizard-item">
        <?php
            $selected_value = '';
            $typename = '';
            $selected_body = '';
            $selected_body_template = '';
            $selected_found = false;
            $user_fields_with_head = array('user_email', 'display_name', 'user_login', 'user_url', 'user_registered', 'user_status', 'spam');
            if ( !isset( $_POST['selected'] ) ) $_POST['selected'] = '';
            foreach ($fields_accos as $group_title => $group_items) {
            ?>
            <optgroup label="<?php echo $group_title; ?>">
                <?php foreach ($group_items as $items) {
                    $value = $items[1];
                    $istype = false;
                    $typename2 = '';
                    
                    $selected = (mysql_real_escape_string($_POST['selected']) == '['.mysql_real_escape_string($items[1]).']') ? "selected" : "";
                    
                    $selected_striped = substr(stripslashes( $_POST['selected']) , 1, -1);
			if ( $selected_striped == stripslashes( $value ) ) { // Dirty hack: sometimes the selected item was not being set for user shortcodes
			$selected = "selected";
			if ( preg_match('/\[types.*usermeta=\"(.*?)\"/', $_POST['selected'], $outp) ) {
				$typename = $outp[1];
                        }
                    }
                    $_POST['selected'] = stripslashes($_POST['selected']);
                    
                    if (!$selected && preg_match('/wpv-post-taxonomy/',$items[1]) && trim($_POST['selected'], '[]') == $items[1]) {
                        $selected = 'selected';
                    }

                    if (!$selected && preg_match('/wpv-view/',$items[1]) && trim($_POST['selected'], '[]') == $items[1]) {
                        $selected = 'selected';
                    }
                    
                    if (!$selected && preg_match('/\[types.*?field=\"(.*?)\"/', $_POST['selected']) && preg_match('/"wpcf\-.*?"/',$items[1])) {

                        preg_match('/\[types.*?field=\"(.*?)\"/', $_POST['selected'], $outp);
                        $sel = $outp[1];
                        preg_match('/"wpcf\-(.*?)"/',$items[1], $outp);
                        $cur = $outp[1];

                        $items[1] = trim($_POST['selected'], '[]');

                        $selected = ($cur==$sel)?'selected':'';
                        $typename = $sel;
                    }

                    if (!$selected && preg_match('/\[types.*usermeta=\"(.*?)\"/', $_POST['selected']) && preg_match('/types.*usermeta=\"(.*?)\"/',$items[1])) {

                        preg_match('/\[types.*usermeta=\"(.*?)\"/', $_POST['selected'], $outp);
                        $sel = $outp[1];
                        preg_match('/types.*usermeta=\"(.*?)\"/',$items[1], $outp);
                        $cur = $outp[1];
                        $usermeta_field = $sel; 
                            
                        $items[1] = trim($_POST['selected'], '[]');
                        
                        $selected = ($cur==$sel)?'selected':'';
                        if ($selected) {
                            $value = $items[1];
                        }
                        
                        $typename = $sel;
                        $typename2 = $cur;                       
                        $istype = true;
                    }

                    if (!$selected && preg_match('/wpv-post-body/', $_POST['selected']) && preg_match('/Body/',$items[0])) {
                        $selected_body = $items[0];
                        preg_match('/view_template\="(.*?)"/', $_POST['selected'], $out);
                        $selected_body_template = $out[1];
                        global $wpdb;
                        $selected_body_template = $wpdb->get_var("SELECT post_title FROM {$wpdb->posts} WHERE post_name = '$selected_body_template'");
                   //     $value = trim($_POST['selected'], '[]');
                   //     if (!$selected_body_template) {
                   //     $value = $items[1];
                   //     }
                        $selected = 'selected';
                    }

                    if ($selected=='selected') {
                        $selected_value = $items[1];
                        $selected_found = true;
                    }

                    $s = preg_match('/"wpcf\-(.*?)"/', $value, $outp);
                    if ($s) {
                        $saved_fields = array();
                        $sets = get_post_meta($_POST["view_id"], '_wpv_layout_settings', true);
                        if ( isset( $sets["real_fields"] ) ) $saved_fields = $sets["real_fields"];
                        
                        $typename2 = $outp[1];
                        $value = isset($saved_fields[$_POST["id"]]) && preg_match('/types.*?field=\"'.$outp[1].'\"/', $saved_fields[$_POST["id"]])?trim($saved_fields[$_POST["id"]], '[]'):'types field="'.$outp[1].'" id=""][/types';
                        
                        $istype = true;
                        
                        if ( !function_exists( 'wpcf_init' ) ) {
				$istype = false;
				if ( strpos(  $items[1], 'wpv-user' ) === 0 ) {
					$value =  $items[1];
				} else {
					$value = 'wpv-post-field name="wpcf-' . $outp[1] .  '"';
				}
                        }
                    }
                    
                    $head = '';
                    if ( substr( $value, 0, 17 ) === "wpv-post-taxonomy" ) {
                        $head = 'wpv-post-taxonomy';
                    } else if ( substr( $value, 0, 14 ) === "wpv-post-field" ) {
            			$head = 'post-field-' . $items[0];
            			// if it is a new WooCommerce Views field
            			if ( preg_match('/\wpv-post-field.*?name=\"views_woo(.*?)\"/', $value, $woo_match) ) {
					$head = 'post-field-views_woo' . $woo_match[1];
				}
                    } else if ( substr( $value, 0, 8 ) === "wpv-post" ) {
            			$head = substr(current(explode(' ',$value)), 4);
            			if ( substr( $value, 0, 15 ) === "wpv-post-status" ) {
					$head = '';
            			}
                    } else if ( substr( $value, 0, 8 ) === "wpv-view" ) {
            			$head = 'post-view';
                    } else if ( substr( $value, 0, 5 ) === "types" ) {
                        if ( !isset($outp[1]) && isset($usermeta_field) ) {
            			 $outp[1] = $usermeta_field;    
            			}
                        else if ( !isset($outp[1]) ) {
                         $outp[1] = '';    
                        }
                        if ( empty( $typename2 ) ){
                           $typename2 = $outp[1];
                        }
                        
                        $head = 'types-field-' . $outp[1]; // Add a table column header only if it's a field for posts
                        
                        if (  empty($typename2) || empty($outp[1]) ){
                            preg_match("/(usermeta|field)=\"([^\"]+)\"/", $value, $new_match);
                            $typename2 = $outp[1] = $new_match[2];
                            $head = ''; // If it's a usermeta field, do not add the table column header
                        }
                        if ( !empty( $typename2 ) ) {
				$istype = true;
                        }
                    } else if ( substr( $value, 0, 12 ) === "wpv-taxonomy" ) { // heading table solumns for wpv-taxonomy-* shortcodes
			if ( in_array( $value, array('wpv-taxonomy-link', 'wpv-taxonomy-title' ) ) ) {
				$head = substr($value, 4);
			}
			if ( $value == 'wpv-taxonomy-post-count' ) {
				$head = 'taxonomy-post_count';
			}
                    } else if ( substr( $value, 0, 8 ) === "wpv-user" ) { // heaading table columns for wpv-user shortcodes
			preg_match('/\wpv-user.*?field=\"(.*?)\"/', $value, $new_match);
			if ( isset( $new_match[1] ) && in_array( $new_match[1], $user_fields_with_head ) ) {
				$head = $new_match[1];
			}
                    }
                    
                    ?>
                    <option value="<?php echo base64_encode('['.$value.']'); ?>"
                            data-fieldname="<?php echo $items[0]; ?>"
                            data-headename="<?php echo $head; ?>"
                            <?php if (
                            $istype 
                      //      ||
                      //      !empty($typename2) 
                            ) { ?>
                            
                            data-istype="1"
                            data-typename="<?php echo $typename2; ?>"
                            <?php } ?>
                            data-rowtitle="<?php echo $items[0]; ?>" <?php echo $selected; ?>>
                        <?php echo $items[0]; ?>
                    </option>

                <?php } ?>
            </optgroup>
        <?php } ?>
    </select>
    <?php //aditional combo for body-templates ?>

    <p class="layout-wizard-body-template-text js-layout-wizard-body-template-text <?php if ( !preg_match('/wpv-post-body/', $_POST['selected']) || !empty( $selected_body_template ) ) { ?>hidden<?php } ?>"><?php echo __('Using Content Template', 'wpv-views'); ?></p>
    <select name="layout-wizard-body-template" class="layout-wizard-body-template <?php if ( !preg_match('/wpv-post-body/', $_POST['selected']) || !empty( $selected_body_template ) ) { ?>hidden<?php } ?>">
        <?php foreach ($content_templates['Content template'] as $items): ?>
        	<option value="<?php echo base64_encode('['.$items[1].']'); ?>" data-rowtitle="<?php echo $items[0]; ?>" <?php if (trim($items[0])==trim($selected_body_template)) echo 'selected' ?> > <?php echo $items[0]; ?></option>
        <?php endforeach; ?>
    </select>
    <?php
     if ((string)$settings["query_type"][0]=='users') {
        $type_usermeta_addon = ' data-type="views-usermeta"';
     }
    ?>
    <button class="button-secondary js-custom-types-fields" 
    <?php if (!preg_match('/types.*?field=|types.*?usermeta=/', $selected_value) || !function_exists('types_get_fields')) { ?> style="display: none" <?php } else { ?>  rel="<?php echo $typename; ?>" <?php } ?>
        <?php if ( isset($type_usermeta_addon) ) { echo $type_usermeta_addon;}?>>
    	<?php echo __('Edit', 'wpv-views'); ?>
    </button>
    <i class="icon-remove-sign js-layout-wizard-remove-field"></i>
</li>
<?php
    $result = array('html' => ob_get_clean(),
                    'selected_found' => $selected_found);
    echo json_encode($result);
    die();
}