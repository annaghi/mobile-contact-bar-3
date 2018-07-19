<?php
/**
 * Mobile Contact Bar Custom Contact
 *
 * @package Mobile_Contact_Bar\Contacts
 * @since 2.0.0
 */

defined( 'ABSPATH' ) || exit;


/**
 * Mobile_Contact_Bar_Contact_Custom class
 */
final class Mobile_Contact_Bar_Contact_Custom {


	/**
	 * Contact type name generated from class name
	 *
	 * @var string
	 */
	private static $type = null;



	/**
	 * Hooks the plugin's admin actions and filters.
	 *
	 * @since 2.0.0
	 */
	public static function plugins_loaded() {
		self::$type = substr( __CLASS__, 27 );

		if ( is_admin() ) {
			add_filter( 'mobile_contact_bar_admin_get_button', array( __CLASS__, 'mobile_contact_bar_admin_get_button' ) );
		}
	}



	/**
	 * Adds a new button into the ContacL List header.
	 *
	 * @since 2.0.0
	 *
	 * @param  array $buttons Button list.
	 * @return array          Updated button list
	 */
	public static function mobile_contact_bar_admin_get_button( $buttons ) {
		$buttons[5] = array(
			'type'  => self::$type,
			'title' => __( '+ New Contact', 'mobile-contact-bar' ),
		);
		return $buttons;
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
			'icon'        => '',
			'title'       => '',
			'placeholder' => 'URI',
			'uri'         => '',
		);
	}
}
