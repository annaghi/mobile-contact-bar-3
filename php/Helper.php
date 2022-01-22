<?php

namespace MobileContactBar;

use ReflectionObject;


final class Helper
{
    /**
     * @param  array|string $name
     * @param  string       $path
     * @return string
     */
    public static function build_class_name( $name, $path = '')
    {
        if ( is_array( $name ))
        {
            $name = implode( '-', $name );
        }

        $path = ltrim( str_replace( __NAMESPACE__, '', $path ), '\\' );

        return ! empty( $path )
            ? __NAMESPACE__ . '\\' . $path . '\\' . $name
            : $name;
    }


    /**
     * @param  array $array
     * @return array
     */
    public static function array_keys_recursive( $array )
    {
        foreach ( $array as $key => $value )
        {
            if ( is_array( $value ))
            {
                $keys[$key] = self::array_keys_recursive( $value );
            }
            else
            {
                $keys[$key] = $key;
            }
        }

        return isset( $keys ) ? $keys : [];
    }


    /**
     * @param  array $array1
     * @param  array $array2
     * @return array         Values from $array1
     */
    public static function array_intersect_key_recursive( $array1, $array2 )
    {
        $array1 = array_intersect_key( $array1, $array2 );
        foreach ( $array1 as $key1 => &$value1 )
        {
            if ( is_array( $value1 ) && is_array( $array2[$key1] ))
            {
                $value1 = self::array_intersect_key_recursive( $value1, $array2[$key1] );
            }
        }
        return $array1;
    }


    /**
     * @param  array $array1
     * @param  array $array2
     * @return array         keys $array1 - keys $array2
     */
    public static function array_minus_key_recursive( $array1, $array2 )
    {
        foreach ( $array1 as $key => $value )
        {
            if ( is_array( $value ))
            {
                if ( ! isset( $array2[$key] ))
                {
                    $diff[$key] = $value;
                }
                elseif ( ! is_array( $array2[$key] ))
                {
                    $diff[$key] = $value;
                }
                else
                {
                    $new_diff = self::array_minus_key_recursive( $value, $array2[$key] );
                    if ( ! empty( $new_diff ))
                    {
                        $diff[$key] = $new_diff;
                    }
                }
            }
            elseif ( ! isset( $array2[$key] ))
            {
                $diff[$key] = $value;
            }
        }

        return ( isset( $diff ) && is_array( $diff ))
            ? $diff
            : [];
    }

    
    /**
     * Search a multidimensional array by key.
     * 
     * @param  mixed       $needle
     * @param  array       $haystack
     * @param  int|string  $key
     * @return array|false
     */
    public static function array_search_by_key( $needle, $haystack, $key )
    {
        if ( ! is_array( $haystack ) || array_diff_key( $haystack, array_filter( $haystack, 'is_array' )))
        {
            return false;
        }

        $index = array_search( $needle, wp_list_pluck( $haystack, $key ));

        return ( false === $index ) ? false : $haystack[$index];
    }


    /**
     * @param  array  $array
     * @param  bool   $is_flatten_value
     * @param  string $prefix
     * @return array
     */
    public static function array_flatten( $array, $is_flatten_value = false, $prefix = '' )
    {
        $result = [];

        foreach ( $array as $key => $value )
        {
            $new_key = ltrim( $prefix . '.' . $key, '.' );

            if ( static::is_indexed_and_flat( $value ))
            {
                $value = $is_flatten_value ? $value : '[' . implode( ', ', $value ) . ']';
            }
            elseif ( is_array( $value ))
            {
                $result = array_merge( $result, static::array_flatten( $value, $is_flatten_value, $new_key ));
                continue;
            }
            $result[$new_key] = $value;
        }

        return $result;
    }
    

    /**
     * @param  mixed $array
     * @return bool
     */
    public static function is_indexed_and_flat( $array )
    {
        if ( ! is_array( $array ) || array_filter( $array, 'is_array' ))
        {
            return false;
        }
        return wp_is_numeric_array( $array );
    }


    /**
     * Get a value from an array of values using a dot-notation path as reference.
     * 
     * @param  mixed      $data
     * @param  string|int $path
     * @param  mixed      $fallback
     * @return mixed
     */
    public static function array_get( $data, $path = '', $fallback = '' )
    {
        $result = $fallback;

        $data = static::array_consolidate( $data );
        $keys = explode( '.', $path );

        foreach ( $keys as $key )
        {
            if ( ! isset( $data[$key] ))
            {
                return $fallback;
            }

            if ( is_object( $data[$key] ))
            {
                $result = $data[$key];
                $data = static::array_consolidate( $result );
                continue;
            }

            $result = $data[$key];
            $data = $result;
        }
        return $result;
    }


    /**
     * Returns an empty array by default if value is scalar.
     * 
     * @param  mixed $value
     * @param  array $fallback
     * @return array
     */
    public static function array_consolidate( $value, array $fallback = [] )
    {
        if ( is_object( $value ))
        {
            $values = get_object_vars( $value );
            $value = static::if_empty( $values, (array) $value, $is_strict = true );
        }
        return is_array( $value ) ? $value : $fallback;
    }


    /**
     * @param  mixed $value
     * @param  mixed $fallback
     * @param  bool  $is_strict
     * @return mixed
     */
    public static function if_empty( $value, $fallback, $is_strict = false )
    {
        $is_empty = $is_strict ? static::is_empty( $value ) : empty( $value );
        return $is_empty ? $fallback : $value;
    }


    /**
     * @param  mixed $value
     * @return bool
     */
    public static function is_empty( $value )
    {
        if ( is_string( $value ))
        {
            return trim( $value ) === '';
        }
        return ! is_numeric( $value ) && ! is_bool( $value ) && empty( $value );
    }


    /**
     * @param  mixed $value
     * @return bool
     */
    public static function is_not_empty( $value )
    {
        return ! static::is_empty( $value );
    }


    /**
     * @param  string|string[] $needles
     * @param  string          $haystack
     * @return bool
     */
    public static function str_contains( $needles, $haystack )
    {
        $needles = array_filter( static::to_array( $needles ), static::class . '::is_not_empty' );
        foreach ( $needles as $needle )
        {
            if ( false !== strpos( $haystack, $needle ))
            {
                return true;
            }
        }
        return false;
    }


    /**
     * @param  mixed $value
     * @param  bool  $is_exploce
     * @return array
     */
    public static function to_array( $value, $is_explode = true )
    {
        if ( is_object( $value ))
        {
            $reflection = new ReflectionObject( $value );
            $properties = $reflection->hasMethod( 'toArray' )
                ? $value->toArray()
                : get_object_vars( $value );
            return json_decode( json_encode( $properties ), true );
        }

        if ( is_scalar( $value ) && $is_explode )
        {
            return static::from_string( $value );
        }

        return (array) $value;
    }


    /**
     * @param  mixed $array
     * @return array
     */
    public static function reindex( $array )
    {
        return static::is_indexed_and_flat($array) ? array_values( $array ) : $array;
    }


    /**
     * @param  mixed  $value
     * @param  bool   $is_strict
     * @return string
     */
    public static function to_string( $value, $is_strict = true )
    {
        if ( is_object( $value ) && in_array( '__toString', get_class_methods( $value )))
        {
            return (string) $value->__toString();
        }

        if ( static::is_empty( $value ))
        {
            return '';
        }

        if ( static::is_indexed_and_flat( $value ))
        {
            return implode( ', ', $value );
        }

        if ( ! is_scalar( $value ))
        {
            return $is_strict ? '' : serialize( $value );
        }

        return (string) $value;
    }


    /**
     * @param  mixed $value
     * @param  mixed $callback
     * @return array
     */
    public static function from_string( $value, $callback = null )
    {
        if ( is_scalar( $value ))
        {
            $value = array_map( 'trim', explode( ',', static::to_string( $value )));
        }

        $callback = static::if_empty( static::to_string( $callback ), static::class . '::is_not_empty' );
        return static::reindex( array_filter( (array) $value, $callback ));
    }


    /**
     * @param  string|string[] $needles
     * @param  string          $haystack
     * @return bool
     */
    public static function str_ends_with( $needles, $haystack )
    {
        $needles = array_filter( static::to_array( $needles ), static::class . '::is_not_empty' );

        foreach ( $needles as $needle )
        {
            if ( substr( $haystack, -strlen( static::to_string( $needle ))) === $needle )
            {
                return true;
            }
        }
        return false;
    }
}
