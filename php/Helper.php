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
}
