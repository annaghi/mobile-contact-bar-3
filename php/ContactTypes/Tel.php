<?php

namespace MobileContactBar\ContactTypes;


final class Tel extends ContactType
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
            'title'       => __( 'Phone', 'mobile-contact-bar' ),
            'checked'     => 0,
            'brand'       => 'fa',
            'group'       => 'solid',
            'icon'        => 'phone',
            'label'       => __( 'Call Us', 'mobile-contact-bar' ),
            'uri'         => '',
            'placeholder' => 'tel:15417543010 or tel:+15417543010',
            'custom'      => self::default_customization(),
            'desc_type'   => __( 'type desc', 'mobile-contact-bar' ),
            'desc_uri'    => __( 'URI desc', 'mobile-contact-bar' ),
        ];
    }
}

