<?php
/**
 * Mobile Contact Bar Email Contact
 *
 * @package Mobile_Contact_Bar\Contacts
 * @since 2.0.0
 */

defined( 'ABSPATH' ) || exit;


/**
 * Mobile_Contact_Bar_Contact_Email class
 */
final class Mobile_Contact_Bar_Contact_Email {


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
		self::$icon = 'far fa-envelope';

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
		$icons[7] = array(
			'type'  => self::$type,
			'icon'  => self::$icon,
			'title' => __( 'Add Email', 'mobile-contact-bar' ),
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
			'title'       => __( 'Email Address', 'mobile-contact-bar' ),
			'placeholder' => 'mailto:username@example.com',
			'uri'         => '',
			'parameters'  => array(
				array(
					'key'         => 'subject',
					'type'        => 'text',
					'placeholder' => __( 'Subject ...', 'mobile-contact-bar' ),
					'value'       => '',
				),
				array(
					'key'         => 'body',
					'type'        => 'textarea',
					'placeholder' => __( 'Text ...', 'mobile-contact-bar' ),
					'value'       => '',
				),
				array(
					'key'         => 'cc',
					'type'        => 'email',
					'placeholder' => __( 'example@domain.com', 'mobile-contact-bar' ),
					'value'       => '',
				),
				array(
					'key'         => 'bcc',
					'type'        => 'email',
					'placeholder' => __( 'example1@domain.com,example2@domain.net', 'mobile-contact-bar' ),
					'value'       => '',
				),
			),
		);
	}
}
