<?php

namespace MobileContactBar;

use MobileContactBar\Controllers\AdminController;
use MobileContactBar\Controllers\AJAXController;
use MobileContactBar\Controllers\CronController;
use MobileContactBar\Controllers\IFrameController;
use MobileContactBar\Controllers\NoticeController;
use MobileContactBar\Controllers\PublicController;
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
    public $languages = '';
    public $name = '';
    public $version = '';
    public $plugin_uri = '';
    public $description = '';

    public $contact_types = [];


    /**
     * @var string
     */
    public $option_version = '';

    
    /**
     * Multidimensional array of the plugin's option, divided into sections: 'settings', 'contacts', 'styles'.
     *
     * @var array
     */
    public $option_bar = [];

    
    /**
     * Controllers
     */
    protected $admin  = null;
    protected $ajax   = null;
    protected $cron   = null;
    protected $iframe = null;
    protected $notice = null;
    protected $public = null;


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
                'languages'   => 'Domain Path',
                'name'        => 'Plugin Name',
                'version'     => 'Version',
                'plugin_uri'  => 'Plugin URI',
                'description' => 'Description',
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
                $this->install();
                restore_current_blog();
            }
            add_action( 'switch_blog', 'wp_switch_roles_and_user', 1, 2 );
        }
        else
        {
            $this->install();
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
     * Loads the plugin's translated strings.
     * Hooks WordPress's actions and filters.
     * 
     * @return void
     */
    public function plugins_loaded()
    {
        load_plugin_textdomain( self::ID, false, plugin_basename( $this->file ) . '/languages' );

        $this->hook_actions_filters();
    }


    /**
     * Hooks WordPress's actions and filters in 4 main areas: admin, notice, ajax, public.
     * 
     * @return void
     */
    public function hook_actions_filters()
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

        $this->cron = abmcb( CronController::class );
        add_filter( 'cron_schedules', [$this->cron, 'cron_schedules'] );
        add_action( 'wp',  [$this->cron, 'wp'] );
        add_action( self::WP_CRON_HOOK, [$this->cron, 'clear_stat_cache'] );

        if ( is_admin() )
        {
            $this->notice = abmcb( NoticeController::class );
            add_action( 'admin_enqueue_scripts', [$this->notice, 'admin_enqueue_scripts'] );
            add_action( 'admin_notices', [$this->notice, 'admin_notices'] );
            add_action( 'wp_ajax_mcb_ajax_dismiss_notice', [$this->notice, 'ajax_dismiss_notice'] );

            $this->admin = abmcb( AdminController::class );
            add_action( 'admin_menu', [$this->admin, 'admin_menu'] );
            add_action( 'admin_init', [$this->admin, 'admin_init'] );
            add_action( 'add_meta_boxes', [$this->admin, 'add_meta_boxes'] );
            add_action( 'admin_enqueue_scripts', [$this->admin, 'admin_enqueue_scripts'] );
            add_action( 'admin_footer', [$this->admin, 'admin_footer'] );
            add_filter( 'pre_update_option_' . self::ID, [$this->admin, 'pre_update_option'], 10, 2 );
            add_filter( 'plugin_action_links_' . plugin_basename( $this->file ), [$this->admin, 'plugin_action_links'] );

            $this->ajax = abmcb( AJAXController::class );
            foreach ( $this->ajax->admin_actions as $admin_action )
            {
                add_action( 'wp_ajax_mcb_' . $admin_action, [$this->ajax, $admin_action], 1 );
            }
        }

        if ( isset( $_GET[self::SLUG . '-iframe'] ))
        {
            $this->iframe = abmcb( IFrameController::class );
            add_action( 'init', [$this->iframe, 'init'] );
        }

        if ( ! is_admin() && ! wp_doing_ajax() && ! isset( $_GET[self::SLUG . '-iframe'] ))
        {
            $this->public = abmcb( PublicController::class );
            add_action( 'init', [$this->public, 'init'] );
        }
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
    private function install()
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
            update_option( self::ID, abmcb( Option::class )->default_option_bar() );
            update_option( self::ID . '_version', $this->version );
        }
        elseif ( $version && version_compare( $version, $this->version, '<' ))
        {
            abmcb( Migrate::class )->run();
            update_option( self::ID . '_version', $this->version );
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
