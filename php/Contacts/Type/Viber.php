<?php

namespace MobileContactBar\Contacts\Type;

use MobileContactBar\Contacts\Input;


final class Viber extends TypeAbstract
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
            'uri'         => 'viber://pa?chatURI=URI',
            'placeholder' => 'place',
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
            'custom'      => abmcb( Input::class )->default_customization(),
            'desc_type'   => __( 'type desc', 'mobile-contact-bar' ),
            'desc_uri'    => __( 'URI desc', 'mobile-contact-bar' ),
        ];
    }
}
