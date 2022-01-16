<?php

namespace MobileContactBar\Migrations;


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
            $this->migrate_option_bar();
            delete_option( 'mcb_option' );

            return true;
        }

        return false;
    }


    /**
     * @return void
     */
    public function migrate_option_bar()
    {
        $settings = $this->migrate_settings();
        $buttons  = $this->migrate_buttons();

        update_option( abmcb()->id, ['settings' => $settings, 'buttons' => $buttons] );
    }


    /**
     * @return array
     */
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
                $settings['bar']['device']              = ( (int) $settings_v1['bar_max_screen_width'] > 1400 ) ? 'both' : 'mobile';
            }
            if ( isset( $settings_v1['bar_is_active'], $settings['bar']['device'] ))
            {
                $settings['bar']['device']              = ( (int) $settings_v1['bar_is_active'] ) ? $settings['bar']['device'] : 'none';    
            }
            if ( isset( $settings_v1['bar_is_new_tab'] ))
            {
                $settings['bar']['is_new_tab']          = (int) $settings_v1['bar_is_new_tab'];
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
                $settings['bar']['is_fixed']            = (int) $settings_v1['bar_is_fixed'];
            }
            if ( isset( $settings_v1['bar_height'] ))
            {
                $settings['bar']['height']              = (int) $settings_v1['bar_height'];
            }
            if ( isset( $settings_v1['bar_color'] ))
            {
                $settings['bar']['color']               = $settings_v1['bar_color'];
            }
            if ( isset( $settings_v1['bar_opacity'] ))
            {
                $settings['bar']['opacity']             = (float) $settings_v1['bar_opacity'];
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
                $settings['icons']['border_width']      = (int) $settings_v1['icon_border_width'];
            }
            if ( isset( $settings_v1['bar_is_toggle'] ))
            {
                $settings['toggle']['is_render']        = (int) $settings_v1['bar_is_toggle'];
            }
            if ( isset( $settings_v1['bar_toggle_color'] ))
            {
                $settings['toggle']['color']            = $settings_v1['bar_toggle_color'];
            }
        }

        return $settings;
    }


    /**
     * @return array
     */
    private function migrate_buttons()
    {
        $buttons = [];

        if ( isset( $this->option_bar_v1['contacts'] ) && is_array( $this->option_bar_v1['contacts'] ))
        {
            $contacts_v1 = $this->option_bar_v1['contacts'];

            foreach( $contacts_v1 as $id_v1 => $contact_v1 )
            {
                $button = [];
                $button['checked'] = 1;

                $uri = $this->migrate_uri( $contact_v1['protocol'], $contact_v1['resource'] );
                $button['uri'] = $uri;

                switch( $id_v1 )
                {
                    case 'phone':
                        $button['type']        = 'Sample';
                        $button['icon']        = 'fas fa-phone';
                        $button['title']       = 'Phone Number for calling';
                        $button['placeholder'] = 'tel:+15417543010';
                        break;

                    case 'text':
                        $button['type']        = 'Sample';
                        $button['icon']        = 'far fa-comment';
                        $button['title']       = 'Phone Number for texting';
                        $button['placeholder'] = 'sms:+15417543010';
                        $button['parameters']  = [
                            [
                                'key'         => 'body',
                                'type'        => 'text',
                                'placeholder' => 'Message ...',
                                'value'       => '',
                            ],
                        ];

                        if( isset( $contact_v1['parameters'], $contact_v1['parameters']['body'] ))
                        {
                            $button['parameters'][0]['value'] = rawurlencode( rawurldecode( $contact_v1['parameters']['body'] ));
                        }
                        break;

                    case 'email':
                        $button['type']        = 'Sample';
                        $button['icon']        = 'far fa-envelope';
                        $button['title']       = 'Email Address';
                        $button['placeholder'] = 'mailto:username@example.com';
                        $button['parameters']  = [
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

                        foreach( $button['parameters'] as &$parameter )
                        {
                            $key = $parameter['key'];

                            if( isset( $contact_v1['parameters'] ) && isset( $contact_v1['parameters'][$key] ))
                            {
                                $parameter['value'] = rawurlencode( rawurldecode( $contact_v1['parameters'][$key] ));
                            }
                        }
                        unset( $parameter );
                        break;

                    case 'skype':
                        $button['type']        = 'Sample';
                        $button['icon']        = 'fab fa-skype';
                        $button['title']       = 'Skype for calling';
                        $button['placeholder'] = 'skype:username?call';
                        break;

                    case 'address':
                        $button['type']        = 'Sample';
                        $button['icon']        = 'fas fa-map-marker-alt';
                        $button['title']       = 'Google Maps';
                        $button['placeholder'] = 'https://google.com/maps/place/Dacre+St,+London+UK/';
                        break;

                    case 'facebook':
                        $button['type']        = 'Sample';
                        $button['icon']        = 'fab fa-facebook-f';
                        $button['title']       = 'Facebook';
                        $button['placeholder'] = 'https://www.facebook.com/username';
                        break;

                    case 'twitter':
                        $button['type']        = 'Sample';
                        $button['icon']        = 'fab fa-twitter';
                        $button['title']       = 'Twitter';
                        $button['placeholder'] = 'https://twitter.com/username';
                        break;

                    case 'googleplus':
                        $button['type']        = 'Sample';
                        $button['icon']        = 'fab fa-google-plus-g';
                        $button['title']       = 'Google+';
                        $button['placeholder'] = 'https://plus.google.com/username';
                        break;

                    case 'instagram':
                        $button['type']        = 'Sample';
                        $button['icon']        = 'fab fa-instagram';
                        $button['title']       = 'Instagram';
                        $button['placeholder'] = 'https://www.instagram.com/username';
                        break;

                    case 'youtube':
                        $button['type']        = 'Sample';
                        $button['icon']        = 'fab fa-youtube';
                        $button['title']       = 'YouTube';
                        $button['placeholder'] = 'https://www.youtube.com/user/username';
                        break;

                    case 'pinterest':
                        $button['type']        = 'Sample';
                        $button['icon']        = 'fab fa-pinterest-p';
                        $button['title']       = 'Pinterest';
                        $button['placeholder'] = 'https://www.pinterest.com/username';
                        break;

                    case 'tumblr':
                        $button['type']        = 'Sample';
                        $button['icon']        = 'fab fa-tumblr';
                        $button['title']       = 'Tumblr';
                        $button['placeholder'] = 'https://username.tumblr.com';
                        break;

                    case 'linkedin':
                        $button['type']        = 'Sample';
                        $button['icon']        = 'fab fa-linkedin-in';
                        $button['title']       = 'LinkedIn';
                        $button['placeholder'] = 'https://www.linkedin.com/in/username';
                        break;

                    case 'vimeo':
                        $button['type']        = 'Sample';
                        $button['icon']        = 'fab fa-vimeo-v';
                        $button['title']       = 'Vimeo';
                        $button['placeholder'] = 'https://vimeo.com/username';
                        break;

                    case 'flickr':
                        $button['type']        = 'Sample';
                        $button['icon']        = 'fab fa-flickr';
                        $button['title']       = 'Flickr';
                        $button['placeholder'] = 'https://www.flickr.com/people/username';
                        break;
                }

                $buttons[] = $button;
            }
        }

        return $buttons;
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
            case 'http':
            case 'https':
                $uri = $resource;
                break;

            case 'tel':
            case 'sms':
            case 'mailto':
                $uri = $protocol . ':' . $resource;
                break;

            case 'skype':
                $uri = $protocol . ':' . $resource . '?chat';
                break;

            default:
                '';
        }

        return esc_url_raw( rawurldecode( $uri ), abmcb()->schemes );
    }
}
