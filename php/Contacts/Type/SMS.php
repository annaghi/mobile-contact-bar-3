<?php

namespace MobileContactBar\Contacts\Type;

use MobileContactBar\Contacts\Input;

final class SMS extends TypeAbstract
{
    private $icon = 'far fa-comment';


    public function __construct()
    {
        parent::__construct();
    }


    public function contact()
    {
        return [
            'type'        => $this->type,
            'title'       => 'SMS',
            'checked'     => 0,
            'icon'        => $this->icon,
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
            'palette'     => abmcb( Input::class )->palette_defaults(),
            'desc_type'   => __( 'type desc', 'mobile-contact-bar' ),
            'desc_uri'    => __( 'URI desc', 'mobile-contact-bar' ),
        ];
    }
}
