<?php

namespace MobileContactBar;

use MobileContactBar\Buttons;
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
        $this->option_migrations = abmcb( Option::class )->get_option( abmcb()->id . '_migrations', 'sanitize_option_migrations' );
        $this->available_migrations = $this->available_migrations();
        $this->needed_migrations = $this->needed_migrations();
    }


    /**
     * @return void
     */
    public function run()
    {
        clearstatcache();

        $this->run_all();

        $option_bar = abmcb( Option::class )->get_option( abmcb()->id, 'sanitize_option_bar' );
        if ( empty( $option_bar['buttons'] ))
        {
            $option_bar['buttons'] = abmcb( Buttons\Input::class )->unchecked_sample_buttons();
        }
        abmcb( Option::class )->update_option( $option_bar, abmcb()->id, 'sanitize_option_bar' );

        abmcb( File::class )->write( abmcb( Option::class )->get_option( abmcb()->id, 'sanitize_option_bar' ));
    }


    /**
     * @return void
     */
    private function run_all()
    {
        foreach ( $this->needed_migrations as $migration => $success )
        {
            $migration_class = Helper::build_class_name( $this->version_to_class_name( $migration ), 'Migrations' );

            if ( class_exists( $migration_class ))
            {
                $this->needed_migrations[$migration] = abmcb( $migration_class )->run();
            }
        }

        $migrations = $this->option_migrations + $this->needed_migrations;
        abmcb( Option::class )->update_option( $migrations, abmcb()->id . '_migrations', 'sanitize_option_migrations' );
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
                    $migrations[] = $this->class_name_to_version( $fileinfo->getFilename() );
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


    /**
     * @param  string $class_name
     * @return string             version
     */
    private function class_name_to_version( $class_name )
    {
        $version = str_replace( ['Migrate_', '.php'], '', $class_name );
        return str_replace( '_', '.', $version );
    }


    /**
     * @param  string $version
     * @return string          class name
     */
    private function version_to_class_name( $version )
    {
        $name = str_replace( '.', '_', $version );
        return 'Migrate_' . $name;
    }
}
