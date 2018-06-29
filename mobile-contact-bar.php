<?php

/**
 * Plugin Name: Mobile Contact Bar
 * Plugin URI:  https://wordpress.org/plugins/mobile-contact-bar/
 * Description: Allow your visitors to contact you via mobile phones, or access your site's pages instantly.
 * Version:     2.0.2
 * Author:      Anna Bansaghi
 * Author URI:  http://mobilecontactbar.com
 *
 * License:     GPL-3.0
 * License URI: https://www.gnu.org/licenses/gpl-3.0.en.html
 *
 * Text Domain: mobile-contact-bar
 * Domain Path: /languages
 *
 *
 * Copyright (C) 2018 by Anna Bansaghi
 *
 * Mobile Contact Bar is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * Mobile Contact Bar is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with Mobile Contact Bar. If not, see <https://www.gnu.org/licenses/gpl-3.0.en.html>.
 */


/**
 * Main plugin file
 *
 * @since 0.1.0
 */


defined( 'ABSPATH' ) or exit;


$plugin_data = get_file_data( __FILE__, array( 'Version' => 'Version' ));

define( 'MOBILE_CONTACT_BAR__PATH'    , __FILE__ );
define( 'MOBILE_CONTACT_BAR__SLUG'    , basename( __FILE__, '.php' ));
define( 'MOBILE_CONTACT_BAR__NAME'    , str_replace( '-', '_', MOBILE_CONTACT_BAR__SLUG ));
define( 'MOBILE_CONTACT_BAR__VERSION' , $plugin_data['Version'] );


// Admin functionality
if( is_admin() )
{
    $dir = plugin_dir_path( __FILE__ );


    // update
    if( get_option( 'mcb_option' ))
    {
        update_option( MOBILE_CONTACT_BAR__NAME . '_update', 1 );
    }
    if( get_option( MOBILE_CONTACT_BAR__NAME . '_update' ))
    {
        include_once $dir . 'includes/admin/class-updater.php';
        add_action( 'plugins_loaded', array( 'Mobile_Contact_Bar_Updater', 'plugins_loaded' ));
    }


    // activate
    include_once $dir . 'includes/admin/class-page.php';
    include_once $dir . 'includes/admin/class-option.php';
    include_once $dir . 'includes/admin/class-settings.php';

    register_activation_hook( __FILE__, array( 'Mobile_Contact_Bar_Page', 'on_activation' ));

    add_action( 'plugins_loaded', array( 'Mobile_Contact_Bar_Page'   , 'plugins_loaded' ));
    add_action( 'plugins_loaded', array( 'Mobile_Contact_Bar_Option' , 'plugins_loaded' ));
}

// Public functionality
else
{
    if( get_option( 'mcb_option' ))
    {
        include_once plugin_dir_path( __FILE__ ) . 'includes/public/class-renderer-v1.php';
    }
    else
    {
        include_once plugin_dir_path( __FILE__ ) . 'includes/public/class-renderer.php';
    }
    add_action( 'plugins_loaded', array( 'Mobile_Contact_Bar_Renderer', 'plugins_loaded' ));
}



// Both admin and public functionality
// Validation and Contacts
if( class_exists( 'Mobile_Contact_Bar_Page' ) || class_exists( 'Mobile_Contact_Bar_Renderer' ))
{
    $dir = plugin_dir_path( __FILE__ );

    include_once $dir . 'includes/class-validator.php';

    foreach( glob( $dir . 'includes/contacts/class-*.php' ) as $path )
    {
        include_once $path;

        $name = substr( basename( $path, '.php' ), 6 );
        $class_name = 'Mobile_Contact_Bar_Contact_' . $name;

        add_action( 'plugins_loaded', array( $class_name, 'plugins_loaded' ));
    }
}
