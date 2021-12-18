<?php

namespace MobileContactBar;

use MobileContactBar\Settings;
use MobileContactBar\Contacts;
use MobileContactBar\Styles;

final class Options
{
    public function get_option( $path, $default_option_method, $is_valid_option_method )
    {
        $option = get_option( $path );
        
        if ( call_user_func( [$this, $is_valid_option_method], $option ))
        {
            return $option;
        }
        else
        {
            return call_user_func( [$this, $default_option_method] );
        }
    }


    public function update_option( $option, $path, $default_option_method, $is_valid_option_method )
    {
        if ( call_user_func( [$this, $is_valid_option_method], $option ))
        {
            update_option( $path, $option );
        }
        else
        {
            update_option( $path, call_user_func( [$this, $default_option_method] ));
        }
    }


    public function is_valid_option_bar( $option_bar )
    {
        if ( ! $option_bar || ! is_array( $option_bar ))
        {
            return false;
        }

        $settings = ( isset( $option_bar['settings'] ) && is_array( $option_bar['settings'] )) ? $option_bar['settings'] : [];
        $contacts = ( isset( $option_bar['contacts'] ) && is_array( $option_bar['contacts'] )) ? $option_bar['contacts'] : [];
        $styles   = ( isset( $option_bar['styles'] )   && is_string( $option_bar['styles'] ))  ? $option_bar['styles']   : '';

        return $this->is_valid_settings( $settings ) && $this->is_valid_contacts( $contacts );
    }


    public function is_valid_settings( $settings )
    {
        $default_settings = abmcb( Settings\Input::class )->fields_defaults();
        $diff = Helper::array_diff_assoc_recursive( $default_settings, $settings );

        return empty( $diff );
    }


    public function is_valid_contacts( $contacts )
    {
        $is_valid = true;

        $contact_keys = ['type', 'id', 'checked', 'icon', 'label', 'uri', 'parameters', 'palette'];
        $palette_keys = ['background_color', 'border_color', 'icon_color', 'label_color'];
        $parameter_keys = ['key', 'value'];

        foreach ( $contacts as $contact )
        {
            if ( is_array( $contact['palette'] ))
            {
                $is_valid = $is_valid && empty( array_diff( $palette_keys, array_keys( $contact['palette'] )));
            }

            $keys = array_keys( $contact );
            $contact_diff = array_diff( $contact_keys, $keys );

            if ( empty( $contact_diff ))
            {
                if ( is_array( $contact['parameters'] ))
                {
                    $is_valid = $is_valid && array_reduce(
                        $contact['parameters'],
                        function( $acc, $parameter ) use ( $parameter_keys ) { return $acc && empty( array_diff( $parameter_keys, array_keys( $parameter ))); }, true
                    );
                }
            }
            elseif ( array_values( $contact_diff ) !== ['parameters'] )
            {
                $is_valid = false;
            }
        }

        return $is_valid;
    }


    /**
     * Returns the default bar-option.
     *
     * @return array Option initialized with default settings, contacts, and generated styles
     */
    public function default_option_bar()
    {
       $settings = abmcb( Settings\Input::class )->fields_defaults();
       $contacts = abmcb( Contacts\Input::class )->fields_samples();
       $styles = Styles\CSS::generate( $settings, $contacts );

       return [
           'settings' => $settings,
           'contacts' => $contacts,
           'styles'   => $styles,
       ];
    }


    /**
     * Returns the default bar-option.
     *
     * @return array Option initialized with default settings, contacts, and generated styles
     */
    public function default_option_bar_with_samples()
    {
       $settings = abmcb( Settings\Input::class )->fields_defaults();
       $contacts = abmcb( Contacts\Input::class )->fields_samples();
       $styles = Styles\CSS::generate( $settings, $contacts );

       return [
           'settings' => $settings,
           'contacts' => $contacts,
           'styles'   => $styles,
       ];
    }


    /**
     * @return array
     */
    public function is_valid_option_migrations( $migrations )
    {
        return ( !! $migrations && is_array( $migrations ));
    }


    public function default_option_migrations()
    {
        return [];
    }
}
