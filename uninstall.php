<?php

defined( 'ABSPATH' ) and defined( 'WP_UNINSTALL_PLUGIN' ) || exit();

require_once ABSPATH . '/wp-admin/includes/file.php';


/**
 * Cleans the database from all plugin related data.
 * Clears plugin related cron events.
 * Deletes plugin's folder from uploads/.
 *
 * @global $wpdb
 * @global $wp_filesystem
 * 
 * @return void
 */
function abmcb_uninstall()
{
    global $wpdb, $wp_filesystem;

    $wpdb->query( "DELETE FROM {$wpdb->options} WHERE option_name LIKE '%mobile_contact_bar%'" );
    $wpdb->query( "DELETE FROM {$wpdb->usermeta} WHERE meta_key LIKE '%mobile_contact_bar%'" );

    wp_clear_scheduled_hook( 'mobile_contact_bar_weekly_scheduled_events' );

    if ( WP_Filesystem() )
    {
        $wp_upload_dir = wp_upload_dir( null, true, true );
        $dir = trailingslashit( $wp_upload_dir['basedir'] . '/mobile-contact-bar' );
        $wp_filesystem->rmdir( wp_normalize_path( $dir ), true );
    }
}


if ( is_multisite() )
{
    $site_ids = get_sites( ['fields' => 'ids'] );

    remove_action( 'switch_blog', 'wp_switch_roles_and_user', 1 );
    foreach ( $site_ids as $site_id )
    {
        switch_to_blog( $site_id );
        abmcb_uninstall();
        restore_current_blog();
    }
    add_action( 'switch_blog', 'wp_switch_roles_and_user', 1, 2 );
}
else
{
    abmcb_uninstall();
}
