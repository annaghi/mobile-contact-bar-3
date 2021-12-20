<?php

namespace MobileContactBar\Contacts\Type;

use MobileContactBar\Contacts\Input;


final class Tel extends TypeAbstract
{
    public function __construct()
    {
        parent::__construct();
    }

    
    public function contact()
    {
        return [
            'type'        => $this->type,
            'title'       => __( 'Phone', 'mobile-contact-bar' ),
            'checked'     => 0,
            'brand'       => 'fa',
            'icon'        => 'solid phone',
            'label'       => __( 'Call Us', 'mobile-contact-bar' ),
            'uri'         => '',
            'placeholder' => 'tel:15417543010 or tel:+15417543010',
            'palette'     => abmcb( Input::class )->palette_defaults(),
            'desc_type'   => __( 'type desc', 'mobile-contact-bar' ),
            'desc_uri'    => __( 'URI desc', 'mobile-contact-bar' ),
        ];
    }
}

