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
				if ( ! isset( $option['settings'][ $section_id ] ) ) {
					$option['settings'][ $section_id ] = array();
				}
				foreach ( $section as $setting_id => $setting ) {
					if ( ! isset( $option['settings'][ $section_id ][ $setting_id ] ) ) {
						$option['settings'][ $section_id ][ $setting_id ] = $setting;
					}
				}
			}

			// Reorganized or renamed settings in v2.1.0.
			if ( isset( $option['settings']['bar']['device'] ) ) {
				$option['settings']['general']['device'] = $option['settings']['bar']['device'];
				unset( $option['settings']['bar']['device'] );
			}

			if ( isset( $option['settings']['bar']['is_new_tab'] ) ) {
				$option['settings']['general']['is_new_tab'] = $option['settings']['bar']['is_new_tab'];
				unset( $option['settings']['bar']['is_new_tab'] );
			}

			if ( isset( $option['settings']['badges']['place'] ) ) {
				$option['settings']['badges']['corner'] = $option['settings']['badges']['place'];
				unset( $option['settings']['badges']['place'] );
			}

			// Reorder sections.
			$ordered_keys = array_keys( Mobile_Contact_Bar_Settings::string_literals() );
			$settings     = array();
			foreach ( $ordered_keys as $key ) {
				$settings[ $key ] = $option['settings'][ $key ];
			}
			$option['settings'] = $settings;

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
