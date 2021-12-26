<?php

namespace MobileContactBar;

use Exception;
use ReflectionClass;
use ReflectionException;
use ReflectionParameter;


abstract class Container
{
    /**
     * @var array
     */
    protected $instances = [];


    /**
     * @param  mixed $class
     * @return mixed
     */
    public function make( $class )
    {
        if ( isset( $this->instances[$class] ))
        {
            return $this->instances[$class];
        }

        $this->instances[$class] = $this->resolve( $class );
        return $this->instances[$class];
    }


    /**
     * @param  string $class
     * @return mixed
     * @throws Exception
     */
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


    /**
     * @param ReflectionParameter[] $parameters
     * @return array
     */
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


    /**
     * @param  ReflectionParameter $parameter
     * @return mixed
     * @throws Exception
     */
    private function resolve_non_class( ReflectionParameter $parameter )
    {
        if ( $parameter->isDefaultValueAvailable())
        {
            return $parameter->getDefaultValue();
        }

        throw new Exception( 'Cannot resolve the unknown!?' );
    }

    
    /**
     * @param  ReflectionParameter $parameter
     * @return null|ReflectionClass|ReflectionNamedType|ReflectionType
     */
    private function get_class( $parameter )
    {
        if ( version_compare( PHP_VERSION, '8', '<' ))
        {
            return $parameter->getClass();
        }
        return $parameter->getType();
    }
}
