<?php

namespace MobileContactBar\Controllers;


final class IFrameController
{
    public $checked_buttons = [];


    /**
     * Hooks WordPress's actions and filters for public pages.
     * 
     * @return void
     */
    public function wp()
    {
        remove_action( 'wp_head', 'print_emoji_detection_script', 7 );
        remove_action( 'wp_print_styles', 'print_emoji_styles' );

        $this->checked_buttons = array_filter( abmcb()->option_bar['buttons'], function ( $button ) { return $button['checked']; });

        if ( count( $this->checked_buttons ) > 0 )
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
                add_action( 'wp_footer', [$this, 'wp_render_mcb'], 99 );
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
        $space     = (int) abmcb()->option_bar['settings']['bar']['space'];
        $offset_32 = ( is_admin_bar_showing() ) ? 32 + $shortest + $space : $shortest + $space;
        $offset_46 = ( is_admin_bar_showing() ) ? 46 + $shortest + $space : $shortest + $space;
        
        if ( $offset_32 > 32 && $offset_46 > 46 )
        {
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
            abmcb()->slug,
            $wp_upload_dir['baseurl'] . '/' . abmcb()->slug . '/' . abmcb()->css,
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
        if ( ! has_action( 'mcb_public_render_html' ))
        {
            add_action( 'mcb_public_render_html', [$this, 'mcb_public_render_html'], 10, 2 );
        }

        do_action( 'mcb_public_render_html', abmcb()->option_bar['settings'], $this->checked_buttons );
    }


    /**
     * Renders the bar.
     *
     * @param  array $settings
     * @param  array $buttons
     * @return void
     */
    public function mcb_public_render_html( $settings, $buttons )
    {
        if ( 1 === did_action( 'mcb_public_render_html' ))
        {    
            echo $this->output( $settings, $buttons );
        }
    }


    /**
     * @return string HTML
     * 
     * @global $wp
     * 
     * @param  array  $settings
     * @param  array  $buttons
     * @return string
     */
    public function output( $settings, $buttons )
    {
        global $wp;

        $out = '';

        $current_url = home_url( add_query_arg( [], $wp->request ));

        $out .= '<div id="mobile-contact-bar">';

        if ( $settings['toggle']['is_render'] && $settings['bar']['is_fixed'] )
        {
            $checked = ( $settings['toggle']['is_closed'] ) ? 'checked' : '';
            $out .= '<input id="mobile-contact-bar-toggle-checkbox" name="mobile-contact-bar-toggle-checkbox" type="checkbox"' . $checked . '>';

            $out .= '<label for="mobile-contact-bar-toggle-checkbox" id="mobile-contact-bar-toggle">';
            $out .= ( $settings['toggle']['label'] ) ? '<div>' . esc_html( $settings['toggle']['label'] ) . '</div>' : '';
            $out .= '<svg viewBox="0 0 550 170" width="110" height="34" fill="currentColor">';
            if ( 'rounded' === $settings['toggle']['shape'] )
            {
                $out .= '<path d="M 550 170 L 497 33 C 490.5 13.4 474.2 0 450 0 H 100 C 75.8 0 59.5 13.4 53 33 L 0 170 Z">';
            }
            else
            {
                $out .= '<path d="M 550 170 L 495 0 H 55 L 0 170 Z">';
            }
            $out .= '</svg>';
            $out .= '</label>';
        }

        $out .= '<nav id="mobile-contact-bar-nav">';
        $out .= '<ul>' . PHP_EOL;
        $new_tab = ( $settings['bar']['is_new_tab'] ) ? ' target="_blank" rel="noopener"' : '';

        foreach ( $buttons as $button )
        {
            $uri = $button['uri'];
            if ( $uri && ! empty( $button['query'] ))
            {
                $query_args = [];
                foreach ( $button['query'] as $parameter )
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

            $badge = abmcb()->button_types[$button['type']]->badge();
            $label = ( esc_attr( $button['label'] ))
                ? sprintf( '<span class="mobile-contact-bar-label">%s</span>', str_replace( '\n', '<br />', esc_attr( $button['label'] )))
                : '';

            if ( 'ti' === $button['brand'] )
            {
                $icon = sprintf(
                    '<span class="mobile-contact-bar-icon">%s%s</span>',
                    file_get_contents( plugin_dir_path( abmcb()->file ) . 'assets/svg/ti/icons/'. $button['icon'] . '.svg' ),
                    ( 'rectangle' === $settings['buttons']['shape'] ) ? $badge : ''
                );
            }
            elseif ( 'fa' === $button['brand'] )
            {
                $icon = sprintf(
                    '<span class="mobile-contact-bar-icon mobile-contact-bar-fa">%s%s</span>',
                    file_get_contents( plugin_dir_path( abmcb()->file ) . 'assets/svg/fa/svgs/' . $button['group'] . '/' . $button['icon'] . '.svg' ),
                    ( 'rectangle' === $settings['buttons']['shape'] ) ? $badge : ''
                );
            }
            else
            {
                $icon = '';
            }

            $id = esc_attr( $button['id'] );
            $out .= sprintf( '<li%s>', ( $id ) ? sprintf( ' id="%s"', $id ) : '' );

            $active = ( $uri == $current_url ) ? ' mobile-contact-bar-active' : '';
            $out .= sprintf( '<a class="mobile-contact-bar-item%s" href="%s"%s>', $active, esc_url( $uri, abmcb()->schemes ), $new_tab );
            if ( 'below' === $settings['buttons']['label_position'] )
            {
                $out .= $icon;
                $out .= $label;
            }
            else
            {
                $out .= $label;
                $out .= $icon;
            }

            $out .= sprintf( '<span class="screen-reader-text">%s</span>', esc_html( $button['text'] ));
            $out .= ( 'circle' === $settings['buttons']['shape'] ) ? $badge : '';
            $out .= '</a>';

            ob_start();
            echo abmcb()->button_types[$button['type']]->script();
            $out .= ob_get_contents();
            ob_end_clean();

            $out .= '</li>' . PHP_EOL;
        }

        $out .= '</ul>';
        $out .= '</nav>';

        $out .= '</div>';

        return $out;
    }
}
