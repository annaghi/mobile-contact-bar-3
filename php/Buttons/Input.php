<?php

namespace MobileContactBar\Buttons;

use MobileContactBar\Icons;
use MobileContactBar\Helper;
use MobileContactBar\ButtonTypes;
use MobileContactBar\Settings;


final class Input
{
    /**
     * Defines sample 'buttons'.
     *
     * @return array
     */
    public function sample_buttons()
    {
        $default_customization = ButtonTypes\Button::default_customization();

        return
        [
            [
                'type'    => 'link',
                'id'      => '',
                'checked' => 1,
                'brand'   => 'fa',
                'group'   => 'solid',
                'icon'    => 'home',
                'label'   => __( 'Home', 'mobile-contact-bar' ),
                'text'    => __( 'Go to Home', 'mobile-contact-bar' ),
                'uri'     => get_site_url(),
                'query'   => [],
                'custom'  => $default_customization,
            ],
            [
                'type'    => 'email',
                'id'      => '',
                'checked' => 1,
                'brand'   => 'fa',
                'group'   => 'regular',
                'icon'    => 'envelope',
                'label'   => __( 'Email', 'mobile-contact-bar' ),
                'text'    => __( 'Send email', 'mobile-contact-bar' ),
                'uri'     => $this->email(),
                'query'   => [
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
                'custom'  => $default_customization,
            ],
            [
                'type'    => 'link',
                'id'      => '',
                'checked' => 1,
                'brand'   => 'fa',
                'group'   => 'solid',
                'icon'    => 'star',
                'label'   => '',
                'text'    => __( 'Rate the plugin', 'mobile-contact-bar' ),
                'uri'     => 'https://wordpress.org/support/plugin/mobile-contact-bar/reviews/?filter=5',
                'query'   => [],
                'custom'  => array_merge( $default_customization,
                    [
                        'icon_color' => ['primary' => '#ffb900', 'secondary' => '#ff9529'],
                    ]
                ),
            ],
            [
                'type'    => 'link',
                'id'      => '',
                'checked' => 1,
                'brand'   => 'fa',
                'group'   => 'brands',
                'icon'    => 'wordpress',
                'label'   => '',
                'text'    => 'WordPress',
                'uri'     => 'https://wordpress.org/plugins/mobile-contact-bar/',
                'query'   => [],
                'custom'  => array_merge( $default_customization,
                    [
                        'background_color' => ['primary' => '#0073aa', 'secondary' => '#00a0d2'],
                        'icon_color'       => ['primary' => '#ffffff', 'secondary' => '#ffffff'],
                    ]
                ),
            ],
            [
                'type'    => 'scrolltotop',
                'id'      => '',
                'checked' => 1,
                'brand'   => 'fa',
                'group'   => 'solid',
                'icon'    => 'chevron-up',
                'label'   => '',
                'text'    => __( 'Scroll to top', 'mobile-contact-bar' ),
                'uri'     => '',
                'custom'  => $default_customization,
            ],
        ];
    }


    /**
     * Retrieves the sample 'buttons' with unchecked items.
     *
     * @return array
     */
    public function unchecked_sample_buttons()
    {
        $sample_buttons = $this->sample_buttons();
        return array_map( function ( $button ) { return array_replace( $button, ['checked' => 0] ); }, $sample_buttons );
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
     * Sanitizes the 'buttons'.
     *
     * @param  array $buttons
     * @return array
     */
    public function sanitize( $buttons = [] )
    {
        if ( ! is_array( $buttons ))
        {
            return $this->unchecked_sample_buttons();
        }

        $sanitized_buttons = [];

        $button_types = abmcb()->button_types;
        $button_types_keys = array_keys( $button_types );
        $empty_default_customization = ButtonTypes\Button::empty_default_customization();

        foreach ( $buttons as $button_key => $button )
        {
            $sanitized_button = [];

            // remove button if invalid 'query'
            if ( isset( $button['query'] ) && ! is_array( $button['query'] ))
            {
                continue;
            }

            // remove button if 'custom' does not exist or invalid
            if ( ! isset( $button['custom'] ) || ! is_array( $button['custom'] ))
            {
                continue;
            }

            // remove button if invalid 'type'
            if ( ! isset( $button['type'] ) || ! in_array( $button['type'], $button_types_keys ))
            {
                continue;
            }

            // Difference can only be with 'query' in Link button type
            $button['custom'] = Helper::array_intersect_key_recursive(
                array_replace_recursive( $empty_default_customization, $button['custom'] ),
                $empty_default_customization
            );
            $diff_button = Helper::array_minus_key_recursive( Helper::array_keys_recursive( $button ), $button_types[$button['type']]->keys());
            // remove button if invalid button keys
            if ( ! empty( $diff_button ) && ['query'] !== array_keys( $diff_button ))
            {
                continue;
            }

            // remove button if invalid parameter keys
            if ( isset( $diff_button['query'] ))
            {
                $diff_query = array_filter(
                    $diff_button['query'],
                    function ( $parameter ) { return ( count( $parameter ) !== 2 || ! isset( $parameter['key'], $parameter['value'] )); }
                );
                if ( ! empty( $diff_query ))
                {
                    continue;
                }
            }

            // remove button if invalid 'brand' but leave empty
            if ( '' !== $button['brand'] && ! in_array( $button['brand'], ['ti', 'fa'] ))
            {
                continue;
            }

            // remove button if 'brand', 'group', or 'icon' do not match
            if ( 'ti' === $button['brand'] && '' !== $button['group']
                && ! ( Icons::is_ti_icon( $button['icon'] )
                    && file_exists( plugin_dir_path( abmcb()->file ) . 'assets/svg/ti/icons/'. $button['icon'] . '.svg' )))
            {
                continue;
            }
            if ( 'fa' === $button['brand']
                && ! ( Icons::is_fa_icon( $button['group'], $button['icon'] )
                    && file_exists( plugin_dir_path( abmcb()->file ) . 'assets/svg/fa/svgs/' . $button['group'] . '/' . $button['icon'] . '.svg' )))
            {
                continue;
            }

            // 'type' is already sanitized
            $sanitized_button['type'] = $button['type'];

            // sanitize 'id'
            $is_any_color = array_filter(
                $button['custom'],
                function ( $color ) { return ! empty( $color['primary'] || ! empty( $color['secondary'] )); }
            );
            $value = sanitize_key( str_replace( ['#', '.'], '', $button['id'] ));
            if ( '' === $value && $is_any_color )
            {
                $sanitized_button['id'] = 'mcb-sample-id-' . ( $this->max_key( $buttons, $sanitized_buttons ) + 1 );
            }
            else
            {
                $sanitized_button['id'] = $value;
            }

            // sanitize 'checked'
            $sanitized_button['checked'] = ( isset( $button['checked'] ) && ( 0 === (int) $button['checked'] || 1 === (int) $button['checked'] ))
                ? $button['checked'] : 0;

            // 'brand' is already sanitized
            $sanitized_button['brand'] = $button['brand'];

            // 'group' is already sanitized
            $sanitized_button['group'] = $button['group'];

            // 'icon' is already sanitized
            $sanitized_button['icon'] = $button['icon'];

            // sanitize 'label'
            $sanitized_button['label'] = sanitize_text_field( $button['label'] );

            // sanitize 'text'
            $sanitized_button['text'] = sanitize_text_field( $button['text'] );

            // sanitize 'uri'
            $sanitized_button['uri'] = $this->sanitize_uri( $button['type'], $button['uri'] );

            // sanitize 'query'
            if ( isset( $button['query'] ))
            {
                $sanitized_button['query'] = [];

                $button_field = $button_types[$button['type']]->field();

                foreach ( $button['query'] as $parameter_key => $parameter )
                {
                    if ( 'link' === $button['type'] )
                    {
                        $field = 'text';
                    }
                    else
                    {
                        $parameter_index = array_search( $parameter['key'], array_column( $button_field['query'], 'key' ));
                        $parameter_type = $button_field['query'][$parameter_index];
                        $field = $parameter_type['field'];
                    }

                    // sanitize 'key'
                    $sanitized_button['query'][$parameter_key]['key'] = $this->sanitize_parameter( $parameter['key'], 'text' );

                    // santitize 'value'
                    $sanitized_button['query'][$parameter_key]['value'] = $this->sanitize_parameter( $parameter['value'], $field );
                }
            }
            // add 'query' for 'link' button type if it was empty
            if ( ( 'link' === $button['type'] ) && ! isset( $button['query'] ))
            {
                $sanitized_button['query'] = [];
            }
            // reindex 'query'
            if ( isset( $sanitized_button['query'] ) && ! empty( $sanitized_button['query'] ))
            {
                $sanitized_button['query'] = array_values( $sanitized_button['query'] );
            }

            // sanitize customization
            foreach ( $button['custom'] as $custom_key => $custom )
            {  
                foreach ( $custom as $option_key => $option )
                {
                    $sanitized_button['custom'][$custom_key][$option_key] = abmcb( Settings\Input::class )->sanitize_color( $option );
                }
            }

            $sanitized_buttons[$button_key] = $sanitized_button;
        }

        // reindex
        return array_values( $sanitized_buttons );
    }


    /**
     * Sanitizes the URI.
     *
     * @param  string $button_type
     * @param  string $uri         URI (URL, phone number, email address, etc.)
     * @return string              Sanitized URI
     */
    public function sanitize_uri( $button_type, $uri )
    {
        if ( '' === $uri )
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
            case 'http':
            case 'https':
                $new_uri = $uri;
                break;

            case 'mailto':
                $new_uri = $this->sanitize_email( $uri );
                break;

            case 'skype':
                $new_uri = $this->sanitize_skype( $uri );
                break;

            case 'sms':
                $new_uri = $this->sanitize_sms( $uri );
                break;

            case 'tel':
                $new_uri = $this->sanitize_tel( $uri );
                break;

            case 'viber':
                $new_uri = $this->sanitize_viber( $uri );
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
        $match = preg_match( '/^mailto:(.*)$/', $sanitized_email, $matches );
        if ( 1 === $match )
        {
            $sanitized_email = sanitize_email( $matches[1] );

            if ( is_email( $sanitized_email ))
            {
                return 'mailto:' . $sanitized_email;
            }
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
     * @param  array $buttons
     * @param  array $sanitized_buttons
     * @return int
     */
    public function max_key( $buttons, $sanitized_buttons )
    {
        $key = -1;
        if ( 0 === count( $buttons ))
        {
            return $key;
        }
        else
        {
            $ids = array_merge( array_column( $buttons, 'id' ), array_column( $sanitized_buttons, 'id' ));

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
