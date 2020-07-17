<?php
/*
Plugin Name: Bulk Actions Pro for Gravity Forms
Plugin URI: http://jetsloth.com/bulk-actions-for-gravity-forms/
Description: Re-order, duplicate and delete fields, copy them to another form, bulk edit their labels, css classes and required settings quicker than ever before
Version: 1.2.27
Requires at least: 4.0
Tested up to: 5.3.2
Author: JetSloth
Author URI: http://jetsloth.com
Text Domain: gf_bulk_actions_pro
*/

define('GF_BULK_ACTIONS_VERSION', '1.2.27');
define('GF_BULK_ACTIONS_HOME', 'http://jetsloth.com');
define('GF_BULK_ACTIONS_NAME', 'Bulk Actions PRO for Gravity Forms');
define('GF_BULK_ACTIONS_TIMEOUT', 20);
define('GF_BULK_ACTIONS_SSL_VERIFY', true);

add_action('gform_loaded', array('GF_BulkActionsPro_AddOn_Bootstrap', 'load'), 5);

class GF_BulkActionsPro_AddOn_Bootstrap {

	public static function load() {
		if (!method_exists('GFForms', 'include_addon_framework')) {
			return;
		}
		require_once('class-gf-bulk-actions-pro-addon.php');
		GFAddOn::register('GFBulkActionsProAddOn');
	}

}

function gf_bulk_actions_pro_addon() {
	if ( ! class_exists( 'GFBulkActionsProAddOn' ) ) {
		return false;
	}

	return GFBulkActionsProAddOn::get_instance();
}


add_action( 'admin_footer', 'gf_bulk_actions_adminbar_quick_links', 10, 2 );
add_action( 'wp_footer', 'gf_bulk_actions_adminbar_quick_links', 10, 2 );
function gf_bulk_actions_adminbar_quick_links() {
	if (gf_bulk_actions_pro_addon() === false) {
		return;
	}

	?>
	<script id="gf_bulk_actions_adminbar_quick_links">
		window.onload = function(){
			var $ = jQuery;
			var $adminBarFormsMenu = $('#wp-admin-bar-gform-form-recent-forms');
			if ($adminBarFormsMenu.length) {
				$adminBarFormsMenu.find('> li > .ab-sub-wrapper > .ab-submenu').each(function(){
					var $formSubmenu = $(this);
					var submenuID = $formSubmenu.attr('id');
					var bulkActionsMenuItemID = submenuID.replace('-default', '-bulk_actions');
					var $lastItem = $formSubmenu.find('li[id$="-edit"]');
					var $bulkActionsItem = $( $lastItem.clone() );
					$bulkActionsItem.attr('id', bulkActionsMenuItemID);
					var bulkActionsItemURL = $bulkActionsItem.find('> a').attr('href');
					$bulkActionsItem.find('> a').attr('href', bulkActionsItemURL.replace('page=gf_edit_forms&id=', 'page=bulk-actions-pro-for-gravity-forms&gform_id=')).text('Bulk Actions');
					$formSubmenu.append($bulkActionsItem);
				});
			}
		};
	</script>
	<?php
}


add_action('init', 'gf_bulk_actions_pro_plugin_updater', 0);
function gf_bulk_actions_pro_plugin_updater() {

	if (gf_bulk_actions_pro_addon() === false) {
		return;
	}

	if ( ! class_exists( 'EDD_SL_Plugin_Updater' ) ) {
		// load our custom updater if it doesn't already exist
		include( dirname( __FILE__ ) . '/inc/EDD_SL_Plugin_Updater.php' );
	}

	// retrieve the license key
	$license_key = trim( gf_bulk_actions_pro_addon()->get_plugin_setting( 'gf_bulk_actions_pro_license_key' ) );

	// setup the updater
	$edd_updater = new EDD_SL_Plugin_Updater( GF_BULK_ACTIONS_HOME, __FILE__, array(
			'version'   => GF_BULK_ACTIONS_VERSION,
			'license'   => $license_key,
			'item_name' => GF_BULK_ACTIONS_NAME,
			'author'    => 'JetSloth'
		)
	);

}
