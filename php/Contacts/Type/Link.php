<?php

namespace MobileContactBar\Contacts\Type;

use MobileContactBar\Contacts\Input;


final class Link extends TypeAbstract
{
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
            'brand'       => 'fa',
            'icon'        => 'solid external-link-square-alt',
            'label'       => '',
            'uri'         => '',
            'placeholder' => 'http(s)://www.somesite.com/path',
            'parameters'  => [],
            'custom'      => abmcb( Input::class )->default_customization(),
            'desc_type'   => __( 'type desc', 'mobile-contact-bar' ),
            'desc_uri'    => __( 'URI desc', 'mobile-contact-bar' ),
        ];
    }
}

