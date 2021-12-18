<?php

namespace MobileContactBar;

final class PublicArea
{
    public $option_bar = [];
    public $checked_contacts = [];


    public function init()
    {
        $this->option_bar = abmcb( Options::class )->get_option( abmcb()->id, 'default_option_bar', 'is_valid_option_bar' );
        $this->checked_contacts = array_filter( $this->option_bar['contacts'], function( $contact ) { return $contact['checked']; });

        if ( count( $this->checked_contacts ) > 0 )
        {
            $is_mobile = wp_is_mobile();
            $device = $this->option_bar['settings']['bar']['device'];
            
            if (( $is_mobile && 'mobile' === $device ) || ( ! $is_mobile && 'desktop' === $device ) || ( 'both' === $device ))
            {
                add_action( 'wp_head', [$this, 'wp_head'], 7 );
                add_action( 'wp_enqueue_scripts', [$this, 'wp_enqueue_scripts'] );
                add_action( 'wp_footer', [$this, 'wp_footer'] );
            }
        }
    }


    /**
     * Loads Font Awesome styles and scripts.
     */
    public function wp_enqueue_scripts()
    {
        wp_enqueue_style(
            'fa',
            plugin_dir_url( abmcb()->file ) . 'dist/css/public.min.css',
            [],
            '5.15.4',
            'all'
        );

        if ( $this->option_bar['settings']['toggle']['is_render'] && $this->option_bar['settings']['toggle']['is_cookie'] )
        {
            wp_enqueue_script(
                'mobile-contact-bar',
                plugin_dir_url( abmcb()->file ) . 'dist/js/public.min.js',
                [],
                abmcb()->version,
                true
            );
        }
    }


    /**
     * Adds plugin related CSS styles within the head section.
     */
    public function wp_head()
    {
        ?>
        <style id="mobile-contact-bar-css" type="text/css" media="screen"><?php echo strip_tags( $this->option_bar['styles'] ); ?></style>
        <?php
    }


    /**
     * Invokes mcb_public_render_html action only once.
     */
    public function wp_footer()
    {
        if ( ! has_action( 'mcb_public_render_html' ))
        {
            add_action( 'mcb_public_render_html', [$this, 'mcb_public_render_html'], 10, 3 );
        }

        do_action( 'mcb_public_render_html', $this->option_bar['settings'], $this->option_bar['contacts'], $this->checked_contacts );
    }


    /**
     * Outputs contact bar.
     *
     * @param array $settings Associative array of settings
     * @param array $contacts Associative array of displayable contacts
     */
    public function mcb_public_render_html( $settings, $contacts, $checked_contacts )
    {
        if ( 1 === did_action( 'mcb_public_render_html' ))
        {    
            echo abmcb( Renderer::class )->bar( $settings, $contacts, $checked_contacts );
        }
    }
}
