<?php

namespace MobileContactBar\Contacts\Type;

use MobileContactBar\Contacts\Input;


final class SMS extends TypeAbstract
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
            'title'       => 'SMS',
            'checked'     => 0,
            'brand'       => 'fa',
            'group'       => 'solid',
            'icon'        => 'sms',
            'label'       => __( 'Phone Number for texting', 'mobile-contact-bar' ),
            'uri'         => '',
            'placeholder' => 'sms:15417543010 or sms:+15417543010',
            'parameters'  => [
                [
                    'field'       => 'text',
                    'key'         => 'body',
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
