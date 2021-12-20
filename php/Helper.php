<?php

namespace MobileContactBar;


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


    public static function array_diff_assoc_recursive( $array1, $array2 )
    {
        foreach ( $array1 as $key => $value )
        {
            if ( is_array( $value ))
            {
                if ( ! isset( $array2[$key] ))
                {
                    $difference[$key] = $value;
                }
                elseif ( ! is_array( $array2[$key] ))
                {
                    $difference[$key] = $value;
                }
                else
                {
                    $new_diff = self::array_diff_assoc_recursive( $value, $array2[$key] );
                    if ( ! empty( $new_diff ))
                    {
                        $difference[$key] = $new_diff;
                    }
                }
            }
            elseif ( ! isset( $array2[$key] ))
            {
                $difference[$key] = $value;
            }
        }

        return ( isset( $difference ) && is_array( $difference ))
            ? $difference
            : [];
    }


    /**
     * 
     * 
     * @param  array      $array Multidimensional array
     * @param  array      $array Multidimensional array
     * @return array             Slice
     */
    public static function array_slice_assoc_recursive( $array1, $array2 )
    {
        if ( ! is_array( $array2 ))
        {
            return $array2;
        }
        if ( ! is_array( $array1 ))
        {
            return $array1;
        }

        $result = [];
        $keys = array_keys( $array1 );
        
        foreach( $keys as $key )
        {
            if ( isset( $array2[$key] ))
            {
                $result[$key] = self::array_slice_assoc_recursive( $array1[$key], $array2[$key] );    
            }
            else
            {
                $result[$key] = self::array_slice_assoc_recursive( $array1[$key], $array1[$key] );
            }
        }

        return $result;
    }


    /**
     * Finds item by key and value - multidimensional array search.
     *
     * @param  string     $key   Item key
     * @param  string     $value Item value at that key
     * @param  array      $array Multidimensional array
     * @return array|bool        Item or false
     */
    public static function array_search_by_key_value( $key, $value, $array )
    {
        foreach ( $array as $id => $item )
        {
            if ( $item[$key] === $value )
            {
                return $item;
            }
        }
        return false;
    }
}
