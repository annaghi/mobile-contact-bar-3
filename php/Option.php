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
        return call_user_func( [$this, $sanitize_option_method], $option, 'decode' );
    }


    public function update_option( $option, $path, $sanitize_option_method )
    {
        $sanitized_option = call_user_func( [$this, $sanitize_option_method], $option, 'encode' );
        update_option( $path, $sanitized_option );
    }


    /**
     * @param  mixed       $option
     * @param  string|null $form   'encode' / 'decode'
     * @return array
     */
    public function sanitize_option_bar( $option, $form = null )
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
            switch ( $form )
            {
                case 'decode':
                    $contacts = $this->decode_contacts( $option['contacts'] );
                    $contacts = abmcb( Contacts\Input::class )->sanitize( $contacts );
                    break;

                case 'encode':
                    $contacts = abmcb( Contacts\Input::class )->sanitize( $option['contacts'] );
                    $contacts = $this->encode_contacts( $contacts );
                    break;

                default:
                    $contacts = [];
            }
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
     * @param  mixed       $option
     * @param  string|null $form   'encode' / 'decode'
     * @return array
     */
    public function sanitize_option_migrations( $option, $form = null )
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
     * @param  array $contacts
     * @return array
     */
    public function decode_contacts( $contacts )
    {
        if ( is_array( $contacts ))
        {
            foreach ( $contacts as &$contact )
            {
                $contact['uri'] = rawurldecode( $contact['uri'] );

                if ( isset( $contact['parameters'] ) && is_array( $contact['parameters'] ))
                {
                    foreach ( $contact['parameters'] as &$parameter )
                    {
                        $parameter['key']   = rawurldecode( $parameter['key'] );
                        $parameter['value'] = rawurldecode( $parameter['value'] );
                    }
                    unset( $parameter );
                }
            }
            unset( $contact );
        }

        return $contacts;
    }


    /**
     * @param  array $contacts
     * @return array
     */
    public function encode_contacts( $contacts )
    {
        if ( is_array( $contacts ))
        {
            foreach ( $contacts as &$contact )
            {
                $contact['uri'] = esc_url_raw( rawurldecode( $contact['uri'] ), abmcb()->schemes );

                if ( isset( $contact['parameters'] ) && is_array( $contact['parameters'] ))
                {
                    foreach ( $contact['parameters'] as &$parameter )
                    {
                        $parameter['key']   = rawurlencode( rawurldecode( $parameter['key'] ));
                        $parameter['value'] = rawurlencode( rawurldecode( $parameter['value'] ));
                    }
                    unset( $parameter );
                }
            }
            unset( $contact );
        }

        return $contacts;
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
