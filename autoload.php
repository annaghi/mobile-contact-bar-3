<?php

defined( 'ABSPATH' ) || exit();


spl_autoload_register( function ( $class_name )
{
    $namespaces = ['MobileContactBar\\' => __DIR__ . '/php/'];
    foreach ( $namespaces as $prefix => $base_dir )
    {
        $length = strlen( $prefix );
        if ( 0 !== strncmp( $prefix, $class_name, $length ))
        {
            continue;
        }
        $file = $base_dir . str_replace( '\\', '/', substr( $class_name, $length )) . '.php';
        if ( ! file_exists( $file ))
        {
            continue;
        }
        require $file;
        break;
    }
});
