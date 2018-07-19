<?php
/**
 * Mobile Contact Bar ScrollTop Contact
 *
 * @package Mobile_Contact_Bar\Contacts
 * @since 2.0.0
 */

defined( 'ABSPATH' ) || exit;


/**
 * Mobile_Contact_Bar_Contact_ScrollTop class
 */
final class Mobile_Contact_Bar_Contact_ScrollTop {


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
		self::$icon = 'fas fa-chevron-circle-up';

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
		$icons[25] = array(
			'type'  => self::$type,
			'icon'  => self::$icon,
			'title' => __( 'Add Scroll Top', 'mobile-contact-bar' ),
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
			'title'       => __( 'Scroll Top', 'mobile-contact-bar' ),
			'placeholder' => '',
			'uri'         => '#',
		);
	}



	/**
	 * Renders inlinie JavaScript on public side.
	 *
	 * @since 2.0.0
	 */
	public static function public_render_scripts() {
		?>
<script id="mobile-contact-bar-scrolltop">
(function() {
	function scrollTo(to = 0, duration = 1000) {
		var start       = window.pageYOffset || document.documentElement.scrollTop || document.body.scrollTop || 0,
			change      = to - start,
			increment   = 20,
			currentTime = 0;

		function animateScroll() {
			currentTime += increment;
			var val = Math.easeInOutQuad(currentTime, start, change, duration);

			window.pageYOffset = val;
			document.documentElement.scrollTop = val;
			document.body.scrollTop = val;

			if( currentTime < duration ) {
				setTimeout(animateScroll, increment);
			}
		}
		animateScroll();
	};

	Math.easeInOutQuad = function( t, b, c, d ) {
		t /= d/2;
		if (t < 1) return c/2*t*t + b;
		t--;
		return -c/2 * (t*(t-2) - 1) + b;
	};

	document.addEventListener('DOMContentLoaded', function() {
		document.scripts['mobile-contact-bar-scrolltop'].parentElement.firstChild.onclick = function( event ) {
			event.preventDefault();
			scrollTo(0, 300);
		}
	});
})();
</script>
		<?php
	}
}
