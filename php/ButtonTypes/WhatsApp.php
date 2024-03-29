<?php

namespace MobileContactBar\ButtonTypes;


final class WhatsApp extends Button
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
            'title'       => 'WhatsApp',
            'checked'     => 0,
            'brand'       => 'fa',
            'group'       => 'brands',
            'icon'        => 'whatsapp',
            'label'       => 'WhatsApp',
            'text'        => 'WhatsApp',
            'uri'         => 'https://api.whatsapp.com/send',
            'placeholder' => 'place',
            'query'       => [
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
            'custom'      => self::default_customization(),
            'desc_type'   => __( 'type desc', 'mobile-contact-bar' ),
            'desc_uri'    => __( 'URI desc', 'mobile-contact-bar' ),
        ];
    }
}
