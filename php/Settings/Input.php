<?php

namespace MobileContactBar\Settings;

final class Input
{
    /**
     * Defines the setting fields.
     *
     * @return array Multidimensional array of settings fields, divided into sections
     */
    public function fields()
    {
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
                        'both'    => __( 'both', 'mobile-contact-bar' ),
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
                'is_fixed' => [
                    'type'     => 'checkbox',
                    'default'  => 1,
                    'title'    => __( 'Fixed Position', 'mobile-contact-bar' ),
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
                    'default'  => 100,
                    'min'      => 0,
                    'max'      => 100,
                    'title'    => __( 'Bar Width', 'mobile-contact-bar' ),
                    'postfix'  => '%',
                    'trigger'  => '<100',
                ],
                'horizontal_alignment' => [
                    'type'     => 'radio',
                    'default'  => 'center',
                    'title'    => __( 'Horizontal Alignment', 'mobile-contact-bar' ),
                    'desc'     => __( 'It has effect if Bar Width is smaller than 100%.', 'mobile-contact-bar' ),
                    'options'  => [
                        'left'   => __( 'left', 'mobile-contact-bar' ),
                        'center' => __( 'center', 'mobile-contact-bar' ),
                        'right'  => __( 'right', 'mobile-contact-bar' ),
                    ],
                    'parent'   => 'width',
                ],
                'opacity' => [
                    'type'     => 'slider',
                    'default'  => 1,
                    'min'      => 0,
                    'max'      => 1,
                    'step'     => 0.05,
                    'title'    => __( 'Bar Opacity', 'mobile-contact-bar' ),
                ],
                'is_border' => [
                    'type'     => 'radio',
                    'default'  => 'none',
                    'title'    => __( 'Border', 'mobile-contact-bar' ),
                    'options'  => [
                        'one'  => __( 'top or bottom', 'mobile-contact-bar' ),
                        'two'  => __( 'top and bottom', 'mobile-contact-bar' ),
                        'none' => __( 'no border', 'mobile-contact-bar' ),
                    ],
                    'trigger'  => '!=none',
                ],
                'border_color' => [
                    'type'     => 'color-picker',
                    'default'  => '#174b38',
                    'title'    => __( 'Border Color', 'mobile-contact-bar' ),
                    'parent'   => 'is_border',
                ],
                'border_width' => [
                    'type'     => 'number',
                    'default'  => 1,
                    'min'      => 0,
                    'max'      => 100,
                    'title'    => __( 'Border Width', 'mobile-contact-bar' ),
                    'postfix'  => 'px',
                    'parent'   => 'is_border',
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
                'alignment' => [
                    'type'     => 'radio',
                    'default'  => 'justified',
                    'title'    => __( 'Alignment', 'mobile-contact-bar' ),
                    'options'  => [
                        'justified' => __( 'justified', 'mobile-contact-bar' ),
                        'centered'  => __( 'centered', 'mobile-contact-bar' ),
                    ],
                    'trigger'  => '==centered',
                ],
                'width' => [
                    'type'     => 'number',
                    'default'  => 70,
                    'min'      => 0,
                    'title'    => __( 'Item Width', 'mobile-contact-bar' ),
                    'postfix'  => 'px',
                    'parent'   => 'alignment',
                ],
                'label_position' => [
                    'type'     => 'radio',
                    'default'  => 'below',
                    'title'    => __( 'Label Position', 'mobile-contact-bar' ),
                    'options'  => [
                        'above' => __( 'above the icon', 'mobile-contact-bar' ),
                        'below' => __( 'below the icon', 'mobile-contact-bar' ),
                    ],
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
                'background_color' => [
                    'type'     => 'color-picker',
                    'default'  => '#252832',
                    'title'    => __( 'Background Color', 'mobile-contact-bar' ),
                ],
                'icon_color' => [
                    'type'     => 'color-picker',
                    'default'  => '#f1f4f8',
                    'title'    => __( 'Icon Color', 'mobile-contact-bar' ),
                ],
                'label_color' => [
                    'type'     => 'color-picker',
                    'default'  => '#f1f4f8',
                    'title'    => __( 'Label Font Color', 'mobile-contact-bar' ),
                ],
                'borders' => [
                    'type'     => 'checkbox-group',
                    'title'    => __( 'Borders', 'mobile-contact-bar' ),
                    'options'  => [
                        'top' => [
                            'type'     => 'checkbox',
                            'default'  => 0,
                            'label'    => __( 'top', 'mobile-contact-bar' ),
                        ],
                        'right' => [
                            'type'     => 'checkbox',
                            'default'  => 0,
                            'label'    => __( 'right', 'mobile-contact-bar' ),
                        ],
                        'bottom' => [
                            'type'     => 'checkbox',
                            'default'  => 0,
                            'label'    => __( 'bottom', 'mobile-contact-bar' ),
                        ],
                        'left' => [
                            'type'     => 'checkbox',
                            'default'  => 0,
                            'label'    => __( 'left', 'mobile-contact-bar' ),
                        ],
                    ],
                    'trigger'  => '==true',
                ],
                'border_color' => [
                    'type'     => 'color-picker',
                    'default'  => '#ffffff',
                    'title'    => __( 'Border Color', 'mobile-contact-bar' ),
                    'parent'   => 'borders',
                ],
                'border_width' => [
                    'type'     => 'number',
                    'default'  => 1,
                    'min'      => 0,
                    'max'      => 100,
                    'title'    => __( 'Border Width', 'mobile-contact-bar' ),
                    'postfix'  => 'px',
                    'parent'   => 'borders',
                ],
                'secondary_colors' => [
                    'type'     => 'checkbox-group',
                    'title'    => __( 'Secondary colors', 'mobile-contact-bar' ),
                    'options'  => [
                        'focus' => [
                            'type'     => 'checkbox',
                            'default'  => 1,
                            'label'    => __( 'focus', 'mobile-contact-bar' ),
                        ],
                        'hover' => [
                            'type'     => 'checkbox',
                            'default'  => 1,
                            'label'    => __( 'hover', 'mobile-contact-bar' ),
                        ],
                        'active' => [
                            'type'     => 'checkbox',
                            'default'  => 0,
                            'label'    => __( 'active', 'mobile-contact-bar' ),
                        ],
                    ],
                    'trigger'  => '==true',
                ],
                'secondary_background_color' => [
                    'type'     => 'color-picker',
                    'default'  => '#32373c',
                    'title'    => __( 'Background Color', 'mobile-contact-bar' ),
                    'parent'   => 'secondary_colors',
                ],
                'secondary_icon_color' => [
                    'type'     => 'color-picker',
                    'default'  => '#d32f2f',
                    'title'    => __( 'Icon Color', 'mobile-contact-bar' ),
                    'parent'   => 'secondary_colors',
                ],
                'secondary_label_color' => [
                    'type'     => 'color-picker',
                    'default'  => '#d32f2f',
                    'title'    => __( 'Label Font Color', 'mobile-contact-bar' ),
                    'parent'   => 'secondary_colors',
                ],
                'secondary_border_color' => [
                    'type'     => 'color-picker',
                    'default'  => '',
                    'title'    => __( 'Border Color (top / bottom)', 'mobile-contact-bar' ),
                    'parent'   => 'secondary_colors',
                ],
                // 'size' => [
                //     'type'     => 'select',
                //     'default'  => 'lg',
                //     'title'    => __( 'Icon Size', 'mobile-contact-bar' ),
                //     'options'  => [
                //         'xs'  => 'xs',
                //         'sm'  => 'sm',
                //         'lg'  => 'lg',
                //         '1x'  => '1x',
                //         '2x'  => '2x',
                //         '3x'  => '3x',
                //         '4x'  => '4x',
                //         '5x'  => '5x',
                //         '6x'  => '6x',
                //         '7x'  => '7x',
                //         '8x'  => '8x',
                //         '9x'  => '9x',
                //         '10x' => '10x',
                //     ],
                // ],
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
                    'label'    => __( 'Slow down toggle animation', 'mobile-contact-bar' ),
                ],
                'background_color' => [
                    'type'     => 'color-picker',
                    'default'  => '#444444',
                    'title'    => __( 'Background Color', 'mobile-contact-bar' ),
                ],
                'label' => [
                    'type'     => 'text',
                    'default'  => 'Contact Us',
                    'title'    => __( 'Short label', 'mobile-contact-bar' ),
                    'desc'     => __( 'Display a short label on the toggle.', 'mobile-contact-bar' ),
                    'trigger'  => '!=',
                ],
                'font_color' => [
                    'type'     => 'color-picker',
                    'default'  => '#f1f4f8',
                    'title'    => __( 'Font Color', 'mobile-contact-bar' ),
                    'parent'   => 'label',
                ],
                'font_size' => [
                    'type'     => 'slider',
                    'default'  => 0.9,
                    'min'      => 0,
                    'max'      => 3,
                    'step'     => 0.05,
                    'postfix'  => 'em',
                    'title'    => __( 'Font Size', 'mobile-contact-bar' ),
                    'parent'   => 'label',
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
                    'postfix'  => 'em',
                    'title'    => __( 'Badge Scale', 'mobile-contact-bar' ),
                    'desc'     => __( 'Transforms the badge size according to the scaling value.', 'mobile-contact-bar' ),
                ],
                'background_color' => [
                    'type'     => 'color-picker',
                    'default'  => '#ff8e00',
                    'title'    => __( 'Background Color', 'mobile-contact-bar' ),
                ],
                'font_color' => [
                    'type'     => 'color-picker',
                    'default'  => '#252832',
                    'title'    => __( 'Font Color', 'mobile-contact-bar' ),
                ],
            ],
        ];
    }


    /**
     * Filters out the default values from the setting fields.
     *
     * @return array Default settings
     */
    public function fields_defaults()
    {
        $defaults = [];
        $fields = $this->fields();

        foreach ( $fields as $section_id => $section )
        {
            foreach ( $section as $field_id => $field )
            {
                if ( isset( $field['default'] ))
                {
                    $defaults[$section_id][$field_id] = $field['default'];
                }
                elseif ( isset( $field['options']))
                {
                    foreach ( $field['options'] as $option_id => $option )
                    {
                        $defaults[$section_id][$field_id][$option_id] = $option['default'];
                    }
                }
            }
        }

        return $defaults;
    }


    /**
     * Sanitizes the settings part of the bar-option.
     * 
     * @param  array $settings The array of settings to be sanitized
     * @return array           Sanitized settings
     */
    public function sanitize( $settings = [] )
    {
        $sanitized_settings = [];
        $input_fields = $this->fields();

        foreach ( $input_fields as $section_id => $section )
        {
            // Fill up with unused sections
            if ( ! isset( $settings[$section_id] ))
            {
                $sanitized_settings[$section_id] = [];
                foreach ( $section as $field_id => $field )
                {
                    $sanitized_settings[$section_id][$field_id] = $field['default'];
                }
            }

            foreach ( $section as $field_id => $field )
            {
                $value = ( isset( $settings[$section_id], $settings[$section_id][$field_id] ))
                    ? $settings[$section_id][$field_id]
                    : null;

                switch ( $field['type'] )
                {
                    case 'select':
                    case 'radio':
                        $sanitized_settings[$section_id][$field_id] =
                            ( in_array( $value, array_keys( $field['options'] )))
                            ? $value
                            : $field['default'];
                        break;

                    case 'color-picker':
                        $sanitized_settings[$section_id][$field_id] = $value === '' ? 'transparent' : $this->sanitize_color( $value );
                        break;

                    case 'checkbox':
                        $value = (int) $value;
                        $sanitized_settings[$section_id][$field_id] =
                            ( 0 === $value || 1 === $value )
                            ? $value
                            : $field['default'];
                        break;

                    case 'checkbox-group':
                        foreach( $field['options'] as $option_id => $option )
                        {
                            $value =
                                isset( $settings[$section_id], $settings[$section_id][$field_id], $settings[$section_id][$field_id][$option_id] )
                                ? $settings[$section_id][$field_id][$option_id]
                                : null;

                            $value = (int) $value;
                            $sanitized_settings[$section_id][$field_id][$option_id] =
                                ( 0 === $value || 1 === $value )
                                ? $value
                                : $option['default'];
                        }
                        break;

                    case 'number':
                        $value = (int) $value;
                        $sanitized_settings[$section_id][$field_id] =
                            (( isset( $field['min'] ) && $value < $field['min'] ) || ( isset( $field['max'] ) && $value > $field['max'] ))
                            ? $field['default']
                            : $value;
                        break;

                    case 'text':
                        $sanitized_settings[$section_id][$field_id] = sanitize_text_field( $value );
                        break;

                    case 'slider':
                        $value = (float) $value;
                        $sanitized_settings[$section_id][$field_id] =
                            ( $field['min'] <= $value || $value <= $field['max'] )
                            ? $value
                            : $field['default'];
                        break;
                }
                
            }
        }

        return $sanitized_settings;
    }


    /**
     * Verifies that a color code is valid.
     *
     * @param  string      $color Color code (Hex or RGBA)
     * @return bool|string        Either false or the valid color code
     */
    public function is_color( $color )
    {
        $color = $this->sanitize_color( $color );

        return $color ? $color : false;
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
        if ( preg_match( '|^#([A-Fa-f0-9]{3}){1,2}$|', $hex_color ))
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
