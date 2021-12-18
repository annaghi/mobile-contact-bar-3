<?php

namespace MobileContactBar;

use DirectoryIterator;
use ReflectionClass;


/**
    @property string $id
    @property string $slug
    @property string $capability
    @property string $page_suffix;
*/
final class Plugin extends Container
{
    const ID = 'mobile_contact_bar';
    const SLUG = 'mobile-contact-bar';
    const CAPABILITY = 'manage_options';
    const PAGE_SUFFIX = 'settings_page_mobile-contact-bar';

    public $file = '';
    public $languages = '';
    public $name = '';
    public $version = '';
    public $plugin_uri = '';
    public $description = '';

    public $contact_types = [];

    protected $notices = null;
    protected $admin = null;
    protected $public = null;

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
     * Constructs the Plugin instance. 
     * 
     * @param string $file The filename of the plugin (__FILE__)
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
     * Creates the default options such as version and bar (settings, contacts, styles) during the plugin activation.
     *
     * @param bool $network_wide Whether to enable the plugin for all sites in the network or just for the current site
     *
     * @global $wpdb
     */
    public function activate( $network_wide = false )
    {
        if ( is_multisite() && $network_wide )
        {
            global $wpdb;

            $blog_ids = $wpdb->get_col( "SELECT blog_id FROM $wpdb->blogs LIMIT 100" );
            foreach ( $blog_ids as $blog_id )
            {
                switch_to_blog( $blog_id );
                $this->install();
                restore_current_blog();
            }
        }
        else
        {
            $this->install();
        }
    }


    public function plugins_loaded()
    {
        load_plugin_textdomain( self::ID, false, plugin_basename( $this->file ) . '/languages' );

        $this->register_hooks();
    }


    /**
     * Hooks WordPress's actions and filters.
     */
    public function register_hooks()
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

        if ( $this->is_admin() )
        {
            $this->notices = abmcb( Notices::class );
            add_action( 'admin_enqueue_scripts', [$this->notices, 'admin_enqueue_scripts'] );
            add_action( 'admin_notices', [$this->notices, 'admin_notices'] );
            add_action( 'wp_ajax_mcb_ajax_dismiss_notice', [$this->notices, 'ajax_dismiss_notice'] );

            $this->admin = abmcb( AdminArea::class );
            $this->admin->register_ajax_hooks();
            add_action( 'admin_head', [$this->admin, 'admin_head'] );
            add_action( 'admin_menu', [$this->admin, 'admin_menu'] );
            add_action( 'admin_init', [$this->admin, 'admin_init'] );
            add_action( 'add_meta_boxes', [$this->admin, 'add_meta_boxes'] );
            add_action( 'admin_enqueue_scripts', [$this->admin, 'admin_enqueue_scripts'] );
            add_action( 'admin_footer', [$this->admin, 'admin_footer'] );
            add_filter( 'pre_update_option_' . self::ID, [$this->admin, 'pre_update_option'], 10, 2 );
            add_filter( 'plugin_action_links_' . plugin_basename( $this->file ), [$this->admin, 'plugin_action_links'] );
        }

        if ( ! $this->is_admin() )
        {
            $this->public = abmcb( PublicArea::class );
            add_action( 'init', [$this->public, 'init'] );
        }
    }


    /**
     * Creates default options on blog creation.
     *
     * @param int $blog_id Blog ID of the newly created blog
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
     * Creates default options after blog creation.
     *
     * @param int $site_id Site ID of the newly created site
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
     * Updates plugin's options.
     * Registers contact types.
     */
    public function init()
    {
        $this->install();

        $this->contact_types = $this->register_contact_types();
    }


    /**
     * Creates or updates version and bar options.
     *
     */
    private function install()
    {
        $version = get_option( self::ID . '_version' );

        if ( ! $version && get_option( 'mcb_option' ))
        {
            abmcb( Migrate::class )->run();
            update_option( self::ID . '_version', $this->version );
        }
        elseif ( ! $version )
        {
            update_option( self::ID, abmcb( Options::class )->default_option_bar_with_samples() );
            update_option( self::ID . '_version', $this->version );
        }
        elseif ( !! $version && version_compare( $version, $this->version, '<' ))
        {
            abmcb( Migrate::class )->run();
            update_option( self::ID . '_version', $this->version );
        }
    }


    private function register_contact_types()
    {
        $contact_types = [];
        $dir = plugin_dir_path( $this->file ) . 'php/Contacts/Type';

        if ( is_dir( $dir ))
        {
            $iterator = new DirectoryIterator( $dir );
            foreach ( $iterator as $fileinfo )
            {
                if ( 'file' === $fileinfo->getType() )
                {
                    $contact_type = str_replace( '.php', '', $fileinfo->getFilename() );
                    $contact_type_class = Helper::build_class_name( $contact_type, 'Contacts\Type');
                    if ( class_exists( $contact_type_class ) && ! ( new \ReflectionClass( $contact_type_class ))->isAbstract() )
                    {
                        $contact_types[strtolower( $contact_type )] = abmcb( $contact_type_class );
                    }
                }
            }
        }

        return $contact_types;
    }


    /**
     * @return bool
     */
    private function is_admin()
    {
        return ( is_admin() || is_network_admin() );
    }
}
