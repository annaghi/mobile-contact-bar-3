<?php

namespace MobileContactBar\ButtonTypes;


final class Viber extends Button
{
    public function __construct()
    {
        parent::__construct();
    }


    public function field()
    {
        return [
            'type'        => $this->type,
            'id'          => '',
            'title'       => 'Viber',
            'checked'     => 0,
            'brand'       => 'fa',
            'group'       => 'brands',
            'icon'        => 'viber',
            'label'       => 'Viber',
            'text'        => 'Viber',
            'uri'         => 'viber://pa?chatURI=your_URI',
            'placeholder' => 'viber://pa?chatURI=your_URI',
            'query'       => [
                [
                    'field'       => 'text',
                    'key'         => 'context',
                    'value'       => '',
                    'placeholder' => __( 'Context ...', 'mobile-contact-bar' ),
                ],
                [
                    'field'       => 'text',
                    'key'         => 'text',
                    'value'       => '',
                    'placeholder' => __( 'Text ...', 'mobile-contact-bar' ),
                ],
            ],
            'custom'      => self::default_customization(),
            'desc_type'   => __( 'type desc', 'mobile-contact-bar' ),
            'desc_uri'    => __( 'URI desc', 'mobile-contact-bar' ),
        ];
    }
}
