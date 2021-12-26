<?php

namespace MobileContactBar\Contacts\Type;

use MobileContactBar\Contacts\Input;


final class WhatsApp extends TypeAbstract
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
            'title'       => 'WhatsApp',
            'checked'     => 0,
            'brand'       => 'fa',
            'group'       => 'brands',
            'icon'        => 'whatsapp',
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
            'custom'      => abmcb( Input::class )->default_customization(),
            'desc_type'   => __( 'type desc', 'mobile-contact-bar' ),
            'desc_uri'    => __( 'URI desc', 'mobile-contact-bar' ),
        ];
    }
}
