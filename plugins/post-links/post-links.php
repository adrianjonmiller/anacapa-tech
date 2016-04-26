<?php
/*
Plugin Name: Post links 
Version: 0.3.3
Plugin URI: http://labs.dagensskiva.com/
Author URI: http://henrikmelin.se/
Description:  Add (any number of) links to a post. Use &lt;?php post_links(); ?&gt; in your template to see them.
Author: Henrik Melin, Kal Ström

	USAGE:
	
	In the Admin section a Link section will appear on the Write/Edit page, normally underneath
	the Upload section.

	The template function works as follows:
	
	<?php post_links(); ?> produced as dl/dt/dd list
	<?php post_links('dl'); ?> produced as dl/dt/dd list
	<?php post_links('ul'); ?> produced as ul/li list
	
	Alternatively, the html can be stored in a variable:

	<?php $links = get_post_links(); ?> returns a dl/dt/dd list

	For more information, see http://labs.dagensskiva.com/plugins/post-links/	
	
	LICENCE:

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
 
$post_links_open_in_blank = false;



/** Don't edit below this line **/

 
// Link fields
$lfds = array('url', 'title', 'description');
// This prefixes the custom fields and js functions.
$cfpx = 'dsc_link_';

/*
**  p2m_admin_build_divs ( )
**
**  This function is called by a hook and interates through our custom fiels and generates
**  the html and javascript code for each of them.
**
*/
function post_links_content () {
	global $cfpx;
?>
	<span id="<?php echo $cfpx; ?>div"> 		
	<?php
		// Get the html for links stored in db
		echo p2m_link_html(0);
		for ($i=1; $i < p2m_get_nbr_links(); $i++) echo p2m_link_html($i);
	?>
	</span>
	<div style="margin-top: 5px; clear: left;">
		<input class="button" type="button" value="<?php _e('Add new link', 'post-links'); ?>" onClick="<?php echo $cfpx; ?>create_new();">
		<input class="button" name="save" type="submit" value="<?php _e('Save and continue editing', 'post-links'); ?>" />
	</div>
<?php
}


/*
**		p2m_link_html
**
*/
function p2m_link_html ($nbr) {
	global $cfpx;
	$post_id = $_GET['post'];
	$color   = (!($nbr & 1)) ? 'ffffff' : 'f4f4f4'; 
	$title = stripslashes(get_post_meta($post_id, $cfpx . 'title_'.$nbr, true));
	$url = get_post_meta($post_id, $cfpx . 'url_'.$nbr, true);
	$description = stripslashes(get_post_meta($post_id, $cfpx . 'description_'.$nbr, true));	
	$html = '
			<div id="'.$cfpx.'external_'.$nbr.'" style="background: #'.$color.'; padding: .2em 0px; border: 0px;">
				<div style="float: right; width: 100px; margin-right: 5px;">
					<input class="button" type="button" value ="'.__('Move up', 'post-links').'" onclick="'.$cfpx.'move_up('.$nbr.');" style="width: 70px;" />
					<input class="button" type="button" value ="'.__('Move down', 'post-links').'" onclick="'.$cfpx.'move_down('.$nbr.');" style="width: 70px;" />
					<input class="button" type="button" value ="'.__('Delete', 'post-links').'" onclick="'.$cfpx.'delete('.$nbr.');" style="width: 70px;" />
				</div>
				<div style="clear: left; margin-right: 105px;">
					<div>
						<label for="'.$cfpx.'url_'.$nbr.'" style="text-align: right; width: 15%; display: block; float: left; margin-right: 1%;"><strong>'.__('URL', 'post-links').'</strong></label>
						<input type="text" name="'.$cfpx.'url_'.$nbr.'" id="'.$cfpx.'url_'.$nbr.'"  value="'. $url .'" style="width: 75%;" />
					</div>
					<div>
						<label for="'.$cfpx.'title_'.$nbr.'" style="text-align: right; width: 15%; display: block; float: left; margin-right: 1%;"><strong>'.__('Title', 'post-links').'</strong></label>
						<input type="text" name="'.$cfpx.'title_'.$nbr.'" id="'.$cfpx.'title_'.$nbr.'" value="'. $title .'" style="width: 75%" />
					</div>
					<div>
						<label for="'.$cfpx.'description_'.$nbr.'" style="text-align: right; width: 15%; display: block; float: left; margin-right: 1%;"><strong>'.__('Description', 'post-links').'</strong></label>
						<textarea name="'.$cfpx.'description_'.$nbr.'" id="'.$cfpx.'description_'.$nbr.'" style="width: 75%;" cols="1">' . $description . '</textarea>
					</div>
				</div>
			</div>';
	return $html;
}

/*
**		p2m_get_nbr_links ()
**
*/
function p2m_get_nbr_links($post = '') {
	global $post, $cfpx, $lfds;
	$post_id = ( $_GET['post'] ) ? $_GET['post'] : $post->ID;
	$nbr  = 0;
	while (get_post_meta($post_id, $cfpx . $lfds[0] . '_' . $nbr, true)) $nbr++;
	return $nbr;
}

/*
**		p2m_save_links ( )
**
*/
function post_links_save ($post_id, $post) {
	global $lfds, $cfpx;
	if (!$post_id) $post_id = attribute_escape($_POST['post_ID']);
	if (!$post_id) return $post;

	$nbr = 0;
	while ($_POST[$cfpx . $lfds[0] . '_' . $nbr] != '') {
		foreach ($lfds as $field) {
			$key = $cfpx . $field . '_' . $nbr;
			$value = stripslashes(attribute_escape($_POST[$key]));
			if (!add_post_meta($post_id, $key, $value, true)) 
				update_post_meta($post_id, $key, $value);
		}
		$nbr++;
	}
	return $post;
}

/*
**		post_links ( )
**		get_post_links ( )
**
*/
function post_links($args = '') { echo get_post_links($args); }
function get_post_links($args = '') {
	global $post, $cfpx;
	
	// Parse args
	$defaults = array('format' => 'dl', 'div' => '', 'title' => '');
	$args = wp_parse_args($args, $defaults);	
	if (get_post_meta($post->ID, $cfpx . 'url_0' , true)) {
		$html = '';
		if ($div = $args['div']) $html .= "<div id='$div'>\n";
		if ($title = $args['title']) $html .= "<h3>$title</h3>\n";
		$html .= "<" . $args['format'] . " class=\"\">\n";
		for ($i = 0; $i < p2m_get_nbr_links(); $i++) {
			$url = wptexturize(get_post_meta($post->ID, $cfpx . 'url_' . $i, true));
			$title = wptexturize(get_post_meta($post->ID, $cfpx . 'title_' . $i, true));
			$description = wptexturize(get_post_meta($post->ID, $cfpx . 'description_' . $i, true));
//			$description = stripslashes($description);
			if (!$title) $title = $url;
			$target = ($post_links_open_in_blank) ? " target=\"_blank\"" : '';
			if ($args['format'] == 'dl') {
				$html .= "<dt><a href=\"$url\" $target>$title</a></dt>\n";
				$html .= "<dd>$description</dd>\n";
			}
			else if ($args['format'] == 'ul') {
				$html .= "<li><p><a href=\"$url\" $target>$title</a></p>\n";
				if (description) $html .= "<p>$description</p>";
				$html .= "</li>\n";		
			}
		}
		$html .= "</" . $args['format'] . ">";
		if ($div = $args['div']) $html .= '</div>';
		return $html;
	}
}


/*
**		p2m_translate ( )
**
*/
function post_links_translate(){
    // Load a language
	load_plugin_textdomain('post-links', PLUGINDIR . '/' . dirname(plugin_basename (__FILE__)) );
}

function post_links_add() {

	// Queue JavaScript
	wp_enqueue_script('jquery', false) ; 
	// $path = get_option('siteurl') . '/wp-content/plugins/' . basename(dirname(__FILE__)) . '/post-links-js.php';
	
	if ( defined('WP_PLUGIN_URL') ) $path = WP_PLUGIN_URL . '/' . dirname(plugin_basename(__FILE__)). '/post-links-js.php';
	else $path = get_option('siteurl') . '/wp-content/plugins/' . basename(dirname(__FILE__)) . '/post-links-js.php';

	wp_enqueue_script('post_links_js', $path, 'jquery');

	// Register the meta boxes
	add_meta_box('links', __('Links', 'post-links'), 'post_links_content', 'post', 'normal');
	add_meta_box('links', __('Links', 'post-links'), 'post_links_content', 'page', 'normal');
}


/*
**  These are the WP hooks.
*/
add_action('init', 'post_links_translate'); 
//add_action('admin_footer', 'p2m_link_build_ui'); 
add_action('save_post', 'post_links_save', 10, 2);
add_action('publish_post', 'post_links_save', 10, 2);
add_action('admin_init', 'post_links_add');

?>
