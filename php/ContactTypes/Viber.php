<?php

namespace MobileContactBar\ContactTypes;


final class Viber extends ContactType
{
    public function __construct()
    {
        parent::__construct();
    }


    public function contact()
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
            'parameters'  => [
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
