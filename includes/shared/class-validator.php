<?php
/**
 * Mobile Contact Bar Validator
 *
 * @package Mobile_Contact_Bar\Shared
 */

defined( 'ABSPATH' ) || exit;

/**
 * Mobile_Contact_Bar_Validator class
 */
final class Mobile_Contact_Bar_Validator {


	/**
	 * Supported protocols.
	 *
	 * @var array
	 */
	public static $protocols = array( 'http', 'https', 'mailto', 'skype', 'sms', 'tel' );



	/**
	 * Checks and cleans the contact with 'skype' or 'sms' protocols for database.
	 *
	 * @see https://codex.wordpress.org/Function_Reference/esc_url_raw
	 *
	 * @since 2.0.0
	 *
	 * @param  string $uri Contact URI.
	 * @return string      Escaped URI if it has 'skype' or 'sms' protocols
	 */
	public static function sanitize_contact_uri_skype_sms( $uri ) {
		if ( '' === $uri || '#' === $uri ) {
			return $uri;
		}

		$parsed_uri = wp_parse_url( $uri );
		$new_uri    = '';

		if ( isset( $parsed_uri['scheme'] ) && ( isset( $parsed_uri['host'] ) || isset( $parsed_uri['path'] ) ) ) {
			switch ( $parsed_uri['scheme'] ) {
				case 'sms':
					$path    = self::sanitize_phone_number( $parsed_uri['path'] );
					$new_uri = ( '' !== $path ) ? $parsed_uri['scheme'] . ':+' . $path : '';
					break;

				case 'skype':
					$path    = self::sanitize_skype_name( $parsed_uri['path'] );
					$action  = in_array( $parsed_uri['query'], array( 'call', 'chat' ), true ) ? $parsed_uri['query'] : 'chat';
					$new_uri = ( '' !== $path ) ? $parsed_uri['scheme'] . ':' . $path . '?' . $action : '';
					break;

				default:
					$new_uri = $uri;
					break;
			}
		}
		return $new_uri;
	}



	/**
	 * Checks and cleans the contact with 'skype' or 'sms' protocols for HTML.
	 *
	 * @see https://codex.wordpress.org/Function_Reference/esc_url
	 *
	 * @since 2.0.0
	 *
	 * @param  string $uri Contact URI.
	 * @return string      Escaped URI if it has 'skype' or 'sms' protocols
	 */
	public static function escape_contact_uri_sms_skype( $uri ) {
		if ( '' === $uri || '#' === $uri ) {
			return $uri;
		}

		$parsed_uri = wp_parse_url( $uri );
		$new_uri    = '';

		if ( isset( $parsed_uri['scheme'] ) && ( isset( $parsed_uri['host'] ) || isset( $parsed_uri['path'] ) ) ) {
			switch ( $parsed_uri['scheme'] ) {
				case 'sms':
					$path    = self::sanitize_phone_number( $parsed_uri['path'] );
					$new_uri = ( '' !== $path ) ? $parsed_uri['scheme'] . ':+' . $path : '';
					break;

				case 'skype':
					$path    = self::sanitize_skype_name( $parsed_uri['path'] );
					$action  = in_array( $parsed_uri['query'], array( 'call', 'chat' ), true ) ? $parsed_uri['query'] : 'chat';
					$new_uri = ( '' !== $path ) ? $parsed_uri['scheme'] . ':' . $path . '?' . $action : '';
					break;

				default:
					$new_uri = $uri;
					break;
			}
		}
		return $new_uri;
	}



	/**
	 * Sanitizes the value part of a query string parameter.
	 *
	 * @since 2.0.0
	 *
	 * @param  string $value Parameter value.
	 * @param  string $type  Parameter type (text, textarea).
	 * @return string        Sanitized parameter value
	 */
	public static function sanitize_parameter_value( $value, $type ) {
		if ( ! $value ) {
			return '';
		}

		switch ( $type ) {
			case 'text':
				$value = sanitize_text_field( $value );
				break;

			case 'textarea':
				$value = sanitize_textarea_field( $value );
				break;

			case 'email':
				$value = self::sanitize_email_addresses( $value );
				break;

			default:
				$value = '';
				break;
		}
		return $value;
	}



	/**
	 * Sanitizes skype name.
	 *
	 * @since 2.0.0
	 *
	 * @param  string $skype_name Skype name.
	 * @return string             Filtered skype name
	 */
	public static function sanitize_skype_name( $skype_name ) {
		if ( preg_match( '/^[a-z][a-z0-9\.,\-_]{5,31}$/i', $skype_name ) ) {
			return $skype_name;
		}

		return '';
	}



	/**
	 * Sanitizes phone number.
	 *
	 * @since 1.2.0
	 *
	 * @param  string $phone Phone number.
	 * @return string        Filtered phone number with a plus sign (+) prefix
	 */
	public static function sanitize_phone_number( $phone ) {
		$phone = preg_replace( '/[^0-9\-]/', '', $phone );

		if ( $phone ) {
			return $phone;
		}

		return '';
	}



	/**
	 * Sanitizes email address list.
	 *
	 * @since 2.0.0
	 *
	 * @param  string $email_addresses Comma separated email address list.
	 * @return string                  Filtered comma separated email address list
	 */
	public static function sanitize_email_addresses( $email_addresses ) {
		$email_addresses = preg_replace( '/\s+/', '', $email_addresses );

		if ( $email_addresses ) {
			$sanitized_list = array();
			$list           = explode( ',', $email_addresses );

			foreach ( $list as $item ) {
				$value = sanitize_email( $item );
				if ( is_email( $value ) ) {
					$sanitized_list[] = $value;
				}
			}
			$email_addresses = join( ',', $sanitized_list );
			return $email_addresses;
		}

		return '';
	}
}
