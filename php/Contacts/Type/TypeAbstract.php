<?php

namespace MobileContactBar\Contacts\Type;

use MobileContactBar\Helper;
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


    public function keys()
    {
        $keys = Helper::array_keys_recursive( $this->contact() );
        unset( $keys['title'] );
        unset( $keys['placeholder'] );
        unset( $keys['desc_type'] );
        unset( $keys['desc_uri'] );

        if ( isset( $keys['parameters'] ))
        {
            $keys['parameters'] = array_map(
                function( &$parameter )
                {
                    unset( $parameter['field'] );
                    unset( $parameter['placeholder'] );
                    return $parameter;
                },
                $keys['parameters']
            );
            unset( $parameter );
        }

        return $keys;
    }
}
