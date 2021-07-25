<?php

defined( 'ABSPATH' ) or exit;


final class Mobile_Contact_Bar_Option {



	/**
	 * Multidimensional array of the plugin's option, divided into sections: 'settings', 'contacts', 'styles'.
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
	 * Hooks WordPress's admin and AJAX actions and filters.
	 *
	 * @since 0.1.0
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
	 * @since 0.1.0
	 */
	public static function init() {
		 self::$option  = get_option( MOBILE_CONTACT_BAR__NAME );
		self::$settings = Mobile_Contact_Bar_Settings::settings();
	}



	/**
	 * Adds sections and fields to the option page.
	 *
	 * @since 0.1.0
	 */
	public static function admin_init() {
		register_setting(
			MOBILE_CONTACT_BAR__NAME . '_group',
			MOBILE_CONTACT_BAR__NAME,
			array( __CLASS__, 'callback_sanitize_option' )
		);

		// Settings Sections

		foreach ( self::$settings as $section_id => $section ) {
			if ( 'badges' == $section_id && ! class_exists( 'WooCommerce' ) ) {
				continue;
			}

			$title = ucfirst( $section_id );

			add_settings_section(
				'mcb-section-' . $section_id,
				__( $title, 'mobile-contact-bar' ),
				false,
				MOBILE_CONTACT_BAR__NAME
			);

			foreach ( $section as $setting_id => $setting ) {
				$args = array(
					'section_id' => $section_id,
					'setting_id' => $setting_id,
					'setting'    => $setting,
				);

				if ( isset( $setting['parent'] ) ) {
					 $args['class'] = 'hidden mcb-child mcb-parent-' . $section_id . '-' . $setting['parent'];
				} elseif ( isset( $setting['trigger'] ) ) {
					$args['class'] = 'mcb-parent mcb-parent-' . $section_id . '-' . $setting_id . ' mcb-trigger-' . $setting['trigger'];
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

		// Contact List Section

		add_settings_section(
			'mcb-section-contacts',
			__( 'Contact List', 'mobile-contact-bar' ),
			false,
			MOBILE_CONTACT_BAR__NAME
		);

		foreach ( self::$option['contacts'] as $contact_id => $contact ) {
			$checked = ( $contact['checked'] ) ? ' mcb-active' : '';
			$odd     = ( $contact_id % 2 == 1 ) ? ' mcb-odd' : '';

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
	 * @param object $object  null
	 * @param array  $section Passed from add_meta_box as sixth parameter.
	 */
	public static function callback_render_section( $object, $section ) {
		$table_id = str_replace( '-section-', '-table-', $section['id'] );

		$out_buttons = '';
		$out_icons   = '';
		if ( 'mcb-table-contacts' == $table_id ) {
			$buttons = apply_filters( 'mcb_admin_add_button', array() );
			ksort( $buttons );

			foreach ( $buttons as $button ) {
				$out_buttons .= sprintf(
					'<li class="mcb-action wp-ui-text-highlight" data-contact-type="' . $button['type'] . '">
                        <span class="mcb-integration">%s</span>
                    </li>',
					esc_html( $button['title'] )
				);
			}

			$icons = apply_filters( 'mcb_admin_add_icon', array() );
			ksort( $icons );

			foreach ( $icons as $icon ) {
				$out_icons .= sprintf(
					'<li class="mcb-action wp-ui-text-highlight" data-contact-type="' . $icon['type'] . '">
                        <i class="mcb-integration %1$s fa-fw" title="%2$s" aria-hidden="true"></i>
                        <span class="screen-reader-text">%2$s</span>
                    </li>',
					esc_attr( $icon['icon'] ),
					esc_attr( $icon['title'] )
				);
			}
		}

		?>
		<table id="<?php esc_attr_e( $table_id ); ?>" class="form-table">
			<?php if ( 'mcb-table-contacts' == $table_id ) { ?>
			<thead>
				<tr>
					<td>
						<ul id="mcb-integration-buttons">
							<?php echo $out_buttons; ?>
						</ul>
					</td>
					<td>
						<ul id="mcb-integration-icons">
							<?php echo $out_icons; ?>
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
	 * @since 0.1.0
	 *
	 * @param array $args section_id, setting_id, setting
	 */
	public static function callback_output_setting( $args ) {
		switch ( $args['setting']['type'] ) {
			case 'color-picker':
				printf(
					'<input type="text" id="mcb-%1$s-%2$s" name="' . MOBILE_CONTACT_BAR__NAME . '[settings][%1$s][%2$s]" class="cs-wp-color-picker" value="%3$s">',
					esc_attr( $args['section_id'] ),
					esc_attr( $args['setting_id'] ),
					esc_attr( self::$option['settings'][ $args['section_id'] ][ $args['setting_id'] ] )
				);
				break;

			case 'select':
				printf(
					'<select id="mcb-%1$s-%2$s" name="' . MOBILE_CONTACT_BAR__NAME . '[settings][%1$s][%2$s]" class="mcb-regular-text">',
					esc_attr( $args['section_id'] ),
					esc_attr( $args['setting_id'] )
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
					'<fieldset class="mcb-radio-label-wrap" id="mcb-%s-%s">',
					esc_attr( $args['section_id'] ),
					esc_attr( $args['setting_id'] )
				);
				foreach ( $args['setting']['options'] as $value => $label ) {
					printf(
						'<label class="mcb-radio-label" for="mcb-%1$s-%2$s--%3$s">
                            <input type="radio" id="mcb-%1$s-%2$s--%3$s" name="' . MOBILE_CONTACT_BAR__NAME . '[settings][%1$s][%2$s]" value="%3$s" %4$s>%5$s
                        </label>',
						esc_attr( $args['section_id'] ),
						esc_attr( $args['setting_id'] ),
						esc_attr( $value ),
						checked( $value, self::$option['settings'][ $args['section_id'] ][ $args['setting_id'] ], false ),
						esc_html( $label )
					);
				}
				echo '</fieldset>';
				break;

			case 'radio-image':
				printf(
					'<fieldset class="mcb-radio-image-wrap" id="mcb-%s-%s">',
					esc_attr( $args['section_id'] ),
					esc_attr( $args['setting_id'] )
				);
				foreach ( $args['setting']['options'] as $value => $url ) {
					printf(
						'<label class="mcb-radio-image">
                            <input type="radio" id="mcb-%1$s-%2$s--%3$s" name="' . MOBILE_CONTACT_BAR__NAME . '[settings][%1$s][%2$s]" value="%3$s" %4$s>
                            <img src="%5$s">
                        </label>',
						esc_attr( $args['section_id'] ),
						esc_attr( $args['setting_id'] ),
						esc_attr( $value ),
						checked( $value, self::$option['settings'][ $args['section_id'] ][ $args['setting_id'] ], false ),
						plugins_url( $url, MOBILE_CONTACT_BAR__PATH )
					);
				}
				echo '</fieldset>';
				break;

			case 'checkbox':
				printf(
					'<label for="mcb-%1$s-%2$s">
                        <input type="checkbox" id="mcb-%1$s-%2$s" name="' . MOBILE_CONTACT_BAR__NAME . '[settings][%1$s][%2$s]" %3$s value="1">%4$s
                    </label>',
					esc_attr( $args['section_id'] ),
					esc_attr( $args['setting_id'] ),
					checked( self::$option['settings'][ $args['section_id'] ][ $args['setting_id'] ], 1, false ),
					esc_html( $args['setting']['label'] )
				);
				break;

			case 'text':
				printf(
					'<input type="text" id="mcb-%1$s-%2$s" name="' . MOBILE_CONTACT_BAR__NAME . '[settings][%1$s][%2$s]" class="mcb-regular-text" value="%3$s">',
					esc_attr( $args['section_id'] ),
					esc_attr( $args['setting_id'] ),
					esc_attr( self::$option['settings'][ $args['section_id'] ][ $args['setting_id'] ] )
				);
				break;

			case 'number':
				printf(
					'<input type="number" id="mcb-%1$s-%2$s" name="' . MOBILE_CONTACT_BAR__NAME . '[settings][%1$s][%2$s]" class="mcb-regular-text" value="%3$d">
                    <span>%4$s</span>',
					esc_attr( $args['section_id'] ),
					esc_attr( $args['setting_id'] ),
					esc_attr( self::$option['settings'][ $args['section_id'] ][ $args['setting_id'] ] ),
					esc_html( $args['setting']['postfix'] )
				);
				break;

			case 'slider':
				printf(
					'<input type="range" id="mcb-%1$s-%2$s" name="' . MOBILE_CONTACT_BAR__NAME . '[settings][%1$s][%2$s]" class="mcb-slider-input" value="%3$s" min="%4$s" max="%5$s" step="%6$s">
                    <span class="mcb-slider-value">%3$s</span>',
					esc_attr( $args['section_id'] ),
					esc_attr( $args['setting_id'] ),
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
	 * Renders the data part of the contact.
	 *
	 * @since 0.1.0
	 *
	 * @param array $args contact_id, contact
	 */
	public static function callback_render_contact_td( $args ) {
		$out = self::output_contact_td( $args );
		echo $out;
	}



	/**
	 * Renders the header part of the contact.
	 *
	 * @since 2.0.0
	 *
	 * @param  array  $contact    Contact
	 * @param  string $contact_id Contact id
	 * @return string             HTML th
	 */
	private static function output_contact_th( $contact_id, $contact ) {
		$prefix = MOBILE_CONTACT_BAR__NAME . '[contacts][' . esc_attr( $contact_id ) . ']';
		$icon   = esc_attr( $contact['icon'] );

		$out = '';

		// 'type' hidden
		$out .= sprintf( '<input name="' . $prefix . '[type]" value="%s" type="hidden">', esc_attr( $contact['type'] ) );

		// 'icon' hidden
		$out .= '<input name="' . $prefix . '[icon]" value="' . $icon . '" type="hidden">';

		// 'title' hidden
		if ( 'Sample' == $contact['type'] ) {
			$out .= sprintf( '<input name="' . $prefix . '[title]" value="%s" type="hidden">', esc_html( $contact['title'] ) );
		}

		$out .= '<ul class="mcb-th">';

		// 'checkbox' input
		$out .= sprintf(
			'<li class="mcb-contact-checkbox"><input name="' . $prefix . '[checked]" value="1" %s type="checkbox"></li>',
			( $contact['checked'] ) ? checked( $contact['checked'], 1, false ) : ''
		);

		// 'icon'
		$out .= '<li class="mcb-contact-icon ui-sortable-handle"><i class="' . $icon . ' fa-lg"></i></li>';

		// 'title'
		if ( 'Sample' == $contact['type'] ) {
			$out .= sprintf( '<li class="mcb-contact-title">%s</li>', esc_html( $contact['title'] ) );
		} else {
			// 'title' input
			$out .= sprintf(
				'<li class="mcb-contact-title"><input name="' . $prefix . '[title]" value="%s" placeholder="%s" type="text"></li>',
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
	 * @param  array $args contact_id, contact
	 * @return string       HTML td
	 */
	private static function output_contact_td( $args ) {
		$sanitized_uri = Mobile_Contact_Bar_Validator::escape_contact_uri( $args['contact']['uri'] );

		$out = '';

		// 'placeholder' hidden
		$out .= sprintf(
			'<input name="' . MOBILE_CONTACT_BAR__NAME . '[contacts][%s][placeholder]" value="%s" type="hidden">',
			esc_attr( $args['contact_id'] ),
			$args['contact']['placeholder']
		);

		$out .= '<ul class="mcb-td">';

		// 'URI' contact with empty placeholder has non-editable URI
		if ( '' == $args['contact']['placeholder'] ) {
			// 'URI' hidden
			$out .= sprintf(
				'<input name="' . MOBILE_CONTACT_BAR__NAME . '[contacts][%s][uri]" value="%s" type="hidden">',
				esc_attr( $args['contact_id'] ),
				$sanitized_uri
			);
			// 'URI'
			$out .= '<li class="mcb-contact-uri">' . $sanitized_uri . '</li>';
		} else {
			// 'URI' input
			$out .= sprintf(
				'<li class="mcb-contact-uri"><input name="' . MOBILE_CONTACT_BAR__NAME . '[contacts][%s][uri]" value="%s" placeholder="%s" type="text"></li>',
				esc_attr( $args['contact_id'] ),
				$sanitized_uri,
				$args['contact']['placeholder']
			);
		}

		$out .= '<li>' . self::row_actions( 'contact', $args['contact']['type'] ) . '</li>';

		$out .= '</ul>';

		return $out;
	}



	/**
	 * Renders the data part of the parameter.
	 *
	 * @since 1.2.0
	 *
	 * @param array $args parameter_id, parameter, contact_id, contact_type
	 */
	public static function callback_render_parameter_td( $args ) {
		$out = self::output_parameter_td( $args );
		echo $out;
	}



	/**
	 * Outputs the header part of the parameter.
	 *
	 * @since 2.0.0
	 *
	 * @param  string $parameter_id
	 * @param  string $parameter_key
	 * @param  string $contact_id
	 * @param  string $contact_type
	 * @return string                HTML TH
	 */
	private static function output_parameter_th( $parameter_id = '', $parameter_key = '', $contact_id = '', $contact_type = 'Custom' ) {
		$out = '';

		if ( 'Custom' == $contact_type ) {
			// 'key' input
			$out .= sprintf(
				'<input name="' . MOBILE_CONTACT_BAR__NAME . '[contacts][%s][parameters][%s][key]" class="mcb-parameter-key" value="%s" placeholder="%s" type="text">',
				esc_attr( $contact_id ),
				esc_attr( $parameter_id ),
				esc_attr( $parameter_key ),
				esc_attr__( 'key', 'mobile-contact-bar' )
			);
		} else {
			// 'key' hidden
			$out .= sprintf(
				'<input name="' . MOBILE_CONTACT_BAR__NAME . '[contacts][%s][parameters][%s][key]" value="%s" type="hidden">',
				esc_attr( $contact_id ),
				esc_attr( $parameter_id ),
				esc_attr( $parameter_key )
			);
			// 'key'
			$out .= $parameter_key;
		}
		return $out;
	}



	/**
	 * Outputs the data part of the parameter.
	 *
	 * @since 2.0.0
	 *
	 * @param  array $args parameter_id, parameter, contact_id, contact_type
	 * @return string       HTML td
	 */
	private static function output_parameter_td( $args ) {
		$out = '';

		// 'type' hidden
		$out .= sprintf(
			'<input name="' . MOBILE_CONTACT_BAR__NAME . '[contacts][%s][parameters][%s][type]" value="%s" type="hidden">',
			esc_attr( $args['contact_id'] ),
			esc_attr( $args['parameter_id'] ),
			esc_attr( $args['parameter']['type'] )
		);

		// 'placeholder' hidden
		$out .= sprintf(
			'<input name="' . MOBILE_CONTACT_BAR__NAME . '[contacts][%s][parameters][%s][placeholder]" value="%s" type="hidden">',
			esc_attr( $args['contact_id'] ),
			esc_attr( $args['parameter_id'] ),
			esc_attr( $args['parameter']['placeholder'] )
		);

		$out .= '<ul class="mcb-ul">';
		$out .= '<li class="mcb-parameter-value">';

		// 'value' input
		switch ( $args['parameter']['type'] ) {
			case 'text':
			case 'email':
				$out .= sprintf(
					'<input type="text" name="' . MOBILE_CONTACT_BAR__NAME . '[contacts][%s][parameters][%s][value]" value="%s" placeholder="%s">',
					esc_attr( $args['contact_id'] ),
					esc_attr( $args['parameter_id'] ),
					esc_attr( $args['parameter']['value'] ),
					esc_attr( $args['parameter']['placeholder'] )
				);
				break;

			case 'textarea':
				$out .= sprintf(
					'<textarea name="' . MOBILE_CONTACT_BAR__NAME . '[contacts][%s][parameters][%s][value]" placeholder="%s">%s</textarea>',
					esc_attr( $args['contact_id'] ),
					esc_attr( $args['parameter_id'] ),
					esc_attr( $args['parameter']['placeholder'] ),
					esc_textarea( $args['parameter']['value'] )
				);
				break;
		}

		$out .= '</li>';

		$out .= '<li>' . self::row_actions( 'parameter', $args['contact_type'] ) . '</li>';

		$out .= '</ul>';

		return $out;
	}



	/**
	 * Sanitizes the option (settings and contacts).
	 *
	 * @since 0.1.0
	 *
	 * @param  array $input Multidimensional array of the option.
	 * @return array        Sanitized option.
	 */
	public static function callback_sanitize_option( $input ) {
		 // SETTINGS

		$settings           = $input['settings'];
		$sanitized_settings = array();

		foreach ( $settings as $section_id => &$section ) {
			// workaround empty checkboxes
			$section = array_replace(
				array_map(
					function ( $setting ) {
						if ( 'checkbox' == $setting['type'] ) {
							return 0;
						}
					},
					self::$settings[ $section_id ]
				),
				$section
			);
			// all settings will be saved, at least with their default values
			$section = array_replace(
				array_map(
					function ( $setting ) {
						return $setting['default'];
					},
					self::$settings[ $section_id ]
				),
				$section
			);
		}
		unset( $section );

		// sanitize settings
		foreach ( self::$settings as $section_id => $section ) {
			if ( 'badges' == $section_id && ! class_exists( 'WooCommerce' ) ) {
				continue;
			}

			foreach ( $section as $setting_id => $setting ) {
				$value = $settings[ $section_id ][ $setting_id ];

				switch ( $setting['type'] ) {
					case 'select':
					case 'radio':
					case 'radio-image':
						$value = ( in_array( $value, array_keys( $setting['options'] ) ) ) ? $value : $setting['default'];
						break;

					case 'color-picker':
						$value = self::sanitize_color( $value );

						if ( ! self::is_color( $value ) ) {
							$value = $setting['default'];
						}
						break;

					case 'checkbox':
						$value = (int) $value;
						$value = ( 0 == $value || 1 == $value ) ? $value : $setting['default'];
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

		// CONTACTS

		$contacts           = $input['contacts'];
		$sanitized_contacts = array();

		$valid_contact_types = array();
		foreach ( glob( plugin_dir_path( MOBILE_CONTACT_BAR__PATH ) . 'includes/contacts/class-*.php' ) as $path ) {
			$valid_contact_types[] = substr( basename( $path, '.php' ), 6 );
		}

		foreach ( $contacts as $contact_id => &$contact ) {
			// remove contact if invalid 'icon', but leave empty icons
			if ( $contact['icon'] && ! Mobile_Contact_Bar_Page::in_icons( $contact['icon'] ) ) {
				unset( $contacts[ $contact_id ] );
			}

			// remove contact if invalid 'type'
			if ( ! in_array( $contact['type'], $valid_contact_types ) ) {
				unset( $contacts[ $contact_id ] );
			}

			// remove empty 'parameters'
			if ( isset( $contact['parameters'] ) && ! $contact['parameters'] ) {
				unset( $contacts[ $contact_id ]['parameters'] );
			}
			// reindex 'parameters'
			if ( isset( $contact['parameters'] ) && $contact['parameters'] ) {
				$contacts[ $contact_id ]['parameters'] = array_values( $contacts[ $contact_id ]['parameters'] );
			}
		}
		unset( $contact );

		// merge and sanitize contacts
		foreach ( $contacts as $contact_id => $contact ) {
			$sanitized_contact = array();

			// sanitize 'checked'
			$sanitized_contact['checked'] = isset( $contact['checked'] ) ? (int) $contact['checked'] : 0;

			// 'type' is already sanitized
			$sanitized_contact['type'] = $contact['type'];

			// 'icon' is already sanitized
			$sanitized_contact['icon'] = $contact['icon'];

			// sanitize 'title'
			$sanitized_contact['title'] = sanitize_text_field( $contact['title'] );

			// copy 'placeholder'
			$sanitized_contact['placeholder'] = $contact['placeholder'];

			// sanitize 'uri'
			$sanitized_contact['uri'] = Mobile_Contact_Bar_Validator::sanitize_contact_uri( $contact['uri'] );

			// sanitize 'parameters'
			if ( isset( $contact['parameters'] ) ) {
				foreach ( $contact['parameters'] as $parameter_id => $parameter ) {
					// sanitize 'key'
					$sanitized_contact['parameters'][ $parameter_id ]['key'] = sanitize_key( $parameter['key'] );

					// sanitize 'type'
					$sanitized_contact['parameters'][ $parameter_id ]['type'] = sanitize_key( $parameter['type'] );

					// sanitize 'placeholder'
					$sanitized_contact['parameters'][ $parameter_id ]['placeholder'] = sanitize_text_field( $parameter['placeholder'] );

					// santitize 'value'
					$sanitized_contact['parameters'][ $parameter_id ]['value'] = Mobile_Contact_Bar_Validator::sanitize_parameter_value( $parameter['value'], $sanitized_contact['parameters'][ $parameter_id ]['type'] );
				}
			}
			$sanitized_contacts[ $contact_id ] = $sanitized_contact;
		}

		// reindex
		$sanitized_contacts = array_values( $sanitized_contacts );

		self::$option['settings'] = $sanitized_settings;
		self::$option['contacts'] = $sanitized_contacts;

		return self::$option;
	}



	/**
	 * Generates the public styles, and stores them in the option.
	 *
	 * @since 0.1.0
	 *
	 * @param  array $new_value The new value.
	 * @param  array $old_value The old value.
	 * @return array            The updated option.
	 */
	public static function pre_update_option( $new_value, $old_value = array() ) {
		$bar    = $new_value['settings']['bar'];
		$icons  = $new_value['settings']['icons'];
		$toggle = $new_value['settings']['toggle'];
		$badges = ( isset( $new_value['settings']['badges'] ) ) ? $new_value['settings']['badges'] : null;

		$contacts       = array_filter(
			$new_value['contacts'],
			function ( $contact ) {
				return $contact['checked'];
			}
		);
		$contacts_count = count( $contacts );

		$styles = '';

		$styles .= '#mobile-contact-bar{';
		$styles .= 'box-sizing:border-box;';
		$styles .= 'display:block;';
		$styles .= 'font-size:100%;';
		$styles .= 'font-size:1rem;';
		$styles .= 'opacity:' . $bar['opacity'] . ';';
		$styles .= 'position:relative;';
		$styles .= 'width:' . $bar['width'] . '%;';
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
		$styles .= 'box-sizing:border-box;';
		$styles .= 'line-height:0;';
		$styles .= 'list-style-type:none;';
		$styles .= 'margin:0;';
		$styles .= 'padding:0;';
		$styles .= 'position:relative;';
		$styles .= 'text-align:center;';
		$styles .= 'width:100%;';
		$styles .= '}';

		$styles .= '#mobile-contact-bar ul li{';
		$styles .= 'box-sizing:border-box;';
		$styles .= 'display:inline-block;';
		$styles .= 'height:' . $bar['height'] . 'px;';
		$styles .= 'margin:0;';
		$styles .= 'padding:0;';
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

		$styles .= '.fa-stack{';
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
			$styles .= 'padding:0;';
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
				$styles .= 'transition:height 1s ease;';
				$styles .= '}';
			}
		} // endif is_toggle

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
			switch ( $badges['place'] ) {
				case 'top-right':
					$styles .= 'top:0;';
					$styles .= 'right:0;';
					break;

				case 'bottom-right':
					$styles .= 'bottom:0;';
					$styles .= 'right:0;';
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

		// bottom
		// Fixed
		if ( 'bottom' == $bar['vertical_position'] && $bar['is_fixed'] ) {
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

		// top
		// fixed
		if ( 'top' == $bar['vertical_position'] && $bar['is_fixed'] ) {
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

		// bottom
		// not fixed
		if ( 'bottom' == $bar['vertical_position'] && ! $bar['is_fixed'] ) {
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

		// top
		// not fixed
		if ( 'top' == $bar['vertical_position'] && ! $bar['is_fixed'] ) {
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

		if ( $bar['width'] < 100 ) {
			switch ( $bar['horizontal_position'] ) {
				case 'center':
					$styles .= '#mobile-contact-bar{';
					$styles .= 'left:50%;';
					$styles .= 'transform:translateX(-50%);';
					$styles .= '}';
					break;
				case 'right':
					$styles .= '#mobile-contact-bar{';
					$styles .= 'left:100%;';
					$styles .= 'transform:translateX(-100%);';
					$styles .= '}';
					break;
			}
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
							<li data-icon="<?php echo $icon; ?>">
								<a href="#" title="<?php echo $icon; ?>">
									<i class="<?php echo $title; ?>"></i>
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
					<?php echo self::output_parameter_th(); ?>
				</th>
				<td>
					<?php
					echo self::output_parameter_td(
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
		if ( ! isset( $_POST['nonce'] ) || ! wp_verify_nonce( $_POST['nonce'], MOBILE_CONTACT_BAR__NAME ) ) {
			exit;
		}
		if ( ! isset( $_POST['contact_type'] ) || ! $_POST['contact_type'] ) {
			exit;
		}

		$contacts = apply_filters( 'mcb_admin_add_contact', array() );
		$contact  = self::mcb_array_search( 'type', $_POST['contact_type'], $contacts );

		if ( $contact ) {
			$data = null;
			$out  = '';

			$out     .= '<tr class="mcb-contact">';
			$out     .= '<th scope="row">';
				$out .= self::output_contact_th( '', $contact );
			$out     .= '</th>';
			$out     .= '<td>';
				$out .= self::output_contact_td(
					array(
						'contact_id' => '',
						'contact'    => $contact,
					)
				);
				$out .= '</td>';
			$out     .= '</tr>';

			$data['contact'] = $out;

			if ( isset( $contact['parameters'] ) && $contact['parameters'] ) {
				$parameters = array();

				foreach ( $contact['parameters'] as $parameter_id => $parameter ) {
					$out = '';

					$out     .= '<tr class="mcb-parameter hidden">';
						$out .= '<th scope="row">';
						$out .= self::output_parameter_th( $parameter_id, $parameter['key'], '', $contact['type'] );
						$out .= '</th>';
						$out .= '<td>';
						$out .= self::output_parameter_td(
							array(
								'parameter_id' => $parameter_id,
								'parameter'    => $parameter,
								'contact_id'   => '',
								'contact_type' => $contact['type'],
							)
						);
						$out .= '</td>';
					$out     .= '</tr>';

					$parameters[] = $out;
				}
				$data['parameters'] = $parameters;
			}

			$response = json_encode( $data );

			echo $response;
			exit;
		}
		exit;
	}



	/**
	 * Outputs the action icons for a contact or a parameter.
	 *
	 * @since 2.0.0
	 *
	 * @param  string $row_type 'contact' or 'parameter'
	 * @return string           HTML Unordered list of actions.
	 */
	private static function row_actions( $row_type, $type ) {
		$actions = '';

		$actions .= '<ul class="mcb-row-icons">';

		switch ( $row_type ) {
			case 'contact':
				$actions .= sprintf(
					'<li class="mcb-action mcb-row-toggle-parameters mcb-invisible">
                        <i class="fas fa-caret-down fa-fw" aria-expanded="false" aria-hidden="true" title="%1$s"></i>
                        <span class="screen-reader-text">%1$s</span>
                    </li>',
					esc_attr__( 'Show query string parameters', 'mobile-contact-bar' )
				);

				$actions .= sprintf(
					'<li class="mcb-action mcb-row-pick-icon">
                        <i class="fab fa-font-awesome-flag fa-fw" aria-hidden="true" title="%1$s"></i>
                        <span class="screen-reader-text">%1$s</span>
                    </li>',
					esc_attr__( 'Select a Font Awesome icon', 'mobile-contact-bar' )
				);

				$actions .= sprintf(
					'<li class="mcb-action mcb-row-add-parameter %1$s">
                        <i class="fas fa-plus fa-fw" aria-hidden="true" title="%2$s"></i>
                        <span class="screen-reader-text">%2$s</span>
                    </li>',
					( 'Custom' == $type ) ? '' : 'mcb-invisible',
					esc_attr__( 'Add a query string parameter', 'mobile-contact-bar' )
				);
				$actions .= sprintf(
					'<li class="mcb-action mcb-row-delete-contact">
                        <i class="fas fa-times fa-fw" aria-hidden="true" title="%1$s"></i>
                        <span class="screen-reader-text">%1$s</span>
                    </li>',
					esc_attr__( 'Delete this contact', 'mobile-contact-bar' )
				);
				break;

			case 'parameter':
				$actions .= sprintf(
					'<li class="mcb-action mcb-row-delete-parameter %1$s">
                        <i class="fas fa-times fa-fw" aria-hidden="true" title="%2$s"></i>
                        <span class="screen-reader-text">%2$s</span>
                    </li>',
					( 'Custom' == $type ) ? '' : 'mcb-invisible',
					esc_attr__( 'Delete this parameter', 'mobile-contact-bar' )
				);
				break;
		}
		$actions .= '</ul>';

		return $actions;
	}



	/**
	 * Verifies that a color code is valid.
	 *
	 * @since 2.0.0
	 *
	 * @param  string $color Color code (Hex or RGBA).
	 * @return bool|string        Either false or the valid color code.
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
	 * @return string        Filtered color code.
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
	 * @since 0.1.0
	 *
	 * @param  string $hex_color Color code.
	 * @return string            Filtererd color code.
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
	 * @since 0.1.0
	 *
	 * @param  string $rgba_color Color code.
	 * @return string             Filtererd color code.
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
	 * @since 0.1.0
	 *
	 * @param  string $float Float number.
	 * @param  int    $min   Interval min value.
	 * @param  int    $max   Interval max value.
	 * @return float|int        Sanitized float number or min value.
	 */
	private static function sanitize_unit_interval( $float, $min, $max ) {
		if ( preg_match( '/^' . $min . '$|^' . $max . '$|^' . $min . '\.\d{1,2}$/', $float ) ) {
			return (float) $float;
		}

		return $min;
	}



	/**
	 * Finds item by key and value - multidimensional array search.
	 *
	 * @since 2.0.0
	 *
	 * @param  string $key   Item key.
	 * @param  string $value Item value at that key.
	 * @param  array  $array Multidimensional array.
	 * @return array|bool        Item or false.
	 */
	private static function mcb_array_search( $key, $value, $array ) {
		foreach ( $array as $id => $item ) {
			if ( $item[ $key ] == $value ) {
				return $item;
			}
		}
		return false;
	}

}
