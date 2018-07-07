<?php
/**
 * Mobile Contact Bar WooCommerce Contact
 *
 * @package Mobile_Contact_Bar\Contacts
 * @since 2.0.0
 */

defined( 'ABSPATH' ) || exit;


/**
 * Mobile_Contact_Bar_Contact_WooCommerce class
 */
final class Mobile_Contact_Bar_Contact_WooCommerce {


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
		if ( class_exists( 'WooCommerce' ) ) {
			self::$type = substr( __CLASS__, 27 );
			self::$icon = 'fas fa-shopping-cart';

			if ( is_admin() ) {
				add_filter( 'mobile_contact_bar_admin_get_icon', array( __CLASS__, 'mobile_contact_bar_admin_get_icon' ) );
			}

			if ( defined( 'WC_VERSION' ) && version_compare( WC_VERSION, '2.3', '>=' ) ) {
				add_filter( 'woocommerce_add_to_cart_fragments', array( __CLASS__, 'woocommerce_add_to_cart_fragments' ) );
			} else {
				add_filter( 'add_to_cart_fragments', array( __CLASS__, 'woocommerce_add_to_cart_fragments' ) );
			}
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
		$icons[20] = array(
			'type'  => self::$type,
			'icon'  => self::$icon,
			'badge' => 1,
			'title' => __( 'Add WooCommerce Cart', 'mobile-contact-bar' ),
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
			'badge'       => 1,
			'title'       => __( 'WooCommerce Cart', 'mobile-contact-bar' ),
			'placeholder' => is_ssl() ? 'https://mysite.com/cart' : 'http://mysite.com/cart',
			'uri'         => get_site_url() . '/cart',
		);
	}



	/**
	 * Outputs badge on public side.
	 *
	 * @since 2.0.0
	 *
	 * @return string HTML fragment
	 */
	public static function public_output_badge() {
		return self::get_badge();
	}



	/**
	 * Hooks to WooCommerce and updates fragment with the cart's current count.
	 *
	 * @since 2.0.0
	 *
	 * @param  array $fragments Fragments.
	 * @return array            Updated fragments
	 */
	public static function woocommerce_add_to_cart_fragments( $fragments ) {
		global $woocommerce;

		$fragments['.mobile-contact-bar-badge'] = self::get_badge();

		return $fragments;
	}



	/**
	 * Returns the cart count within its HTML.
	 *
	 * @since 2.0.0
	 *
	 * @return string Badge HTML
	 */
	private static function get_badge() {
		if ( ! class_exists( 'WooCommerce' ) ) {
			return '';
		}
		return sprintf( '<span class="mobile-contact-bar-badge">%d</span>', wp_kses_data( WC()->cart->get_cart_contents_count() ) );
	}
}
