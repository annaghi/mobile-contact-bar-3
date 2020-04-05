<?php

defined( 'ABSPATH' ) or exit;


final class Mobile_Contact_Bar_Contact_Text
{

    private static $type = null;
    private static $icon = null;

    public static function plugins_loaded()
    {
        self::$type = substr( __CLASS__, 27 );
        self::$icon = 'far fa-comment';

        if( is_admin() )
        {
            add_filter( 'mcb_admin_add_icon'    , array( __CLASS__, 'mcb_admin_add_icon' ));
            add_filter( 'mcb_admin_add_contact' , array( __CLASS__, 'mcb_admin_add_contact' ));
        }
    }



    public static function mcb_admin_add_icon( $icons )
    {
        $icons[6] = array(
            'type'  => self::$type,
            'icon'  => self::$icon,
            'title' => __( 'Add Text', 'mobile-contact-bar' ),
        );
        return $icons;
    }



    public static function mcb_admin_add_contact( $contacts )
    {
        $contacts[] = array(
            'checked'     => 0,
            'type'        => self::$type,
            'icon'        => self::$icon,
            'title'       => __( 'Phone Number for texting', 'mobile-contact-bar' ),
            'placeholder' => 'sms:+15417543010',
            'uri'         => '',
            'parameters'  => array(
                array(
                    'key'         => 'body',
                    'type'        => 'text',
                    'placeholder' => __( 'Message ...', 'mobile-contact-bar' ),
                    'value'       => '',
                ),
            ),
        );
        return $contacts;
    }
}
