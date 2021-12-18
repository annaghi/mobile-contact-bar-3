<?php

defined( 'ABSPATH' ) and defined( 'WP_UNINSTALL_PLUGIN' ) || exit();


/**
 * Database cleaning process
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

        $plugin_options = $wpdb->get_results( "SELECT option_name FROM $wpdb->options WHERE option_name LIKE '%mobile_contact_bar%'" );
        foreach( $plugin_options as $option )
        {
            delete_option( $option->option_name );
        }
        $wpdb->query( "DELETE FROM {$wpdb->usermeta} WHERE meta_key LIKE '%mobile_contact_bar%'" );

        restore_current_blog();
    }
}
else
{
    $plugin_options = $wpdb->get_results( "SELECT option_name FROM $wpdb->options WHERE option_name LIKE '%mobile_contact_bar%'" );
    foreach( $plugin_options as $option )
    {
        delete_option( $option->option_name );
        $wpdb->query( "DELETE FROM {$wpdb->usermeta} WHERE meta_key LIKE '%mobile_contact_bar%'" );
    }
}
