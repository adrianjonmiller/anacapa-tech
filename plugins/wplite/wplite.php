<?php
/*
Plugin Name: WPlite
Plugin URI: http://wplite.nanogeex.com
Description: Wordpress, without the fat.
Version: 2.8.4
Author: Muhammad Hirman <muhammadhirman@gmail.com>
Author URI: http://mh.nanogeex.com
*/

/*  

Copyright 2009  WPlite  (email: muhammadhirman@gmail.com)

This program is free software: you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation, either version 3 of the License, or
(at your option) any later version.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program.  If not, see <http://www.gnu.org/licenses/>.

*/


if (isset($_POST['update_wplite_options']))
	wplite_save_options();

add_action('admin_menu', 'wplite_init');
add_action('admin_menu', 'wplite_disable_menus');

add_action('admin_head', 'wplite_save_meta_boxes');
add_action('admin_head', 'wplite_disable_metas');

register_activation_hook(__FILE__, 'set_wplite_options');
register_deactivation_hook(__FILE__, 'unset_wplite_options');

$wplite_menu = null;
$wplite_submenu = null;

function wplite_init() {
	add_options_page('WPlite', 'WPlite', 8, basename(__FILE__), 'wplite_options_page');

	// save default menu and submenu, otherwise it will be permanently hidden
	// and we can't display it in our options
	global $wplite_menu, $wplite_submenu, $menu, $submenu;
	$wplite_menu = $menu;
	$wplite_submenu = $submenu;
}

function wplite_disable_menus() {

	if (current_user_can('manage_options'))
		return;
	
	global $menu, $submenu;

	$options = wplite_get_options();
	$disabled_menu_items    = $options['disabled_menu_items'];
	$disabled_submenu_items = $options['disabled_submenu_items'];

	if (in_array('index.php', $disabled_menu_items))
		wplite_remove_the_dashboard(); 

	foreach ($menu as $index => $item) {
		if ($item == 'index.php')
			continue;

		if (in_array($item[2], $disabled_menu_items))
			unset($menu[$index]);
	
		if (!empty($submenu[$item[2]]))
			foreach ($submenu[$item[2]] as $subindex => $subitem) 
				if (in_array($subitem[2], $disabled_submenu_items))
					unset($submenu[$item[2]][$subindex]);
	}
}

function wplite_save_meta_boxes() {
	global $wp_meta_boxes, $title;

	if (!$wp_meta_boxes)
		return;
	
	$options = wplite_get_options();

	$id = str_replace(' ', '', strtolower($title));

	$pages = &$options['meta_box_pages'];
	if (!$pages || !is_array($pages))
		$pages = array();
	$pages[$id] = current($wp_meta_boxes);

	$titles = &$options['meta_box_page_titles'];
	$titles[$id] = $title;

	wplite_update_options($options);
}

function wplite_disable_metas() {
	if (current_user_can('manage_options'))
		return;
	
	global $wp_meta_boxes, $title;
	if (!$wp_meta_boxes)
		return;

	$options = wplite_get_options();
	$disabled_meta_boxes = $options['disabled_meta_boxes'];

	$page_id = strtolower(str_replace(' ', '', $title));

	foreach ($wp_meta_boxes as &$page) {
		foreach ($page as &$section) {
			$core = &$section['core'];
			foreach ($core as $box_id => $box) {
				$id = $page_id . '_' . $box_id;
				if (in_array($id, $disabled_meta_boxes)) {
					unset($core[$box_id]);
				}
			}
		}
	}
}

function wplite_save_options() {
	$disabled_menu_items = $_POST['disabled_menu_items'];
	if (!$disabled_menu_items)
		$disabled_menu_items = array();

	$disabled_submenu_items = $_POST['disabled_submenu_items'];
	if (!$disabled_submenu_items)
		$disabled_submenu_items = array();
	
	$disabled_meta_boxes = $_POST['disabled_meta_boxes'];
	if (!$disabled_meta_boxes)
		$disabled_meta_boxes = array();

	$options = wplite_get_options();
	$options['disabled_menu_items']    = $disabled_menu_items;
	$options['disabled_submenu_items'] = $disabled_submenu_items;
	$options['disabled_meta_boxes']    = $disabled_meta_boxes;
	wplite_update_options($options);
}

function wplite_options_page() {

	$options = wplite_get_options();

	if (isset($_POST['update_wplite_options']))
		echo '<div id="message" class="updated fade"><p>Options saved.</p></div>';

	?>
	
	<div class="wrap">
	
	<h2>WPlite</h2>

	<form method="post">

	<table border="0">
	<tr>
		<td valign="top" style="padding-right: 2em">

			<h3>Disable Menu Items</h3>
		
	<?php

	global $wplite_menu, $wplite_submenu;

	$disabled_menu_items = $options['disabled_menu_items'];
	if (!$disabled_menu_items)
		$disabled_menu_items = array();

	$disabled_submenu_items = $options['disabled_submenu_items'];
	if (!$disabled_submenu_items)
		$disabled_submenu_items = array();

	foreach ($wplite_menu as $item) {
		$id    = $item[2];
		$name  = $item[0];
		$class = $item[4];

		$checked    = in_array($id, $disabled_menu_items);
		$checkedStr = $checked ? ' checked="checked"' : '';

		if (!$name) {
			if ($class === 'wp-menu-separator') 
				$name = "(separator)";
			else if ($class === 'wp-menu-separator-last')
				continue;
		}
		$name = preg_replace("/<span.*>.*<\/span>/i", "", $name);

		echo '<input type="checkbox"' .	$checkedStr . 
			' name="disabled_menu_items[]"  value="'.$id.'" />&nbsp;' . $name . "<br />\n";

		$submenu = $wplite_submenu[$id];

		if ($submenu)  {
			foreach ($submenu as $item) {
				$id   = $item[2];
				$name = $item[0];	

				// exclude WPlite itself
				if ($id === basename(__FILE__))
					continue;

				$checked    = in_array($id, $disabled_submenu_items);
				$checkedStr = $checked ? ' checked="checked"' : '';

				echo '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<input type="checkbox"' . $checkedStr .
					' name="disabled_submenu_items[]" value="' . $id . '" />&nbsp;' . $name . "<br />\n";
			}
		}

	}

	?>
	
			
		</td>
		<td valign="top" style="padding-right: 2em">
			<h3>Disable Meta Boxes</h3>
	
	<?php

	$meta_box_pages = $options['meta_box_pages'];
	if (!$meta_box_pages)
		$meta_box_pages = array();
		
	$meta_box_page_titles = $options['meta_box_page_titles'];
	if (!$meta_box_page_titles)
		$meta_box_page_titles = array();

	$disabled_boxes = $options['disabled_meta_boxes'];
	if (!$disabled_boxes || !is_array($disabled_boxes))
		$disabled_boxes = array();
	
	if (empty($meta_box_pages)) {
		echo '<p>Nothing to disable yet.</p>',
			'<p>Please visit a page with \'meta boxes\' e.g. Dashboard',
			'and return to this page to see more options.</p>';

	} else {
	  
		foreach ($meta_box_pages as $page_id => $page) {
			$title = $meta_box_page_titles[$page_id];

			echo $title . '<br />';

			foreach ($page as $section) {
				$core = current($section);
				foreach ($core as $box_id => $box) {
					$id = $page_id . '_' . $box_id;

					$name = $box['title'];
					$name = preg_replace('/<span.*<\/span>/', '', $name);

					$checked = in_array($id, $disabled_boxes);
					$checkedStr = $checked ? ' checked="checked"' : '';
					echo '<input type="checkbox"' . $checkedStr . ' name="disabled_meta_boxes[]" value="' 
						. $id . '" />&nbsp;' .  $name . "<br />\n";
				}
			}

			echo '<br />';
		}
	}

	?>
	
		</td>
	</tr>
	</table> 
	
	<p class="submit">
		<input name="submit" type="submit" value="Save Options" />
	</p> 
	
	<input type="hidden" name="update_wplite_options" value="1" /></form></div>

	<?php
}

function wplite_get_options() {
	$options = get_option('wplite_options');

	if ($options && is_array($options) && !empty($options))
		return $options; 
	
	return wplite_default_options();
}

function wplite_default_options() {
	$options = array(
		'default_menu_items' => array(),
		'default_meta_boxes' => array(),
		'disabled_menu_items' => array(),
		'disabled_meta_boxes' => array()
	);

	return $options;
}

function wplite_update_option($name, $value) {
	$options = wplite_get_options();
	$options[$name] = $value;
	wplite_update_options($options);
}

function wplite_update_options($options) {
	update_option('wplite_options', $options);
}

function set_wplite_options() {
	add_option('wplite_options');
	update_option('wplite_options', wplite_default_options());
}

function unset_wplite_options() {
	delete_option('wplite_options');
}

function wplite_remove_the_dashboard() {
	global $menu, $submenu, $user_ID;
	$the_user = new WP_User($user_ID);
	reset($menu); $page = key($menu);
	while ((__('Dashboard') != $menu[$page][0]) && next($menu))
		$page = key($menu);
	if (__('Dashboard') == $menu[$page][0]) unset($menu[$page]);
		reset($menu); $page = key($menu);
	while (!$the_user->has_cap($menu[$page][1]) && next($menu))
		$page = key($menu);
	if (preg_match('#wp-admin/?(index.php)?$#',$_SERVER['REQUEST_URI']) && ('index.php' != $menu[$page][2]))
		wp_redirect(get_option('siteurl') . '/wp-admin/' . $menu[$page][2]);
}

function wplite_debug($var) {
	echo '<pre>' . print_r($var, true) . '</pre>';
}

?>
