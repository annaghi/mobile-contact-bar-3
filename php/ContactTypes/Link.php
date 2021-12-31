<?php

namespace MobileContactBar\ContactTypes;


final class Link extends ContactType
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
            'title'       => __( 'Link', 'mobile-contact-bar' ),
            'checked'     => 0,
            'brand'       => 'fa',
            'group'       => 'solid',
            'icon'        => 'external-link-square-alt',
            'label'       => '',
            'text'        => __( 'Go to this link', 'mobile-contact-bar' ),
            'uri'         => '',
            'placeholder' => 'http(s)://www.somesite.com/path',
            'parameters'  => [],
            'custom'      => self::default_customization(),
            'desc_type'   => __( 'type desc', 'mobile-contact-bar' ),
            'desc_uri'    => __( 'URI desc', 'mobile-contact-bar' ),
        ];
    }
}

