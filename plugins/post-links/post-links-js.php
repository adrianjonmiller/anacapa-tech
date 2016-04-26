<?php
	require_once(dirname(__FILE__).'/../../../wp-config.php');
	header("Content-Type: text/javascript");
	global $lfds, $cfpx;
?>
		// var theirbox = $('postexcerpt');
		// var ourbox = $('<?php echo $cfpx; ?>box_div');
		// theirbox.parentNode.insertBefore(ourbox, theirbox);

		function <?php echo $cfpx; ?>create_new ( ) {
			var nbr_links = <?php echo $cfpx; ?>nbr_links();
			// Save the values in the link form
			var values = Array();
			for (var i=0; i < nbr_links ; i++) {
<?php foreach ($lfds as $field) echo "\t\t\t\tvalues['${cfpx}${field}_' + i] = jQuery(('#${cfpx}${field}_' + i)).val(); \n"; ?>
			}
			var html = "<?php echo addcslashes(addslashes(p2m_link_html(8888)), "\n\r"); ?>";
			html = html.replace(/8888/g, (nbr_links));
			if ((nbr_links & 1)) html = html.replace('ffffff', 'f4f4f4'); 
			// Gotta change this to insert() if we move to Prototype 1.6
//			new Insertion.Bottom(jQuery('<?php echo $cfpx; ?>div'), html);
			jQuery('#<?php echo $cfpx; ?>div').append(html);
			// Restore the values in the link form
			for (var i = 0; i < nbr_links; i++) {
<?php foreach ($lfds as $field) echo "\t\t\t\tjQuery(('#${cfpx}${field}_' + i)).val(values['${cfpx}${field}_' + i]); \n"; ?>
			}

		}
		function <?php echo $cfpx; ?>delete (field_id) {
			link_nbr = <?php echo $cfpx; ?>nbr_links() - 1;
			for (var i = parseFloat(field_id); i < link_nbr; i++ ) {
				if (jQuery('#<?php echo $cfpx; ?>title_' + (i + 1))) {
<?php foreach ($lfds as $field) echo "\t\t\t\t\tjQuery(('#${cfpx}${field}_' + i)).val(jQuery(('#${cfpx}${field}_' + (i + 1))).val()); \n"; ?>
				}
			}
			jQuery('#<?php echo $cfpx; ?>external_' + link_nbr).remove(); 
		}
		function <?php echo $cfpx; ?>nbr_links() {
			var fieldsets = jQuery('#<?php echo $cfpx;?>div div'); //			
		 	return (parseFloat(fieldsets.length) ) / 6;
		}
		function <?php echo $cfpx; ?>move_up(field_id) {
			if (field_id > 0) {
<?php foreach ($lfds as $field) echo "\t\t\t\t${cfpx}swap('#${cfpx}${field}_' + field_id, '#${cfpx}${field}_' + (field_id - 1)); \n"; ?>
			}
		}
		function <?php echo $cfpx; ?>move_down(field_id) {
			if (field_id < <?php echo $cfpx; ?>nbr_links()) {
<?php foreach ($lfds as $field) echo "\t\t\t\t${cfpx}swap('#${cfpx}${field}_' + field_id, '#${cfpx}${field}_' + (field_id + 1)); \n"; ?>
			}		
		}
		function <?php echo $cfpx; ?>swap(id1, id2) {		
			var var1 = jQuery(id1).val();
			var var2 = jQuery(id2).val();
			jQuery(id1).val(var2);
			jQuery(id2).val(var1);		
		}
