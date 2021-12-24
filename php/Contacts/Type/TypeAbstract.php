<?php

namespace MobileContactBar\Contacts\Type;

use ReflectionClass;


abstract class TypeAbstract
{
    protected $type = '';


    public function __construct()
    {
        $this->type = strtolower(( new ReflectionClass( $this ))->getShortName());
    }


    protected function contact()
    {
        return [];
    }
}
