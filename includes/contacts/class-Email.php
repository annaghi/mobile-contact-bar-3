<?php

defined( 'ABSPATH' ) or exit;


final class Mobile_Contact_Bar_Contact_Email
{

    private static $type = null;
    private static $icon = null;

    public static function plugins_loaded()
    {
        self::$type = substr( __CLASS__, 27 );
        self::$icon = 'far fa-envelope';

        if( is_admin() )
        {
            add_filter( 'mcb_admin_add_icon'    , array( __CLASS__, 'mcb_admin_add_icon' ));
            add_filter( 'mcb_admin_add_contact' , array( __CLASS__, 'mcb_admin_add_contact' ));
        }
    }



    public static function mcb_admin_add_icon( $icons )
    {
        $icons[7] = array(
            'type'  => self::$type,
            'icon'  => self::$icon,
            'title' => __( 'Add Email', 'mobile-contact-bar' ),
        );
        return $icons;
    }



    public static function mcb_admin_add_contact( $contacts )
    {
        $contacts[] = array(
            'checked'     => 0,
            'type'        => self::$type,
            'icon'        => self::$icon,
            'title'       => __( 'Email Address', 'mobile-contact-bar' ),
            'placeholder' => 'mailto:username@example.com',
            'uri'         => '',
            'parameters'  => array(
                array(
                    'key'         => 'subject',
                    'type'        => 'text',
                    'placeholder' => __( 'Subject ...', 'mobile-contact-bar' ),
                    'value'       => '',
                ),
                array(
                    'key'         => 'body',
                    'type'        => 'textarea',
                    'placeholder' => __( 'Text ...', 'mobile-contact-bar' ),
                    'value'       => '',
                ),
                array(
                    'key'         => 'cc',
                    'type'        => 'email',
                    'placeholder' => __( 'example@domain.com', 'mobile-contact-bar' ),
                    'value'       => '',
                ),
                array(
                    'key'         => 'bcc',
                    'type'        => 'email',
                    'placeholder' => __( 'example1@domain.com,example2@domain.net', 'mobile-contact-bar' ),
                    'value'       => '',
                ),
            ),
        );
        return $contacts;
    }
}
