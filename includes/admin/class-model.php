<?php
/**
 * Mobile Contact Bar Model Page
 *
 * @package Mobile_Contact_Bar\Admin
 */

defined( 'ABSPATH' ) || exit;


/**
 * Mobile_Contact_Bar_Page class
 */
final class Mobile_Contact_Bar_Model {


	/**
	 * Plugin's option, divided into 3 subarrays: 'settings', 'contacts', 'styles'.
	 *
	 * @var array
	 */
	public static $option = null;



	/**
	 * Renders Real-time Model and Plugin Info meta box
	 *
	 * @since 2.0.0
	 */
	public static function callback_render_model() {
		self::$option = get_option( MOBILE_CONTACT_BAR__NAME );

		$plugin_data = get_file_data(
			MOBILE_CONTACT_BAR__PATH,
			array(
				'Description' => 'Description',
				'Plugin URI'  => 'Plugin URI',
				'Author URI'  => 'Author URI',
			)
		);

		?>
		<div id="mcb-model">
			<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 620 1600" width="248" height="640">
				<rect
					fill-opacity="0"
					width="520"
					height="1600"
					x="50"
					y="0" />
				<rect
					fill="#dddddd"
					width="520"
					height="1200"
					x="50"
					y="150" />
				<g id="mcb-model-content-group">
					<rect
						fill="#fdfdfd"
						width="400"
						height="240"
						x="110"
						y="210" />
					<rect
						fill="#fdfdfd"
						width="180"
						height="180"
						x="110"
						y="490" />
					<rect
						fill="#fdfdfd"
						width="180"
						height="180"
						x="330"
						y="490" />
					<rect
						fill="#fdfdfd"
						width="400"
						height="380"
						x="110"
						y="710" />
					<rect
						fill="#fdfdfd"
						width="400"
						height="160"
						x="110"
						y="1130" />
				</g>
				<?php
				self::render_placeholder();
				if ( ! self::$option['settings']['bar']['is_fixed'] ) :
					self::render_bar();
				endif;
				?>
				<g id="mcb-model-mobile-group" transform="translate(0,360)">
					<?php
					if ( self::$option['settings']['bar']['is_fixed'] ) :
						self::render_bar();
					endif;
					?>
					<path
						id="mcb-model-mobile"
						fill="#333333"
						d="M550 0h-480c-38 0-66 30-70 70v730c0 38 30 66 70 70h480c38 0 66-30 70-70v-730c0-38-30-66-70-70zM570 750h-520v-700h520zM310 840c-19.032 0-34.34-14.676-34.34-32.708 0-18.032 15.408-32.708 34.34-32.708 18.832 0 34.238 14.772 34.238 32.708-.1 18.128-15.408 32.708-34.238 32.708z" />
				<rect
					id="mcb-model-mobile-draggable"
					fill="#000000"
					fill-opacity="0"
					width="620"
					height="870" />
				</g>
			</svg>
			<footer><em><sup>*</sup> <?php esc_html_e( 'The model is an approximation. A lot depends on your active theme"s styles.', 'mobile-contact-bar' ); ?></em></footer>
		</div>

		<div id="mcb-about">
			<h2><?php echo 'Mobile Contact Bar'; ?> <?php echo esc_attr( MOBILE_CONTACT_BAR__VERSION ); ?></h2>
			<p><?php echo esc_html( $plugin_data['Description'] ); ?></p>
			<ul>
				<li><a href="<?php echo esc_url( $plugin_data['Plugin URI'] . '#developers' ); ?>" target="_blank"><?php esc_html_e( 'Changelog', 'mobile-contact-bar' ); ?></a></li>
				<li><a href="<?php echo esc_url( 'https://wordpress.org/support/plugin/mobile-contact-bar' ); ?>" target="_blank"><?php esc_html_e( 'Forum', 'mobile-contact-bar' ); ?></a></li>
				<li><a href="<?php echo esc_url( 'https://wordpress.org/support/plugin/mobile-contact-bar' ); ?>" target="_blank"><?php esc_html_e( 'Requests', 'mobile-contact-bar' ); ?></a></li>
			</ul>
			<footer>
			<?php
			echo wp_kses_post(
				sprintf(
					/* translators: %s plugin URI */
					__( 'Thank you for networking with <a href="%s">MCB</a>.', 'mobile-contact-bar' ),
					esc_url( $plugin_data['Plugin URI'] )
				)
			);
			?>
			</footer>
		</div>
		<?php
	}



	/**
	 * Renders bar as a foreign object
	 *
	 * @since 2.1.0
	 */
	public static function render_bar() {
		$settings = self::$option['settings'];
		$contacts = self::$option['contacts'];

		$contacts = array_filter(
			$contacts, function( $contact ) {
				return $contact['checked'];
			}
		);

		$y = 50;

		if ( $settings['bar']['is_fixed'] ) :
			if ( 'top' === $settings['bar']['vertical_position'] ) :
				$y = 50 + $settings['bar']['space_height'];
			elseif ( 'bottom' === $settings['bar']['vertical_position'] ) :
				$y = 750 - $settings['bar']['height'] - $settings['bar']['space_height'];
			endif;
		else :
			if ( 'top' === $settings['bar']['vertical_position'] ) :
				$y = 150 - $settings['bar']['placeholder_height'] + $settings['bar']['space_height'];
			elseif ( 'bottom' === $settings['bar']['vertical_position'] ) :
				$y = 1350 + $settings['bar']['placeholder_height'] - $settings['bar']['height'] - $settings['bar']['space_height'];
			endif;
		endif;

		?>
		<foreignobject id="mcb-model-bar" x="50" y="<?php echo esc_attr( $y ); ?>" height="<?php echo esc_attr( $settings['bar']['height'] ); ?>" width="520">
			<div id="mobile-contact-bar">
				<?php
				if ( $settings['toggle']['is_render'] && $settings['bar']['is_fixed'] ) :
					$checked = 'closed' === $settings['toggle']['state'];
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
						foreach ( $contacts as $contact ) :
							$class = 'Mobile_Contact_Bar_Contact_' . $contact['type'];
							$badge = ( method_exists( $class, 'public_output_badge' ) ) ? '<span class="mobile-contact-bar-badge"></span>' : '';

							printf(
								'<li><a><span class="mobile-contact-bar-fa-stack fa-stack fa-%s"><i class="fa-fw %s"></i>%s</span></a></li>',
								esc_attr( $settings['icons']['size'] ),
								esc_attr( $contact['icon'] ),
								wp_kses_post( $badge )
							);
						endforeach;
						?>
					</ul>
				</div>
			</div>
		</foreignobject>
		<?php
	}



	/**
	 * Renders placeholder
	 *
	 * @since 2.1.0
	 */
	public static function render_placeholder() {
		$settings = self::$option['settings'];
		$contacts = self::$option['contacts'];

		$y = 1350;

		if ( 'top' === $settings['bar']['vertical_position'] ) {
			$y = 150 - $settings['bar']['placeholder_height'];
		} elseif ( 'bottom' === $settings['bar']['vertical_position'] ) {
			$y = 1350;
		}

		?>
		<rect
			id="mcb-model-placeholder"
			fill="<?php echo esc_attr( $settings['bar']['placeholder_color'] ); ?>"
			width="520"
			height="<?php echo esc_attr( $settings['bar']['placeholder_height'] ); ?>"
			x="50"
			y="<?php echo esc_attr( $y ); ?>" />
		<?php
	}
}
