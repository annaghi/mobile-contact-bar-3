<?php

/**
 * Plugin Name:       Mobile Contact Bar
 * Plugin URI:        https://wordpress.org/plugins/mobile-contact-bar/
 * Description:       Allow your visitors to contact you via mobile phones, or access your site's pages instantly.
 * Version:           3.0.0
 * Author:            Anna Bansaghi
 * Author URI:        https://github.com/annaghi/mobile-contact-bar
 * License:           GPLv3
 * License URI:       https://www.gnu.org/licenses/gpl-3.0.en.html
 * Requires at least: 4.9
 * Requires PHP:      5.6.20
 * Text Domain:       mobile-contact-bar
 *
 *
 * Mobile Contact Bar - Call-to-Actions on WordPress sites
 * Copyright (C) 2022 by Anna Bansaghi
 * 
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 * 
 * You should have received a copy of the GNU General Public License
 * along with this program.  If not, see <https://www.gnu.org/licenses/>.
 */


defined( 'ABSPATH' ) || exit();


if ( ! class_exists( 'MobileContactBar_Plugin_Check' ))
{
    include_once __DIR__ . '/activate.php';
}

if (( new MobileContactBar_Plugin_Check( __FILE__ ))->can_proceed())
{
    require_once __DIR__ . '/autoload.php';

    function abmcb( $class = null )
    {
        $plugin = MobileContactBar\Plugin::load( __FILE__ );
        return ( is_null( $class )) ? $plugin : $plugin->make( $class );
    }

    $mobile_contact_bar_plugin = MobileContactBar\Plugin::load( __FILE__ );
    register_activation_hook( __FILE__, [$mobile_contact_bar_plugin, 'activate'] );
    register_deactivation_hook( __FILE__, [$mobile_contact_bar_plugin, 'deactivate'] );
    add_action( 'plugins_loaded', [$mobile_contact_bar_plugin, 'plugins_loaded'] );
}
