<?php

namespace MobileContactBar\Migrations;

use MobileContactBar\Settings;
use MobileContactBar\Contacts;
use MobileContactBar\Styles;


final class Migrate_2_0_0
{
    public $option_bar_v1 = false;


    /**
     * @return bool
     */
    public function run()
    {
        $option_bar_v1 = get_option( 'mcb_option' );

        if ( $option_bar_v1 && is_array( $option_bar_v1 ))
        {
            $this->option_bar_v1 = $option_bar_v1;
            $this->migrate_bar();
            delete_option( 'mcb_option' );

            return true;
        }

        return false;
    }


    public function migrate_bar()
    {
        $settings = $this->migrate_settings();
        $contacts = $this->migrate_contacts();
        $styles   = '';

        $option_bar = [
            'settings' => $settings,
            'contacts' => $contacts,
            'styles'   => $styles,
        ];

        update_option( abmcb()->id, $option_bar );
    }


    private function migrate_settings()
    {
        $settings = [];

        if ( isset( $this->option_bar_v1['settings'] ) && is_array( $this->option_bar_v1['settings'] ))
        {
            $settings_v1 = $this->option_bar_v1['settings'];

            $settings['bar']['placeholder_height']      = 0;
            $settings['toggle']['label']                = '';

            if ( isset( $settings_v1['bar_max_screen_width'] ))
            {
                $settings['bar']['device']              = ( $settings_v1['bar_max_screen_width'] > 1400 ) ? 'both' : 'mobile';
            }
            if ( isset( $settings_v1['bar_is_active'], $settings['bar']['device'] ))
            {
                $settings['bar']['device']              = ( $settings_v1['bar_is_active'] ) ? $settings['bar']['device'] : 'none';    
            }
            if ( isset( $settings_v1['bar_is_new_tab'] ))
            {
                $settings['bar']['is_new_tab']          = $settings_v1['bar_is_new_tab'];
            }
            if ( isset( $settings_v1['bar_horizontal_align'] ))
            {
                $settings['bar']['horizontal_position'] = $settings_v1['bar_horizontal_align'];
            }
            if ( isset( $settings_v1['bar_position'] ))
            {
                $settings['bar']['vertical_position']   = $settings_v1['bar_position'];
            }
            if ( isset( $settings_v1['bar_is_fixed'] ))
            {
                $settings['bar']['is_fixed']            = $settings_v1['bar_is_fixed'];
            }
            if ( isset( $settings_v1['bar_height'] ))
            {
                $settings['bar']['height']              = $settings_v1['bar_height'];
            }
            if ( isset( $settings_v1['bar_color'] ))
            {
                $settings['bar']['color']               = $settings_v1['bar_color'];
            }
            if ( isset( $settings_v1['bar_opacity'] ))
            {
                $settings['bar']['opacity']             = $settings_v1['bar_opacity'];
            }
            if ( isset( $settings_v1['icon_size'] ))
            {
                $settings['icons']['size']              = $settings_v1['icon_size'];
            }
            if ( isset( $settings_v1['icon_color'] ))
            {
                $settings['icons']['color']             = $settings_v1['icon_color'];
            }
            if ( isset( $settings_v1['icon_is_border'] ))
            {
                $settings['icons']['is_border']         = 'four';
            }
            if ( isset( $settings_v1['icon_border_color'] ))
            {
                $settings['icons']['border_color']      = $settings_v1['icon_border_color'];
            }
            if ( isset( $settings_v1['icon_border_width'] ))
            {
                $settings['icons']['border_width']      = $settings_v1['icon_border_width'];
            }
            if ( isset( $settings_v1['bar_is_toggle'] ))
            {
                $settings['toggle']['is_render']        = $settings_v1['bar_is_toggle'];
            }
            if ( isset( $settings_v1['bar_toggle_color'] ))
            {
                $settings['toggle']['color']            = $settings_v1['bar_toggle_color'];
            }
        }

        return $settings;
    }


    private function migrate_contacts()
    {
        $contacts = [];

        if ( isset( $this->option_bar_v1['contacts'] ) && is_array( $this->option_bar_v1['contacts'] ))
        {
            $contacts_v1 = $this->option_bar_v1['contacts'];

            foreach( $contacts_v1 as $id_v1 => $contact_v1 )
            {
                $contact = [];
                $contact['checked'] = 1;

                $uri = $this->migrate_uri( $contact_v1['protocol'], $contact_v1['resource'] );
                $contact['uri'] = Contacts\Validator::sanitize_contact_uri( $uri );

                switch( $id_v1 )
                {
                    case 'phone':
                        $contact['type']        = 'Sample';
                        $contact['icon']        = 'fas fa-phone';
                        $contact['title']       = 'Phone Number for calling';
                        $contact['placeholder'] = 'tel:+15417543010';
                        break;

                    case 'text':
                        $contact['type']        = 'Sample';
                        $contact['icon']        = 'far fa-comment';
                        $contact['title']       = 'Phone Number for texting';
                        $contact['placeholder'] = 'sms:+15417543010';
                        $contact['parameters']  = [
                            [
                                'key'         => 'body',
                                'type'        => 'text',
                                'placeholder' => 'Message ...',
                                'value'       => '',
                            ],
                        ];

                        if( isset( $contact_v1['parameters'], $contact_v1['parameters']['body'] ))
                        {
                            $value = urldecode( $contact_v1['parameters']['body'] );
                            $contact['parameters'][0]['value'] = Contacts\Validator::sanitize_parameter_value( $value, 'text' );
                        }
                        break;

                    case 'email':
                        $contact['type']        = 'Sample';
                        $contact['icon']        = 'far fa-envelope';
                        $contact['title']       = 'Email Address';
                        $contact['placeholder'] = 'mailto:username@example.com';
                        $contact['parameters']  = [
                            [
                                'key'         => 'subject',
                                'type'        => 'text',
                                'placeholder' => 'Subject ...',
                                'value'       => '',
                            ],
                            [
                                'key'         => 'body',
                                'type'        => 'textarea',
                                'placeholder' => 'Text ...',
                                'value'       => '',
                            ],
                            [
                                'key'         => 'cc',
                                'type'        => 'email',
                                'placeholder' => 'example@domain.com',
                                'value'       => '',
                            ],
                            [
                                'key'         => 'bcc',
                                'type'        => 'email',
                                'placeholder' => 'example1@domain.com,example2@domain.net',
                                'value'       => '',
                            ],
                        ];

                        foreach( $contact['parameters'] as $parameter_id => &$parameter )
                        {
                            $key = $parameter['key'];

                            if( isset( $contact_v1['parameters'] ) && isset( $contact_v1['parameters'][$key] ))
                            {
                                $value = urldecode( $contact_v1['parameters'][$key] );
                                $parameter['value'] = Contacts\Validator::sanitize_parameter_value( $value, $parameter['type'] );
                            }
                        }

                        unset( $parameter );
                        break;

                    case 'skype':
                        $contact['type']        = 'Sample';
                        $contact['icon']        = 'fab fa-skype';
                        $contact['title']       = 'Skype for calling';
                        $contact['placeholder'] = 'skype:username?call';
                        break;

                    case 'address':
                        $contact['type']        = 'Sample';
                        $contact['icon']        = 'fas fa-map-marker-alt';
                        $contact['title']       = 'Google Maps';
                        $contact['placeholder'] = 'https://google.com/maps/place/Dacre+St,+London+UK/';
                        break;

                    case 'facebook':
                        $contact['type']        = 'Sample';
                        $contact['icon']        = 'fab fa-facebook-f';
                        $contact['title']       = 'Facebook';
                        $contact['placeholder'] = 'https://www.facebook.com/username';
                        break;

                    case 'twitter':
                        $contact['type']        = 'Sample';
                        $contact['icon']        = 'fab fa-twitter';
                        $contact['title']       = 'Twitter';
                        $contact['placeholder'] = 'https://twitter.com/username';
                        break;

                    case 'googleplus':
                        $contact['type']        = 'Sample';
                        $contact['icon']        = 'fab fa-google-plus-g';
                        $contact['title']       = 'Google+';
                        $contact['placeholder'] = 'https://plus.google.com/username';
                        break;

                    case 'instagram':
                        $contact['type']        = 'Sample';
                        $contact['icon']        = 'fab fa-instagram';
                        $contact['title']       = 'Instagram';
                        $contact['placeholder'] = 'https://www.instagram.com/username';
                        break;

                    case 'youtube':
                        $contact['type']        = 'Sample';
                        $contact['icon']        = 'fab fa-youtube';
                        $contact['title']       = 'YouTube';
                        $contact['placeholder'] = 'https://www.youtube.com/user/username';
                        break;

                    case 'pinterest':
                        $contact['type']        = 'Sample';
                        $contact['icon']        = 'fab fa-pinterest-p';
                        $contact['title']       = 'Pinterest';
                        $contact['placeholder'] = 'https://www.pinterest.com/username';
                        break;

                    case 'tumblr':
                        $contact['type']        = 'Sample';
                        $contact['icon']        = 'fab fa-tumblr';
                        $contact['title']       = 'Tumblr';
                        $contact['placeholder'] = 'https://username.tumblr.com';
                        break;

                    case 'linkedin':
                        $contact['type']        = 'Sample';
                        $contact['icon']        = 'fab fa-linkedin-in';
                        $contact['title']       = 'LinkedIn';
                        $contact['placeholder'] = 'https://www.linkedin.com/in/username';
                        break;

                    case 'vimeo':
                        $contact['type']        = 'Sample';
                        $contact['icon']        = 'fab fa-vimeo-v';
                        $contact['title']       = 'Vimeo';
                        $contact['placeholder'] = 'https://vimeo.com/username';
                        break;

                    case 'flickr':
                        $contact['type']        = 'Sample';
                        $contact['icon']        = 'fab fa-flickr';
                        $contact['title']       = 'Flickr';
                        $contact['placeholder'] = 'https://www.flickr.com/people/username';
                        break;
                }

                $contacts[] = $contact;
            }
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
    private function migrate_uri( $protocol, $resource )
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
