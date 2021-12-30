<?php

namespace MobileContactBar\ContactTypes;


final class Skype extends ContactType
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
            'title'       => 'Skype',
            'checked'     => 0,
            'brand'       => 'fa',
            'group'       => 'brands',
            'icon'        => 'skype',
            'label'       => 'Skype',
            'text'        => 'Skype',
            'uri'         => '',
            'placeholder' => 'skype:username?chat or skype:username?call',
            'custom'      => self::default_customization(),
            'desc_type'   => __( 'type desc', 'mobile-contact-bar' ),
            'desc_uri'    => __( 'Use skype:username?call or skype:username?chat', 'mobile-contact-bar' ),
        ];
    }
}
