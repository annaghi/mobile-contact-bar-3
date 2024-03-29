<?php

namespace MobileContactBar\ButtonTypes;


final class Email extends Button
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
            'title'       => __( 'Email', 'mobile-contact-bar' ),
            'checked'     => 0,
            'brand'       => 'fa',
            'group'       => 'regular',
            'icon'        => 'envelope',
            'label'       => __( 'Email', 'mobile-contact-bar' ),
            'text'        => __( 'Send email', 'mobile-contact-bar' ),
            'uri'         => '',
            'placeholder' => 'mailto:username@example.com',
            'query'       => [
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
            'custom'      => self::default_customization(),
            'desc_type'   => __( 'type desc', 'mobile-contact-bar' ),
            'desc_uri'    => __( 'URI desc', 'mobile-contact-bar' ),
        ];
    }
}
