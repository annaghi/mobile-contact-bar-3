<?php

defined( 'ABSPATH' ) or exit;


final class Mobile_Contact_Bar_Contact_WhatsApp
{

    private static $type = null;
    private static $icon = null;

    public static function plugins_loaded()
    {
        self::$type = substr( __CLASS__, 27 );
        self::$icon = 'fab fa-whatsapp';

        if( is_admin() )
        {
            add_filter( 'mcb_admin_add_icon'    , array( __CLASS__, 'mcb_admin_add_icon' ));
            add_filter( 'mcb_admin_add_contact' , array( __CLASS__, 'mcb_admin_add_contact' ));
        }
    }



    public static function mcb_admin_add_icon( $icons )
    {
        $icons[8] = array(
            'type'  => self::$type,
            'icon'  => self::$icon,
            'title' => __( 'Add WhatsApp', 'mobile-contact-bar' ),
        );
        return $icons;
    }



    public static function mcb_admin_add_contact( $contacts )
    {
        $contacts[] = array(
            'checked'     => 0,
            'type'        => self::$type,
            'icon'        => self::$icon,
            'title'       => 'WhatsApp',
            'placeholder' => '', // contact with empty placeholder has non-editable URI
            'uri'         => 'https://api.whatsapp.com/send',
            'parameters'  => array(
                array(
                    'key'         => 'phone',
                    'type'        => 'text',
                    'placeholder' => __( '15417543010', 'mobile-contact-bar' ),
                    'value'       => '',
                ),
                array(
                    'key'         => 'text',
                    'type'        => 'text',
                    'placeholder' => __( 'Message ...', 'mobile-contact-bar' ),
                    'value'       => '',
                ),
            ),
        );
        return $contacts;
    }
}
