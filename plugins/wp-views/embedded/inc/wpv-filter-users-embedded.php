<?php

/**
 * Add a filter to add the query by users
 */
function get_users_query($view_settings) {
    global $WP_Views, $current_user, $wplogger, $no_parameter_found;
    $items = array();
    //print_r($view_settings);exit;
    $args = array();
    $include = array();
    $exclude = array();
    
    
    if ( isset( $view_settings['roles_type'][0] ) ){
        $args['role'] = $view_settings['roles_type'][0];
    }
    if ( isset( $view_settings['users-show-current'] ) && $view_settings['users-show-current'] == 1 ){
        $exclude[] = $current_user->ID;
    }
    if ( isset( $view_settings['users_orderby'] ) ){
        $args['orderby'] = $view_settings['users_orderby'];
    }
    if ( isset( $view_settings['users_order'] ) ){
        $args['order'] = $view_settings['users_order'];
    }
    
    // Users orderby and order based on URL params - for table sorting
    
    if (isset($_GET['wpv_column_sort_id']) && esc_attr($_GET['wpv_column_sort_id']) != '' && esc_attr($_GET['wpv_view_count']) == $WP_Views->get_view_count()) {
        $field = esc_attr($_GET['wpv_column_sort_id']);
        if ( in_array( $field, array('user_email', 'user_login', 'display_name', 'user_url', 'user_registered') ) ) {
		$args['orderby'] = $field;
		
		if (isset($_GET['wpv_column_sort_dir']) && esc_attr($_GET['wpv_column_sort_dir']) != '') {
			$args['order'] = strtoupper(esc_attr($_GET['wpv_column_sort_dir']));
		}
        
        }
        
    }
    
    //Limit & Offest
    if ( $view_settings['users_limit'] !== '-1' && $view_settings['users_limit'] !== -1 ){
        $args['number'] = $view_settings['users_limit'];
    }
    $args['offset'] = $view_settings['users_offset'];
    
    //Users filter
    if ( isset($view_settings['users_mode']) && !empty($view_settings['users_mode'][0])  ){
        
        //Include/Exclude list of users
        if ( $view_settings['users_mode'][0] == 'this_user' ){
            if ( $view_settings['users_query_in'] == 'exclude' ){
                if ( !empty($view_settings['users_id']) ){
                    $user_list = array_map('trim', explode(',', $view_settings['users_id']));
                    for ($i=0;$i<count($user_list);$i++){
                        $exclude[] = $user_list[$i];
                    }
                }
            }
            if ( $view_settings['users_query_in'] == 'include' ){
                if ( !empty($view_settings['users_id']) ){
                    $user_list = array_map('trim', explode(',', $view_settings['users_id']));
                    for ($i=0;$i<count($user_list);$i++){
                        $include[] = $user_list[$i];
                    }
                    $args['include'] = $include;
                }
            }
        }
        
        //Show user by url
        if ( $view_settings['users_mode'][0] == 'by_url' ){
            $user_list = array();
            if ( isset($_GET[$view_settings['users_url']]) ){
                if ( is_array($_GET[$view_settings['users_url']]) ){
                    if ( $view_settings['users_url_type'] == 'id' ){   
                        $user_list = $_GET[$view_settings['users_url']];
                    }else{
                        $users = $_GET[$view_settings['users_url']];
                        for ( $i=0;$i<count($users);$i++){
                            if ( $cuser_id = username_exists( $users[$i] ) ){
                                $user_list[] = $cuser_id;
                            }
                        }
                    }
                }
                else{
                    if ( $view_settings['users_url_type'] == 'id' ){   
                        $user_list = array($_GET[$view_settings['users_url']]);
                    }else{
                        if ( $cuser_id = username_exists( $_GET[$view_settings['users_url']] ) ){
                             $user_list = array($cuser_id);  
                        }
                    }
                }
                
                if ( $view_settings['users_query_in'] == 'exclude' ){
                    for ($i=0;$i<count($user_list);$i++){
                        $exclude[] = $user_list[$i];
                    }
                }else{
			if ( empty( $user_list ) ) $user_list = array('0');
                    $args['include'] = $user_list;
                }
            }   
        }
        
        //Show user by shortcode
        if ( $view_settings['users_mode'][0] == 'shortcode' ){
            
            $user_list = array();
            $list = $WP_Views->view_shortcode_attributes[0];
            if ( isset($list[$view_settings['users_shortcode']]) ){
                $users = $list[$view_settings['users_shortcode']];
                $users = array_map('trim', explode(',', $users));
                for ( $i=0;$i<count($users);$i++){
                      if ( $view_settings['users_shortcode_type'] == 'id' ){
                            $user_list =  $users;  
                      }
                      else{
                        for ( $i=0;$i<count($users);$i++){
                            if ( $cuser_id = username_exists( $users[$i] ) ){
                                $user_list[] = $cuser_id;
                            }
                        }  
                      }  
                }
                
                if ( $view_settings['users_query_in'] == 'exclude' ){
                    for ($i=0;$i<count($user_list);$i++){
                        $exclude[] = $user_list[$i];
                    }
                }else{
			if ( empty( $user_list ) ) $user_list = array('0');
                    $args['include'] = $user_list;
                }
            }   
        }
        
    }
    
    
    //Usermeta filter
    $total_meta = 0;
    foreach ($view_settings as $index => $value) {
       if ( preg_match("/usermeta-field-(.*)_type/",$index, $match) ){
           $field = $match[1];
           $type = $value;
           $compare = $view_settings['usermeta-field-'.$field.'_compare'];
           $value = $view_settings['usermeta-field-'.$field.'_value'];
           $value = wpv_apply_user_functions($value);
           if ( $value != $no_parameter_found ) {
		    
		if ( $field == 'user_email' || $field == 'user_login' || $field == 'user_url' || $field == 'display_name' ){
    		$args['search'] = ''.$value.'';// remove * wildcards
    		$args['search_columns'] = array($field);
		}else{
    		$total_meta++;
    		$args['meta_query'][] = array( 'key' => $field, 'value' => $value, 'compare' => $compare, 'type' => $type );
		}
	   }

       }
    }
    if ( $total_meta >1 ){
        $args['meta_query']['relation'] = $view_settings['usermeta_fields_relationship'];
    }
    
    if ( !empty( $exclude ) ) {
	$args['exclude'] = $exclude;
    }
    
    $wplogger->log($args, WPLOG_DEBUG);
    
    $args = apply_filters('wpv_filter_user_query', $args, $view_settings);
    
    //print_r($args);
    $user_query = new WP_User_Query( $args );
    if ( ! empty( $user_query->results ) ) {
        $items = $user_query->results;
       
    }
    
    $items = apply_filters('wpv_filter_user_post_query', $items, $args, $view_settings);
    
    return $items;
}