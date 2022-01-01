<?php

defined( 'ABSPATH' ) and defined( 'WP_UNINSTALL_PLUGIN' ) || exit();


global $wpdb;

/**
 * Cleans the database from all plugin related data.
 * Clears plugin related cron events.
 *
 * @global $wpdb
 */
function abmcb_uninstall() {
    $wpdb->query( "DELETE FROM {$wpdb->options} WHERE option_name LIKE '%mobile_contact_bar%'" );
    $wpdb->query( "DELETE FROM {$wpdb->usermeta} WHERE meta_key LIKE '%mobile_contact_bar%'" );
    wp_clear_scheduled_hook( 'mobile_contact_bar_weekly_scheduled_events' );
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
