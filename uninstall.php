<?php

defined( 'ABSPATH' ) and defined( 'WP_UNINSTALL_PLUGIN' ) or exit;


/**
 * Database cleaning process
 *
 * @since 0.1.0
 *
 * @global $wpdb
 */

global $wpdb;

if( is_multisite() )
{
    $blog_ids = $wpdb->get_col( "SELECT blog_id FROM $wpdb->blogs" );
    foreach( $blog_ids as $blog_id )
    {
        switch_to_blog( $blog_id );

        $plugin_options = $wpdb->get_results( "SELECT option_name FROM $wpdb->options WHERE option_name LIKE 'mobile_contact_bar%'" );
        foreach( $plugin_options as $option )
        {
            delete_option( $option->option_name );
        }

        restore_current_blog();
    }
}
else
{
    $plugin_options = $wpdb->get_results( "SELECT option_name FROM $wpdb->options WHERE option_name LIKE 'mobile_contact_bar%'" );
    foreach( $plugin_options as $option )
    {
        delete_option( $option->option_name );
    }
}

if( ! $user = wp_get_current_user() )
{
    wp_die( -1 );
}
$user_options = $wpdb->get_results( "SELECT meta_key FROM $wpdb->usermeta WHERE meta_key LIKE '%mobile-contact-bar%'" );
foreach( $user_options as $option )
{
    delete_user_option( $user->ID, $option->meta_key, true );
}
