<?php

namespace MobileContactBar\ContactTypes;

use MobileContactBar\Helper;
use ReflectionClass;


abstract class ContactType
{
    protected $type = '';


    public function __construct()
    {
        $this->type = strtolower(( new ReflectionClass( $this ))->getShortName());
    }


    protected function contact()
    {
        return [];
    }


    public function keys()
    {
        $keys = Helper::array_keys_recursive( $this->contact() );
        unset( $keys['title'] );
        unset( $keys['placeholder'] );
        unset( $keys['desc_type'] );
        unset( $keys['desc_uri'] );

        if ( isset( $keys['parameters'] ))
        {
            $keys['parameters'] = array_map(
                function( &$parameter )
                {
                    unset( $parameter['field'] );
                    unset( $parameter['placeholder'] );
                    return $parameter;
                },
                $keys['parameters']
            );
            unset( $parameter );
        }

        return $keys;
    }


    /**
     * Defines the input fields for custom settings fields.
     *
     * @return array Multidimensional array
     */
    public static function custom_input_fields()
    {
        return
        [
            'background_color' => [
                'title'     => __( 'Background Color', 'mobile-contact-bar' ),
                'primary'   => [
                    'type'    => 'color-picker',
                    'default' => '',
                ],
                'secondary' => [
                    'type'    => 'color-picker',
                    'default' => '',
                ]
            ],
            'icon_color' => [
                'title'     => __( 'Icon Color', 'mobile-contact-bar' ),
                'primary'   => [
                    'type'    => 'color-picker',
                    'default' => '',
                ],
                'secondary' => [
                    'type'    => 'color-picker',
                    'default' => '',
                ]
            ],
            'label_color' => [
                'title'     => __( 'Label Color', 'mobile-contact-bar' ),
                'primary'   => [
                    'type'    => 'color-picker',
                    'default' => '',
                ],
                'secondary' => [
                    'type'    => 'color-picker',
                    'default' => '',
                ]
            ],
            'border_color' => [
                'title'     => __( 'Border Color', 'mobile-contact-bar' ),
                'primary'   => [
                    'type'    => 'color-picker',
                    'default' => '',
                ],
                'secondary' => [
                    'type'    => 'color-picker',
                    'default' => '',
                ]
            ],
        ]; 
    }



    /**
     * Retrieves the custom settings fields with default values.
     *
     * @return array
     */
    public static function default_customization()
    {
        $defaults = [];
        $input_fields = self::custom_input_fields();

        foreach ( $input_fields as $custom_key => $custom )
        {
            foreach ( $custom as $field_key => $field )
            {
                if ( isset( $field['default'] ))
                {
                    $defaults[$custom_key][$field_key] = $field['default'];
                }
            }
        }

        return $defaults;
    }


    /**
     * Retrieves the custom settings fields with empty default values.
     *
     * @return array
     */
    public static function empty_default_customization()
    {
        $defaults = [];
        $input_fields = self::custom_input_fields();

        foreach ( $input_fields as $custom_key => $custom )
        {
            foreach ( $custom as $field_key => $field )
            {
                if ( isset( $field['default'] ))
                {
                    $defaults[$custom_key][$field_key] = '';
                }
            }
        }

        return $defaults;
    }
}
