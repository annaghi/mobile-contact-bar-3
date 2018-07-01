<?php
/**
 * Mobile Contact Bar Uninstall
 *
 * Uninstalling Mobile Contact Bar deletes options.
 *
 * @package Mobile_Contact_Bar\Uninstaller
 * @global $wpdb
 */

defined( 'WP_UNINSTALL_PLUGIN' ) || exit;


global $wpdb;


// Clear Options table.
if ( is_multisite() ) {
	$mobile_contact_bar_blog_ids = $wpdb->get_col( "SELECT blog_id FROM $wpdb->blogs" );
	foreach ( $mobile_contact_bar_blog_ids as $blog_id ) {
		switch_to_blog( $blog_id );

		$mobile_contact_bar_plugin_options = $wpdb->get_results( "SELECT option_name FROM $wpdb->options WHERE option_name LIKE 'mobile_contact_bar%'" );
		foreach ( $mobile_contact_bar_plugin_options as $option ) {
			delete_option( $option->option_name );
		}

		restore_current_blog();
	}
} else {
	$mobile_contact_bar_plugin_options = $wpdb->get_results( "SELECT option_name FROM $wpdb->options WHERE option_name LIKE 'mobile_contact_bar%'" );
	foreach ( $mobile_contact_bar_plugin_options as $option ) {
		delete_option( $option->option_name );
	}
}


// Clear Usermeta table.
$mobile_contact_bar_user = wp_get_current_user();
if ( ! $mobile_contact_bar_user ) {
	wp_die( -1 );
}
$mobile_contact_bar_user_options = $wpdb->get_results( "SELECT meta_key FROM $wpdb->usermeta WHERE meta_key LIKE '%mobile-contact-bar%'" );
foreach ( $mobile_contact_bar_user_options as $option ) {
	delete_user_option( $mobile_contact_bar_user->ID, $option->meta_key, true );
}


// Clear any cached data that has been removed.
wp_cache_flush();
