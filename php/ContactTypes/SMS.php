<?php

namespace MobileContactBar\ContactTypes;


final class SMS extends ContactType
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
            'title'       => 'SMS',
            'checked'     => 0,
            'brand'       => 'fa',
            'group'       => 'solid',
            'icon'        => 'sms',
            'label'       => 'SMS',
            'text'        => __( 'Send SMS text', 'mobile-contact-bar' ),
            'uri'         => '',
            'placeholder' => 'sms:15417543010 or sms:+15417543010',
            'query'       => [
                [
                    'field'       => 'text',
                    'key'         => 'body',
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
