<?php

namespace MobileContactBar;

use MobileContactBar\Settings;
use MobileContactBar\Styles;
use DirectoryIterator;


final class Migrate
{
    /**
     * @var array
     */
    public $option_migrations = [];

    
    /**
     * @var string[]
     */
    public $available_migrations = [];


    /**
     * @var array
     */
    public $needed_migrations = [];


    public function __construct()
    {
        $this->option_migrations = abmcb( Options::class )->get_option( abmcb()->id . '_migrations', 'default_option_migrations', 'is_valid_option_migrations' );
        $this->available_migrations = $this->available_migrations();
        $this->needed_migrations = $this->needed_migrations();
    }


    public function run()
    {
        $this->run_all();
        $this->refresh_option_bar();
    }


    private function run_all()
    {
        foreach ( $this->needed_migrations as $migration => $success )
        {
            $migration_class = Helper::build_class_name( $this->versionToClassName( $migration ), 'Migrations' );

            if ( class_exists( $migration_class ))
            {
                $this->needed_migrations[$migration] = abmcb( $migration_class )->run();
            }
        }

        $migrations = $this->option_migrations + $this->needed_migrations;
        abmcb( Options::class )->update_option( $migrations, abmcb()->id . '_migrations', 'default_option_migrations', 'is_valid_option_migrations' );
    }


    /**
     * @return string[]
     */
    protected function available_migrations()
    {
        $migrations = [];
        $dir = plugin_dir_path( abmcb()->file ) . 'php/Migrations';

        if ( is_dir( $dir ))
        {
            $iterator = new DirectoryIterator( $dir );
            foreach ( $iterator as $fileinfo )
            {
                if ( 'file' === $fileinfo->getType() )
                {
                    $migrations[] = $this->classNameToVersion( $fileinfo->getFilename() );
                }
            }
        }

        natsort( $migrations );

        return array_values( $migrations );
    }


    /**
     * @return array
     */
    protected function needed_migrations()
    {
        $start_from = get_option( abmcb()->id . '_version', '0.0.0' );

        $needed_migrations = array_filter(
            $this->available_migrations,
            function ( $available_migration ) use ( $start_from ) { return $available_migration > $start_from; }
        );

        return array_fill_keys( $needed_migrations, false );
    }


    private function refresh_option_bar()
    {
        $refreshed_option_bar = [];

        $option_bar = get_option( abmcb()->id );

        if ( $option_bar && is_array( $option_bar ))
        {
            $default_settings = abmcb( Settings\Input::class )->default_settings();
            $settings = $default_settings;

            if ( isset( $option_bar['settings'] ) && is_array( $option_bar['settings'] ))
            {
                $settings = Helper::array_intersect_key_recursive( array_replace_recursive( $default_settings, $option_bar['settings'] ), $default_settings );
            }

            $sample_contacts = abmcb( Contacts\Input::class )->sample_contacts();
            $contacts = array_map( function ( $contact ) { return array_replace( $contact, ['checked' => 0] ); }, $sample_contacts );

            if ( isset( $option_bar['contacts'] ) && is_array( $option_bar['contacts'] ))
            {
                $contacts = $option_bar['contacts'];
            }

            $styles = Styles\CSS::output( $settings, $contacts );
    
            $refreshed_option_bar = [
                'settings' => $settings,
                'contacts' => $contacts,
                'styles'   => $styles,
            ];
        }

        abmcb( Options::class )->update_option( $refreshed_option_bar, abmcb()->id, 'default_option_bar', 'is_valid_option_bar' );
    }


    private function classNameToVersion( $className )
    {
        $version = str_replace( ['Migrate_', '.php'], '', $className );
        return str_replace( '_', '.', $version );
    }

    private function versionToClassName( $version )
    {
        $className = str_replace( '.', '_', $version );
        return 'Migrate_' . $className;
    }
}
