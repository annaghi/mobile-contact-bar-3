<?php

namespace MobileContactBar;

use DirectoryIterator;
use ReflectionClass;


/**
 * @property string $id
 * @property string $slug
 * @property string $capability
 * @property string $page_suffix;
 * @property string $schemes;
 * @property string $wp_cron_hook;
*/
final class Plugin extends Container
{
    const ID = 'mobile_contact_bar';
    const SLUG = 'mobile-contact-bar';
    const CAPABILITY = 'manage_options';
    const PAGE_SUFFIX = 'settings_page_mobile-contact-bar';
    const SCHEMES = ['viber', 'tel', 'sms', 'skype', 'mailto', 'https', 'http'];
    const WP_CRON_HOOK = 'mobile_contact_bar_weekly_scheduled_events';


    public $file = '';
    public $name = '';
    public $version = '';
    public $plugin_uri = '';


    /**
     * @var array
     */
    public $contact_types = [];


    /**
     * @var string
     */
    public $base_css = 'css/mcb-base.css';


    /**
     * @var string
     */
    public $option_version = '';

    
    /**
     * Multidimensional array of the plugin's option, divided into sections: 'settings', 'contacts'.
     *
     * @var array
     */
    public $option_bar = [];

    
    /**
     * Plugin instance
     * 
     * @var Plugin
     */
    protected static $instance = null;


    /**
     * Creates instance of MobileContactBar\Plugin class, and ensures that only one instance exists.
     * 
     * @param  string $file The filename of the plugin (__FILE__)
     * @return Plugin       Plugin instance
     */
    final public static function load( $file = '' )
    {
        return isset( static::$instance )
            ? static::$instance
            : static::$instance = new static( $file );
    }


    /**
     * Constructor, which extracts plugin data from the main plugin file. 
     * 
     * @param  string $file The filename of the plugin (__FILE__)
     * @return void
     */
    public function __construct( $file = '' )
    {
        $this->file = $file;

        $plugin_data = get_file_data(
            $file,
            [
                'name'        => 'Plugin Name',
                'version'     => 'Version',
                'plugin_uri'  => 'Plugin URI',
            ],
            'plugin'
        );

        array_walk( $plugin_data, function ( $value, $key )
        {
            if ( property_exists( $this, $key ))
            {
                $this->$key = $value;
            }
        });
    }


    /**
     * Reads class constants when they are accessed as properties.
     * 
     * @param  mixed $property
     * @return mixed
     */
    public function __get( $property )
    {
        if ( property_exists( $this, $property ))
        {
            return $this->$property;
        }

        $constant = 'static::' . strtoupper( $property );
        if ( defined( $constant ))
        {
            return constant( $constant );
        }
    }


    /**
     * Runs the plugin installation during the plugin activation.
     *
     * @param  bool $network_wide Whether to enable the plugin for all sites in the network or just for the current site
     * @return void
     */
    public function activate( $network_wide = false )
    {
        if ( is_multisite() && $network_wide )
        {
            $site_ids = get_sites( ['fields' => 'ids'] );

            remove_action( 'switch_blog', 'wp_switch_roles_and_user', 1 );
            foreach ( $site_ids as $site_id )
            {
                switch_to_blog( $site_id );
                $this->install( 'activate' );
                restore_current_blog();
            }
            add_action( 'switch_blog', 'wp_switch_roles_and_user', 1, 2 );
        }
        else
        {
            $this->install( 'activate' );
        }
    }


    /**
     * Runs the plugin suspension during the plugin deactivation.
     *
     * @param  bool $network_wide Whether to enable the plugin for all sites in the network or just for the current site
     * @return void
     */
    public function deactivate( $network_wide = false )
    {
        if ( is_multisite() && $network_wide )
        {
            $site_ids = get_sites( ['fields' => 'ids'] );

            remove_action( 'switch_blog', 'wp_switch_roles_and_user', 1 );
            foreach ( $site_ids as $site_id )
            {
                switch_to_blog( $site_id );
                $this->unschedule_cron_events();
                restore_current_blog();
            }
            add_action( 'switch_blog', 'wp_switch_roles_and_user', 1, 2 );
        }
        else
        {
            $this->unschedule_cron_events();
        }
    }


    /**
     * Hooks plugin into WordPress's actions and filters.
     * 
     * @return void
     */
    public function plugins_loaded()
    {
        add_action( 'init', [$this, 'init'] );

        if ( version_compare( get_bloginfo( 'version' ), '5.1', '<' ))
        {
            add_action( 'wpmu_new_blog', [$this, 'wpmu_new_blog'] );
        }
        else
        {
            add_action( 'wp_initialize_site', [$this, 'wp_initialize_site'] );
        }

        abmcb( Hooks::class )->add();
    }


    /**
     * Runs the plugin installation for the newly created site.
     *
     * @param  int  $blog_id Blog ID of the newly created blog
     * @return void
     */
    public function wpmu_new_blog( $blog_id )
    {
        if ( ! is_plugin_active_for_network( plugin_basename( $this->file )))
        {
            return;
        }

        switch_to_blog( $blog_id );
        $this->install();
        restore_current_blog();
    }


    /**
     * Runs the plugin installation for the newly created site.
     *
     * @param  int  $site_id Site ID of the newly created site
     * @return void
     */
    public function wp_initialize_site( $site )
    {
        if ( ! is_plugin_active_for_network( plugin_basename( $this->file )))
        {
            return;
        }

        switch_to_blog( $site->blog_id );
        $this->install();
        restore_current_blog();
    }


    /**
     * Runs the plugin installation on each request.
     * 
     * @return void
     */
    public function init()
    {
        $this->install();

        $this->option_version = get_option( self::ID . '_version' );
        $this->option_bar = abmcb( Option::class )->get_option( self::ID, 'sanitize_option_bar' );
    }


    /**
     * Creates instances for contact types.
     * Creates or updates the plugin options (version, bar) in the database - when needed.
     * 
     * @return void
     */
    private function install( $activate = '' )
    {
        $this->register_contact_types();

        $version = get_option( self::ID . '_version' );

        if ( ! $version && get_option( 'mcb_option' ))
        {
            abmcb( Migrate::class )->run();
            update_option( self::ID . '_version', $this->version );
        }
        elseif ( ! $version )
        {
            abmcb( Option::class )->update_option( abmcb( Option::class )->default_option_bar(), self::ID, 'sanitize_option_bar' );
            abmcb( File::class )->create();
            abmcb( File::class )->write( abmcb( Option::class )->get_option( self::ID, 'sanitize_option_bar' ) );
            update_option( self::ID . '_version', $this->version );
        }
        elseif ( $version && version_compare( $version, $this->version, '<' ))
        {
            abmcb( Migrate::class )->run();
            update_option( self::ID . '_version', $this->version );
        }
        elseif ( 'activate' === $activate )
        {
            abmcb( Migrate::class )->run();
        }
    }


    /**
     * Creates instances for contact types.
     *  
     * @return void
     */
    private function register_contact_types()
    {
        $contact_types = [];

        $dir = plugin_dir_path( $this->file ) . 'php/ContactTypes';

        if ( is_dir( $dir ))
        {
            $iterator = new DirectoryIterator( $dir );
            foreach ( $iterator as $fileinfo )
            {
                if ( 'file' === $fileinfo->getType() )
                {
                    $contact_type = str_replace( '.php', '', $fileinfo->getFilename() );
                    $contact_type_class = Helper::build_class_name( $contact_type, 'ContactTypes');
                    if ( class_exists( $contact_type_class ) && ! ( new ReflectionClass( $contact_type_class ))->isAbstract() )
                    {
                        $contact_types[strtolower( $contact_type )] = abmcb( $contact_type_class );
                    }
                }
            }
        }

        uasort( $contact_types, function ( $a, $b ) { return strcmp( $a->field()['title'], $b->field()['title'] ); });
        $this->contact_types = $contact_types;
    }


    /**
     * Unschedule cron events.
     * 
     * @return void
     */
    private function unschedule_cron_events()
    {
        $timestamp = wp_next_scheduled( self::WP_CRON_HOOK );
        wp_unschedule_event( $timestamp, self::WP_CRON_HOOK );
    }
}
