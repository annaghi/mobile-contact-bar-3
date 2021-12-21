<?php

namespace MobileContactBar\Contacts\Type;

use MobileContactBar\Contacts\Input;


final class Skype extends TypeAbstract
{
    public function __construct()
    {
        parent::__construct();
    }


    public function contact()
    {
        return [
            'type'        => $this->type,
            'title'       => 'Skype',
            'checked'     => 0,
            'brand'       => 'fa',
            'icon'        => 'brands skype',
            'label'       => 'Skype',
            'uri'         => '',
            'placeholder' => 'skype:username?chat or skype:username?call',
            'custom'      => abmcb( Input::class )->default_customization(),
            'desc_type'   => __( 'type desc', 'mobile-contact-bar' ),
            'desc_uri'    => __( 'Use skype:username?call or skype:username?chat', 'mobile-contact-bar' ),
        ];
    }
}
