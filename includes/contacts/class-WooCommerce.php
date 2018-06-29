<?php

defined( 'ABSPATH' ) or exit;


final class Mobile_Contact_Bar_Contact_WooCommerce
{

    private static $type = null;
    private static $icon = null;

    public static function plugins_loaded()
    {
        if( class_exists( 'WooCommerce' ))
        {
            self::$type = substr( __CLASS__, 27 );
            self::$icon = 'fas fa-shopping-cart';

            if( is_admin() )
            {
                add_filter( 'mcb_admin_add_icon'    , array( __CLASS__, 'mcb_admin_add_icon' ));
                add_filter( 'mcb_admin_add_contact' , array( __CLASS__, 'mcb_admin_add_contact' ));
            }

            if( defined( 'WC_VERSION' ) && version_compare( WC_VERSION, '2.3', '>=' ))
            {
                add_filter( 'woocommerce_add_to_cart_fragments', array( __CLASS__, 'woocommerce_add_to_cart_fragments' ));
            }
            else
            {
                add_filter( 'add_to_cart_fragments', array( __CLASS__, 'woocommerce_add_to_cart_fragments' ));
            }
        }
    }



    public static function mcb_admin_add_icon( $icons )
    {
        $icons[20] = array(
            'type'  => self::$type,
            'icon'  => self::$icon,
            'title' => __( 'Add WooCommerce Cart', 'mobile-contact-bar' ),
        );
        return $icons;
    }



    public static function mcb_admin_add_contact( $contacts )
    {
        $contacts[] = array(
            'checked'     => 0,
            'type'        => self::$type,
            'icon'        => self::$icon,
            'title'       => __( 'WooCommerce Cart', 'mobile-contact-bar' ),
            'placeholder' => is_ssl() ? 'https://mysite.com/cart' : 'http://mysite.com/cart',
            'uri'         => get_site_url() . '/cart',
        );
        return $contacts;
    }



    public static function output_badge()
    {
        return self::get_badge();
    }



    public static function woocommerce_add_to_cart_fragments( $fragments )
    {
        global $woocommerce;

        $fragments['.mobile-contact-bar-badge'] = self::get_badge();

        return $fragments;
    }



    private static function get_badge()
    {
        if( ! class_exists( 'WooCommerce' ))
        {
            return '';
        }
        return sprintf( '<span class="mobile-contact-bar-badge">%d</span>', wp_kses_data( WC()->cart->get_cart_contents_count() ));
    }
}
