<?php

namespace MobileContactBar\Contacts\Type;

use MobileContactBar\Contacts\Input;

final class Skype extends TypeAbstract
{
    private $icon = 'fab fa-skype';


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
            'icon'        => $this->icon,
            'label'       => 'Skype',
            'uri'         => 'https://api.whatsapp.com/send',
            'placeholder' => 'skype:username?chat or skype:username?call',
            'palette'     => abmcb( Input::class )->palette_defaults(),
            'short_desc'  => __( 'short desc', 'mobile-contact-bar' ),
            'long_desc'   => __( 'Use skype:username?call or skype:username?chat', 'mobile-contact-bar' ),
        ];
    }
}
