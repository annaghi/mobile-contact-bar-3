<?php
/**
 * Mobile Contact Bar Text Contact
 *
 * @package Mobile_Contact_Bar\Contacts
 * @since 2.0.0
 */

defined( 'ABSPATH' ) || exit;


/**
 * Mobile_Contact_Bar_Contact_Text class
 */
final class Mobile_Contact_Bar_Contact_Text {


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
		self::$icon = 'far fa-comment';

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
		$icons[6] = array(
			'type'  => self::$type,
			'icon'  => self::$icon,
			'title' => __( 'Add Text', 'mobile-contact-bar' ),
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
			'title'       => __( 'Phone Number for texting', 'mobile-contact-bar' ),
			'placeholder' => 'sms:+15417543010',
			'uri'         => '',
			'parameters'  => array(
				array(
					'key'         => 'body',
					'type'        => 'text',
					'placeholder' => __( 'Message ...', 'mobile-contact-bar' ),
					'value'       => '',
				),
			),
		);
	}
}
