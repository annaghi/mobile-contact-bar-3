<?php

defined( 'ABSPATH' ) || exit();


class MobileContactBar_Plugin_Check
{
    protected $file;
    protected $versions;


    public function __construct( $file, array $versions = [] )
    {
        $this->file = realpath( $file );

        $plugin_data = get_file_data(
            $this->file,
            [
                'php' => 'Requires PHP',
                'wp'  => 'Requires at least',
            ],
            'plugin'
        );
        $this->versions = wp_parse_args( $versions, $plugin_data );
    }


    public function can_proceed()
    {
        if ( $this->is_valid() )
        {
            return true;
        }

        add_action( 'admin_notices', [$this, 'admin_notices'] );
        return false;
    }


    public function is_valid()
    {
        return $this->is_PHP_valid() && $this->is_WP_valid();
    }


    public function is_PHP_valid()
    {
        return ! version_compare( PHP_VERSION, $this->versions['php'], '<' );
    }


    public function is_WP_valid()
    {
        global $wp_version;
        return ! version_compare( $wp_version, $this->versions['wp'], '<' );
    }


    public function admin_notices( $plugin )
    {
        if ( $this->is_valid())
        {
            return;
        }

        $plugin_data = get_file_data( $this->file, ['name' => 'Plugin Name'], 'plugin' );
        $this->view_notice( $plugin_data['name'] );
    }


    protected function get_messages()
    {
        return [
            __( 'The %s plugin is currently NOT RUNNING, you can deactivate it.', 'mobile-contact-bar' ),
            __( 'This plugin requires %s or greater in order to work properly.', 'mobile-contact-bar' ),
            __( 'PHP version', 'mobile-contact-bar' ),
            __( 'WordPress version', 'mobile-contact-bar' ),
            __( 'You can use the %s plugin to restore %s to the previous version, or', 'mobile-contact-bar' ),
            __( 'please contact your hosting provider or server administrator about', 'mobile-contact-bar' ),
            __( 'please consider', 'mobile-contact-bar' ),
            __( 'updating PHP', 'mobile-contact-bar' ),
            __( 'updating WordPress', 'mobile-contact-bar' ),
        ];
    }


    protected function view_notice( $plugin_name )
    {
        $notice_template = '<div class="notice notice-error error"><p><strong>%s</strong></p><p>%s</p><p>%s</p></div>';
        $messages = $this->get_messages();
        $rollback_message =
        sprintf(
            $messages[4],
            '<a href="https://wordpress.org/plugins/wp-rollback/">WP Rollback</a>',
            $plugin_name
        );
        if ( ! $this->is_PHP_valid() )
        {
            printf(
                $notice_template,
                sprintf( $messages[0], $plugin_name ),
                sprintf( $messages[1], $messages[2] . ' ' . $this->versions['php'] ),
                $rollback_message . ' ' . $messages[5] . ' ' . sprintf( '<a href="https://wordpress.org/support/update-php/" target="_blank">%s</a>', $messages[7] )
            );
        }
        elseif ( ! $this->is_WP_valid() )
        {
            printf(
                $notice_template,
                sprintf( $messages[0], $plugin_name ),
                sprintf( $messages[1], $messages[3] . ' ' . $this->versions['wp'] ),
                $rollback_message . ' ' . $messages[6] . ' ' . sprintf( '<a href="%s">%s</a>', esc_url( admin_url( 'update-core.php' )), $messages[8] )
            );
        }
    }
}


if ( ! function_exists( 'logg' ))
{
    function logg( $m ) {}
}

if ( ! function_exists('console_log'))
{
    function console_log( $m ) {}
}
