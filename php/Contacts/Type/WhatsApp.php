<?php

namespace MobileContactBar\Contacts\Type;

use MobileContactBar\Contacts\Input;

final class WhatsApp extends TypeAbstract
{
    private $icon = 'fab fa-whatsapp';


    public function __construct()
    {
        parent::__construct();
    }


    public function contact()
    {
        return [
            'type'        => $this->type,
            'title'       => 'WhatsApp',
            'checked'     => 0,
            'icon'        => $this->icon,
            'label'       => 'WhatsApp',
            'uri'         => 'https://api.whatsapp.com/send',
            'placeholder' => 'place',
            'parameters'  => [
                [
                    'field'       => 'text',
                    'key'         => 'phone',
                    'value'       => '',
                    'placeholder' => '15417543010',
                ],
                [
                    'field'       => 'text',
                    'key'         => 'text',
                    'value'       => '',
                    'placeholder' => __( 'Message ...', 'mobile-contact-bar' ),
                ],
            ],
            'palette'     => abmcb( Input::class )->palette_defaults(),
            'short_desc'  => __( 'short desc', 'mobile-contact-bar' ),
            'long_desc'   => __( 'long desc', 'mobile-contact-bar' ),
        ];
    }
}
