<?php

namespace MobileContactBar\Settings;

use MobileContactBar\Helper;


final class Input
{
    /**
     * Defines the input fields for the 'settings'.
     *
     * @return array Multidimensional array
     */
    public function fields()
    {
        $primary_color = __( 'primary', 'mobile-contact-bar' );
        $secondary_color = __( 'secondary', 'mobile-contact-bar' );

        return
        [
            'bar' => [
                'device' => [
                    'type'     => 'radio',
                    'default'  => 'none',
                    'title'    => __( 'Display on Devices', 'mobile-contact-bar' ),
                    'options'  => [
                        'mobile'  => __( 'mobile', 'mobile-contact-bar' ),
                        'desktop' => __( 'desktop', 'mobile-contact-bar' ),
                        'both'    => __( 'mobile and desktop', 'mobile-contact-bar' ),
                        'none'    => __( 'disabled', 'mobile-contact-bar' ),
                    ],
                ],
                'is_new_tab' => [
                    'type'     => 'checkbox',
                    'default'  => 0,
                    'title'    => __( 'Open in New Tab', 'mobile-contact-bar' ),
                    'label'    => __( 'Open links in a new browser tab', 'mobile-contact-bar' ),
                ],
                'position' => [
                    'type'     => 'radio',
                    'default'  => 'bottom',
                    'title'    => __( 'Position', 'mobile-contact-bar' ),
                    'options'  => [
                        'top'    => __( 'top', 'mobile-contact-bar' ),
                        'bottom' => __( 'bottom', 'mobile-contact-bar' ),
                        'left'   => __( 'left', 'mobile-contact-bar' ),
                        'right'  => __( 'right', 'mobile-contact-bar' ),
                    ],
                ],
                'is_fixed' => [
                    'type'     => 'checkbox',
                    'default'  => 1,
                    'title'    => __( 'Fixed Bar', 'mobile-contact-bar' ),
                    'label'    => __( 'Fix bar at its position relative to the viewport', 'mobile-contact-bar' ),
                ],
                'span' => [
                    'type'     => 'radio',
                    'trigger'  => '!=stretch',
                    'default'  => 'stretch',
                    'title'    => __( 'Span on the Longest Side', 'mobile-contact-bar' ),
                    'options'  => [
                        'stretch' => __( 'stretched', 'mobile-contact-bar' ),
                        'fix_min' => __( 'fixed size min', 'mobile-contact-bar' ),
                        'fix_max' => __( 'fixed size max', 'mobile-contact-bar' ),
                    ],
                ],
                'alignment' => [
                    'type'     => 'slider',
                    'parent'   => 'span',
                    'default'  => 50,
                    'min'      => 0,
                    'max'      => 100,
                    'step'     => 1,
                    'title'    => __( 'Align on the Longest Side', 'mobile-contact-bar' ),
                    'desc'     => __( 'Align bar across the longest side.', 'mobile-contact-bar' ),
                    'postfix'  => '%',
                ],
                'icon_size' => [
                    'type'     => 'radio',
                    'parent'   => 'span',
                    'default'  => 'equal',
                    'title'    => __( 'Icon Size', 'mobile-contact-bar' ),
                    'options'  => [
                        'equal'       => __( 'equal', 'mobile-contact-bar' ),
                        'fit_content' => __( 'fit content', 'mobile-contact-bar' ),
                    ],
                ],
                'shortest' => [
                    'type'     => 'number',
                    'default'  => 50,
                    'min'      => 0,
                    'title'    => __( 'Shortest Side', 'mobile-contact-bar' ),
                    'postfix'  => 'px',
                ],
                'space' => [
                    'type'     => 'number',
                    'default'  => 0,
                    'min'      => 0,
                    'title'    => __( 'Space from the Screen Edge', 'mobile-contact-bar' ),
                    'postfix'  => 'px',
                ],
                'opacity' => [
                    'type'     => 'slider',
                    'default'  => 1,
                    'min'      => 0,
                    'max'      => 1,
                    'step'     => 0.05,
                    'title'    => __( 'Bar Opacity', 'mobile-contact-bar' ),
                ],
                'is_borders' => [
                    'type'     => 'checkbox-group',
                    'trigger'  => '==true',
                    'title'    => __( 'Borders', 'mobile-contact-bar' ),
                    'options'  => [
                        'top' => [
                            'default' => 0,
                            'label'   => __( 'top', 'mobile-contact-bar' ),
                        ],
                        'right' => [
                            'default' => 0,
                            'label'   => __( 'right', 'mobile-contact-bar' ),
                        ],
                        'bottom' => [
                            'default' => 0,
                            'label'   => __( 'bottom', 'mobile-contact-bar' ),
                        ],
                        'left' => [
                            'default' => 0,
                            'label'   => __( 'left', 'mobile-contact-bar' ),
                        ],
                    ],
                ],
                'border_color' => [
                    'type'     => 'color-picker',
                    'parent'   => 'is_borders',
                    'default'  => '#174b38',
                    'title'    => __( 'Border Color', 'mobile-contact-bar' ),
                ],
                'border_width' => [
                    'type'     => 'number',
                    'parent'   => 'is_borders',
                    'default'  => 1,
                    'min'      => 0,
                    'max'      => 100,
                    'title'    => __( 'Border Width', 'mobile-contact-bar' ),
                    'postfix'  => 'px',
                ],
                'is_secondary_colors' => [
                    'type'     => 'checkbox-group',
                    'title'    => __( 'Use Secondary Colors', 'mobile-contact-bar' ),
                    'options'  => [
                        'focus' => [
                            'default' => 1,
                            'label'   => __( 'on focus', 'mobile-contact-bar' ),
                        ],
                        'hover' => [
                            'default' => 1,
                            'label'   => __( 'on hover', 'mobile-contact-bar' ),
                        ],
                        'active' => [
                            'default' => 1,
                            'label'   => __( 'when active', 'mobile-contact-bar' ),
                        ],
                    ],
                ],
                'show'         => [
                    'type'     => 'checkbox-group',
                    'title'    => __( 'Show on', 'mobile-contact-bar' ),
                    'options'  => array_merge(
                        [
                            'homepage' => [
                                'default' => 1,
                                'label'   => __( 'Homepage', 'mobile-contact-bar' ),
                            ],
                        ],
                        $this->post_types()
                    ),
                ],
            ],

            'buttons' => [
                'label_position' => [
                    'type'     => 'radio',
                    'default'  => 'below',
                    'title'    => __( 'Label Position', 'mobile-contact-bar' ),
                    'options'  => [
                        'above' => __( 'above the icon', 'mobile-contact-bar' ),
                        'below' => __( 'below the icon', 'mobile-contact-bar' ),
                    ],
                ],
                'padding'=> [
                    'type'     => 'slider',
                    'default'  => 0.8,
                    'min'      => 0,
                    'max'      => 5,
                    'step'     => 0.05,
                    'postfix'  => 'em',
                    'title'    => __( 'Padding along the longest axis', 'mobile-contact-bar' ),
                ],
                'gap' => [
                    'type'     => 'slider',
                    'default'  => 0.4,
                    'min'      => 0,
                    'max'      => 3,
                    'step'     => 0.2,
                    'postfix'  => 'em',
                    'title'    => __( 'Gap between Icon and Label', 'mobile-contact-bar' ),
                ],
                'icon_size' => [
                    'type'     => 'slider',
                    'default'  => 1.35,
                    'min'      => 0,
                    'max'      => 5,
                    'step'     => 0.05,
                    'postfix'  => 'em',
                    'title'    => __( 'Icon Size', 'mobile-contact-bar' ),
                ],
                'label_size' => [
                    'type'     => 'slider',
                    'default'  => 0.8,
                    'min'      => 0,
                    'max'      => 5,
                    'step'     => 0.05,
                    'postfix'  => 'em',
                    'title'    => __( 'Label Font Size', 'mobile-contact-bar' ),
                ],
                'is_borders' => [
                    'type'     => 'checkbox-group',
                    'trigger'  => '==true',
                    'title'    => __( 'Borders', 'mobile-contact-bar' ),
                    'options'  => [
                        'top' => [
                            'default' => 0,
                            'label'   => __( 'top', 'mobile-contact-bar' ),
                        ],
                        'right' => [
                            'default' => 0,
                            'label'   => __( 'right', 'mobile-contact-bar' ),
                        ],
                        'bottom' => [
                            'default' => 0,
                            'label'   => __( 'bottom', 'mobile-contact-bar' ),
                        ],
                        'left' => [
                            'default' => 0,
                            'label'   => __( 'left', 'mobile-contact-bar' ),
                        ],
                    ],
                ],
                'border_width' => [
                    'type'     => 'number',
                    'parent'   => 'is_borders',
                    'default'  => 1,
                    'min'      => 0,
                    'max'      => 100,
                    'title'    => __( 'Border Width', 'mobile-contact-bar' ),
                    'postfix'  => 'px',
                ],
                'border_color' => [
                    'type'     => 'color-picker-group',
                    'parent'   => 'is_borders',
                    'title'    => __( 'Border Color', 'mobile-contact-bar' ),
                    'options'  => [
                        'primary'   => [
                            'default' => '',
                            'desc'    => $primary_color,
                        ],
                        'secondary' => [
                            'default' => '',
                            'desc'    => $secondary_color,
                        ],
                    ],
                ],
                'background_color' => [
                    'type'     => 'color-picker-group',
                    'title'    => __( 'Background Color', 'mobile-contact-bar' ),
                    'options'  => [
                        'primary'   => [
                            'default' => '#252832',
                            'desc'    => $primary_color,
                        ],
                        'secondary' => [
                            'default' => '#32373c',
                            'desc'    => $secondary_color,
                        ],
                    ],
                ],
                'icon_color' => [
                    'type'     => 'color-picker-group',
                    'title'    => __( 'Icon Color', 'mobile-contact-bar' ),
                    'options'  => [
                        'primary' => [
                            'default' => '#f1f4f8',
                            'desc'    => $primary_color,
                        ],
                        'secondary' => [
                            'default' => '#d32f2f',
                            'desc'    => $secondary_color,
                        ],
                    ],
                ],
                'label_color' => [
                    'type'     => 'color-picker-group',
                    'title'    => __( 'Label Color', 'mobile-contact-bar' ),
                    'options'  => [
                        'primary'   => [
                            'default' => '#f1f4f8',
                            'desc'    => $primary_color,
                        ],
                        'secondary' => [
                            'default' => '#d32f2f',
                            'desc'    => $secondary_color,
                        ],
                    ],
                ],
            ],

            'toggle' => [
                'is_render' => [
                    'type'     => 'checkbox',
                    'default'  => 0,
                    'title'    => __( 'Render Toggle', 'mobile-contact-bar' ),
                    'label'    => __( 'Render toggle for the bar', 'mobile-contact-bar' ),
                    'desc'     => __( 'The toggle will be displayed only if the bar is fixed at its position.', 'mobile-contact-bar' ),
                ],
                'is_closed' => [
                    'type'     => 'checkbox',
                    'default'  => 0,
                    'title'    => __( 'Closed State', 'mobile-contact-bar' ),
                    'label'    => __( 'Set toggle state to be closed as default', 'mobile-contact-bar' ),
                ],
                'is_cookie' => [
                    'type'     => 'checkbox',
                    'default'  => 0,
                    'title'    => __( 'State Cookie', 'mobile-contact-bar' ),
                    'label'    => __( 'Save toggle state (open or closed) in a cookie', 'mobile-contact-bar' ),
                ],
                'is_animation' => [
                    'type'     => 'checkbox',
                    'default'  => 1,
                    'title'    => __( 'Animation', 'mobile-contact-bar' ),
                    'label'    => __( 'Slow down toggle with animation', 'mobile-contact-bar' ),
                ],
                'shape' => [
                    'type'     => 'radio',
                    'default'  => 'rounded',
                    'title'    => __( 'Shape', 'mobile-contact-bar' ),
                    'options'  => [
                        'rounded' => __( 'rounded corners', 'mobile-contact-bar' ),
                        'sharp'   => __( 'sharp corners', 'mobile-contact-bar' ),
                    ],
                ],
                'label' => [
                    'type'     => 'text',
                    'trigger'  => '!=',
                    'default'  => 'Contact Us',
                    'title'    => __( 'Short label', 'mobile-contact-bar' ),
                    'desc'     => __( 'Display a short label on the toggle.', 'mobile-contact-bar' ),
                ],
                'font_size' => [
                    'type'     => 'slider',
                    'parent'   => 'label',
                    'default'  => 0.9,
                    'min'      => 0,
                    'max'      => 3,
                    'step'     => 0.05,
                    'postfix'  => 'em',
                    'title'    => __( 'Font Size', 'mobile-contact-bar' ),
                ],
                'font_color' => [
                    'type'     => 'color-picker-group',
                    'parent'   => 'label',
                    'title'    => __( 'Font Color', 'mobile-contact-bar' ),
                    'options'  => [
                        'primary' => [
                            'default' => '#f1f4f8',
                            'desc'    => $primary_color,
                        ],
                        'secondary' => [
                            'default' => '',
                            'desc'    => $secondary_color,
                        ],
                    ],
                ],
                'background_color' => [
                    'type'     => 'color-picker-group',
                    'title'    => __( 'Background Color', 'mobile-contact-bar' ),
                    'options'  => [
                        'primary' => [
                            'default' => '#444444',
                            'desc'    => $primary_color,
                        ],
                        'secondary' => [
                            'default' => '',
                            'desc'    => $secondary_color,
                        ],
                    ],
                ],
            ],

            'badges' => [
                'position' => [
                    'type'     => 'radio',
                    'default'  => 'top-right',
                    'title'    => __( 'Position', 'mobile-contact-bar' ),
                    'options'  => [
                        'top-right'    => __( 'top-right', 'mobile-contact-bar' ),
                        'bottom-right' => __( 'bottom-right', 'mobile-contact-bar' ),
                        'bottom-left'  => __( 'bottom-left', 'mobile-contact-bar' ),
                        'top-left'     => __( 'top-left', 'mobile-contact-bar' ),
                    ],
                ],
                'size' => [
                    'type'     => 'slider',
                    'default'  => 0.8,
                    'min'      => 0,
                    'max'      => 1,
                    'step'     => 0.05,
                    'title'    => __( 'Badge Scale', 'mobile-contact-bar' ),
                    'desc'     => __( 'Transforms the badge size according to this scaling value.', 'mobile-contact-bar' ),
                ],
                'font_color' => [
                    'type'     => 'color-picker-group',
                    'title'    => __( 'Font Color', 'mobile-contact-bar' ),
                    'options'  => [
                        'primary' => [
                            'default' => '#252832',
                            'desc'    => $primary_color,
                        ],
                        'secondary' => [
                            'default' => '',
                            'desc'    => $secondary_color,
                        ],
                    ],
                ],
                'background_color' => [
                    'type'     => 'color-picker-group',
                    'title'    => __( 'Background Color', 'mobile-contact-bar' ),
                    'options'  => [
                        'primary' => [
                            'default' => '#ff8e00',
                            'desc'    => $primary_color,
                        ],
                        'secondary' => [
                            'default' => '',
                            'desc'    => $secondary_color,
                        ],
                    ],
                ],
            ],
        ];
    }


    private function post_types()
    {
        $options = [];
        $post_types = get_post_types( ['public' => true], 'objects' );
        unset( $post_types['attachment'] );

        foreach ( $post_types as $post_type )
        {
            $options[$post_type->name] = [
                'default' => 1,
                'label'   => __( $post_type->labels->name ),
            ];
        }

        return $options;
    }


    /**
     * Retrieves the 'settings' with default values.
     *
     * @return array
     */
    public function default_settings()
    {
        $defaults = [];
        $fields = $this->fields();

        foreach ( $fields as $section_key => $section )
        {
            foreach ( $section as $setting_key => $setting )
            {
                if ( isset( $setting['default'] ))
                {
                    $defaults[$section_key][$setting_key] = $setting['default'];
                }
                elseif ( isset( $setting['options'] ))
                {
                    foreach ( $setting['options'] as $option_key => $option )
                    {
                        if ( isset( $option['default'] ))
                        {
                            $defaults[$section_key][$setting_key][$option_key] = $option['default'];
                        }
                    }
                }
            }
        }

        return $defaults;
    }


    /**
     * Retrieves the 'settings' with empty default values.
     *
     * @return array
     */
    public function empty_default_settings()
    {
        $defaults = [];
        $fields = $this->fields();

        foreach ( $fields as $section_key => $section )
        {
            foreach ( $section as $setting_key => $setting )
            {
                if ( isset( $setting['default'] ) && 'checkbox' === $setting['type'] )
                {
                    $defaults[$section_key][$setting_key] = 0;
                }
                elseif ( isset( $setting['default'] ))
                {
                    $defaults[$section_key][$setting_key] = $setting['default'];
                }
                elseif ( isset( $setting['options'] ))
                {
                    foreach ( $setting['options'] as $option_key => $option )
                    {
                        if ( isset( $option['default'] ) && 'checkbox-group' === $setting['type'] )
                        {
                            $defaults[$section_key][$setting_key][$option_key] = 0;
                        }
                        elseif ( isset( $option['default'] ))
                        {
                            $defaults[$section_key][$setting_key][$option_key] = $option['default'];
                        }
                    }
                }
            }
        }

        return $defaults;
    }


    /**
     * @return array
     */
    public function sections()
    {
        return array_keys( $this->fields() );
    }


    /**
     * Sanitizes the 'settings'.
     * 
     * @param  array $settings
     * @return array
     */
    public function sanitize( $settings = [] )
    {
        if ( ! is_array( $settings ) || empty( $settings ))
        {
            return $this->default_settings();
        }

        $sanitized_settings = [];

        $empty_default_settings = $this->empty_default_settings();
        $refreshed_settings = Helper::array_intersect_key_recursive(
            array_replace_recursive( $empty_default_settings, $settings ),
            $empty_default_settings
        );

        $fields = $this->fields();
        foreach ( $fields as $section_key => $section )
        {
            foreach ( $section as $setting_key => $setting )
            {
                $value = $refreshed_settings[$section_key][$setting_key];

                switch ( $setting['type'] )
                {
                    case 'radio':
                    case 'select':
                        $sanitized_settings[$section_key][$setting_key] = $this->sanitize_radio_select( $setting, $value );
                        break;

                    case 'color-picker':
                        $sanitized_settings[$section_key][$setting_key] = $this->sanitize_color( $value );
                        break;

                    case 'color-picker-group':
                        foreach( $setting['options'] as $option_key => $option )
                        {
                            $sanitized_settings[$section_key][$setting_key][$option_key] = $this->sanitize_color( $value[$option_key] );
                        }
                        break;

                    case 'checkbox':
                        $sanitized_settings[$section_key][$setting_key] = $this->sanitize_zero_one( $setting, $value );
                        break;

                    case 'checkbox-group':
                        foreach( $setting['options'] as $option_key => $option )
                        {
                            $sanitized_settings[$section_key][$setting_key][$option_key] = $this->sanitize_zero_one( $option, $value[$option_key] );
                        }
                        break;

                    case 'number':
                        $sanitized_settings[$section_key][$setting_key] = $this->sanitize_int( $setting, $value );
                        break;

                    case 'text':
                        $sanitized_settings[$section_key][$setting_key] = sanitize_text_field( $value );
                        break;

                    case 'slider':
                        $sanitized_settings[$section_key][$setting_key] = $this->sanitize_float( $setting, $value );
                        break;
                }
            }
        }

        return $sanitized_settings;
    }


    public function sanitize_zero_one( $setting, $value )
    {
        return ( 0 === (int) $value || 1 === (int) $value )
            ? (int) $value
            : $setting['default'];
    }


    public function sanitize_int( $setting, $value )
    {
        if ( isset( $setting['min'] ) && (int) $value < $setting['min'] )
        {
            return (int) $setting['min'];
        }

        if ( isset( $setting['max'] ) && (int) $value > $setting['max'] )
        {
            return (int) $setting['max'];
        }

        return (int) $value;
    }


    public function sanitize_float( $setting, $value )
    {
        if ( isset( $setting['min'] ) && (float) $value < $setting['min'] )
        {
            return (float) $setting['min'];
        }

        if ( isset( $setting['max'] ) && (float) $value > $setting['max'] )
        {
            return (float) $setting['max'];
        }

        return (float) $value;
    }


    public function sanitize_radio_select( $setting, $value )
    {
        return ( in_array( $value, array_keys( $setting['options'] )))
            ? $value
            : $setting['default'];
    }


    /**
     * Sanitizes color code.
     *
     * @param  string $value Color code (Hex, RGB, or RGBA)
     * @return string        Filtered color code
     */
    public function sanitize_color( $value )
    {
        $sanitized_color = $this->sanitize_hex_color( $value );

        if ( ! $sanitized_color )
        {
            $sanitized_color = $this->sanitize_rgb_color( $value );
        }
        if ( ! $sanitized_color )
        {
            $sanitized_color = $this->sanitize_rgba_color( $value );
        }

        return $sanitized_color;
    }


    /**
     * Sanitizes hexadecimal color code.
     *
     * @param  string $value Color code
     * @return string        Filtererd color code
     *
     * @see https://developer.wordpress.org/reference/functions/sanitize_hex_color/
     */
    private function sanitize_hex_color( $value )
    {
        if ( preg_match( '/^#([A-Fa-f0-9]{3}){1,2}$/', $value ))
        {
            return $value;
        }

        return '';
    }


    /**
     * Sanitizes RGB color code.
     *
     * @param  string $value Color code
     * @return string        Filtererd color code
     */
    private function sanitize_rgb_color( $value )
    {
        if ( preg_match( '/^rgb\((\d{1,3}),\s*(\d{1,3}),\s*(\d{1,3})\)$/i', $value ))
        {
            return $value;
        }

        return '';
    }


    /**
     * Sanitizes RGBA color code.
     *
     * @param  string $value Color code
     * @return string        Filtererd color code
     */
    private function sanitize_rgba_color( $value )
    {
        if ( preg_match( '/^rgba\((\d{1,3}),\s*(\d{1,3}),\s*(\d{1,3}),\s*(\d*(?:\.\d+)?)\)$/i', $value ))
        {
            return $value;
        }

        return '';
    }
}
