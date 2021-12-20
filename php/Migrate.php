<?php

namespace MobileContactBar;

use MobileContactBar\Settings\Input;
use MobileContactBar\Styles\CSS;
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
        $this->refresh_settings();
    }


    private function run_all()
    {
logg($this->needed_migrations);
        foreach ( $this->needed_migrations as $migration => $success )
        {
            $migration_class = Helper::build_class_name( $migration, 'Migrations' );

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
                    $migrations[] = str_replace( ['.php'], '', $fileinfo->getFilename() );
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
        $start_from = 'Migrate_' . str_replace( '.', '_', $start_from );

        $needed_migrations = array_filter(
            $this->available_migrations,
            function( $available_migration ) use ( $start_from ) { return $available_migration > $start_from; }
        );

        return array_fill_keys( $needed_migrations, false );
    }


    private function refresh_settings()
    {
        $old_option_bar = get_option( abmcb()->id );

        if ( !! $old_option_bar )
        {
            $settings = $this->refreshed_settings();
            $contacts = ( isset( $old_option_bar['contacts'] ) && is_array( $old_option_bar['contacts'] )) ? $old_option_bar['contacts'] : [];
            $styles   = CSS::generate( $settings, $contacts );
    
            $option_bar = [
                'settings' => $settings,
                'contacts' => $contacts,
                'styles'   => $styles,
            ];
    
            abmcb( Options::class )->update_option( $option_bar, abmcb()->id, 'default_option_bar', 'is_valid_option_bar' );
        }
    }


    protected function refreshed_settings()
    {
        $settings = [];

        $default_settings = abmcb( Settings\Input::class )->fields_defaults();

        $old_option_bar = get_option( abmcb()->id );

        if ( isset( $old_option_bar['settings'] ) && is_array( $old_option_bar['settings'] ))
        {
            $old_settings = $old_option_bar['settings'];

            foreach ( $default_settings as $section_id => $section )
            {
                if ( isset( $old_settings[$section_id] ))
                {
                    foreach ( $section as $setting_id => $setting )
                    {
                        if ( isset( $old_settings[$section_id][$setting_id] ))
                        {
                            $settings[$section_id][$setting_id] = $old_settings[$section_id][$setting_id];
                        }
                        else
                        {
                            $settings[$section_id][$setting_id] = $setting;
                        }
                    }
                }
                else
                {
                    $settings[$section_id] = $section;
                }
            }
        }
        else
        {
            $settings = $default_settings;
        }

        return $settings;
    }
}
