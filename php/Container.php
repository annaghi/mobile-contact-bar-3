<?php

namespace MobileContactBar;

use Exception;
use ReflectionClass;
use ReflectionException;
use ReflectionParameter;


abstract class Container
{
    protected $instances = [];


    public function make( $class )
    {
        if ( isset( $this->instances[$class] ))
        {
            return $this->instances[$class];
        }

        $this->instances[$class] = $this->resolve( $class );
        return $this->instances[$class];
    }


    private function resolve( $class )
    {
        $reflector = new ReflectionClass( $class );

        if ( ! $reflector->isInstantiable())
        {
            throw new Exception( "[$class] is not instantiable" );
        }

        $constructor = $reflector->getConstructor();

        if ( is_null( $constructor ))
        {
            return new $class;
        }

        $parameters = $constructor->getParameters();
        $dependencies = $this->get_dependencies( $parameters );

        return $reflector->newInstanceArgs( $dependencies );
    }


    private function get_dependencies( $parameters )
    {
        $dependencies = [];

        foreach ( $parameters as $parameter )
        {
            $dependency = $this->get_class( $parameter );

            if ( is_null( $dependency ))
            {
                $dependencies[] = $this->resolve_non_class( $parameter );
            }
            else
            {
                $dependencies[] = $this->resolve( $dependency->name );
            }
        }
        return $dependencies;
    }


    private function resolve_non_class( ReflectionParameter $parameter )
    {
        if ( $parameter->isDefaultValueAvailable())
        {
            return $parameter->getDefaultValue();
        }

        throw new Exception( 'Erm.. Cannot resolve the unknown!?' );
    }

    
    private function get_class( $parameter )
    {
        if (version_compare( phpversion(), '8', '<' ))
        {
            return $parameter->getClass(); // @compat PHP < 8
        }
        return $parameter->getType();
    }
}
