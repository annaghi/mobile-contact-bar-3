<?php
/**
 * Mobile Contact Bar WhatsApp Contact
 *
 * @package Mobile_Contact_Bar\Contacts
 * @since 2.0.0
 */

defined( 'ABSPATH' ) || exit;


/**
 * Mobile_Contact_Bar_Contact_WhatsApp class
 */
final class Mobile_Contact_Bar_Contact_WhatsApp {


	/**
	 * Contact type name generated from class name
	 *
	 * @var string
	 */
	private static $type = null;



	/**
	 * Font Awesome icon name
	 *
	 * @var string
	 */
	private static $icon = null;



	/**
	 * Hooks the plugin's admin actions and filters.
	 *
	 * @since 2.0.0
	 */
	public static function plugins_loaded() {
		self::$type = substr( __CLASS__, 27 );
		self::$icon = 'fab fa-whatsapp';

		if ( is_admin() ) {
			add_filter( 'mobile_contact_bar_admin_get_icon', array( __CLASS__, 'mobile_contact_bar_admin_get_icon' ) );
		}
	}



	/**
	 * Adds a new icon into the ContacL List header.
	 *
	 * @since 2.0.0
	 *
	 * @param  array $icons Icon list.
	 * @return array        Updated icon list
	 */
	public static function mobile_contact_bar_admin_get_icon( $icons ) {
		$icons[8] = array(
			'type'  => self::$type,
			'icon'  => self::$icon,
			'title' => __( 'Add WhatsApp', 'mobile-contact-bar' ),
		);
		return $icons;
	}



	/**
	 * Returns a new contact entry.
	 *
	 * @since 2.0.0
	 *
	 * @return array Contact
	 */
	public static function admin_get_contact() {
		return array(
			'checked'     => 0,
			'type'        => self::$type,
			'icon'        => self::$icon,
			'title'       => 'WhatsApp',
			'placeholder' => '', // Contact with empty placeholder has non-editable URI.
			'uri'         => 'https://api.whatsapp.com/send',
			'parameters'  => array(
				array(
					'key'         => 'phone',
					'type'        => 'text',
					'placeholder' => __( '15417543010', 'mobile-contact-bar' ),
					'value'       => '',
				),
				array(
					'key'         => 'text',
					'type'        => 'text',
					'placeholder' => __( 'Message ...', 'mobile-contact-bar' ),
					'value'       => '',
				),
			),
		);
	}
}
