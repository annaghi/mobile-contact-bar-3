<?php

namespace MobileContactBar\Controllers;


final class IFrameController
{
    public $checked_contacts = [];


    /**
     * Hooks WordPress's actions and filters for public pages.
     * 
     * @return void
     */
    public function init()
    {
        remove_action( 'wp_head', 'print_emoji_detection_script', 7 );
        remove_action( 'wp_print_styles', 'print_emoji_styles' );

        $this->checked_contacts = array_filter( abmcb()->option_bar['contacts'], function ( $contact ) { return $contact['checked']; });

        if ( count( $this->checked_contacts ) > 0 )
        {
            add_action( 'wp_enqueue_scripts', [$this, 'wp_enqueue_scripts'] );

            if ( is_admin_bar_showing() && 'top' === abmcb()->option_bar['settings']['bar']['position'] )
            {
                add_action( 'wp_head', [$this, 'wp_head'], 99 );
                add_action( 'wp_after_admin_bar_render', [$this, 'wp_render_mcb'] );
            }
            elseif ( 'top' === abmcb()->option_bar['settings']['bar']['position'] )
            {
                add_action( 'wp_head', [$this, 'wp_head'], 99 );
                add_action( 'wp_footer', [$this, 'wp_render_mcb'] );
            }
            else
            {
                add_action( 'wp_footer', [$this, 'wp_render_mcb'], 99 );
            }
        }
    }


    /**
     * Renders inline styles if the bar will be positioned at the top of the screen.
     * 
     * @return void
     */
    public function wp_head()
    {
        $type_attr = current_theme_supports( 'html5', 'style' ) ? '' : ' type="text/css"';
        $shortest  = (int) abmcb()->option_bar['settings']['bar']['shortest'];
        $offset_32 = ( is_admin_bar_showing() ) ? 32 + $shortest : $shortest;
        $offset_46 = ( is_admin_bar_showing() ) ? 46 + $shortest : $shortest;

        ?>
<style id="<?php echo abmcb()->slug, '-inline-css'; ?>"<?php echo $type_attr; ?> media="screen">
    html { margin-top: <?php echo $offset_32; ?>px !important; }
    * html body { margin-top: <?php echo $offset_32; ?>px !important; }
    <?php if ( is_admin_bar_showing() ) : ?>
        .admin-bar { --global--admin-bar--height:<?php echo $offset_32; ?>px; }
    <?php else : ?>
        :root { --global--admin-bar--height:<?php echo $offset_32; ?>px; }
    <?php endif; ?>
    @media screen and ( max-width: 782px ) {
        html { margin-top: <?php echo $offset_46; ?>px !important; }
        * html body { margin-top: <?php echo $offset_46; ?>px !important; }
        <?php if ( is_admin_bar_showing() ) : ?>
            .admin-bar { --global--admin-bar--height:<?php echo $offset_46; ?>px; }
        <?php else : ?>
            :root { --global--admin-bar--height:<?php echo $offset_46; ?>px; }
        <?php endif; ?>
    }
</style>
        <?php
    }


    /**
     * Loads styles and optional scripts for the plugin.
     * 
     * @return void
     */
    public function wp_enqueue_scripts()
    {
        $wp_upload_dir = wp_get_upload_dir();
        wp_enqueue_style(
            abmcb()->slug . '-base',
            $wp_upload_dir['baseurl'] . '/' . abmcb()->slug . '/' . abmcb()->base_css,
            [],
            abmcb()->version,
            'all'
        );

        if ( abmcb()->option_bar['settings']['toggle']['is_render'] && abmcb()->option_bar['settings']['toggle']['is_cookie'] )
        {
            wp_enqueue_script(
                abmcb()->slug,
                plugin_dir_url( abmcb()->file ) . 'assets/js/public.js',
                [],
                abmcb()->version,
                true
            );
        }
    }


    /**
     * Invokes mcb_public_render_html action only once.
     * 
     * @return void
     */
    public function wp_render_mcb()
    {
        echo $this->output();
    }


    /**
     * @return string HTML
     * 
     * @global $wp
     * 
     * @return string
     */
    public function output()
    {
        global $wp;

        $out = '';

        $current_url = home_url( add_query_arg( [], $wp->request ));

        $settings = abmcb()->option_bar['settings'];
        $contacts = $this->checked_contacts;

        $paths = [
            'top_rounded'    => '<path d="M 550 0 L 496.9 137.2 C 490.4 156.8 474.1 170 451.4 170 H 98.6 C 77.9 170 59.6 156.8 53.1 137.2 L 0 0 z">',
            'top_sharp'      => '<path d="M 550 0 L 494.206 170 H 65.794 L 0 0 z">',
            'bottom_rounded' => '<path d="M 550 170 L 496.9 32.8 C 490.4 13.2 474.1 0 451.4 0 H 98.6 C 77.9 0 59.6 13.2 53.1 32.8 L 0 170 z">',
            'bottom_sharp'   => '<path d="M 550 170 L 494.206 0 H 65.794 L 0 170 z">',
        ];

        $out .= '<div id="mobile-contact-bar">';

        if ( $settings['toggle']['is_render'] && $settings['bar']['is_sticky'] && in_array( $settings['bar']['position'], ['bottom', 'top'] ))
        {
            $checked = ( $settings['toggle']['is_closed'] ) ? 'checked' : '';
            $out .= '<input id="mobile-contact-bar-toggle-checkbox" name="mobile-contact-bar-toggle-checkbox" type="checkbox"' . $checked . '>';
            $out .= '<label for="mobile-contact-bar-toggle-checkbox" id="mobile-contact-bar-toggle">';
            $out .= ( $settings['toggle']['label'] ) ? '<span>' . esc_html( $settings['toggle']['label'] ) . '</span>' : '';

            $out .= '<svg viewBox="0 0 550 170" width="110" height="34" fill="currentColor">';
            if ( 'bottom' === $settings['bar']['position'] )
            {
                if ( 'rounded' === $settings['toggle']['shape'] )
                {
                    $out .= $paths['bottom_rounded'];
                }
                else
                {
                    $out .= $paths['bottom_sharp'];
                }
            }
            elseif ( 'top' === $settings['bar']['position'] )
            {
                if ( 'rounded' === $settings['toggle']['shape'] )
                {
                    $out .= $paths['top_rounded'];
                }
                else
                {
                    $out .= $paths['top_sharp'];
                }
            }
            $out .= '</svg>';
            $out .= '</label>';
        }

        $out .= '<nav id="mobile-contact-bar-nav">';
        $out .= '<ul>' . PHP_EOL;
        $new_tab = ( $settings['bar']['is_new_tab'] ) ? ' target="_blank" rel="noopener"' : '';

        foreach ( $contacts as $contact )
        {
            $uri = $contact['uri'];
            if ( $uri && ! empty( $contact['query'] ))
            {
                $query_args = [];
                foreach ( $contact['query'] as $parameter )
                {
                    $key   = rawurlencode( $parameter['key'] );
                    $value = rawurlencode( $parameter['value'] );

                    if ( $key && $value )
                    {
                        $query_args[$key] = $value;
                    }
                }
                $uri = add_query_arg( $query_args, $uri );
            }

            $badge = abmcb()->contact_types[$contact['type']]->badge();
            $label = ( esc_attr( $contact['label'] ))
                ? sprintf( '<span class="mobile-contact-bar-label">%s</span>', str_replace( '\n', '<br />', esc_attr( $contact['label'] )))
                : '';

            if ( 'ti' === $contact['brand'] )
            {
                $icon = sprintf(
                    '<span class="mobile-contact-bar-icon">%s%s</span>',
                    file_get_contents( plugin_dir_path( abmcb()->file ) . 'assets/svg/ti/icons/'. $contact['icon'] . '.svg' ),
                    $badge
                );
            }
            elseif ( 'fa' === $contact['brand'] )
            {
                $icon = sprintf(
                    '<span class="mobile-contact-bar-icon mobile-contact-bar-fa">%s%s</span>',
                    file_get_contents( plugin_dir_path( abmcb()->file ) . 'assets/svg/fa/svgs/' . $contact['group'] . '/' . $contact['icon'] . '.svg' ),
                    $badge
                );
            }
            else
            {
                $icon = '';
            }

            $id = esc_attr( $contact['id'] );
            $out .= sprintf( '<li%s>', ( $id ) ? sprintf( ' id="%s"', $id ) : '' );

            $active = ( $uri == $current_url ) ? ' mobile-contact-bar-active' : '';
            $out .= sprintf( '<a class="mobile-contact-bar-item%s" href="%s"%s>', $active, esc_url( $uri, abmcb()->schemes ), $new_tab );
            if ( $settings['icons_labels']['label_position'] === 'below' )
            {
                $out .= $icon;
                $out .= $label;
            }
            else
            {
                $out .= $label;
                $out .= $icon;
            }
            $out .= sprintf( '<span class="screen-reader-text">%s</span>', esc_html( $contact['text'] ));
            $out .= '</a>';

            ob_start();
            echo abmcb()->contact_types[$contact['type']]->script();
            $out .= ob_get_contents();
            ob_end_clean();

            $out .= '</li>' . PHP_EOL;
        }

        $out .= '</ul>';
        $out .= '</nav>';

        $out .= '</div>';

        unset( $settings );
        unset( $contacts );

        return $out;
    }
}
