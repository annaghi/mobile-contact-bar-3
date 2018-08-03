<?php
/**
 * Plugin Name: Mobile Contact Bar
 * Plugin URI:  https://wordpress.org/plugins/mobile-contact-bar/
 * Description: Allow your visitors to contact you via mobile phones, or access your site's pages instantly.
 * Version:     2.1.0
 * Author:      Anna Bansaghi
 * Author URI:  http://mobilecontactbar.com
 *
 * License:     GPL-3.0
 * License URI: https://www.gnu.org/licenses/gpl-3.0.en.html
 *
 * Text Domain: mobile-contact-bar
 * Domain Path: /languages
 *
 * @package Mobile_Contact_Bar
 */

defined( 'ABSPATH' ) || exit;


$mobile_contact_bar_plugin_data = get_file_data( __FILE__, array( 'Version' => 'Version' ) );

define( 'MOBILE_CONTACT_BAR__PATH', __FILE__ );
define( 'MOBILE_CONTACT_BAR__SLUG', basename( __FILE__, '.php' ) );
define( 'MOBILE_CONTACT_BAR__NAME', str_replace( '-', '_', MOBILE_CONTACT_BAR__SLUG ) );
define( 'MOBILE_CONTACT_BAR__VERSION', $mobile_contact_bar_plugin_data['Version'] );



/* Admin functionality */
if ( is_admin() ) {
	$mobile_contact_bar_dir = plugin_dir_path( __FILE__ );

	// Plugin upgrade.
	if ( get_option( 'mcb_option' ) ) {
		update_option( MOBILE_CONTACT_BAR__NAME . '_upgrade', 1 );
	}
	if ( get_option( MOBILE_CONTACT_BAR__NAME . '_upgrade' ) ) {
		include_once $mobile_contact_bar_dir . 'includes/admin/class-upgrader.php';
		add_action( 'plugins_loaded', array( 'Mobile_Contact_Bar_Upgrader', 'plugins_loaded' ) );
	}

	// Plugin load.
	include_once $mobile_contact_bar_dir . 'includes/admin/class-updater.php';
	include_once $mobile_contact_bar_dir . 'includes/admin/class-page.php';
	include_once $mobile_contact_bar_dir . 'includes/admin/class-option.php';
	include_once $mobile_contact_bar_dir . 'includes/admin/class-settings.php';
	include_once $mobile_contact_bar_dir . 'includes/admin/class-model.php';

	register_activation_hook( __FILE__, array( 'Mobile_Contact_Bar_Page', 'on_activation' ) );

	add_action( 'plugins_loaded', array( 'Mobile_Contact_Bar_Page', 'plugins_loaded' ) );
	add_action( 'plugins_loaded', array( 'Mobile_Contact_Bar_Option', 'plugins_loaded' ) );

	// Plugin update.
	add_action( 'upgrader_process_complete', array( 'Mobile_Contact_Bar_Page', 'upgrader_process_complete' ), 10, 2 );

	/* Public functionality */
} else {

	if ( get_option( 'mcb_option' ) ) {
		include_once plugin_dir_path( __FILE__ ) . 'includes/public/class-renderer-v1.php';
		add_action( 'plugins_loaded', array( 'Mobile_Contact_Bar_Renderer_V1', 'plugins_loaded' ) );
	} else {
		include_once plugin_dir_path( __FILE__ ) . 'includes/public/class-renderer.php';
		add_action( 'plugins_loaded', array( 'Mobile_Contact_Bar_Renderer', 'plugins_loaded' ) );
	}
}



/* Shared functionality */
$mobile_contact_bar_dir = plugin_dir_path( __FILE__ );

require_once $mobile_contact_bar_dir . 'includes/shared/class-validator.php';

foreach ( glob( $mobile_contact_bar_dir . 'includes/shared/contacts/class-*.php' ) as $path ) {
	include_once $path;

	$mobile_contact_bar_type       = substr( basename( $path, '.php' ), 6 );
	$mobile_contact_bar_class_name = 'Mobile_Contact_Bar_Contact_' . $mobile_contact_bar_type;

	add_action( 'plugins_loaded', array( $mobile_contact_bar_class_name, 'plugins_loaded' ) );
}
