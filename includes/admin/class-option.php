<?php
/**
 * Mobile Contact Bar Option Page Meta Boxes
 *
 * @package Mobile_Contact_Bar\Admin
 */

defined( 'ABSPATH' ) || exit;


/**
 * Mobile_Contact_Bar_Option class
 */
final class Mobile_Contact_Bar_Option {



	/**
	 * Plugin's option, divided into 3 subarrays: 'settings', 'contacts', 'styles'.
	 *
	 * @var array
	 */
	public static $option = null;



	/**
	 * Multidimensional array of settings, divided into sections.
	 *
	 * @var array
	 */
	public static $settings = null;



	/**
	 * Contact types.
	 *
	 * @var array
	 */
	public static $contact_types = null;



	/**
	 * Hooks WordPress's admin and AJAX actions and filters.
	 *
	 * @since 0.0.1
	 */
	public static function plugins_loaded() {
		add_action( 'init', array( __CLASS__, 'init' ) );
		add_action( 'admin_init', array( __CLASS__, 'admin_init' ) );
		add_action( 'admin_footer', array( __CLASS__, 'admin_footer' ) );

		add_filter( 'pre_update_option_' . MOBILE_CONTACT_BAR__NAME, array( __CLASS__, 'pre_update_option' ), 10, 2 );

		add_action( 'wp_ajax_ajax_add_contact', array( __CLASS__, 'ajax_add_contact' ) );
	}



	/**
	 * Initializes option and settings.
	 *
	 * @since 0.0.1
	 */
	public static function init() {
		self::$option   = get_option( MOBILE_CONTACT_BAR__NAME );
		self::$settings = Mobile_Contact_Bar_Settings::settings();
		foreach ( glob( plugin_dir_path( MOBILE_CONTACT_BAR__PATH ) . 'includes/shared/contacts/class-*.php' ) as $path ) {
			self::$contact_types[] = substr( basename( $path, '.php' ), 6 );
		}
	}



	/**
	 * Adds sections and fields to the option page.
	 *
	 * @since 0.0.1
	 */
	public static function admin_init() {
		register_setting(
			MOBILE_CONTACT_BAR__NAME . '_group',
			MOBILE_CONTACT_BAR__NAME,
			array( __CLASS__, 'callback_sanitize_option' )
		);

		$titles = Mobile_Contact_Bar_Settings::string_literals();

		/* Settings Sections */
		foreach ( self::$settings as $section_id => $section ) {
			if ( 'badges' === $section_id && ! class_exists( 'WooCommerce' ) ) {
				continue;
			}

			add_settings_section(
				'mcb-section-' . $section_id,
				$titles[ $section_id ],
				false,
				MOBILE_CONTACT_BAR__NAME
			);

			foreach ( $section as $setting_id => $setting ) {
				$args = array(
					'section_id' => $section_id,
					'setting_id' => $setting_id,
					'setting'    => $setting,
				);

				$args['class'] = 'mcb-setting-' . $section_id . '-' . $setting_id;
				if ( isset( $setting['visible'] ) ) {
					$args['class'] .= ' hidden mcb-child';
					foreach ( $setting['visible'] as $parent => $trigger ) {
						$args['class'] .= ' mcb-parent-' . $parent . '--' . $trigger;
					}
				}

				add_settings_field(
					$setting_id,
					$setting['title'],
					array( __CLASS__, 'callback_output_setting' ),
					MOBILE_CONTACT_BAR__NAME,
					'mcb-section-' . $section_id,
					$args
				);
			}
		}

		/* Contact List Section */
		add_settings_section(
			'mcb-section-contacts',
			__( 'Contact List', 'mobile-contact-bar' ),
			false,
			MOBILE_CONTACT_BAR__NAME
		);

		foreach ( self::$option['contacts'] as $contact_id => $contact ) {
			$checked = ( $contact['checked'] ) ? ' mcb-active' : '';
			$odd     = ( ( $contact_id % 2 ) === 1 ) ? ' mcb-odd' : '';

			$contact_args = array(
				'class'      => 'mcb-contact' . $checked . $odd,
				'contact_id' => $contact_id,
				'contact'    => $contact,
			);

			add_settings_field(
				$contact_id,
				self::output_contact_th( $contact_id, $contact ),
				array( __CLASS__, 'callback_render_contact_td' ),
				MOBILE_CONTACT_BAR__NAME,
				'mcb-section-contacts',
				$contact_args
			);

			if ( isset( $contact['parameters'] ) ) {
				foreach ( $contact['parameters'] as $parameter_id => $parameter ) {
					$parameter_args = array(
						'class'        => 'mcb-parameter hidden',
						'parameter_id' => $parameter_id,
						'parameter'    => $parameter,
						'contact_id'   => $contact_id,
						'contact_type' => $contact['type'],
					);

					add_settings_field(
						$contact_id . '-' . $parameter_id,
						self::output_parameter_th( $parameter_id, $parameter['key'], $contact_id, $contact['type'] ),
						array( __CLASS__, 'callback_render_parameter_td' ),
						MOBILE_CONTACT_BAR__NAME,
						'mcb-section-contacts',
						$parameter_args
					);
				}
			}
		}
	}



	/**
	 * Renders a meta box.
	 *
	 * @since 1.2.0
	 *
	 * @param object $object  null.
	 * @param array  $section Passed from add_meta_box as sixth parameter.
	 */
	public static function callback_render_section( $object, $section ) {
		$table_id = str_replace( '-section-', '-table-', $section['id'] );

		$out_buttons = '';
		$out_icons   = '';
		if ( 'mcb-table-contacts' === $table_id ) {
			$buttons = apply_filters( 'mobile_contact_bar_admin_get_button', array() );
			ksort( $buttons );

			$icons = apply_filters( 'mobile_contact_bar_admin_get_icon', array() );
			ksort( $icons );
		}

		?>
		<table id="<?php echo esc_attr( $table_id ); ?>" class="form-table">
			<?php if ( 'mcb-table-contacts' === $table_id ) { ?>
			<thead>
				<tr>
					<td>
						<ul id="mcb-integration-buttons">
							<?php foreach ( $buttons as $button ) { ?>
							<li class="mcb-action wp-ui-text-highlight" data-contact-type="<?php echo esc_attr( $button['type'] ); ?>">
								<span class="mcb-integration"><?php echo esc_html( $button['title'] ); ?></span>
							</li>
							<?php } ?>
						</ul>
					</td>
					<td>
						<ul id="mcb-integration-icons">
							<?php foreach ( $icons as $icon ) { ?>
							<li class="mcb-action wp-ui-text-highlight" data-contact-type="<?php echo esc_attr( $icon['type'] ); ?>">
								<span class="fa-stack">
									<i class="mcb-integration <?php echo esc_attr( $icon['icon'] ); ?>" title="<?php echo esc_attr( $icon['title'] ); ?>" aria-hidden="true"></i>
									<span class="screen-reader-text"><?php echo esc_attr( $icon['title'] ); ?></span>
									<?php if ( isset( $icon['badge'] ) && $icon['badge'] ) { ?>
										<span class="mcb-badge mcb-badge-<?php echo esc_attr( self::$option['settings']['badges']['corner'] ); ?> wp-ui-notification"></span>
									<?php } ?>
								</span>
							</li>
							<?php } ?>
						</ul>
					</td>
				</tr>
			</thead>
			<tfoot>
				<tr>
					<td colspan="2" class="wp-ui-text-highlight">
						<i class="fas fa-share fa-fw fa-rotate-270" aria-hidden="true"></i>
						<span><?php esc_html_e( 'Drag and drop to reorder', 'mobile-contact-bar' ); ?></span>
					</td>
				</tr>
			</tfoot>
			<?php } ?>
			<tbody>
				<?php do_settings_fields( MOBILE_CONTACT_BAR__NAME, $section['id'] ); ?>
			</tbody>
		</table>
		<?php
	}



	/**
	 * Outputs a setting field.
	 *
	 * @since 0.0.1
	 *
	 * @param array $args section_id, setting_id, setting.
	 */
	public static function callback_output_setting( $args ) {
		$id   = 'mcb-' . $args['section_id'] . '-' . $args['setting_id'];
		$name = MOBILE_CONTACT_BAR__NAME . '[settings][' . $args['section_id'] . '][' . $args['setting_id'] . ']';

		switch ( $args['setting']['type'] ) {
			case 'color-picker':
				printf(
					'<input type="text" id="%s" name="%s" class="cs-wp-color-picker" value="%s">',
					esc_attr( $id ),
					esc_attr( $name ),
					esc_attr( self::$option['settings'][ $args['section_id'] ][ $args['setting_id'] ] )
				);
				break;

			case 'select':
				printf(
					'<select id="%s" name="%s" class="mcb-regular-text">',
					esc_attr( $id ),
					esc_attr( $name )
				);
				foreach ( $args['setting']['options'] as $value => $label ) {
					printf(
						'<option value="%s" %s>%s</option>',
						esc_attr( $value ),
						selected( $value, self::$option['settings'][ $args['section_id'] ][ $args['setting_id'] ], false ),
						esc_html( $label )
					);
				}
				echo '</select>';
				break;

			case 'radio':
				printf(
					'<fieldset class="mcb-radio-label-wrap" id="%s">',
					esc_attr( $id )
				);
				$class = ( 'vertical' === $args['setting']['align'] ) ? 'mcb-radio-v' : 'mcb-radio-h';
				foreach ( $args['setting']['options'] as $value => $label ) {
					printf(
						'<label class="mcb-radio-label %1$s" for="%2$s--%4$s">
                            <input type="radio" id="%2$s--%4$s" name="%3$s" value="%4$s" %5$s>%6$s
                        </label>',
						esc_attr( $class ),
						esc_attr( $id ),
						esc_attr( $name ),
						esc_attr( $value ),
						checked( $value, self::$option['settings'][ $args['section_id'] ][ $args['setting_id'] ], false ),
						esc_html( $label )
					);
				}
				echo '</fieldset>';
				break;

			case 'checkbox':
				printf(
					'<label for="%1$s">
                        <input type="checkbox" id="%1$s" name="%2$s" %3$s value="1">%4$s
                    </label>',
					esc_attr( $id ),
					esc_attr( $name ),
					checked( self::$option['settings'][ $args['section_id'] ][ $args['setting_id'] ], 1, false ),
					esc_html( $args['setting']['label'] )
				);
				break;

			case 'text':
				printf(
					'<input type="text" id="%s" name="%s" class="mcb-regular-text" value="%s">',
					esc_attr( $id ),
					esc_attr( $name ),
					esc_attr( self::$option['settings'][ $args['section_id'] ][ $args['setting_id'] ] )
				);
				break;

			case 'number':
				printf(
					'<input type="number" id="%s" name="%s" class="mcb-regular-text" value="%d">
                    <span>%s</span>',
					esc_attr( $id ),
					esc_attr( $name ),
					esc_attr( self::$option['settings'][ $args['section_id'] ][ $args['setting_id'] ] ),
					esc_html( $args['setting']['postfix'] )
				);
				break;

			case 'slider':
				printf(
					'<input type="range" id="%1$s" name="%2$s" class="mcb-slider-input" value="%3$s" min="%4$s" max="%5$s" step="%6$s">
                    <span class="mcb-slider-value">%3$s</span>',
					esc_attr( $id ),
					esc_attr( $name ),
					esc_attr( self::$option['settings'][ $args['section_id'] ][ $args['setting_id'] ] ),
					esc_attr( $args['setting']['min'] ),
					esc_attr( $args['setting']['max'] ),
					esc_attr( $args['setting']['step'] )
				);
				break;
		}

		if ( isset( $args['setting']['desc'] ) ) {
			printf( '<p class="description">%s</p>', esc_html( $args['setting']['desc'] ) );
		}
	}



	/**
	 * Renders the header part of the contact.
	 *
	 * @since 2.0.0

	 * @param  string $contact_id Contact id.
	 * @param  array  $contact    Contact.
	 * @return string             HTML th
	 */
	private static function output_contact_th( $contact_id, $contact ) {
		$prefix = MOBILE_CONTACT_BAR__NAME . '[contacts][' . $contact_id . ']';
		$out    = '';

		// Hidden 'type'.
		$out .= sprintf(
			'<input name="%s[type]" value="%s" type="hidden">',
			esc_attr( $prefix ),
			esc_attr( $contact['type'] )
		);

		// Hidden 'icon'.
		$out .= sprintf(
			'<input name="%s[icon]" value="%s" type="hidden">',
			esc_attr( $prefix ),
			esc_attr( $contact['icon'] )
		);

		// Hidden 'badge'.
		if ( isset( $contact['badge'] ) && $contact['badge'] ) {
			$out .= sprintf(
				'<input name="%s[badge]" value="%s" type="hidden">',
				esc_attr( $prefix ),
				esc_attr( $contact['badge'] )
			);
		}

		// Hidden 'title'.
		if ( 'Sample' === $contact['type'] ) {
			$out .= sprintf(
				'<input name="%s[title]" value="%s" type="hidden">',
				esc_attr( $prefix ),
				esc_html( $contact['title'] )
			);
		}

		$out .= '<ul class="mcb-th">';

		// Input 'checkbox'.
		$out .= sprintf(
			'<li class="mcb-contact-checkbox"><input name="%s[checked]" value="1" %s type="checkbox"></li>',
			esc_attr( $prefix ),
			( $contact['checked'] ) ? checked( $contact['checked'], 1, false ) : ''
		);

		// Displayed 'icon' and 'badge'.
		$out .= sprintf(
			'<li class="mcb-contact-icon ui-sortable-handle"><span class="fa-stack"><i class="%s fa-lg"></i>%s</span></li>',
			esc_attr( $contact['icon'] ),
			( isset( $contact['badge'] ) && $contact['badge'] ) ? sprintf( '<span class="mcb-badge mcb-badge-%s wp-ui-notification"></span>', esc_attr( self::$option['settings']['badges']['corner'] ) ) : ''
		);

		// Displayed 'title'.
		if ( 'Sample' === $contact['type'] ) {
			$out .= sprintf( '<li class="mcb-contact-title">%s</li>', esc_html( $contact['title'] ) );
		} else {
			// Input 'title'.
			$out .= sprintf(
				'<li class="mcb-contact-title"><input name="%s[title]" value="%s" placeholder="%s" type="text"></li>',
				esc_attr( $prefix ),
				esc_html( $contact['title'] ),
				esc_attr__( 'Short name', 'mobile-contact-bar' )
			);
		}

		$out .= '</ul>';

		return $out;
	}



	/**
	 * Renders the data part of the contact.
	 *
	 * @since 2.0.0
	 *
	 * @param  array $args contact_id, contact.
	 */
	public static function callback_render_contact_td( $args ) {
		$prefix = MOBILE_CONTACT_BAR__NAME . '[contacts][' . $args['contact_id'] . ']';
		$uri    = Mobile_Contact_Bar_Validator::escape_contact_uri_sms_skype( $args['contact']['uri'] );

		// Hidden 'placeholder'.
		printf(
			'<input name="%s[placeholder]" value="%s" type="hidden">',
			esc_attr( $prefix ),
			esc_attr( $args['contact']['placeholder'] )
		);

		echo '<ul class="mcb-td">';

		// 'URI' contact with empty placeholder has non-editable URI.
		if ( '' === $args['contact']['placeholder'] ) {
			// Hidden 'URI'.
			printf(
				'<input name="%1$s[uri]" value="%2$s" type="hidden">
				<li class="mcb-contact-uri">%2$s</li>',
				esc_attr( $prefix ),
				esc_url( $uri, Mobile_Contact_Bar_Validator::$protocols )
			);
		} else {
			// Input 'URI' and displayed 'URI'.
			printf(
				'<li class="mcb-contact-uri"><input name="%s[uri]" value="%s" placeholder="%s" type="text"></li>',
				esc_attr( $prefix ),
				esc_url( $uri, Mobile_Contact_Bar_Validator::$protocols ),
				esc_attr( $args['contact']['placeholder'] )
			);
		}

		echo '<li>';
		self::render_row_actions( 'contact', $args['contact']['type'] );
		echo '</li>';

		echo '</ul>';
	}



	/**
	 * Outputs the header part of the parameter.
	 *
	 * @since 2.0.0
	 *
	 * @param  string $parameter_id  Parameter id.
	 * @param  string $parameter_key Parameter key.
	 * @param  string $contact_id    Contact id.
	 * @param  string $contact_type  Contact type.
	 * @return string                HTML TH
	 */
	private static function output_parameter_th( $parameter_id = '', $parameter_key = '', $contact_id = '', $contact_type = 'Custom' ) {
		$prefix = MOBILE_CONTACT_BAR__NAME . '[contacts][' . $contact_id . '][parameters][' . $parameter_id . ']';
		$out    = '';

		if ( 'Custom' === $contact_type ) {
			// Input 'key'.
			$out .= sprintf(
				'<input name="%s[key]" class="mcb-parameter-key" value="%s" placeholder="%s" type="text">',
				esc_attr( $prefix ),
				esc_attr( $parameter_key ),
				esc_attr__( 'key', 'mobile-contact-bar' )
			);
		} else {
			// Hidden 'key' and displayed 'key'.
			$out .= sprintf(
				'<input name="%1$s[key]" value="%2$s" type="hidden">%2$s',
				esc_attr( $prefix ),
				esc_attr( $parameter_key )
			);
		}
		return $out;
	}



	/**
	 * Renders the data part of the parameter.
	 *
	 * @since 2.0.0
	 *
	 * @param  array $args parameter_id, parameter, contact_id, contact_type.
	 */
	public static function callback_render_parameter_td( $args ) {
		$prefix = MOBILE_CONTACT_BAR__NAME . '[contacts][' . $args['contact_id'] . '][parameters][' . $args['parameter_id'] . ']';

		// Hidden 'type'.
		printf(
			'<input name="%s[type]" value="%s" type="hidden">',
			esc_attr( $prefix ),
			esc_attr( $args['parameter']['type'] )
		);

		// Hidden 'placeholder'.
		printf(
			'<input name="%s[placeholder]" value="%s" type="hidden">',
			esc_attr( $prefix ),
			esc_attr( $args['parameter']['placeholder'] )
		);

		echo '<ul class="mcb-ul">';
		echo '<li class="mcb-parameter-value">';

		// Input 'value'.
		switch ( $args['parameter']['type'] ) {
			case 'text':
			case 'email':
				printf(
					'<input type="text" name="%s[value]" value="%s" placeholder="%s">',
					esc_attr( $prefix ),
					esc_attr( $args['parameter']['value'] ),
					esc_attr( $args['parameter']['placeholder'] )
				);
				break;

			case 'textarea':
				printf(
					'<textarea name="%s[value]" placeholder="%s">%s</textarea>',
					esc_attr( $prefix ),
					esc_attr( $args['parameter']['placeholder'] ),
					esc_textarea( $args['parameter']['value'] )
				);
				break;
		}

		echo '</li>';

		echo '<li>';
		self::render_row_actions( 'parameter', $args['contact_type'] );
		echo '</li>';

		echo '</ul>';
	}



	/**
	 * Sanitizes the option (settings and contacts).
	 *
	 * @since 0.0.1
	 *
	 * @param  array $input Multidimensional array of the option.
	 * @return array        Sanitized option
	 */
	public static function callback_sanitize_option( $input ) {

		/* SETTINGS */

		$settings           = $input['settings'];
		$sanitized_settings = array();

		foreach ( $settings as $section_id => &$section ) {
			// Workaround empty checkboxes.
			$section = array_replace(
				array_map(
					function( $setting ) {
						if ( 'checkbox' === $setting['type'] ) {
							return 0; }}, self::$settings[ $section_id ]
				),
				$section
			);
			// All settings will be saved, at least with their default values.
			$section = array_replace(
				array_map(
					function( $setting ) {
							return $setting['default']; }, self::$settings[ $section_id ]
				),
				$section
			);
		}
		unset( $section );

		foreach ( self::$settings as $section_id => $section ) {
			foreach ( $section as $setting_id => $setting ) {
				$value = $settings[ $section_id ][ $setting_id ];

				switch ( $setting['type'] ) {
					case 'select':
					case 'radio':
					case 'radio-image':
						$value = ( in_array( $value, array_keys( $setting['options'] ), true ) ) ? $value : $setting['default'];
						break;

					case 'color-picker':
						$value = self::sanitize_color( $value );

						if ( ! self::is_color( $value ) ) {
							$value = $setting['default'];
						}
						break;

					case 'checkbox':
						$value = (int) $value;
						$value = ( 0 === $value || 1 === $value ) ? $value : $setting['default'];
						break;

					case 'number':
						$value = (int) $value;
						if ( ( isset( $setting['min'] ) && $value < $setting['min'] ) || ( isset( $setting['max'] ) && $value > $setting['max'] ) ) {
							$value = $setting['default'];
						}
						break;

					case 'text':
						$value = sanitize_text_field( $value );
						break;

					case 'slider':
						$value = self::sanitize_unit_interval( $value, $setting['min'], $setting['max'] );
						break;
				}
				$sanitized_settings[ $section_id ][ $setting_id ] = $value;
			}
		}

		/* CONTACTS */

		$contacts           = $input['contacts'];
		$sanitized_contacts = array();

		foreach ( $contacts as $contact_id => &$contact ) {
			// Remove contact if invalid 'icon', but leave empty icons.
			if ( $contact['icon'] && ! Mobile_Contact_Bar_Page::in_icons( $contact['icon'] ) ) {
				unset( $contacts[ $contact_id ] );
			}

			// Remove contact if invalid 'type'.
			if ( ! in_array( strtolower( $contact['type'] ), self::$contact_types, true ) ) {
				unset( $contacts[ $contact_id ] );
			}

			// Remove empty 'parameters'.
			if ( isset( $contact['parameters'] ) && ! $contact['parameters'] ) {
				unset( $contacts[ $contact_id ]['parameters'] );
			}
			// Reindex 'parameters'.
			if ( isset( $contact['parameters'] ) && $contact['parameters'] ) {
				$contacts[ $contact_id ]['parameters'] = array_values( $contacts[ $contact_id ]['parameters'] );
			}
		}
		unset( $contact );

		// Merge and sanitize contacts.
		foreach ( $contacts as $contact_id => $contact ) {
			$sanitized_contact = array();

			// Sanitize 'checked'.
			$sanitized_contact['checked'] = (int) $contact['checked'];

			// 'type' is already sanitized.
			$sanitized_contact['type'] = $contact['type'];

			// 'icon' is already sanitized.
			$sanitized_contact['icon'] = $contact['icon'];

			// Sanitize 'badge'.
			$sanitized_contact['badge'] = (int) $contact['badge'];

			// Sanitize 'title'.
			$sanitized_contact['title'] = sanitize_text_field( $contact['title'] );

			// Copy 'placeholder'.
			$sanitized_contact['placeholder'] = $contact['placeholder'];

			// Sanitize 'uri'.
			$sanitized_contact['uri'] = esc_url_raw( Mobile_Contact_Bar_Validator::sanitize_contact_uri_skype_sms( $contact['uri'] ), Mobile_Contact_Bar_Validator::$protocols );

			// sanitize 'parameters'.
			if ( isset( $contact['parameters'] ) ) {
				foreach ( $contact['parameters'] as $parameter_id => $parameter ) {
					// Sanitize 'key'.
					$sanitized_contact['parameters'][ $parameter_id ]['key'] = sanitize_key( $parameter['key'] );

					// Sanitize 'type'.
					$sanitized_contact['parameters'][ $parameter_id ]['type'] = sanitize_key( $parameter['type'] );

					// Sanitize 'placeholder'.
					$sanitized_contact['parameters'][ $parameter_id ]['placeholder'] = sanitize_text_field( $parameter['placeholder'] );

					// Santitize 'value'.
					$sanitized_contact['parameters'][ $parameter_id ]['value'] = Mobile_Contact_Bar_Validator::sanitize_parameter_value( $parameter['value'], $sanitized_contact['parameters'][ $parameter_id ]['type'] );
				}
			}
			$sanitized_contacts[ $contact_id ] = $sanitized_contact;
		}

		// Reindex.
		$sanitized_contacts = array_values( $sanitized_contacts );

		self::$option['settings'] = $sanitized_settings;
		self::$option['contacts'] = $sanitized_contacts;

		return array(
			'settings' => $sanitized_settings,
			'contacts' => $sanitized_contacts,
		);
	}



	/**
	 * Generates the public styles, and stores them in the option.
	 *
	 * @since 0.0.1
	 *
	 * @param  array $new_value The new value.
	 * @return array            The updated option
	 */
	public static function pre_update_option( $new_value ) {
		$general = $new_value['settings']['general'];
		$bar     = $new_value['settings']['bar'];
		$icons   = $new_value['settings']['icons'];
		$toggle  = $new_value['settings']['toggle'];
		$badges  = ( isset( $new_value['settings']['badges'] ) ) ? $new_value['settings']['badges'] : null;

		$contacts       = array_filter(
			$new_value['contacts'], function( $contact ) {
				return $contact['checked'];
			}
		);
		$contacts_count = count( $contacts );

		$styles = '';

		$styles .= '#mobile-contact-bar{';
		$styles .= '-webkit-box-sizing:border-box;';
		$styles .= 'box-sizing:border-box;';
		$styles .= 'display:block;';
		$styles .= 'font-size:100%;';
		$styles .= 'font-size:1rem;';
		$styles .= 'opacity:' . $bar['opacity'] . ';';
		$styles .= 'position:relative;';
		$styles .= 'white-space:nowrap;';
		$styles .= ( $bar['width'] > 100 ) ? 'width:100%;' : 'width:' . $bar['width'] . '%;';
		$styles .= 'z-index:9998;';
		$styles .= '}';

		$styles .= '#mobile-contact-bar:before,';
		$styles .= '#mobile-contact-bar:after{';
		$styles .= 'content:"";';
		$styles .= 'display:table;';
		$styles .= '}';

		$styles .= '#mobile-contact-bar:after{';
		$styles .= 'clear:both;';
		$styles .= '}';

		$styles .= '#mobile-contact-bar-outer{';
		$styles .= 'background-color:' . $bar['color'] . ';';
		$styles .= '-webkit-box-sizing:border-box;';
		$styles .= 'box-sizing:border-box;';
		switch ( $bar['is_border'] ) {
			case 'one':
				switch ( $bar['vertical_position'] ) {
					case 'top':
						$styles .= 'border-bottom:' . $bar['border_width'] . 'px solid ' . $bar['border_color'] . ';';
						break;

					case 'bottom':
						$styles .= 'border-top:' . $bar['border_width'] . 'px solid ' . $bar['border_color'] . ';';
						break;
				}
				break;

			case 'two':
				$styles .= 'border-top:' . $bar['border_width'] . 'px solid ' . $bar['border_color'] . ';';
				$styles .= 'border-bottom:' . $bar['border_width'] . 'px solid ' . $bar['border_color'] . ';';
				break;
		}
		$styles .= 'height:' . $bar['height'] . 'px;';
		$styles .= 'overflow:hidden;';
		$styles .= 'width:100%;';
		$styles .= '}';

		$styles .= '#mobile-contact-bar ul{';
		$styles .= '-webkit-box-sizing:border-box;';
		$styles .= 'box-sizing:border-box;';
		$styles .= 'height:100%;';
		$styles .= 'line-height:0;';
		$styles .= 'list-style-type:none;';
		$styles .= 'margin:0;';
		$styles .= 'padding:0;';
		$styles .= 'position:relative;';
		$styles .= 'text-align:center;';
		$styles .= 'width:100%;';
		$styles .= '}';

		$styles .= '#mobile-contact-bar ul li{';
		$styles .= '-webkit-box-sizing:border-box;';
		$styles .= 'box-sizing:border-box;';
		$styles .= 'display:inline-block;';
		$styles .= 'height:' . $bar['height'] . 'px;';
		$styles .= 'margin:0;';
		$styles .= 'text-align:center;';
		switch ( $bar['is_border'] ) {
			case 'one':
				$styles .= 'height:' . ( $bar['height'] - $bar['border_width'] ) . 'px;';
				break;

			case 'two':
				$styles .= 'height:' . ( $bar['height'] - 2 * $bar['border_width'] ) . 'px;';
				break;

			case 'none':
				$styles .= 'height:' . $bar['height'] . 'px;';
				break;
		}
		switch ( $icons['alignment'] ) {
			case 'centered':
				$styles .= 'width:' . $icons['width'] . 'px;';
				break;

			case 'justified':
				$styles .= 'width:' . ( 100 / $contacts_count ) . '%;';
				break;
		}
		$styles .= '}';

		switch ( $icons['is_border'] ) {
			case 'two':
				$styles .= '#mobile-contact-bar ul li{';
				$styles .= 'border-left:' . $icons['border_width'] . 'px solid ' . $icons['border_color'] . ';';
				$styles .= '}';

				$styles .= '#mobile-contact-bar ul li:last-child{';
				$styles .= 'border-right:' . $icons['border_width'] . 'px solid ' . $icons['border_color'] . ';';
				$styles .= '}';
				break;

			case 'four':
				$styles .= '#mobile-contact-bar ul li{';
				$styles .= 'border-top:' . $icons['border_width'] . 'px solid ' . $icons['border_color'] . ';';
				$styles .= 'border-bottom:' . $icons['border_width'] . 'px solid ' . $icons['border_color'] . ';';
				$styles .= 'border-left:' . $icons['border_width'] . 'px solid ' . $icons['border_color'] . ';';
				$styles .= '}';

				$styles .= '#mobile-contact-bar ul li:last-child{';
				$styles .= 'border-right:' . $icons['border_width'] . 'px solid ' . $icons['border_color'] . ';';
				$styles .= '}';
				break;
		}

		$styles .= '#mobile-contact-bar ul li a{';
		$styles .= 'color:' . $icons['color'] . ';';
		$styles .= 'cursor:pointer;';
		$styles .= 'display: block;';
		$styles .= 'height:100%;';
		$styles .= 'position:relative;';
		$styles .= 'z-index:9998;';
		$styles .= '}';

		$styles .= '#mobile-contact-bar ul li a:active,';
		$styles .= '#mobile-contact-bar ul li a:focus{';
		$styles .= 'outline:none;';
		$styles .= '}';

		$styles .= '.mobile-contact-bar-fa-stack{';
		$styles .= 'height:2em;';
		$styles .= 'line-height:2em;';
		$styles .= 'width:2em;';
		$styles .= 'position:relative;';
		$styles .= 'top:50%;';
		$styles .= '-webkit-transform:translateY(-50%);';
		$styles .= '-ms-transform:translateY(-50%);';
		$styles .= 'transform:translateY(-50%);';
		$styles .= '}';

		if ( $toggle['is_render'] && $bar['is_fixed'] ) {
			$styles .= '#mobile-contact-bar-toggle-checkbox:checked ~ #mobile-contact-bar-outer{';
			$styles .= 'height:0;';
			$styles .= '}';

			$styles .= '#mobile-contact-bar-toggle-checkbox{';
			$styles .= 'display:none;';
			$styles .= 'position:absolute;';
			$styles .= '}';

			$styles .= '#mobile-contact-bar-toggle{';
			$styles .= 'cursor:pointer;';
			$styles .= 'display:table;';
			$styles .= 'line-height:0;';
			$styles .= 'margin:0 auto;';
			$styles .= 'position:relative;';
			$styles .= 'z-index:2;';
			$styles .= '}';

			$styles .= '#mobile-contact-bar-toggle span{';
			$styles .= 'display:inline-block;';
			$styles .= 'color:' . $icons['color'] . ';';
			$styles .= 'font-size:' . $toggle['size'] . 'rem;';
			$styles .= 'position:absolute;';
			$styles .= 'bottom:50%;';
			$styles .= 'left:50%;';
			$styles .= '-webkit-transform:translate(-50%);';
			$styles .= '-ms-transform:translate(-50%);';
			$styles .= 'transform:translate(-50%);';
			$styles .= 'text-align:center;';
			$styles .= 'width:100%;';
			$styles .= 'z-index:2;';
			$styles .= '}';

			$styles .= '#mobile-contact-bar-toggle svg{';
			$styles .= 'display:inline-block;';
			$styles .= 'pointer-events:none;';
			$styles .= 'fill:' . $toggle['color'] . ';';
			$styles .= 'z-index:1;';
			$styles .= '}';

			if ( $toggle['is_animation'] ) {
				$styles .= '#mobile-contact-bar-outer{';
				$styles .= '-webkit-transition:height 1s ease;';
				$styles .= '-moz-transition:height 1s ease;';
				$styles .= '-o-transition:height 1s ease;';
				$styles .= 'transition:height 1s ease;';
				$styles .= '}';
			}
		} // Endif is_toggle.

		if ( $badges ) {
			$styles .= '.mobile-contact-bar-badge{';
			$styles .= 'background-color:' . $badges['background_color'] . ';';
			$styles .= 'border-radius:100%;';
			$styles .= 'color:' . $badges['font_color'] . ';';
			$styles .= 'display:block;';
			$styles .= 'font-size:' . $badges['size'] . 'em;';
			$styles .= 'height:1.5em;';
			$styles .= 'width:1.5em;';
			$styles .= 'line-height:1.5;';
			$styles .= 'position:absolute;';
			switch ( $badges['corner'] ) {
				case 'top-right':
					$styles .= 'top:0;';
					$styles .= 'right:0;';
					break;

				case 'bottom-right':
					$styles .= 'right:0;';
					$styles .= 'bottom:0;';
					break;

				case 'bottom-left':
					$styles .= 'bottom:0;';
					$styles .= 'left:0;';
					break;

				case 'top-left':
					$styles .= 'top:0;';
					$styles .= 'left:0;';
					break;
			}
			$styles .= 'text-indent:0;';
			$styles .= '}';

		}

		$styles .= '/*(no-admin*/';

		// Bottom and Fixed position.
		if ( 'bottom' === $bar['vertical_position'] && $bar['is_fixed'] ) {
			if ( $bar['placeholder_height'] > 0 ) {
				$styles .= 'body{';
				$styles .= 'border-bottom:' . $bar['placeholder_height'] . 'px solid ' . $bar['placeholder_color'] . '!important;';
				$styles .= '}';
			}

			$styles .= '#mobile-contact-bar{';
			$styles .= 'position:fixed;';
			$styles .= 'left:0;';
			$styles .= ( $bar['space_height'] > 0 ) ? 'bottom:' . $bar['space_height'] . 'px;' : 'bottom:0;';
			$styles .= '}';
		}

		// Top and Fixed position.
		if ( 'top' === $bar['vertical_position'] && $bar['is_fixed'] ) {
			if ( $bar['placeholder_height'] > 0 ) {
				$styles .= 'body{';
				$styles .= 'border-top:' . $bar['placeholder_height'] . 'px solid ' . $bar['placeholder_color'] . '!important;';
				$styles .= '}';
			}

			$styles .= '#mobile-contact-bar{';
			$styles .= 'position:fixed;';
			$styles .= 'left:0;';
			$styles .= ( $bar['space_height'] > 0 ) ? 'top:' . $bar['space_height'] . 'px;' : 'top:0;';
			$styles .= '}';

			if ( $toggle['is_render'] ) {
				$styles .= '#mobile-contact-bar-toggle{';
				$styles .= 'position:absolute;';
				$styles .= 'bottom:-34px;';
				$styles .= 'left:50%;';
				$styles .= 'transform:translateX(-50%);';
				$styles .= '}';
			}
		}

		// Bottom and Not fixed position.
		if ( 'bottom' === $bar['vertical_position'] && ! $bar['is_fixed'] ) {
			if ( $bar['placeholder_height'] > 0 ) {
				$styles .= 'body{';
				$styles .= 'border-bottom:' . $bar['placeholder_height'] . 'px solid ' . $bar['placeholder_color'] . '!important;';
				$styles .= '}';
			}

			$styles .= '#mobile-contact-bar{';
			$styles .= 'margin-top:-' . $bar['height'] . 'px;';
			$styles .= 'position:relative;';
			$styles .= 'left:0;';

			if ( $bar['placeholder_height'] > 0 ) {
				$styles .= ( $bar['space_height'] > 0 ) ? 'bottom:' . ( $bar['space_height'] - $bar['placeholder_height'] ) . 'px;' : 'bottom:-' . $bar['placeholder_height'] . 'px;';
			} else {
				$styles .= ( $bar['space_height'] > 0 ) ? 'bottom:' . $bar['space_height'] . 'px;' : 'bottom:0;';
			}
			$styles .= '}';
		}

		// Top and Not fixed position.
		if ( 'top' === $bar['vertical_position'] && ! $bar['is_fixed'] ) {
			if ( $bar['placeholder_height'] > 0 ) {
				$styles .= 'body{';
				$styles .= 'border-top:' . $bar['placeholder_height'] . 'px solid ' . $bar['placeholder_color'] . '!important;';
				$styles .= '}';
			}

			$styles .= '#mobile-contact-bar{';
			$styles .= 'position:absolute;';
			$styles .= 'left:0;';
			$styles .= ( $bar['space_height'] > 0 ) ? 'top:' . $bar['space_height'] . 'px;' : 'top:0;';
			$styles .= '}';
		}

		$styles .= '/*no-admin)*/';

		if ( $bar['width'] < 100 ) {
			switch ( $bar['horizontal_position'] ) {
				case 'center':
					$styles .= '#mobile-contact-bar{';
					$styles .= 'left:50%;';
					$styles .= '-webkit-transform:translateX(-50%);';
					$styles .= '-ms-transform:translateX(-50%);';
					$styles .= 'transform:translateX(-50%);';
					$styles .= '}';
					break;
				case 'right':
					$styles .= '#mobile-contact-bar{';
					$styles .= 'left:100%;';
					$styles .= '-webkit-transform:translateX(-100%);';
					$styles .= '-ms-transform:translateX(-100%);';
					$styles .= 'transform:translateX(-100%);';
					$styles .= '}';
					break;
			}
		}

		// Add @media query.
		if ( 'css' === $general['device_detection'] ) {
			$styles .= '@media screen and (min-width:' . $general['max_screen_width'] . 'px){#mobile-contact-bar{display:none;}}';
		}

		$new_value['styles'] = $styles;

		return $new_value;
	}



	/**
	 * Renders templates.
	 *
	 * @since 2.0.0
	 */
	public static function admin_footer() {

		$parameter_th_allowed_html = array(
			'input' => array(
				'name'        => array(),
				'class'       => array(),
				'value'       => array(),
				'placeholder' => array(),
				'type'        => array(),
			),
		);
		?>

		<script type="text/html" id="mcb-tmpl-icon-picker">
			<div id="mcb-icon-picker-container">
				<div class="icon-picker-control">
					<a data-direction="back" href="#">
						<i class="fas fa-angle-left fa-lg"></i>
					</a>
					<input class="" placeholder="<?php esc_attr_e( 'Search', 'mobile-contact-bar' ); ?>" type="text">
					<a data-direction="forward" href="#">
						<i class="fas fa-angle-right fa-lg"></i>
					</a>
				</div>
				<ul class="icon-picker-list">
					<?php
					$icons = Mobile_Contact_Bar_Page::icons();
					foreach ( $icons as $icon_id => $section ) :
						foreach ( $section as $icon ) :
							$title = $icon_id . ' fa-' . $icon;
							?>
							<li data-icon="<?php echo esc_attr( $icon ); ?>">
								<a href="#" title="<?php echo esc_attr( $icon ); ?>">
									<i class="<?php echo esc_attr( $title ); ?>"></i>
								</a>
							</li>
							<?php
						endforeach;
					endforeach;
					?>
				</ul>
			</div>
		</script>



		<script type="text/html" id="mcb-tmpl-parameter">
			<tr class="mcb-parameter">
				<th scope="row">
					<?php echo wp_kses( self::output_parameter_th(), $parameter_th_allowed_html ); ?>
				</th>
				<td>
					<?php
					self::callback_render_parameter_td(
						array(
							'parameter_id' => '',
							'parameter'    => array(
								'key'         => '',
								'type'        => 'text',
								'placeholder' => 'value',
								'value'       => '',
							),
							'contact_id'   => '',
							'contact_type' => 'Custom',
						)
					);
					?>
				</td>
			</tr>
		</script>

		<?php
	}



	/**
	 * Renders a contact and its parameters.
	 *
	 * @since 2.0.0
	 *
	 * @uses $_POST
	 */
	public static function ajax_add_contact() {
		if ( isset( $_POST['nonce'], $_POST['contact_type'] ) && wp_verify_nonce( sanitize_key( $_POST['nonce'] ), MOBILE_CONTACT_BAR__NAME ) ) {

			$type = sanitize_text_field( wp_unslash( $_POST['contact_type'] ) );
			if ( in_array( strtolower( $type ), self::$contact_types, true ) ) {

				$class   = 'Mobile_Contact_Bar_Contact_' . $type;
				$contact = $class::admin_get_contact();

				if ( $contact ) {
					$data = null;
					$out  = '';

					$out .= '<tr class="mcb-contact">';
					$out .= '<th scope="row">';
					$out .= self::output_contact_th( '', $contact );
					$out .= '</th>';

					$out .= '<td>';
					ob_start();
					self::callback_render_contact_td(
						array(
							'contact_id' => '',
							'contact'    => $contact,
						)
					);
					$out .= ob_get_contents();
					ob_end_clean();
					$out .= '</td>';

					$out .= '</tr>';

					$data['contact'] = $out;

					if ( isset( $contact['parameters'] ) && $contact['parameters'] ) {
						$parameters = array();

						foreach ( $contact['parameters'] as $parameter_id => $parameter ) {
							$out = '';

							$out .= '<tr class="mcb-parameter hidden">';
							$out .= '<th scope="row">';
							$out .= self::output_parameter_th( $parameter_id, $parameter['key'], '', $contact['type'] );
							$out .= '</th>';

							$out .= '<td>';
							ob_start();
							self::callback_render_parameter_td(
								array(
									'parameter_id' => $parameter_id,
									'parameter'    => $parameter,
									'contact_id'   => '',
									'contact_type' => $contact['type'],
								)
							);
							$out .= ob_get_contents();
							ob_end_clean();
							$out .= '</td>';

							$out .= '</tr>';

							$parameters[] = $out;
						}
						$data['parameters'] = $parameters;
					}

					echo wp_json_encode( $data );
					exit;
				}
			}
		}
		exit;
	}



	/**
	 * Renders the action icons for a contact or a parameter.
	 *
	 * @since 2.0.0
	 *
	 * @param string $row_type     Which is 'contact' or 'parameter'.
	 * @param string $contact_type Contact type.
	 */
	private static function render_row_actions( $row_type, $contact_type ) {

		echo '<ul class="mcb-row-icons">';

		switch ( $row_type ) {
			case 'contact':
				printf(
					'<li class="mcb-action mcb-row-toggle-parameters mcb-invisible">
                        <i class="fas fa-caret-down fa-fw" aria-expanded="false" aria-hidden="true" title="%1$s"></i>
                        <span class="screen-reader-text">%1$s</span>
                    </li>',
					esc_attr__( 'Show query string parameters', 'mobile-contact-bar' )
				);

				printf(
					'<li class="mcb-action mcb-row-pick-icon">
                        <i class="fab fa-font-awesome-flag fa-fw" aria-hidden="true" title="%1$s"></i>
                        <span class="screen-reader-text">%1$s</span>
                    </li>',
					esc_attr__( 'Select a Font Awesome icon', 'mobile-contact-bar' )
				);

				printf(
					'<li class="mcb-action mcb-row-add-parameter %1$s">
                        <i class="fas fa-plus fa-fw" aria-hidden="true" title="%2$s"></i>
                        <span class="screen-reader-text">%2$s</span>
                    </li>',
					( 'Custom' === $contact_type ) ? '' : 'mcb-invisible',
					esc_attr__( 'Add a query string parameter', 'mobile-contact-bar' )
				);
				printf(
					'<li class="mcb-action mcb-row-delete-contact">
                        <i class="fas fa-times fa-fw" aria-hidden="true" title="%1$s"></i>
                        <span class="screen-reader-text">%1$s</span>
                    </li>',
					esc_attr__( 'Delete this contact', 'mobile-contact-bar' )
				);
				break;

			case 'parameter':
				printf(
					'<li class="mcb-action mcb-row-delete-parameter %1$s">
                        <i class="fas fa-times fa-fw" aria-hidden="true" title="%2$s"></i>
                        <span class="screen-reader-text">%2$s</span>
                    </li>',
					( 'Custom' === $contact_type ) ? '' : 'mcb-invisible',
					esc_attr__( 'Delete this parameter', 'mobile-contact-bar' )
				);
				break;
		}
		echo '</ul>';
	}



	/**
	 * Verifies that a color code is valid.
	 *
	 * @since 2.0.0
	 *
	 * @param  string $color Color code (Hex or RGBA).
	 * @return bool|string   Either false or the valid color code
	 */
	private static function is_color( $color ) {
		$color = self::sanitize_color( $color );

		if ( $color ) {
			return $color;
		}

		return false;
	}



	/**
	 * Sanitizes color code.
	 *
	 * @since 2.0.0
	 *
	 * @param  string $color Color code (Hex or RGBA).
	 * @return string        Filtered color code
	 */
	private static function sanitize_color( $color ) {
		$new_color = self::sanitize_hex_color( $color );

		if ( ! $new_color ) {
			$new_color = self::sanitize_rgba_color( $color );
		}

		return $new_color;
	}



	/**
	 * Sanitizes hexadecimal color code.
	 *
	 * @since 0.0.1
	 *
	 * @param  string $hex_color Color code.
	 * @return string            Filtererd color code
	 *
	 * @see https://developer.wordpress.org/reference/functions/sanitize_hex_color/
	 */
	private static function sanitize_hex_color( $hex_color ) {
		if ( preg_match( '|^#([A-Fa-f0-9]{3}){1,2}$|', $hex_color ) ) {
			return $hex_color;
		}

		return '';
	}



	/**
	 * Sanitizes RGBA color code.
	 *
	 * @since 0.0.1
	 *
	 * @param  string $rgba_color Color code.
	 * @return string             Filtererd color code
	 */
	private static function sanitize_rgba_color( $rgba_color ) {
		if ( preg_match( '/^rgba\((\d{1,3}),\s*(\d{1,3}),\s*(\d{1,3}),\s*(\d*(?:\.\d+)?)\)$/i', $rgba_color ) ) {
			return $rgba_color;
		}

		return '';
	}



	/**
	 * Sanitizes float value of [0,1] closed interval.
	 *
	 * @since 0.0.1
	 *
	 * @param  string $float Float number.
	 * @param  int    $min   Interval min value.
	 * @param  int    $max   Interval max value.
	 * @return float|int     Sanitized float number or min value
	 */
	private static function sanitize_unit_interval( $float, $min, $max ) {
		if ( preg_match( '/^' . $min . '$|^' . $max . '$|^' . $min . '\.\d{1,2}$/', $float ) ) {
			return (float) $float;
		}

		return $min;
	}
}
