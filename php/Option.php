<?php

namespace MobileContactBar;

use MobileContactBar\Settings;
use MobileContactBar\Contacts;
use MobileContactBar\Styles;


final class Option
{
    public function get_option( $path, $sanitize_option_method )
    {
        $option = get_option( $path );
        return call_user_func( [$this, $sanitize_option_method], $option );
    }


    public function update_option( $option, $path, $sanitize_option_method )
    {
        $sanitized_option = call_user_func( [$this, $sanitize_option_method], $option );
        update_option( $path, $sanitized_option );
    }


    /**
     * @param  mixed $option
     * @return array
     */
    public function sanitize_option_bar( $option )
    {
        if ( $option && is_array( $option ) && isset( $option['settings'] ))
        {
            $settings = abmcb( Settings\Input::class )->sanitize( $option['settings'] );    
        }
        else
        {
            $settings = abmcb( Settings\Input::class )->default_settings();    
        }

        if ( $option && is_array( $option ) && isset( $option['contacts'] ))
        {
            $contacts = abmcb( Contacts\Input::class )->sanitize( $option['contacts'] );
        }
        else
        {
            $contacts = [];
        }

        return [
            'settings' => $settings,
            'contacts' => $contacts,
            'styles'   => abmcb( Styles\CSS::class )->output( $settings, $contacts ),
        ];
    }


    /**
     * @param  mixed $option
     * @return array
     */
    public function sanitize_option_migrations( $option )
    {
        if ( $option && is_array( $option ))
        {
            return array_filter( $option, 'is_bool' );
        }
        else
        {
            return [];
        }
    }


    /**
     * Returns the default bar-option.
     *
     * @return array Option initialized with default settings, contacts, and generated styles
     */
    public function default_option_bar()
    {
       $settings = abmcb( Settings\Input::class )->default_settings();
       $contacts = abmcb( Contacts\Input::class )->sample_contacts();
       $styles   = abmcb( Styles\CSS::class )->output( $settings, $contacts );

       return [
           'settings' => $settings,
           'contacts' => $contacts,
           'styles'   => $styles,
       ];
    }
}
