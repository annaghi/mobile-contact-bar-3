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

            $settings['icons_labels']['alignment']                  = $old_settings['icons']['alignment'];
            $settings['icons_labels']['width']                      = $old_settings['icons']['width'];
            if ( $old_settings['icons']['is_border'] === 'none' )
            {
                $settings['icons_labels']['borders']['top']         = 0;
                $settings['icons_labels']['borders']['right']       = 0;
                $settings['icons_labels']['borders']['bottom']      = 0;
                $settings['icons_labels']['borders']['left']        = 0;
            }
            elseif ( $old_settings['icons']['is_border'] === 'two' )
            {
                $settings['icons_labels']['borders']['top']         = 0;
                $settings['icons_labels']['borders']['right']       = 1;
                $settings['icons_labels']['borders']['bottom']      = 0;
                $settings['icons_labels']['borders']['left']        = 1;
            }
            elseif ( $old_settings['icons']['is_border'] === 'four' )
            {
                $settings['icons_labels']['borders']['top']         = 1;
                $settings['icons_labels']['borders']['right']       = 1;
                $settings['icons_labels']['borders']['bottom']      = 1;
                $settings['icons_labels']['borders']['left']        = 1;
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

            $contacts = array_map( function( $old_contact ) {
                $contact = $old_contact;
                $contact['label'] = '';
                $contact['palette'] = $palette;
                unset( $contact['title'] );
                unset( $contact['placeholder'] );
                return $contact;
            }, $old_contacts );

            return $contacts;
        }
        else
        {
            $contacts = $old_contacts;
        }
        
        return $contacts;
    }
}
