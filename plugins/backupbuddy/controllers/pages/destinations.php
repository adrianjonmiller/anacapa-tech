<?php
$default_tab = 0;
if ( is_numeric( pb_backupbuddy::_GET( 'tab' ) ) ) {
	$default_tab = pb_backupbuddy::_GET( 'tab' );
}


wp_enqueue_script( 'thickbox' );
wp_print_scripts( 'thickbox' );
wp_print_styles( 'thickbox' );

//pb_backupbuddy::disalert( 'backup_stash_advert', 'Active BackupBuddy customers already have a <b>BackupBuddy Stash</b> account with <span class="pb_label pb_label">1 GB Free Storage</span>. Just login on the <a href="?page=pb_backupbuddy_destinations">Remote Destinations</a> page.' );
?>


<style type="text/css">
#backupbuddy-meta-link-wrap a.show-settings {
	float: right;
	margin: 0 0 0 6px;
}
#screen-meta-links #backupbuddy-meta-link-wrap a {
	background: none;
}
#screen-meta-links #backupbuddy-meta-link-wrap a:after {
	content: '';
	margin-right: 5px;
}
</style>
<script type="text/javascript">
jQuery(document).ready( function() {
	jQuery('#screen-meta-links').append(
		'<div id="backupbuddy-meta-link-wrap" class="hide-if-no-js screen-meta-toggle">' +
			'<a href="" class="show-settings pb_backupbuddy_begintour"><?php _e( "Tour Page", "it-l10n-backupbuddy" ); ?></a>' +
		'</div>'
	);
});
</script>
<?php
// Tutorial
pb_backupbuddy::load_script( 'jquery.joyride-2.0.3.js' );
pb_backupbuddy::load_script( 'modernizr.mq.js' );
pb_backupbuddy::load_style( 'joyride.css' );
?>
<ol id="pb_backupbuddy_tour" style="display: none;">
	<li data-class="nav-tab-0">Remote destinations allow you to send your backups offsite to another location for safe-keeping.</li>
	<li data-class="nav-tab-1" data-button="Finish">View a list of backups recently sent to a remote destination.</li>
</ol>
<script>
jQuery(window).load(function() {
	jQuery(document).on( 'click', '.pb_backupbuddy_begintour', function(e) {
		jQuery("#pb_backupbuddy_tour").joyride({
			tipLocation: 'top',
		});
		return false;
	});
});
</script>



<script type="text/javascript">
	function pb_backupbuddy_selectdestination( destination_id, destination_title, callback_data, delete_after, mode ) {
		if ( callback_data != '' ) {
			jQuery.post( '<?php echo pb_backupbuddy::ajax_url( 'remote_send' ); ?>', { destination_id: destination_id, destination_title: destination_title, file: callback_data, trigger: 'manual' }, 
				function(data) {
					data = jQuery.trim( data );
					if ( data.charAt(0) != '1' ) {
						alert( '<?php _e("Error starting remote send", 'it-l10n-backupbuddy' ); ?>:' + "\n\n" + data );
					} else {
						alert( "<?php _e('Your file has been scheduled to be sent now. It should arrive shortly.', 'it-l10n-backupbuddy' ); ?> <?php _e( 'You will be notified by email if any problems are encountered.', 'it-l10n-backupbuddy' ); ?>" + "\n\n" + data.slice(1) );
					}
				}
			);
			
			/* Try to ping server to nudge cron along since sometimes it doesnt trigger as expected. */
			jQuery.post( '<?php echo admin_url('admin-ajax.php'); ?>',
				function(data) {
				}
			);

		} else {
			//window.location.href = '<?php echo pb_backupbuddy::page_url(); ?>&custom=remoteclient&destination_id=' + destination_id;
			window.location.href = '<?php
			if ( is_network_admin() ) {
				echo network_admin_url( 'admin.php' );
			} else {
				echo admin_url( 'admin.php' );
			}
			?>?page=pb_backupbuddy_backup&custom=remoteclient&destination_id=' + destination_id;
		}
	}
</script>


<?php
pb_backupbuddy::$ui->title( __( 'Remote Destinations', 'it-l10n-backupbuddy' ) . ' <a href="javascript:void(0)" class="add-new-h2" onClick="jQuery(\'.backupbuddy-destination-sends\').toggle()">View recently sent files</a>' ); //  . ' <a href="javascript:void(0)" class="add-new-h2">Add New</a>' )


if ( defined( 'BACKUPBUDDY_DEV' ) && ( true === BACKUPBUDDY_DEV ) ) {
	echo '<h3>Remote API Access';
	if ( defined( 'BACKUPBUDDY_API_ENABLE' ) && ( TRUE == BACKUPBUDDY_API_ENABLE ) && ( defined( 'BACKUPBUDDY_API_SALT' ) && ( 'CHANGEME' != BACKUPBUDDY_API_SALT ) && ( strlen( BACKUPBUDDY_API_SALT ) >= 5 ) ) ) {
		echo '</h3>';
		
		require_once( pb_backupbuddy::plugin_path() . '/classes/remote_api.php' );
		
		if ( ( ! isset( pb_backupbuddy::$options['remote_api']['keys'][0] ) ) || ( '' == pb_backupbuddy::$options['remote_api']['keys'][0] ) || ( '1' == pb_backupbuddy::_POST( 'regenerate_api_key' ) ) ) {
			pb_backupbuddy::alert( 'New remote access API key generated.' );
			if ( '1' == pb_backupbuddy::_POST( 'regenerate_api_key' ) ) { // Generating due to form.
				pb_backupbuddy::verify_nonce(); // Security check.
			}
			pb_backupbuddy::$options['remote_api']['keys'][0] = backupbuddy_remote_api::generate_key();
			pb_backupbuddy::save();
		}
		?>
		
		<form method="post">
			<?php pb_backupbuddy::nonce(); ?>
			<input type="hidden" name="regenerate_api_key" value="1">
			<button class="button secondary-button" onClick="jQuery('.backupbuddy_api_key-hide').toggle(); return false;"><?php _e( 'Show Remote Access API Key', 'it-l10n-backupbuddy' ); ?></button><span class="backupbuddy_api_key-hide" style="display: none;">&nbsp;&nbsp;<input type="submit" name="submit" value="<?php _e( 'Generate New Site API Access Key', 'it-l10n-backupbuddy' ); ?>" class="button button-primary"></span>
			<br>
			<br>
			<div class="backupbuddy_api_key-hide" style="display: none;">
				<b><?php _e( 'Remote Acesss API Key', 'it-l10n-backupbuddy' ); ?>:</b><br>
				<textarea style="width: 100%; padding: 15px;" readonly="readonly" onClick="this.focus();this.select();"><?php echo pb_backupbuddy::$options['remote_api']['keys'][0]; ?></textarea>
				<br><br>
			</div>
		</form>
		
		<?php
	} else {
		?>
		<button class="button secondary-button" style="vertical-align: 1px;" onClick="jQuery('.backupbuddy_api_wpconfig-hide').toggle(); return false;"><?php _e( 'Enable Remote Access', 'it-l10n-backupbuddy' ); ?></button>
		</h3>
		Remote API Access allows other sites with your API access key entered to push to or pull data from this site.
		<span class="backupbuddy_api_wpconfig-hide" style="display: none;">
			<br>
			<b>For added security you must manually add the following to your wp-config.php file to enable. Refresh this page after adding the following:</b>
			<br>
	<textarea style="width: 100%; padding: 15px;" readonly="readonly" onClick="this.focus();this.select();">
	define( 'BACKUPBUDDY_API_ENABLE', true ); // Requires API key to access.
	define( 'BACKUPBUDDY_API_SALT', '<?php echo pb_backupbuddy::random_string( 32 ); ?>' ); // Randomly generated security salt for added security and uniquely identifying different sites. 5+ chars minimum length.
	</textarea>
		</span>
		<br><br>
		<?php
	}
	
	echo '<br>';
	echo '<h3>Remote Destinations</h3>';
}



echo '<div class="backupbuddy-destination-sends" style="display: none;"><br>';
require_once( 'server_info/remote_sends.php' );
echo '<br></div>';


echo '<iframe id="pb_backupbuddy_iframe" src="' . pb_backupbuddy::ajax_url( 'destinationTabs' ) . '&tab=' . $default_tab . '&action_verb=to%20manage%20files" width="100%" height="4000" frameBorder="0">Error #4584594579. Browser not compatible with iframes.</iframe>';


?>

<br style="clear: both;"><br style="clear: both;">

<?php
// Handles thickbox auto-resizing. Keep at bottom of page to avoid issues.
if ( !wp_script_is( 'media-upload' ) ) {
	wp_enqueue_script( 'media-upload' );
	wp_print_scripts( 'media-upload' );
}
