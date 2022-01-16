<?php

namespace MobileContactBar;

use MobileContactBar\Styles;


final class File
{
    /**
     * @return void
     */
    public function create()
    {
        $wp_upload_dir = wp_upload_dir();
        if ( ! realpath( $wp_upload_dir['basedir'] ))
        {
            $wp_upload_dir = wp_upload_dir( null, true, true );
        }

        $dir = trailingslashit( $wp_upload_dir['basedir'] . '/' . abmcb()->slug );

        $htaccess  = "Options -Indexes\n";
        $htaccess .= "deny from all\n";
        $htaccess .= "<FilesMatch '\.(css)$'>\n";
        $htaccess .= "Order Allow,Deny\n";
        $htaccess .= "Allow from all\n";
        $htaccess .= "</FilesMatch>\n";

        $files = [
            $dir . 'index.php'       => "<?php\n// Silence is golden.\n",
            $dir . '.htaccess'       => $htaccess,
            $dir . 'css/index.php'   => "<?php\n// Silence is golden.\n",
            $dir . abmcb()->base_css => '',
        ];
        foreach ( $files as $file => $contents )
        {
            if ( wp_mkdir_p( dirname( $file )) && ! realpath( $file ))
            {
                file_put_contents( wp_normalize_path( $file ), $contents );
            }
        }
    }


    /**
     * Writes the base styles to the uploads/ folder.
     *
     * @param  array $option_bar
     * @return void
     */
    public function write( $option_bar = [] )
    {
        if ( $option_bar && is_array( $option_bar ) && isset( $option_bar['settings'], $option_bar['buttons'] ))
        {
            $wp_upload_dir = wp_upload_dir();
            if ( ! realpath( $wp_upload_dir['basedir'] ))
            {
                $wp_upload_dir = wp_upload_dir( null, true, true );
            }
    
            $base_css = wp_normalize_path( $wp_upload_dir['basedir'] . '/' . abmcb()->slug . '/' . abmcb()->base_css );
            file_put_contents( $base_css, abmcb( Styles\CSS::class )->output( $option_bar['settings'], $option_bar['buttons'] ));
        }
    }
}
