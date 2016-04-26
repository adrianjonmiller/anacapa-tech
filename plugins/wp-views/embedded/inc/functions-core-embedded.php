<?php

/**
* Check the existence of a kind of View (normal or archive)
*
* @param $query_mode kind of View object: normal or archive
* @return array() of relevant Views if they exists or false if not
*/

function wpv_check_views_exists( $query_mode ) {
	$all_views_ids = _wpv_get_all_view_ids($query_mode);
	if ( count( $all_views_ids ) != 0 ) {
		return $all_views_ids;
	} else {
		return false;
	}
}

/**
* Get the IDs for all Views of a kind of View (normal or archive)
*
* @param $view_query_mode kind of View object: normal or archive
* @return array() of relevant Views if they exists or empty array if not
*/

function _wpv_get_all_view_ids( $view_query_mode ) {
	global $wpdb, $WP_Views;
	$q = ( 'SELECT ID FROM ' . $wpdb->prefix . 'posts WHERE post_type="view"' );
	$all_views = $wpdb->get_results( $q );
	$view_ids = array();
	foreach ( $all_views as $key => $view ) {
		$settings = $WP_Views->get_view_settings( $view->ID );
		if( $settings['view-query-mode'] != $view_query_mode ) {
			unset( $all_views[$key] );
		} else {
			$view_ids[] = $view->ID;
		}
	}
	return $view_ids;
}