<?php

namespace MobileContactBar\Contacts;

use MobileContactBar\Icons;
use MobileContactBar\Helper;
use MobileContactBar\ContactTypes;
use MobileContactBar\Settings;


final class Input
{
    /**
     * Defines sample 'contacts'.
     *
     * @return array
     */
    public function sample_contacts()
    {
        $default_customization = ContactTypes\ContactType::default_customization();

        return
        [
            [
                'type'        => 'link',
                'id'          => '',
                'checked'     => 1,
                'brand'       => 'fa',
                'group'       => 'solid',
                'icon'        => 'home',
                'label'       => __( 'Home' ),
                'text'        => __( 'Go to Home' ),
                'uri'         => get_site_url(),
                'parameters'  => [],
                'custom'      => $default_customization,
            ],
            [
                'type'        => 'email',
                'id'          => '',
                'checked'     => 1,
                'brand'       => 'fa',
                'group'       => 'regular',
                'icon'        => 'envelope',
                'label'       => __( 'Email' ),
                'text'        => __( 'Send email', 'mobile-contact-bar' ),
                'uri'         => $this->email(),
                'parameters'  => [
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
                ],
                'custom'      => $default_customization,
            ],
            [
                'type'        => 'any',
                'id'          => '',
                'checked'     => 1,
                'brand'       => 'fa',
                'group'       => 'solid',
                'icon'        => 'star',
                'label'       => '',
                'text'        => __( 'Rate the plugin', 'mobile-contact-bar' ),
                'uri'         => 'https://wordpress.org/support/plugin/mobile-contact-bar/reviews/?filter=5#new-post',
                'custom'      => array_merge( $default_customization,
                    [
                        'icon_color' => ['primary' => '#ffb900', 'secondary' => '#ff9529'],
                    ]
                ),
            ],
            [
                'type'        => 'link',
                'id'          => '',
                'checked'     => 1,
                'brand'       => 'fa',
                'group'       => 'brands',
                'icon'        => 'wordpress',
                'label'       => '',
                'text'        => 'WordPress',
                'uri'         => 'https://wordpress.org/plugins/mobile-contact-bar/',
                'parameters'  => [],
                'custom'      => array_merge( $default_customization,
                    [
                        'background_color' => ['primary' => '#0073aa', 'secondary' => '#00a0d2'],
                        'icon_color'       => ['primary' => '#ffffff', 'secondary' => '#ffffff'],
                    ]
                ),
            ],
            [
                'type'        => 'scrolltotop',
                'id'          => '',
                'checked'     => 1,
                'brand'       => 'fa',
                'group'       => 'solid',
                'icon'        => 'chevron-up',
                'label'       => '',
                'text'        => __( 'Scroll to top', 'mobile-contact-bar' ),
                'uri'         => '',
                'custom'      => $default_customization,
            ],
        ];
    }


    /**
     * Retrieves the sample 'contacts' with unchecked items.
     *
     * @return array
     */
    public function unchecked_sample_contacts()
    {
        $sample_contacts = $this->sample_contacts();
        return array_map( function ( $contact ) { return array_replace( $contact, ['checked' => 0] ); }, $sample_contacts );
    }


    /**
     * Generates email address for sample input.
     *
     * @return string Email address
     */
    private function email()
    {
        if ( is_user_logged_in() )
        {
            $current_user = wp_get_current_user();
            return 'mailto:' . $current_user->data->user_email;
        }

        return 'mailto:username@example.com';
    }


    /**
     * Sanitizes the 'contacts'.
     *
     * @param  array $contacts
     * @return array
     */
    public function sanitize( $contacts = [] )
    {
        if ( ! is_array( $contacts ))
        {
            return $this->unchecked_sample_contacts();
        }

        $sanitized_contacts = [];

        $contact_types = abmcb()->contact_types;
        $contact_types_keys = array_keys( $contact_types );
        $empty_default_customization = ContactTypes\ContactType::empty_default_customization();

        foreach ( $contacts as $contact_key => $contact )
        {
            $sanitized_contact = [];

            // remove contact if invalid 'type'
            if ( ! in_array( $contact['type'], $contact_types_keys ))
            {
                continue;
            }

            // remove contact if invalid 'parameters'
            if ( isset( $contact['parameters'] ) && ! is_array( $contact['parameters'] ))
            {
                continue;
            }

            // Difference can only be with 'parameters' in Link contact type
            $contact['custom'] = Helper::array_intersect_key_recursive(
                array_replace_recursive( $empty_default_customization, $contact['custom'] ),
                $empty_default_customization
            );
            $diff_contact = Helper::array_minus_key_recursive( Helper::array_keys_recursive( $contact ), $contact_types[$contact['type']]->keys());
            // remove contact if invalid contact keys
            if ( ! empty( $diff_contact ) && ( ['parameters'] !== array_keys( $diff_contact ) || 'link' !== $contact['type'] ))
            {
                continue;
            }

            // remove contact if invalid parameter keys
            if ( isset( $diff_contact['parameters'] ))
            {
                $diff_parameters = array_filter(
                    $diff_contact['parameters'],
                    function ( $parameter ) { return ( count( $parameter ) !== 2 || ! isset( $parameter['key'], $parameter['value'] )); }
                );
                if ( ! empty( $diff_parameters ))
                {
                    continue;
                }
            }

            // remove contact if invalid 'brand' but leave empty
            if ( '' !== $contact['brand'] && ! in_array( $contact['brand'], ['ti', 'fa'] ))
            {
                continue;
            }

            // remove contact if 'brand', 'group', or 'icon' do not match
            if ( 'ti' === $contact['brand'] && '' !== $contact['group']
                && ! ( Icons::is_ti_icon( $contact['icon'] )
                    && file_exists( plugin_dir_path( abmcb()->file ) . 'assets/svg/ti/icons/'. $contact['icon'] . '.svg' )))
            {
                continue;
            }
            if ( 'fa' === $contact['brand']
                && ! ( Icons::is_fa_icon( $contact['group'], $contact['icon'] )
                    && file_exists( plugin_dir_path( abmcb()->file ) . 'assets/svg/fa/svgs/' . $contact['group'] . '/' . $contact['icon'] . '.svg' )))
            {
                continue;
            }

            // 'type' is already sanitized
            $sanitized_contact['type'] = $contact['type'];

            // sanitize 'id'
            $is_any_color = array_filter(
                $contact['custom'],
                function ( $color ) { return ! empty( $color['primary'] || ! empty( $color['secondary'] )); }
            );
            $value = sanitize_key( str_replace( ['#', '.'], '', $contact['id'] ));
            if ( '' === $value && $is_any_color )
            {
                $sanitized_contact['id'] = 'mcb-sample-id-' . ( $this->max_key( $contacts, $sanitized_contacts ) + 1 );
            }
            else
            {
                $sanitized_contact['id'] = $value;
            }

            // sanitize 'checked'
            $sanitized_contact['checked'] = ( isset( $contact['checked'] ) && ( 0 === (int) $contact['checked'] || 1 === (int) $contact['checked'] ))
                ? $contact['checked'] : 0;

            // 'brand' is already sanitized
            $sanitized_contact['brand'] = $contact['brand'];

            // 'group' is already sanitized
            $sanitized_contact['group'] = $contact['group'];

            // 'icon' is already sanitized
            $sanitized_contact['icon'] = $contact['icon'];

            // sanitize 'label'
            $sanitized_contact['label'] = sanitize_text_field( $contact['label'] );

            // sanitize 'text'
            $sanitized_contact['text'] = sanitize_text_field( $contact['text'] );

            // sanitize 'uri'
            $sanitized_contact['uri'] = $this->sanitize_contact_uri( $contact['type'], $contact['uri'] );

            // sanitize 'parameters'
            if ( isset( $contact['parameters'] ))
            {
                $sanitized_contact['parameters'] = [];

                $contact_field = $contact_types[$contact['type']]->field();

                foreach ( $contact['parameters'] as $parameter_key => $parameter )
                {
                    if ( 'link' === $contact['type'] )
                    {
                        $field = 'text';
                    }
                    else
                    {
                        $parameter_index = array_search( $parameter['key'], array_column( $contact_field['parameters'], 'key' ));
                        $parameter_type = $contact_field['parameters'][$parameter_index];
                        $field = $parameter_type['field'];
                    }

                    // sanitize 'key'
                    $sanitized_contact['parameters'][$parameter_key]['key'] = $this->sanitize_parameter( $parameter['key'], 'text' );

                    // santitize 'value'
                    $sanitized_contact['parameters'][$parameter_key]['value'] = $this->sanitize_parameter( $parameter['value'], $field );
                }
            }
            // add 'parameters' for 'link' contact type if it was empty
            if ( 'link' === $contact['type'] && ! isset( $contact['parameters'] ))
            {
                $sanitized_contact['parameters'] = [];
            }
            // reindex 'parameters'
            if ( isset( $sanitized_contact['parameters'] ) && ! empty( $sanitized_contact['parameters'] ))
            {
                $sanitized_contact['parameters'] = array_values( $sanitized_contact['parameters'] );
            }

            // sanitize customization
            foreach ( $contact['custom'] as $custom_key => $custom )
            {  
                foreach ( $custom as $option_key => $option )
                {
                    $sanitized_contact['custom'][$custom_key][$option_key] = abmcb( Settings\Input::class )->sanitize_color( $option );
                }
            }

            $sanitized_contacts[$contact_key] = $sanitized_contact;
        }

        // reindex
        return array_values( $sanitized_contacts );
    }


    /**
     * Sanitizes the contact URI.
     *
     * @param  string $contact_type
     * @param  string $uri          Contact URI (URL, phone number, email address, etc.)
     * @return string               Sanitized URI
     */
    public function sanitize_contact_uri( $contact_type, $uri )
    {
        if ( '' === $uri || 'any' === $contact_type )
        {
            return $uri;
        }

        $new_uri = '';

        $scheme = array_reduce(
            abmcb()->schemes,
            function ( $acc, $scheme ) use ( $uri ) { return ( strpos( $uri, $scheme ) > -1 ) ? $scheme : $acc; },
            ''
        );

        switch( $scheme )
        {
            case 'sms':
                $new_uri = $this->sanitize_sms( $uri );
                break;

            case 'tel':
                $new_uri = $this->sanitize_tel( $uri );
                break;

            case 'skype':
                $new_uri = $this->sanitize_skype( $uri );
                break;

            case 'viber':
                $new_uri = $this->sanitize_viber( $uri );
                break;

            case 'mailto':
                $new_uri = $this->sanitize_email( $uri );
                break;

            case 'http':
            case 'https':
                $parsed_uri = parse_url( $uri );

                if ( isset( $parsed_uri['path'] ))
                {
                    $new_uri = $parsed_uri['scheme'] . '://' . $parsed_uri['host'] . $parsed_uri['path'];
                }
                else
                {
                    $new_uri = $parsed_uri['scheme'] . '://' . $parsed_uri['host'];
                }
                break;

            default:
                $new_uri = '';
        }
    
        return $new_uri;
    }


    /**
     * Sanitizes the key and value part of a query string parameter.
     *
     * @param  string $value Parameter key or value
     * @param  string $type  Parameter type (text, textarea)
     * @return string        Sanitized parameter key or value
     */
    public function sanitize_parameter( $value, $field )
    {
        $sanitized_value = '';

        switch( $field )
        {
            case 'text':
                $sanitized_value = sanitize_text_field( $value );
                break;

            case 'textarea':
                $sanitized_value = sanitize_textarea_field( $value );
                break;

            case 'email':
                $sanitized_value = $this->sanitize_email_addresses( $value );
                break;

            default:
                $sanitized_value = '';
        }

        return $sanitized_value;
    }


    /**
     * Sanitizes 'skype' URI.
     *
     * @param  string $skype URI with 'skype' scheme, a name or a phone number prefixed with + sign
     * @return string        Sanitized URI
     */
    public function sanitize_skype( $skype )
    {
        $sanitized_skype = preg_replace( '/\s+/', '', $skype );
        if ( preg_match( '/^skype:[a-z][a-z0-9\.,\-_]{5,31}\?(call|chat)$/', $sanitized_skype ))
        {
            return $sanitized_skype;
        }
        if ( preg_match( '/^skype:\+[0-9]+\?(call|chat)$/', $sanitized_skype ))
        {
            return $sanitized_skype;
        }

        return '';
    }


    /**
     * Sanitizes 'viber' URI.
     *
     * @param  string $viber URI with 'viber' scheme, path 'pa', and 'chatURI'
     * @return string        Sanitized URI
     */
    public function sanitize_viber( $viber )
    {
        $sanitized_viber = preg_replace( '/\s+/', '', $viber );
        if ( preg_match( '/^viber:\/\/pa\?chatURI=.*/', $sanitized_viber ))
        {
            return $sanitized_viber;
        }

        return '';
    }


    /**
     * Sanitizes 'sms' URI.
     *
     * @param  string $sms URI with 'sms' scheme, an optional + sign, and a phone number
     * @return string      Sanitized URI
     */
    public function sanitize_sms( $sms )
    {
        $sanitized_sms = preg_replace( '/\s+/', '', $sms );
        $sanitized_sms = preg_replace( '/[\.\-\(\)]/', '', $sanitized_sms );
        if ( preg_match( '/^sms:\+?[0-9]+$/', $sanitized_sms ))
        {
            return $sanitized_sms;
        }

        return '';
    }


    /**
     * Sanitizes 'tel' URI.
     *
     * @param  string $tel URI with 'tel' scheme, an optional + sign, and a phone number
     * @return string      Sanitized URI
     */
    public function sanitize_tel( $tel )
    {
        $sanitized_tel = preg_replace( '/\s+/', '', $tel );
        $sanitized_tel = preg_replace( '/[\.\-\(\)]/', '', $sanitized_tel );
        if ( preg_match( '/^tel:\+?[0-9]+$/', $sanitized_tel ))
        {
            return $sanitized_tel;
        }

        return '';
    }


    /**
     * Sanitizes 'mailto' URI.
     *
     * @param  string $email URI with 'mailto' scheme, and an email address
     * @return string        Sanitized URI
     */
    public function sanitize_email( $email )
    {
        $sanitized_email = preg_replace( '/\s+/', '', $email );
        preg_match( '/^mailto:(.*)$/', $sanitized_email, $matches );
        $sanitized_email = sanitize_email( $matches[1] );

        if ( is_email( $sanitized_email ))
        {
            return 'mailto:' . $sanitized_email;
        }

        return '';
    }


    /**
     * Sanitizes email address list.
     *
     * @param  string $email_addresses Comma separated email address list
     * @return string                  Filtered comma separated email address list
     */
    public function sanitize_email_addresses( $email_addresses )
    {
        $sanitized_email_adresses = '';

        $email_addresses = preg_replace( '/\s+/', '', $email_addresses );

        if ( ! empty( $email_addresses ))
        {
            $sanitized_emails = [];
            $emails = explode( ',', $email_addresses );

            foreach ( $emails as $item )
            {
                $email = sanitize_email( $item );
                if ( is_email( $email ))
                {
                    $sanitized_emails[] = $email;
                }
            }

            $sanitized_email_adresses = join( ',', $sanitized_emails );
        }

        return $sanitized_email_adresses;
    }


    /**
     * @param  array $contacts
     * @param  array $sanitized_contacts
     * @return int
     */
    public function max_key( $contacts, $sanitized_contacts )
    {
        $key = -1;
        if ( 0 === count( $contacts ))
        {
            return $key;
        }
        else
        {
            $ids = array_merge( array_column( $contacts, 'id' ), array_column( $sanitized_contacts, 'id' ));

            foreach( $ids as $id )
            {
                $match = preg_match( '/^mcb-sample-id-([0-9]+)$/', $id, $matches );
                if ( 1 === $match )
                {
                    $key = max( $matches[1], $key );
                }
            }
            return $key;
        }
    }
}
