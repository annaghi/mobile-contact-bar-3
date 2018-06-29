<?php

defined( 'ABSPATH' ) or exit;


final class Mobile_Contact_Bar_Contact_Custom
{

    private static $type = null;

    public static function plugins_loaded()
    {
        self::$type = substr( __CLASS__, 27 );

        if( is_admin() )
        {
            add_filter( 'mcb_admin_add_button'  , array( __CLASS__, 'mcb_admin_add_button' ));
            add_filter( 'mcb_admin_add_contact' , array( __CLASS__, 'mcb_admin_add_contact' ));
        }
    }



    public static function mcb_admin_add_button( $buttons )
    {
        $buttons[5] = array(
            'type'  => self::$type,
            'title' => __( '+ New Contact', 'mobile-contact-bar' ),
        );
        return $buttons;
    }



    public static function mcb_admin_add_contact( $contacts )
    {
        $contacts[] = array(
            'checked'     => 0,
            'type'        => self::$type,
            'icon'        => '',
            'title'       => '',
            'placeholder' => 'URI',
            'uri'         => '',
        );
        return $contacts;
    }
}
