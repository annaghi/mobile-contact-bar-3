<?php

namespace MobileContactBar\Modules;

use MobileContactBar\Helper;
use MobileContactBar\Option;
use MobileContactBar\Controllers\CronController;
use MobileContactBar\Sinergi\BrowserDetector\Browser;
use MobileContactBar\Vectorface\Whip\Whip;
use WP_Debug_Data;


final class SystemInfo
{
    /**
     * @return string
     */
    public function get()
    {
        if ( ! class_exists( 'WP_Debug_Data' ))
        {
            require_once ABSPATH . 'wp-admin/includes/class-wp-debug-data.php';
        }

        add_filter( 'gettext_default', [$this, 'gettext_default'], 10, 2 );
        $debug_data = WP_Debug_Data::debug_data();
        remove_filter( 'gettext_default', [$this, 'gettext_default'], 10 );

        $details = [
            'plugin'            => $this->get_plugin_details(),
            'browser'           => $this->get_browser_details(),
            'database'          => $this->get_database_details( $debug_data ),
            'server'            => $this->get_server_details( $debug_data ),
            'wordpress'         => $this->get_wordpress_details( $debug_data ),
            'mu-plugins'        => $this->get_must_use_plugin_details(),
            'network-plugins'   => $this->get_network_active_plugin_details(),
            'active-plugins'    => $this->get_active_plugin_details(),
            'inactive-plugins'  => $this->get_inactive_plugin_details(),
            'settings'          => $this->get_plugin_settings(),
        ];

        $system_info = array_reduce( array_keys( $details ), function ( $acc, $key ) use ( $details )
        {
            $values = Helper::array_get( $details[$key], 'values' );
            if ( empty( $values ))
            {
                return $acc;
            }
            $title = strtoupper( Helper::array_get( $details[$key], 'title' ));

            return $acc . $this->implode( $title, $values );
        });

        return trim( $system_info );
    }


    /**
     * Filters English translation.
     * 
     * @param  string $translation
     * @param  string $text
     * @return string
     */
    public function gettext_default( $translation, $text )
    {
        return $text;
    }


    /**
     * @return array
     */
    public function get_plugin_details()
    {
        require_once ABSPATH . '/wp-admin/includes/plugin.php';

        return [
            'title'  => 'Plugin Details',
            'values' => [
                'Migrations'        => Helper::if_empty( http_build_query( abmcb( Option::class )->get_option( abmcb()->id . '_migrations', 'sanitize_option_migrations' ), '', ', ' ), 'No' ),
                'Network Activated' => ( is_plugin_active_for_network( plugin_basename( abmcb()->file ))) ? 'Yes' : 'No',
                'Version'           => abmcb()->version,
            ],
        ];
    }


    /**
     * @return array
     */
    public function get_browser_details()
    {
        $browser = new Browser();
        $name = esc_attr( $browser->getName() );
        $userAgent = esc_attr( $browser->getUserAgent()->getUserAgentString() );
        $version = esc_attr( $browser->getVersion() );

        return [
            'title'  => 'Browser Details',
            'values' => [
                'Browser Name' => sprintf( '%s %s', $name, $version ),
                'Browser UA'   => $userAgent,
            ],
        ];
    }


    /**
     * @param  array $debug_data
     * @return array
     */
    public function get_database_details( $debug_data )
    {
        $database = Helper::array_get( $debug_data, 'wp-database' );

        return [
            'title'  => 'Database Details',
            'values' => [
                'Charset'          => Helper::array_get( $database, 'fields.database_charset.value' ),
                'Collation'        => Helper::array_get( $database, 'fields.database_collate.value' ),
                'Extension'        => Helper::array_get( $database, 'fields.extension.value' ),
                'Version (client)' => Helper::array_get( $database, 'fields.client_version.value' ),
                'Version (server)' => Helper::array_get( $database, 'fields.server_version.value' ),
            ],
        ];
    }


    /**
     * @param  array $debug_data
     * @return array
     */
    public function get_server_details( $debug_data )
    {
        $media  = Helper::array_get( $debug_data, 'wp-media' );
        $server = Helper::array_get( $debug_data, 'wp-server' );

        return [
            'title'  => 'Server Details',
            'values' => [
                'cURL Version'            => Helper::array_get( $server, 'fields.curl_version.value' ),
                'Display Errors'          => Helper::if_empty( $this->get_ini( 'display_errors' ), 'No' ),
                'File Uploads'            => Helper::array_get( $media, 'fields.file_uploads.value' ),
                'GD version'              => Helper::array_get( $media, 'fields.gd_version.value' ),
                'Ghostscript version'     => Helper::array_get( $media, 'fields.ghostscript_version.value' ),
                'Host Name'               => $this->get_host_name(),
                'ImageMagick version'     => Helper::array_get( $media, 'fields.imagemagick_version.value' ),
                'Intl'                    => Helper::if_empty( phpversion( 'intl' ), 'No' ),
                'IPv6'                    => var_export( defined('AF_INET6'), true ),
                'Max Effective File Size' => Helper::array_get( $media, 'fields.max_effective_size.value' ),
                'Max Execution Time'      => Helper::array_get( $server, 'fields.time_limit.value' ),
                'Max File Uploads'        => Helper::array_get( $media, 'fields.max_file_uploads.value' ),
                'Max Input Time'          => Helper::array_get( $server, 'fields.max_input_time.value' ),
                'Max Input Variables'     => Helper::array_get( $server, 'fields.max_input_variables.value' ),
                'Memory Limit'            => Helper::array_get( $server, 'fields.memory_limit.value' ),
                'Multibyte'               => Helper::if_empty( phpversion( 'mbstring' ), 'No' ),
                'Permalinks Supported'    => Helper::array_get( $server, 'fields.pretty_permalinks.value' ),
                'PHP Version'             => Helper::array_get( $server, 'fields.php_version.value' ),
                'Post Max Size'           => Helper::array_get( $server, 'fields.php_post_max_size.value' ),
                'SAPI'                    => Helper::array_get( $server, 'fields.php_sapi.value' ),
                'Sendmail'                => $this->get_ini( 'sendmail_path' ),
                'Server Architecture'     => Helper::array_get( $server, 'fields.server_architecture.value' ),
                'Server Software'         => Helper::array_get( $server, 'fields.httpd_software.value' ),
                'SUHOSIN Installed'       => Helper::array_get( $server, 'fields.suhosin.value' ),
                'Upload Max Filesize'     => Helper::array_get( $server, 'fields.upload_max_filesize.value' ),
            ],
        ];
    }


    /**
     * @param  string $name
     * @return string
     */
    protected function get_ini( $name )
    {
        return ( function_exists( 'ini_get' )) ? ini_get( $name ) : 'ini_get() is disabled.';
    }


    /**
     * @return string
     */
    protected function get_host_name()
    {
        return sprintf( '%s (%s)', $this->detect_webhost_provider(), $this->get_ip_address() );
    }


    /**
     * @return string
     */
    protected function detect_webhost_provider()
    {
        $checks = [
            '.accountservergroup.com'    => 'Site5',
            '.gridserver.com'            => 'MediaTemple Grid',
            '.inmotionhosting.com'       => 'InMotion Hosting',
            '.ovh.net'                   => 'OVH',
            '.pair.com'                  => 'pair Networks',
            '.stabletransit.com'         => 'Rackspace Cloud',
            '.stratoserver.net'          => 'STRATO',
            '.sysfix.eu'                 => 'SysFix.eu Power Hosting',
            'bluehost.com'               => 'Bluehost',
            'DH_USER'                    => 'DreamHost',
            'Flywheel'                   => 'Flywheel',
            'ipagemysql.com'             => 'iPage',
            'ipowermysql.com'            => 'IPower',
            'localhost:/tmp/mysql5.sock' => 'ICDSoft',
            'mysqlv5'                    => 'NetworkSolutions',
            'PAGELYBIN'                  => 'Pagely',
            'secureserver.net'           => 'GoDaddy',
            'WPE_APIKEY'                 => 'WP Engine',
        ];

        foreach ( $checks as $key => $value )
        {
            if ( ! $this->is_webhost_found( $key ))
            {
                continue;
            }
            return $value;
        }

        return implode( ',', array_filter( [DB_HOST, filter_input( INPUT_SERVER, 'SERVER_NAME' )] ));
    }


    /**
     * @param  string $key
     * @return bool
     */
    protected function is_webhost_found( $key )
    {
        return defined( $key )
            || filter_input( INPUT_SERVER, $key )
            || Helper::str_contains( $key, filter_input( INPUT_SERVER, 'SERVER_NAME' ))
            || Helper::str_contains( $key, DB_HOST )
            || ( function_exists( 'php_uname') && Helper::str_contains( $key, php_uname() ));
    }


    /**
     * @return string
     */
    public function get_ip_address()
    {
        $whitelist = [];
        $is_using_cloudflare = ! empty( filter_input( INPUT_SERVER, 'CF-Connecting-IP' ));

        if ( $is_using_cloudflare )
        {
            $cloudflare_ips = abmcb( CronController::class )->get_cloudflare_ips();
            $whitelist[Whip::CLOUDFLARE_HEADERS] = [Whip::IPV4 => $cloudflare_ips['v4']];
            if ( defined( 'AF_INET6' ))
            {
                $whitelist[Whip::CLOUDFLARE_HEADERS][Whip::IPV6] = $cloudflare_ips['v6'];
            }
        }

        $whip = new Whip( Whip::ALL_METHODS, $whitelist );
        $ip_address = $whip->getValidIpAddress();

        return ( false === $ip_address )
            ? 'unknown'
            : (string) $ip_address;
    }


    /**
     * @param  array $debug_data
     * @return array
     */
    public function get_wordpress_details( $debug_data )
    {
        $constants = Helper::array_get( $debug_data, 'wp-constants' );
        $wordpress = Helper::array_get( $debug_data, 'wp-core' );

        return [
            'title'  => 'WordPress Configuration',
            'values' => [
                'Email Domain'               => substr( strrchr( abmcb( Option::class )->get_wp_option( 'admin_email' ), '@' ), 1 ),
                'Environment'                => Helper::array_get( $wordpress, 'fields.environment_type.value' ),
                'Hidden From Search Engines' => Helper::array_get( $wordpress, 'fields.blog_public.value' ),
                'Home URL'                   => Helper::array_get( $wordpress, 'fields.home_url.value' ),
                'HTTPS'                      => Helper::array_get( $wordpress, 'fields.https_status.value' ),
                'Language (site)'            => Helper::array_get( $wordpress, 'fields.site_language.value' ),
                'Language (user)'            => Helper::array_get( $wordpress, 'fields.user_language.value' ),
                'Multisite'                  => Helper::array_get( $wordpress, 'fields.multisite.value' ),
                'Page For Posts ID'          => abmcb( Option::class )->get_wp_option( 'page_for_posts' ),
                'Page On Front ID'           => abmcb( Option::class )->get_wp_option( 'page_on_front' ),
                'Permalink Structure'        => Helper::array_get( $wordpress, 'fields.permalink.value' ),
                'Post Stati'                 => implode( ', ', get_post_stati() ),
                'Remote Post'                => abmcb( CronController::class )->get_remote_post_test(),
                'SCRIPT_DEBUG'               => Helper::array_get( $constants, 'fields.SCRIPT_DEBUG.value' ),
                'Show On Front'              => abmcb( Option::class )->get_wp_option( 'show_on_front' ),
                'Site URL'                   => Helper::array_get( $wordpress, 'fields.site_url.value' ),
                'Theme (active)'             => sprintf( '%s v%s', Helper::array_get( $debug_data, 'wp-active-theme.fields.name.value'), Helper::array_get( $debug_data, 'wp-active-theme.fields.version.value' )),
                'Theme (parent)'             => Helper::array_get( $debug_data, 'wp-parent-theme.name', 'No' ),
                'Timezone'                   => Helper::array_get( $wordpress, 'fields.timezone.value' ),
                'User Count'                 => Helper::array_get( $wordpress, 'fields.user_count.value' ),
                'Version'                    => Helper::array_get( $wordpress, 'fields.version.value' ),
                'WP_CACHE'                   => Helper::array_get( $constants, 'fields.WP_CACHE.value' ),
                'WP_DEBUG'                   => Helper::array_get( $constants, 'fields.WP_DEBUG.value' ),
                'WP_DEBUG_DISPLAY'           => Helper::array_get( $constants, 'fields.WP_DEBUG_DISPLAY.value' ),
                'WP_DEBUG_LOG'               => Helper::array_get( $constants, 'fields.WP_DEBUG_LOG.value' ),
                'WP_MAX_MEMORY_LIMIT'        => Helper::array_get( $constants, 'fields.WP_MAX_MEMORY_LIMIT.value' ),
            ],
        ];
    }


    /**
     * @return array
     */
    public function get_must_use_plugin_details()
    {
        $plugins = get_mu_plugins();
        $mu_plugins = ( empty( $plugins )) ? [] : $this->normalize_plugin_list( $plugins );

        return [
            'title'  => 'Must-Use Plugins',
            'values' => $mu_plugins,
        ];
    }


    /**
     * @return array
     */
    public function get_network_active_plugin_details()
    {
        $plugins = Helper::array_consolidate( get_site_option( 'active_sitewide_plugins', [] ));

        if ( ! is_multisite() || empty( $plugins ))
        {
            return [];
        }

        $network_plugins = $this->normalize_plugin_list( array_intersect_key( get_plugins(), $plugins ));

        return [
            'title'  => 'Network Active Plugins',
            'values' => $network_plugins,
        ];
    }


    /**
     * @return array
     */
    public function get_active_plugin_details()
    {
        $plugins  = get_plugins();
        $active   = abmcb( Option::class )->get_wp_option( 'active_plugins', [] );
        $inactive = array_diff_key( $plugins, array_flip( $active ));

        $active_plugins = $this->normalize_plugin_list( array_diff_key( $plugins, $inactive ));

        return [
            'title'  => 'Active Plugins',
            'values' => $active_plugins,
        ];
    }


    /**
     * @return array
     */
    public function get_inactive_plugin_details()
    {
        $active   = abmcb( Option::class )->get_wp_option( 'active_plugins', [] );
        $inactive = $this->normalize_plugin_list( array_diff_key( get_plugins(), array_flip( $active )));

        $network_active_plugins = $this->get_network_active_plugin_details();
        $network_active_plugins = Helper::array_get( $network_active_plugins, 'values' );
        $inactive_plugins = ( empty( $network_active_plugins ) ? $inactive : array_diff( $inactive, $network_active_plugins ));

        return [
            'title'  => 'Inactive Plugins',
            'values' => $inactive_plugins,
        ];
    }


    /**
     * @param  array $plugins
     * @return array
     */
    protected function normalize_plugin_list( array $plugins )
    {
        $plugins = array_map( function ( $plugin )
        {
            return sprintf( '%s v%s', Helper::array_get( $plugin, 'Name' ), Helper::array_get( $plugin, 'Version' ));
        }, $plugins );

        natcasesort( $plugins );

        return array_flip( $plugins );
    }


    /**
     * @return array
     */
    public function get_plugin_settings()
    {
        $settings = Helper::array_flatten( abmcb()->option_bar['settings'], true );

        return [
            'title'  => 'Plugin Settings',
            'values' => $settings,
        ];
    }


    /**
     * @param  string $title
     * @param  array  $details
     * @return string
     */
    protected function implode( $title, array $details )
    {
        $strings = ['[' . $title . ']'];
        $padding = max( array_map( 'strlen', array_keys( $details )));
        $padding = max( [$padding, 40] );

        foreach ( $details as $key => $value )
        {
            $pad = $padding - ( mb_strlen( $key, 'UTF-8' ) - strlen( $key ));
            $strings[] = is_string( $key )
                ? sprintf( '%s : %s', str_pad( $key, $pad, '.' ), $value )
                : ' - ' . $value;
        }

        return implode( PHP_EOL, $strings ) . PHP_EOL . PHP_EOL;
    }
}
