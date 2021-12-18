<?php

namespace MobileContactBar\Contacts\Type;

use ReflectionClass;

abstract class TypeAbstract
{
    protected $type = '';


    public function __construct()
    {
        $this->type = strtolower(( new ReflectionClass( $this ))->getShortName());
        add_filter( 'mcb_admin_contact_types', [$this, 'mcb_admin_contact_types'] );
    }


    protected function contact()
    {
        return [];
    }


    public function mcb_admin_contact_types( $contacts )
    {
        $contacts[$this->type] = $this->contact();
        return $contacts;
    }
}
