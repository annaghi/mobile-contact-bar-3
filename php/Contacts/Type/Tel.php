<?php

namespace MobileContactBar\Contacts\Type;

use MobileContactBar\Contacts\Input;

final class Tel extends TypeAbstract
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
            'title'       => __( 'Phone', 'mobile-contact-bar' ),
            'checked'     => 0,
            'icon'        => $this->icon,
            'label'       => __( 'Call Us', 'mobile-contact-bar' ),
            'uri'         => '',
            'placeholder' => 'tel:15417543010 or tel:+15417543010',
            'palette'     => abmcb( Input::class )->palette_defaults(),
            'short_desc'  => __( 'short desc', 'mobile-contact-bar' ),
            'long_desc'   => __( 'long desc', 'mobile-contact-bar' ),
        ];
    }
}

