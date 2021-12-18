<?php

namespace MobileContactBar\Contacts\Type;

use MobileContactBar\Contacts\Input;

final class Viber extends TypeAbstract
{
    private $icon = 'fab fa-viber';


    public function __construct()
    {
        parent::__construct();
    }


    public function contact()
    {
        return [
            'type'        => $this->type,
            'title'       => 'Viber',
            'checked'     => 0,
            'icon'        => $this->icon,
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
            'palette'     => abmcb( Input::class )->palette_defaults(),
            'short_desc'  => __( 'short desc', 'mobile-contact-bar' ),
            'long_desc'   => __( 'long desc', 'mobile-contact-bar' ),
        ];
    }
}
