<?php

namespace MobileContactBar\Contacts\Type;

use MobileContactBar\Contacts\Input;

final class Email extends TypeAbstract
{
    private $icon = 'far fa-envelope';


    public function __construct()
    {
        parent::__construct();
    }


    public function contact()
    {
        return [
            'type'        => $this->type,
            'title'       => __( 'Email', 'mobile-contact-bar' ),
            'checked'     => 0,
            'icon'        => $this->icon,
            'label'       => __( 'Email', 'mobile-contact-bar' ),
            'uri'         => '',
            'placeholder' => 'mailto:username@example.com',
            'parameters'  => [
                [
                    'field'       => 'text',
                    'key'         => 'subject',
                    'value'       => '',
                    'placeholder' => __( 'Subject ...', 'mobile-contact-bar' ),
                ],
                [
                    'field'       => 'textarea',
                    'key'         => 'body',
                    'value'       => '',
                    'placeholder' => __( 'Text ...', 'mobile-contact-bar' ),
                ],
                [
                    'field'       => 'email',
                    'key'         => 'cc',
                    'value'       => '',
                    'placeholder' => __( 'example@domain.com', 'mobile-contact-bar' ),
                ],
                [
                    'field'       => 'email',
                    'key'         => 'bcc',
                    'value'       => '',
                    'placeholder' => __( 'example1@domain.com,example2@domain.net', 'mobile-contact-bar' ),
                ],
            ],
            'palette'     => abmcb( Input::class )->palette_defaults(),
            'desc_type'   => __( 'type desc', 'mobile-contact-bar' ),
            'desc_uri'    => __( 'URI desc', 'mobile-contact-bar' ),
        ];
    }
}
