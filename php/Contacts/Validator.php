<?php

namespace MobileContactBar\Contacts;


final class Validator
{
    const SCHEMES = ['viber', 'tel', 'sms', 'skype', 'mailto', 'https', 'http'];


    /**
     * Escapes the contact URI.
     *
     * @param  string $uri Contact URI (URL, phone number, email address, etc.)
     * @return string      Escaped URI
     */
    public static function escape_contact_uri( $uri )
    {
        if ( '' === $uri )
        {
            return $uri;
        }

        $new_uri = '';

        $uri = strtolower( $uri );
        $scheme = array_reduce(
            self::SCHEMES,
            function( $acc, $scheme ) use( $uri ) { return ( strpos( $uri, $scheme ) > -1 ) ? $scheme : $acc; },
            ''
        );

        switch( $scheme )
        {
            case 'tel':
            case 'sms':
                $new_uri = self::sanitize_phone_number( $uri );
                break;

            case 'skype':
                $new_uri = self::sanitize_skype( $uri );
                break;

            case 'viber':
                $new_uri = self::sanitize_viber( $uri );
                break;

            case 'mailto':
                $new_uri = self::sanitize_email( $uri );
                break;

            case 'http':
            case 'https':
                $parsed_uri = parse_url( $uri );

                if ( isset( $parsed_uri['path'] ))
                {
                    $new_uri = untrailingslashit( esc_url( $parsed_uri['scheme'] . '://' . $parsed_uri['host'] . $parsed_uri['path'] ));
                }
                else
                {
                    $new_uri = untrailingslashit( esc_url( $parsed_uri['scheme'] . '://' . $parsed_uri['host'] ));
                }
                break;

            default:
                $new_uri = '';
        }
        return $new_uri;
    }


    /**
     * Sanitizes the contact URI.
     *
     * @param  string $uri Contact URI (URL, phone number, email address, etc.)
     * @return string      Sanitized URI
     */
    public static function sanitize_contact_uri( $uri )
    {
        if ( '' === $uri )
        {
            return $uri;
        }

        $new_uri = '';

        $uri = strtolower( $uri );
        $scheme = array_reduce(
            self::SCHEMES,
            function( $acc, $scheme ) use( $uri ) { return ( strpos( $uri, $scheme ) > -1 ) ? $scheme : $acc; },
            ''
        );

        switch( $scheme )
        {
            case 'tel':
            case 'sms':
                $new_uri = self::sanitize_phone_number( $uri );
                break;

            case 'skype':
                $new_uri = self::sanitize_skype( $uri );
                break;

            case 'viber':
                $new_uri = self::sanitize_viber( $uri );
                break;

            case 'mailto':
                $new_uri = self::sanitize_email( $uri );
                break;

            case 'http':
            case 'https':
                $parsed_uri = parse_url( $uri );

                if ( isset( $parsed_uri['path'] ))
                {
                    $new_uri = untrailingslashit( esc_url_raw( $parsed_uri['scheme'] . '://' . $parsed_uri['host'] . $parsed_uri['path'] ));
                }
                else
                {
                    $new_uri = untrailingslashit( esc_url_raw( $parsed_uri['scheme'] . '://' . $parsed_uri['host'] ));
                }
                break;

            default:
                $new_uri = '';
        }

        return $new_uri;
    }


    /**
     * Sanitizes the value part of a query string parameter.
     *
     * @param  string $value Parameter value
     * @param  string $type  Parameter type (text, textarea)
     * @return string        Sanitized parameter value
     */
    public static function sanitize_parameter_value( $value, $field )
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
                $sanitized_value = self::sanitize_email_addresses( $value );
                break;

            default:
                $sanitized_value = '';
        }

        return $sanitized_value;
    }


    /**
     * Sanitizes skype URI.
     *
     * @param  string $skype
     * @return string        Filtered skype URI
     */
    public static function sanitize_skype( $skype )
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
     * Sanitizes viber URI.
     *
     * @param  string $viber
     * @return string        Filtered viber URI
     */
    public static function sanitize_viber( $viber )
    {
        $sanitized_viber = preg_replace( '/\s+/', '', $viber );
        if ( preg_match( '/^viber:\/\/pa\?chatURI=.*/i', $sanitized_viber ))
        {
            return $sanitized_viber;
        }

        return '';
    }


    /**
     * Sanitizes phone number.
     *
     * @param  string $phone Phone number with protocol and an optional + sign
     * @return string        Filtered phone number
     */
    public static function sanitize_phone_number( $phone )
    {
        $sanitized_phone = preg_replace( '/\s+/', '', $phone );
        $sanitized_phone = preg_replace( '/[\.\-\(\)]/', '', $sanitized_phone );
        if ( preg_match( '/^tel:\+?[0-9]+$/i', $sanitized_phone ))
        {
            return $sanitized_phone;
        }

        return '';
    }


    public static function sanitize_email( $email )
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
    public static function sanitize_email_addresses( $email_addresses )
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
}
