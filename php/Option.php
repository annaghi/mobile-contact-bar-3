<?php

namespace MobileContactBar;

use MobileContactBar\Settings;
use MobileContactBar\Buttons;


final class Option
{
    public function update_option( $option, $path, $sanitize_option_method )
    {
        $sanitized_option = call_user_func( [$this, $sanitize_option_method], $option, 'encode' );
        update_option( $path, $sanitized_option );
    }


    public function get_option( $path, $sanitize_option_method )
    {
        $option = get_option( $path );
        return call_user_func( [$this, $sanitize_option_method], $option, 'decode' );
    }


    /**
     * @param  mixed       $option
     * @param  string|null $form   'encode' | 'decode'
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

        if ( $option && is_array( $option ) && isset( $option['buttons'] ))
        {
            switch ( $form )
            {
                case 'encode':
                    $buttons = abmcb( Buttons\Input::class )->sanitize( $option['buttons'] );
                    $buttons = $this->encode_buttons( $buttons );
                    break;

                case 'decode':
                    $buttons = $this->decode_buttons( $option['buttons'] );
                    $buttons = abmcb( Buttons\Input::class )->sanitize( $buttons );
                    break;

                default:
                    $buttons = [];
            }
        }
        else
        {
            $buttons = [];
        }

        return [
            'settings' => $settings,
            'buttons'  => $buttons,
        ];
    }


    /**
     * @param  array $buttons
     * @return array
     */
    public function encode_buttons( $buttons )
    {
        if ( is_array( $buttons ))
        {
            foreach ( $buttons as &$button )
            {
                $button['uri'] = esc_url_raw( untrailingslashit( rawurldecode( $button['uri'] )), abmcb()->schemes );

                if ( isset( $button['query'] ) && is_array( $button['query'] ))
                {
                    foreach ( $button['query'] as &$parameter )
                    {
                        $parameter['key']   = rawurlencode( rawurldecode( $parameter['key'] ));
                        $parameter['value'] = rawurlencode( rawurldecode( $parameter['value'] ));
                    }
                    unset( $parameter );
                }
            }
            unset( $button );
        }

        return $buttons;
    }


    /**
     * @param  array $buttons
     * @return array
     */
    public function decode_buttons( $buttons )
    {
        if ( is_array( $buttons ))
        {
            foreach ( $buttons as &$button )
            {
                $button['uri'] = untrailingslashit( rawurldecode( $button['uri'] ));

                if ( isset( $button['query'] ) && is_array( $button['query'] ))
                {
                    foreach ( $button['query'] as &$parameter )
                    {
                        $parameter['key']   = rawurldecode( $parameter['key'] );
                        $parameter['value'] = rawurldecode( $parameter['value'] );
                    }
                    unset( $parameter );
                }
            }
            unset( $button );
        }

        return $buttons;
    }


    /**
     * Returns the default bar-option.
     *
     * @return array Option initialized with default settings, buttons
     */
    public function default_option_bar()
    {
       $settings = abmcb( Settings\Input::class )->default_settings();
       $buttons = abmcb( Buttons\Input::class )->sample_buttons();

       return [
           'settings' => $settings,
           'buttons'  => $buttons,
       ];
    }


    /**
     * @param  mixed       $option
     * @param  string|null $form   'encode' | 'decode'
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
     * @param  string $path
     * @param  mixed  $fallback
     * @return mixed
     */
    public function get_wp_option( $path, $fallback = '' )
    {
        return get_option( $path, $fallback );
    }
}
