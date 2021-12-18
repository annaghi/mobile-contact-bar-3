<?php

namespace MobileContactBar\Contacts\Type;

use MobileContactBar\Contacts\Input;

final class Link extends TypeAbstract
{
    private $icon = 'fas fa-external-link-square-alt';


    public function __construct()
    {
        parent::__construct();
    }

    
    public function contact()
    {
        return [
            'type'        => $this->type,
            'title'       => __( 'Link', 'mobile-contact-bar' ),
            'checked'     => 0,
            'icon'        => $this->icon,
            'label'       => '',
            'uri'         => '',
            'placeholder' => 'http(s)://www.somesite.com/path',
            'parameters'  => [
                [
                    'field'       => 'text',
                    'key'         => '',
                    'value'       => '',
                    'placeholder' => '',
                ],
            ],
            'palette'     => abmcb( Input::class )->palette_defaults(),
            'short_desc'  => __( 'short desc', 'mobile-contact-bar' ),
            'long_desc'   => __( 'long desc', 'mobile-contact-bar' ),
        ];
    }
}

