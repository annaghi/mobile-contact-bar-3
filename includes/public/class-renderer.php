<?php
/**
 * Mobile Contact Bar Public Renderer
 *
 * @package Mobile_Contact_Bar\Public
 */

defined( 'ABSPATH' ) || exit;


/**
 * Mobile_Contact_Bar_Renderer class
 */
final class Mobile_Contact_Bar_Renderer {


	/**
	 * Plugin's option, divided into 3 subarrays: 'settings', 'contacts', 'styles'.
	 *
	 * @var array
	 */
	public static $option = null;



	/**
	 * Hooks WP public actions.
	 *
	 * @since 0.0.1
	 */
	public static function plugins_loaded() {
		self::$option = get_option( MOBILE_CONTACT_BAR__NAME );

		if ( self::$option && isset( self::$option['contacts'] ) && isset( self::$option['styles'] ) ) {
			self::$option['contacts'] = array_filter(
				self::$option['contacts'], function( $contact ) {
					return $contact['checked'];
				}
			);

			$device    = self::$option['settings']['general']['device'];
			$detection = self::$option['settings']['general']['device_detection'];

			if ( self::$option['contacts'] && 'none' !== $device ) {

				$is_mobile  = 'php' === $detection && 'mobile' === $device && wp_is_mobile();
				$is_desktop = 'php' === $detection && 'desktop' === $device && ! wp_is_mobile();

				if ( $is_mobile || $is_desktop || 'both' === $device || 'css' === $detection ) {
					add_action( 'wp_enqueue_scripts', array( __CLASS__, 'wp_enqueue_scripts' ) );
					add_action( 'wp_footer', array( __CLASS__, 'wp_footer' ) );
				}
			}
		}
	}



	/**
	 * Loads Font Awesome and custom styles, also the scripts.
	 *
	 * @since 0.0.1
	 */
	public static function wp_enqueue_scripts() {
		wp_enqueue_style(
			'mobile-contact-bar',
			plugins_url( 'assets/css/public.min.css', MOBILE_CONTACT_BAR__PATH ),
			array(),
			'5.1.0',
			'all'
		);

		$styles = wp_kses( self::$option['styles'], array( "\'", '\"' ) );
		wp_add_inline_style( 'mobile-contact-bar', $styles );

		if ( self::$option['settings']['toggle']['is_render'] && self::$option['settings']['toggle']['is_cookie'] ) {
			wp_enqueue_script(
				'mobile-contact-bar',
				plugins_url( 'assets/js/public.min.js', MOBILE_CONTACT_BAR__PATH ),
				array(),
				MOBILE_CONTACT_BAR__VERSION,
				true
			);
		}
	}



	/**
	 * Invokes mobile_contact_bar_public_render_html action only once.
	 *
	 * @since 0.0.1
	 */
	public static function wp_footer() {
		if ( ! has_action( 'mobile_contact_bar_public_render_html' ) ) {
			add_action( 'mobile_contact_bar_public_render_html', array( __CLASS__, 'render_html' ), 10, 2 );
		}

		do_action( 'mobile_contact_bar_public_render_html', self::$option['contacts'], self::$option['settings'] );
	}



	/**
	 * Outputs contact bar.
	 *
	 * @since 0.0.1
	 *
	 * @param array $contacts Associative array of displayable contacts.
	 * @param array $settings Associative array of settings.
	 */
	public static function render_html( $contacts, $settings ) {
		if ( 1 === did_action( 'mobile_contact_bar_public_render_html' ) ) : ?>

			<div id="mobile-contact-bar">

			<?php
			if ( $settings['toggle']['is_render'] && $settings['bar']['is_fixed'] ) :
				$checked = 'closed' === self::$option['settings']['toggle']['state'];
				if ( self::$option['settings']['toggle']['is_cookie'] && isset( $_COOKIE['mobile_contact_bar_toggle'] ) ) {
					$checked = 'closed' === $_COOKIE['mobile_contact_bar_toggle'];
				}
				?>
				<input id="mobile-contact-bar-toggle-checkbox" name="mobile-contact-bar-toggle-checkbox" type="checkbox" <?php checked( true, $checked, true ); ?>>

				<label for="mobile-contact-bar-toggle-checkbox" id="mobile-contact-bar-toggle">
				<?php if ( $settings['toggle']['label'] ) : ?>
					<span><?php echo esc_attr( $settings['toggle']['label'] ); ?></span>
				<?php endif; ?>

					<svg viewBox="0 0 550 170" width="110" height="34">

					<?php if ( 'bottom' === $settings['bar']['vertical_position'] && 'rounded' === $settings['toggle']['shape'] ) : ?>
						<path d="M 550 170 L 496.9 32.8 C 490.4 13.2 474.1 0 451.4 0 H 98.6 C 77.9 0 59.6 13.2 53.1 32.8 L 0 170 z">
					<?php elseif ( 'bottom' === $settings['bar']['vertical_position'] && 'sharp' === $settings['toggle']['shape'] ) : ?>
						<path d="M 550 170 L 494.206 0 H 65.794 L 0 170 z">
					<?php elseif ( 'top' === $settings['bar']['vertical_position'] && 'rounded' === $settings['toggle']['shape'] ) : ?>
						<path d="M 550 0 L 496.9 137.2 C 490.4 156.8 474.1 170 451.4 170 H 98.6 C 77.9 170 59.6 156.8 53.1 137.2 L 0 0 z">
					<?php elseif ( 'top' === $settings['bar']['vertical_position'] && 'sharp' === $settings['toggle']['shape'] ) : ?>
						<path d="M 550 0 L 494.206 170 H 65.794 L 0 0 z">
					<?php endif; ?>

					</svg>

				</label>
			<?php endif; ?>

			<div id="mobile-contact-bar-outer">
				<ul>
				<?php

				$new_tab = ( $settings['bar']['is_new_tab'] ) ? ' target="_blank"' : '';

				foreach ( $contacts as $contact ) :

					$uri     = Mobile_Contact_Bar_Validator::escape_contact_uri_sms_skype( $contact['uri'] );
					$new_tab = ( substr( $uri, 0, 4 ) === 'http' ) ? $new_tab : '';
					$class   = 'Mobile_Contact_Bar_Contact_' . $contact['type'];
					$counter = ( method_exists( $class, 'public_output_badge' ) ) ? $class::public_output_badge() : '';
					$js      = '';
					if ( method_exists( $class, 'public_render_scripts' ) ) {
						ob_start();
						$class::public_render_scripts();
						$js = ob_get_contents();
						ob_end_clean();
					}

					if ( isset( $contact['parameters'] ) ) {
						$query_arg = array();

						foreach ( $contact['parameters'] as $parameter ) {
							if ( $parameter['value'] ) {
								$key               = sanitize_key( $parameter['key'] );
								$query_arg[ $key ] = rawurlencode( $parameter['value'] );
							}
						}
						$uri = add_query_arg( $query_arg, $uri );
					}

					printf(
						'<li><a data-rel="external" href="%s"%s><span class="fa-stack fa-%s"><i class="fa-fw %s"></i>%s<span class="screen-reader-text">%s</span></span></a>%s</li>',
						esc_url( $uri, Mobile_Contact_Bar_Validator::$protocols ),
						wp_kses_post( $new_tab ),
						esc_attr( $settings['icons']['size'] ),
						esc_attr( $contact['icon'] ),
						wp_kses_post( $counter ),
						esc_html( $contact['title'] ),
						$js // phpcs:ignore WordPress.XSS.EscapeOutput.OutputNotEscaped
					);
				endforeach;
				?>
				</ul>
			</div>
			</div>
			<?php
		endif;
	}
}
