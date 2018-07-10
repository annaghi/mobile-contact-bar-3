<?php
/**
 * Mobile Contact Bar Admin Updater
 *
 * @package Mobile_Contact_Bar\Admin
 */

defined( 'ABSPATH' ) || exit;


/**
 * Mobile_Contact_Bar_Updater class
 */
final class Mobile_Contact_Bar_Updater {


	/**
	 * Creates or updates plugin options.
	 *
	 * @since 2.0.0
	 *
	 * @param array $default_option Default option.
	 */
	public static function update_plugin_options( $default_option ) {
		$version = get_option( MOBILE_CONTACT_BAR__NAME . '_version' );
		$option  = get_option( MOBILE_CONTACT_BAR__NAME );

		if ( $option ) {

			// Add new settings.
			foreach ( $default_option['settings'] as $section_id => $section ) {
				foreach ( $section as $setting_id => $setting ) {
					if ( ! isset( $option['settings'][ $section_id ][ $setting_id ] ) ) {
						$option['settings'][ $section_id ][ $setting_id ] = $setting;
					}
				}
			}

			// Renamed setting in v2.1.0.
			$option['settings']['badges']['corner'] = $option['settings']['badges']['place'];
			unset( $option['settings']['badges']['place'] );

			// Added new field to contacts in v2.1.0.
			foreach ( $option['contacts'] as &$contact ) {
				if ( 'WooCommerce' === $contact['type'] ) {
					$contact['badge'] = 1;
				}
			}
			unset( $contact );

			// Update styles.
			if ( ! isset( $option['styles'] ) || ! $option['styles'] ) {
				$option = Mobile_Contact_Bar_Option::pre_update_option( $option );
			}

			update_option( MOBILE_CONTACT_BAR__NAME, $option );
		} else {
			add_option( MOBILE_CONTACT_BAR__NAME, $default_option );
		}

		update_option( MOBILE_CONTACT_BAR__NAME . '_version', MOBILE_CONTACT_BAR__VERSION );
	}
}
