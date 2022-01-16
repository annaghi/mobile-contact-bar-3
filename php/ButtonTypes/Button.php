<?php

namespace MobileContactBar\ButtonTypes;

use MobileContactBar\Helper;
use ReflectionClass;


abstract class Button
{
    protected $type = '';


    public function __construct()
    {
        $this->type = strtolower(( new ReflectionClass( $this ))->getShortName());
    }


    protected function field()
    {
        return [];
    }


    public function keys()
    {
        $keys = Helper::array_keys_recursive( $this->field() );
        unset( $keys['title'] );
        unset( $keys['placeholder'] );
        unset( $keys['desc_type'] );
        unset( $keys['desc_uri'] );

        if ( isset( $keys['query'] ) && is_array( $keys['query'] ))
        {
            $keys['query'] = array_map(
                function ( $parameter )
                {
                    $parameter_keys = $parameter;
                    unset( $parameter_keys['field'] );
                    unset( $parameter_keys['placeholder'] );
                    return $parameter_keys;
                },
                $keys['query']
            );
        }

        return $keys;
    }


    public function badge()
    {
        return '';
    }


    public function script()
    {
        return '';
    }


    /**
     * Defines the input fields for contact['custom'].
     *
     * @return array Multidimensional array
     */
    public static function custom_fields()
    {
        $primary_color = __( 'primary', 'mobile-contact-bar' );
        $secondary_color = __( 'secondary', 'mobile-contact-bar' );

        return
        [
            'background_color' => [
                'type'      => 'color-picker-group',
                'title'     => __( 'Background Color', 'mobile-contact-bar' ),
                'options'   => [
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
            'icon_color' => [
                'type'      => 'color-picker-group',
                'title'     => __( 'Icon Color', 'mobile-contact-bar' ),
                'options'   => [
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
            'label_color' => [
                'type'      => 'color-picker-group',
                'title'     => __( 'Label Color', 'mobile-contact-bar' ),
                'options'   => [
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
            'border_color' => [
                'type'      => 'color-picker-group',
                'title'     => __( 'Border Color', 'mobile-contact-bar' ),
                'options'   => [
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
        ]; 
    }


    /**
     * Retrieves the contact['custom'] with default values.
     *
     * @return array
     */
    public static function default_customization()
    {
        $defaults = [];
        $custom_fields = self::custom_fields();

        foreach ( $custom_fields as $custom_key => $custom )
        {
            foreach ( $custom['options'] as $option_key => $option )
            {
                if ( isset( $option['default'] ))
                {
                    $defaults[$custom_key][$option_key] = $option['default'];
                }
            }
        }

        return $defaults;
    }


    /**
     * Retrieves the contact['custom'] with empty default values.
     *
     * @return array
     */
    public static function empty_default_customization()
    {
        $defaults = [];
        $custom_fields = self::custom_fields();

        foreach ( $custom_fields as $custom_key => $custom )
        {
            foreach ( $custom['options'] as $option_key => $option )
            {
                if ( isset( $option['default'] ))
                {
                    $defaults[$custom_key][$option_key] = '';
                }
            }
        }

        return $defaults;
    }
}
