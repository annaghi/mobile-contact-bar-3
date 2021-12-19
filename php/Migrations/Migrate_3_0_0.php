<?php

namespace MobileContactBar\Migrations;

use MobileContactBar\Helper;
use MobileContactBar\Settings\Input as SettingsInput;
use MobileContactBar\Contacts\Input as ContactsInput;
use MobileContactBar\Styles\CSS;

final class Migrate_3_0_0
{
    /**
     * @return bool
     */
    public function run()
    {
        $this->migrate_bar();
logg(__METHOD__);

        return true;
    }


    public function migrate_bar()
    {
        $settings = $this->migrate_settings();
        $contacts = $this->migrate_contacts();
        $styles   = CSS::generate( $settings, $contacts );

        $option_bar = [
            'settings' => $settings,
            'contacts' => $contacts,
            'styles'   => $styles,
        ];

        update_option( abmcb()->id, $option_bar );
    }


    // TODO migrate icon size
    private function migrate_settings()
    {
        $settings = [];
        $new_settings = abmcb( SettingsInput::class )->fields_defaults();

        $old_option_bar = get_option( abmcb()->id );
        $old_settings = ( isset( $old_option_bar['settings'] ) && is_array( $old_option_bar['settings'] )) ? $old_option_bar['settings'] : [];

        if ( ! empty ( array_column( $old_settings, 'icons' )))
        {
            $settings = Helper::array_slice_assoc_recursive( $new_settings, $old_settings );

            $settings['bar']['horizontal_alignment']                = $old_settings['bar']['horizontal_position'];
            $settings['bar']['vertical_alignment']                  = $old_settings['bar']['vertical_position'];
            if ( $old_settings['bar']['vertical_alignment'] === 'top' && $bar['is_border'] === 'one' )
            {
                $settings['bar']['is_borders']['top']               = 0;
                $settings['bar']['is_borders']['bottom']            = 1;
            }
            elseif ( $old_settings['bar']['vertical_alignment'] === 'bottom' && $bar['is_border'] === 'one' )
            {
                $settings['bar']['is_borders']['top']               = 1;
                $settings['bar']['is_borders']['bottom']            = 0;
            }
            elseif ( $bar['is_border'] === 'two' )
            {
                $settings['bar']['is_borders']['top']               = 1;
                $settings['bar']['is_borders']['bottom']            = 1;
            }
            else
            {
                $settings['bar']['is_borders']['top']               = 0;
                $settings['bar']['is_borders']['bottom']            = 0;
            }

            $settings['icons_labels']['alignment']                  = $old_settings['icons']['alignment'];
            $settings['icons_labels']['width']                      = $old_settings['icons']['width'];
            if ( $old_settings['icons']['is_border'] === 'two' )
            {
                $settings['icons_labels']['is_borders']['top']         = 0;
                $settings['icons_labels']['is_borders']['right']       = 1;
                $settings['icons_labels']['is_borders']['bottom']      = 0;
                $settings['icons_labels']['is_borders']['left']        = 1;
            }
            elseif ( $old_settings['icons']['is_border'] === 'four' )
            {
                $settings['icons_labels']['is_borders']['top']         = 1;
                $settings['icons_labels']['is_borders']['right']       = 1;
                $settings['icons_labels']['is_borders']['bottom']      = 1;
                $settings['icons_labels']['is_borders']['left']        = 1;
            }
            else
            {
                $settings['icons_labels']['is_borders']['top']         = 0;
                $settings['icons_labels']['is_borders']['right']       = 0;
                $settings['icons_labels']['is_borders']['bottom']      = 0;
                $settings['icons_labels']['is_borders']['left']        = 0;
            }
            $settings['icons_labels']['border_color']               = $old_settings['icons']['border_color'];
            $settings['icons_labels']['border_width']               = $old_settings['icons']['border_width'];
            $settings['icons_labels']['icon_size']                  = $old_settings['icons']['size'];
            $settings['icons_labels']['background_color']           = $old_settings['bar']['color'];
            $settings['icons_labels']['icon_color']                 = $old_settings['icons']['color'];
            $settings['icons_labels']['secondary_colors']['hover']  = 0;
            $settings['icons_labels']['secondary_colors']['focus']  = 0;
            $settings['icons_labels']['secondary_colors']['active'] = 0;
            $settings['icons_labels']['secondary_background_color'] = '';
            $settings['icons_labels']['secondary_icon_color']       = '';
            $settings['icons_labels']['secondary_label_color']      = '';
            $settings['icons_labels']['secondary_border_color']     = '';

            $settings['toggle']['background_color']                 = $old_settings['toggle']['color'];
            $settings['toggle']['font_color']                       = $old_settings['icons']['color'];
            $settings['toggle']['font_size']                        = $old_settings['toggle']['size'];

            if ( isset( $old_settings['badges'] ))
            {
                $settings['badges']['position']                     = $old_settings['badges']['place'];
                $settings['badges']['font_size']                    = $old_settings['badges']['size'];
            }
        }
        else
        {
            $settings = $old_settings;
        }

        return $settings;
    }


    private function migrate_contacts()
    {
        $contacts = [];

        $old_option_bar = get_option( abmcb()->id );
        $old_contacts = ( isset( $old_option_bar['contacts'] ) && is_array( $old_option_bar['contacts'] )) ? $old_option_bar['contacts'] : [];

        if ( ! empty ( array_column( $old_contacts, 'title' )))
        {
            $palette = abmcb( ContactsInput::class )->palette_defaults();

            foreach ( $old_contacts as $old_contact )
            {
                $contact = $old_contact;
                unset( $contact['title'] );
                unset( $contact['placeholder'] );
                $contact['id'] = '';
                $contact['label'] = '';
                $contact['palette'] = $palette;
                $contact['type'] = strtolower( $old_contact['type'] );
                switch ( $contact['type'] )
                {
                    case 'custom':
                        $contact['type'] = 'link';
                        break;
                    case 'text':
                        $contact['type'] = 'sms';
                        break;
                    case 'sample':
                        $contact['type'] = $this->determine_contact_type( $old_contact['uri'], $old_contact['placeholder'] );
                        break;
                }

                if ( 'link' === $contact['type'] && ! isset( $contact['parameters'] ))
                {
                    $contact['parameters'] = [];
                }

                if ( isset( $old_contact['parameters'] ) && is_array( $old_contact['parameters'] ))
                {
                    $contact['parameters'] = [];

                    foreach ( $old_contact['parameters'] as $old_parameter )
                    {
                        $parameter = [];
                        $parameter = $old_parameter;
                        unset( $parameter['type'] );

                        $contact['parameters'][] = $parameter;
                    }
                }

                $contacts[] = $contact;
            }

            return $contacts;
        }
        else
        {
            $contacts = $old_contacts;
        }
        
        return $contacts;
    }


    private function determine_contact_type( $uri, $placeholder )
    {
        $contact_type = '';

        if ( $placeholder === '' && $uri === 'https://api.whatsapp.com/send' )
        {
            $contact_type = 'whatsapp';
            return $contact_type;
        }

        $schemes = ['tel', 'sms', 'skype', 'mailto', 'https', 'http'];

        $scheme = array_reduce(
            $schemes,
            function( $acc, $scheme ) use( $placeholder ) { return ( strpos( $placeholder, $scheme ) > -1 ) ? $scheme : $acc; },
            ''
        );

        switch( $scheme )
        {
            case 'tel':
                $contact_type = 'tel';
                break;
            case 'sms':
                $contact_type = 'sms';
                break;

            case 'skype':
                $contact_type = 'skype';
                break;

            case 'mailto':
                $contact_type = 'email';
                break;

            case 'http':
            case 'https':
                $contact_type = 'link';
                break;
            
            default:
                $contact_type = 'link';
        }

        return $contact_type;
    }
}
