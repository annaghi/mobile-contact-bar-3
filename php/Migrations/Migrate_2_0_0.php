<?php

namespace MobileContactBar\Migrations;

use MobileContactBar\Settings\Input as SettingsInput;
use MobileContactBar\Contacts\Input as ContactsInput;
use MobileContactBar\Contacts\Validator as ContactsValidator;
use MobileContactBar\Styles\CSS;

final class Migrate_2_0_0
{
    public $old_option_plugin = false;


    /**
     * @return bool
     */
    public function run()
    {
        $old_option_plugin = get_option( 'mcb_option' );

        if ( !! $old_option_plugin && is_array( $old_option_plugin ))
        {
logg(__METHOD__);
            $this->old_option_plugin = $old_option_plugin;
            $this->migrate_bar();

            return true;
        }

        return false;
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
        delete_option( 'mcb_option' );
    }


    private function migrate_settings()
    {
        $settings = abmcb( SettingsInput::class )->fields_defaults();

        if ( isset( $this->old_option_plugin['settings'] ) && is_array( $this->old_option_plugin['settings'] ))
        {
            $old_settings = $this->old_option_plugin['settings'];

            $settings['bar']['device']                              = ( $old_settings['bar_max_screen_width'] > 1400 ) ? 'both' : 'mobile';
            $settings['bar']['device']                              = ( $old_settings['bar_is_active'] ) ? $settings['bar']['device'] : 'none';
            $settings['bar']['is_new_tab']                          = ( isset( $old_settings['bar_is_new_tab'] )) ? $old_settings['bar_is_new_tab'] : 0;
            $settings['bar']['vertical_alignment']                  = $old_settings['bar_position'];
            $settings['bar']['is_fixed']                            = $old_settings['bar_is_fixed'];
            $settings['bar']['height']                              = $old_settings['bar_height'];
            $settings['bar']['horizontal_alignment']                = ( isset( $old_settings['bar_horizontal_align'] )) ? $old_settings['bar_horizontal_align'] : 'center';
            $settings['bar']['opacity']                             = $old_settings['bar_opacity'];
            $settings['bar']['placeholder_height']                  = 0;

            $settings['icons_labels']['is_border']                  = ! $old_settings['icon_is_border'] ? 'none' : 'around';
            $settings['icons_labels']['border_color']               = $old_settings['icon_border_color'];
            $settings['icons_labels']['border_width']               = $old_settings['icon_border_width'];
            $settings['icons_labels']['icon_size']                  = $old_settings['icon_size'];
            $settings['icons_labels']['background_color']           = $old_settings['bar_color'];
            $settings['icons_labels']['icon_color']                 = $old_settings['icon_color'];
            $settings['icons_labels']['secondary_colors']['hover']  = 0;
            $settings['icons_labels']['secondary_colors']['focus']  = 0;
            $settings['icons_labels']['secondary_colors']['active'] = 0;
            $settings['icons_labels']['secondary_background_color'] = '';
            $settings['icons_labels']['secondary_icon_color']       = '';
            $settings['icons_labels']['secondary_label_color']      = '';
            $settings['icons_labels']['secondary_border_color']     = '';

            $settings['toggle']['is_render']                        = $old_settings['bar_is_toggle'];
            $settings['toggle']['background_color']                 = $old_settings['bar_toggle_color'];
            $settings['toggle']['font_color']                       = $old_settings['icon_color'];
        }

        return $settings;
    }


    private function migrate_contacts()
    {
        $contacts = [];
        $contact_types = array_keys( apply_filters( 'mcb_admin_contact_types', [] ));

        if ( isset( $this->old_option_plugin['contacts'] ) && is_array( $this->old_option_plugin['contacts'] ))
        {
            $old_contacts = $this->old_option_plugin['contacts'];

            foreach( $old_contacts as $old_id => $old_contact )
            {
                $contact = [];
                $contact['checked'] = 1;
                $contact['id'] = '';
                $contact['palette'] = abmcb( ContactsInput::class )->palette_defaults();

                $uri = $this->build_uri( $old_contact['protocol'], $old_contact['resource'] );
                $contact['uri'] = ContactsValidator::sanitize_contact_uri( $uri );

                switch( $old_id )
                {
                    case 'phone':
                        $contact['type']       = 'tel';
                        $contact['icon']       = 'fas fa-phone';
                        $contact['label']      = '';
                        break;

                    case 'text':
                        $contact['type']       = 'sms';
                        $contact['icon']       = 'far fa-sms';
                        $contact['label']      = '';
                        $contact['parameters'] = [
                            [
                                'key'   => 'body',
                                'value' => '',
                            ],
                        ];

                        if( isset( $old_contact['parameters'], $old_contact['parameters']['body'] ))
                        {
                            $value = urldecode( $old_contact['parameters']['body'] );
                            $contact['parameters'][0]['value'] = ContactsValidator::sanitize_parameter_value( $value, 'text' );
                        }
                        break;

                    case 'email':
                        $contact['type']       = 'email';
                        $contact['icon']       = 'far fa-envelope';
                        $contact['label']      = '';
                        $contact['parameters'] = [
                            [
                                'key'   => 'subject',
                                'value' => '',
                            ],
                            [
                                'key'   => 'body',
                                'value' => '',
                            ],
                            [
                                'key'   => 'cc',
                                'value' => '',
                            ],
                            [
                                'key'   => 'bcc',
                                'value' => '',
                            ],
                        ];

                        $contact_type  = $contact_types['email'];

                        foreach( $contact['parameters'] as $parameter_id => &$parameter )
                        {
                            $key = $parameter['key'];
                            $parameter_index = array_search( $key, array_column( $contact_type['parameters'], 'key' ));
                            $parameter_type = $contact_type['parameters'][$parameter_index];

                            if( isset( $old_contact['parameters'], $old_contact['parameters'][$key] ))
                            {
                                $value = urldecode( $old_contact['parameters'][$key] );
                                $parameter['value'] = ContactsValidator::sanitize_parameter_value( $value, $parameter_type['field'] );
                            }
                        }
                        unset( $parameter );
                        break;

                    case 'skype':
                        $contact['type']  = 'skype';
                        $contact['icon']  = 'fab fa-skype';
                        $contact['label'] = '';
                        break;

                    case 'address':
                        $contact['type']  = 'link';
                        $contact['icon']  = 'fas fa-map-marker-alt';
                        $contact['label'] = '';
                        break;

                    case 'facebook':
                        $contact['type']  = 'link';
                        $contact['icon']  = 'fab fa-facebook-f';
                        $contact['label'] = '';
                        break;

                    case 'twitter':
                        $contact['type']  = 'link';
                        $contact['icon']  = 'fab fa-twitter';
                        $contact['label'] = '';
                        break;

                    case 'googleplus':
                        $contact['type']  = 'link';
                        $contact['icon']  = 'fab fa-google-plus-g';
                        $contact['label'] = '';
                        break;

                    case 'instagram':
                        $contact['type']  = 'link';
                        $contact['icon']  = 'fab fa-instagram';
                        $contact['label'] = '';
                        break;

                    case 'youtube':
                        $contact['type']  = 'link';
                        $contact['icon']  = 'fab fa-youtube';
                        $contact['label'] = '';
                        break;

                    case 'pinterest':
                        $contact['type']  = 'link';
                        $contact['icon']  = 'fab fa-pinterest-p';
                        $contact['label'] = '';
                        break;

                    case 'tumblr':
                        $contact['type']  = 'link';
                        $contact['icon']  = 'fab fa-tumblr';
                        $contact['label'] = '';
                        break;

                    case 'linkedin':
                        $contact['type']  = 'link';
                        $contact['icon']  = 'fab fa-linkedin-in';
                        $contact['label'] = '';
                        break;

                    case 'vimeo':
                        $contact['type']  = 'link';
                        $contact['icon']  = 'fab fa-vimeo-v';
                        $contact['label'] = '';
                        break;

                    case 'flickr':
                        $contact['type']  = 'link';
                        $contact['icon']  = 'fab fa-flickr';
                        $contact['label'] = '';
                        break;
                }

                $contacts[] = $contact;
            }

            // $missing_default_contacts = array_filter(
            //     $new_contacts,
            //     function( $contact ) use ( $contacts ) {
            //         return ! in_array( $contact['icon'], array_column( $contacts, 'icon' ));
            //     }
            // );
            // $contacts = array_merge( $contacts, $missing_default_contacts );
        }
        else
        {
            $contacts = abmcb( ContactsInput::class )->fields_sampoes();
        }

        return $contacts;
    }


    /**
     * Creates new URI.
     *
     * @param  string $protocol [description]
     * @param  string $resource [description]
     * @return string           URI
     */
    private function build_uri( $protocol, $resource )
    {
        $uri = '';

        switch( $protocol )
        {
            case 'tel':
            case 'sms':
            case 'mailto':
                $uri = $protocol . ':' . $resource;
                break;

            case 'skype':
                $uri = $protocol . ':' . $resource . '?chat';
                break;

            case 'http':
            case 'https':
                $uri = $resource;
                break;
        }

        return $uri;
    }
}
