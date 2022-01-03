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
                'vertical_alignment' => [
                    'type'     => 'radio',
                    'default'  => 'bottom',
                    'title'    => __( 'Vertical Alignment', 'mobile-contact-bar' ),
                    'options'  => [
                        'top'    => __( 'top', 'mobile-contact-bar' ),
                        'bottom' => __( 'bottom', 'mobile-contact-bar' ),
                    ],
                ],
                'is_sticky' => [
                    'type'     => 'checkbox',
                    'default'  => 1,
                    'title'    => __( 'Sticky Position', 'mobile-contact-bar' ),
                    'label'    => __( 'Fix bar at its position relative to the viewport', 'mobile-contact-bar' ),
                ],
                'height' => [
                    'type'     => 'number',
                    'default'  => 70,
                    'min'      => 0,
                    'title'    => __( 'Bar Height', 'mobile-contact-bar' ),
                    'postfix'  => 'px',
                ],
                'width' => [
                    'type'     => 'number',
                    'trigger'  => '<100',
                    'default'  => 100,
                    'min'      => 0,
                    'max'      => 100,
                    'title'    => __( 'Bar Width', 'mobile-contact-bar' ),
                    'postfix'  => '%',
                ],
                'horizontal_alignment' => [
                    'type'     => 'radio',
                    'parent'   => 'width',
                    'default'  => 'center',
                    'title'    => __( 'Horizontal Alignment', 'mobile-contact-bar' ),
                    'desc'     => __( 'It has effect if Bar Width is smaller than 100%.', 'mobile-contact-bar' ),
                    'options'  => [
                        'left'   => __( 'left', 'mobile-contact-bar' ),
                        'center' => __( 'center', 'mobile-contact-bar' ),
                        'right'  => __( 'right', 'mobile-contact-bar' ),
                    ],
                ],
                'opacity' => [
                    'type'     => 'slider',
                    'default'  => 1,
                    'min'      => 0,
                    'max'      => 1,
                    'step'     => 0.05,
                    'title'    => __( 'Bar Opacity', 'mobile-contact-bar' ),
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
                'is_borders' => [
                    'type'     => 'checkbox-group',
                    'trigger'  => '==true',
                    'title'    => __( 'Borders', 'mobile-contact-bar' ),
                    'options'  => [
                        'top' => [
                            'default' => 0,
                            'label'   => __( 'top', 'mobile-contact-bar' ),
                        ],
                        'bottom' => [
                            'default' => 0,
                            'label'   => __( 'bottom', 'mobile-contact-bar' ),
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
                'space_height' => [
                    'type'     => 'number',
                    'default'  => 0,
                    'min'      => 0,
                    'title'    => __( 'Space Height', 'mobile-contact-bar' ),
                    'postfix'  => 'px',
                ],
                'placeholder_height' => [
                    'type'     => 'number',
                    'default'  => 50,
                    'min'      => 0,
                    'title'    => __( 'Placeholder Height', 'mobile-contact-bar' ),
                    'postfix'  => 'px',
                ],
                'placeholder_color' => [
                    'type'     => 'color-picker',
                    'default'  => '#ff647d',
                    'title'    => __( 'Placeholder Color', 'mobile-contact-bar' ),
                ],
            ],

            'icons_labels' => [
                'label_position' => [
                    'type'     => 'radio',
                    'default'  => 'below',
                    'title'    => __( 'Label Position', 'mobile-contact-bar' ),
                    'options'  => [
                        'above' => __( 'above the icon', 'mobile-contact-bar' ),
                        'below' => __( 'below the icon', 'mobile-contact-bar' ),
                    ],
                ],
                'alignment' => [
                    'type'     => 'radio',
                    'trigger'  => '==centered',
                    'default'  => 'justified',
                    'title'    => __( 'Alignment', 'mobile-contact-bar' ),
                    'options'  => [
                        'justified' => __( 'justified', 'mobile-contact-bar' ),
                        'centered'  => __( 'centered', 'mobile-contact-bar' ),
                    ],
                ],
                'width' => [
                    'type'     => 'number',
                    'parent'   => 'alignment',
                    'default'  => 70,
                    'min'      => 0,
                    'title'    => __( 'Item Width', 'mobile-contact-bar' ),
                    'postfix'  => 'px',
                ],
                'gap' => [
                    'type'     => 'slider',
                    'default'  => 0.4,
                    'min'      => 0,
                    'max'      => 2,
                    'step'     => 0.2,
                    'postfix'  => 'em',
                    'title'    => __( 'Gap between Icon and Label', 'mobile-contact-bar' ),
                ],
                'icon_size' => [
                    'type'     => 'slider',
                    'default'  => 1.35,
                    'min'      => 0,
                    'max'      => 3,
                    'step'     => 0.05,
                    'postfix'  => 'em',
                    'title'    => __( 'Icon Size', 'mobile-contact-bar' ),
                ],
                'label_size' => [
                    'type'     => 'slider',
                    'default'  => 0.8,
                    'min'      => 0,
                    'max'      => 3,
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
                'border_color' => [
                    'type'     => 'color-picker-group',
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
                'shape' => [
                    'type'     => 'radio',
                    'default'  => 'rounded',
                    'title'    => __( 'Shape', 'mobile-contact-bar' ),
                    'options'  => [
                        'rounded' => __( 'rounded corners', 'mobile-contact-bar' ),
                        'sharp'   => __( 'sharp corners', 'mobile-contact-bar' ),
                    ],
                ],
                'is_animation' => [
                    'type'     => 'checkbox',
                    'default'  => 1,
                    'title'    => __( 'Animation', 'mobile-contact-bar' ),
                    'label'    => __( 'Slow down toggle with animation', 'mobile-contact-bar' ),
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
                'label' => [
                    'type'     => 'text',
                    'trigger'  => '!=',
                    'default'  => 'Contact Us',
                    'title'    => __( 'Short label', 'mobile-contact-bar' ),
                    'desc'     => __( 'Display a short label on the toggle.', 'mobile-contact-bar' ),
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
                'font_size' => [
                    'type'     => 'slider',
                    'default'  => 0.8,
                    'min'      => 0,
                    'max'      => 2,
                    'step'     => 0.05,
                    'title'    => __( 'Badge Scale', 'mobile-contact-bar' ),
                    'desc'     => __( 'Transforms the badge size according to the scaling value.', 'mobile-contact-bar' ),
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
            ],
        ];
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
                    case 'select':
                    case 'radio':
                        $sanitized_settings[$section_key][$setting_key] =
                            ( in_array( $value, array_keys( $setting['options'] )))
                            ? $value
                            : $setting['default'];
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
                        $value = (int) $value;
                        $sanitized_settings[$section_key][$setting_key] =
                            ( 0 === $value || 1 === $value )
                            ? $value
                            : $setting['default'];
                        break;

                    case 'checkbox-group':
                        foreach( $setting['options'] as $option_key => $option )
                        {
                            $value_ = (int) $value[$option_key];
                            $sanitized_settings[$section_key][$setting_key][$option_key] =
                                ( 0 === $value_ || 1 === $value_ )
                                ? $value_
                                : $option['default'];
                        }
                        break;

                    case 'number':
                        $value = (int) $value;
                        $sanitized_settings[$section_key][$setting_key] =
                            (( isset( $setting['min'] ) && $value < $setting['min'] ) || ( isset( $setting['max'] ) && $value > $setting['max'] ))
                            ? $setting['default']
                            : $value;
                        break;

                    case 'text':
                        $sanitized_settings[$section_key][$setting_key] = sanitize_text_field( $value );
                        break;

                    case 'slider':
                        $value = (float) $value;
                        $sanitized_settings[$section_key][$setting_key] =
                            ( $setting['min'] <= $value || $value <= $setting['max'] )
                            ? $value
                            : $setting['default'];
                        break;
                }
            }
        }

        return $sanitized_settings;
    }


    /**
     * Sanitizes color code.
     *
     * @param  string $color Color code (Hex, RGB, or RGBA)
     * @return string        Filtered color code
     */
    public function sanitize_color( $color )
    {
        $sanitized_color = $this->sanitize_hex_color( $color );

        if ( ! $sanitized_color )
        {
            $sanitized_color = $this->sanitize_rgb_color( $color );
        }
        if ( ! $sanitized_color )
        {
            $sanitized_color = $this->sanitize_rgba_color( $color );
        }

        return $sanitized_color;
    }


    /**
     * Sanitizes hexadecimal color code.
     *
     * @param  string $hex_color Color code
     * @return string            Filtererd color code
     *
     * @see https://developer.wordpress.org/reference/functions/sanitize_hex_color/
     */
    private function sanitize_hex_color( $hex_color )
    {
        if ( preg_match( '/^#([A-Fa-f0-9]{3}){1,2}$/', $hex_color ))
        {
            return $hex_color;
        }

        return '';
    }


    /**
     * Sanitizes RGB color code.
     *
     * @param  string $rgb_color Color code
     * @return string            Filtererd color code
     */
    private function sanitize_rgb_color( $rgb_color )
    {
        if ( preg_match( '/^rgb\((\d{1,3}),\s*(\d{1,3}),\s*(\d{1,3})\)$/i', $rgb_color ))
        {
            return $rgb_color;
        }

        return '';
    }


    /**
     * Sanitizes RGBA color code.
     *
     * @param  string $rgba_color Color code
     * @return string             Filtererd color code
     */
    private function sanitize_rgba_color( $rgba_color )
    {
        if ( preg_match( '/^rgba\((\d{1,3}),\s*(\d{1,3}),\s*(\d{1,3}),\s*(\d*(?:\.\d+)?)\)$/i', $rgba_color ))
        {
            return $rgba_color;
        }

        return '';
    }
}
