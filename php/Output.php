<?php

namespace MobileContactBar;

use MobileContactBar\Contacts\Validator;


final class Output
{
    public function bar( $settings, $contacts )
    {
        $out = '';
           
        $paths = [
            'top_rounded'    => '<path d="M 550 0 L 496.9 137.2 C 490.4 156.8 474.1 170 451.4 170 H 98.6 C 77.9 170 59.6 156.8 53.1 137.2 L 0 0 z">',
            'top_sharp'      => '<path d="M 550 0 L 494.206 170 H 65.794 L 0 0 z">',
            'bottom_rounded' => '<path d="M 550 170 L 496.9 32.8 C 490.4 13.2 474.1 0 451.4 0 H 98.6 C 77.9 0 59.6 13.2 53.1 32.8 L 0 170 z">',
            'bottom_sharp'   => '<path d="M 550 170 L 494.206 0 H 65.794 L 0 170 z">',
        ];

        $out .= '<div id="mobile-contact-bar">';

        if ( $settings['toggle']['is_render'] && $settings['bar']['is_fixed'] )
        {
            $checked = isset( $settings['toggle']['is_closed'] ) && $settings['toggle']['is_closed'] ? 'checked' : '';
            $out .= '<input id="mobile-contact-bar-toggle-checkbox" name="mobile-contact-bar-toggle-checkbox" type="checkbox"' . $checked . '>';
            $out .= '<label for="mobile-contact-bar-toggle-checkbox" id="mobile-contact-bar-toggle">';
            $out .= ( $settings['toggle']['label'] ) ? '<span>' . esc_attr( $settings['toggle']['label'] ) . '</span>' : '';

            $out .= '<svg viewBox="0 0 550 170" width="110" height="34">';
            if ( 'bottom' === $settings['bar']['vertical_alignment'] )
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
            elseif ( 'top' === $settings['bar']['vertical_alignment'] )
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
        $out .= '<ul>';
        $new_tab = ( $settings['bar']['is_new_tab'] ) ? 'target="_blank" rel="noopener"' : '';

        foreach ( $contacts as $contact )
        {
            $id = isset( $contact['id'] )
                ? sprintf( 'id="%s"', esc_attr( $contact['id'] ))
                : '';

            $uri = Validator::escape_contact_uri( $contact['uri'] );

            if ( ! empty( $uri ) && ! empty( $contact['parameters'] ) && is_array( $contact['parameters'] ))
            {
                $query_arg = [];

                foreach ( $contact['parameters'] as $parameter )
                {
                    if ( $parameter['value'] )
                    {
                        $key             = sanitize_key( $parameter['key'] );
                        $query_arg[$key] = urlencode( $parameter['value'] );
                    }
                }
                $uri = add_query_arg( $query_arg, $uri );
            }

            $badge = apply_filters( 'mcb_public_add_badge', '', $contact['type'] );
            $label = ( ! empty( $contact['label'] )) ? sprintf( '<span class="mobile-contact-bar-label">%s</span>', str_replace( '\n', '<br />', esc_attr( $contact['label'] ))) : '';

            // TODO move validation to Options
            if ( 'fa' === $contact['brand'] )
            {
                $path = plugin_dir_url( abmcb()->file ) . 'assets/icons/fa/svgs/' . $contact['group'] . '/' . $contact['icon'] . '.svg';
                $svg = file_get_contents( $path );

                $icon = sprintf( '<span class="mobile-contact-bar-icon mobile-contact-bar-fa">%s%s</span>', $svg, $badge );
            }
            elseif ( 'ti' === $contact['brand'] )
            {
                $path = plugin_dir_url( abmcb()->file ) . 'assets/icons/ti/icons/'. $contact['icon'] . '.svg';
                $svg = file_get_contents( $path );

                $icon = sprintf( '<span class="mobile-contact-bar-icon">%s%s</span>', $svg, $badge );
            }
            else
            {
                $icon = '';
            }

            $out .= sprintf( '<li class="mobile-contact-bar-item" %s>', $id );
            // $out .= sprintf( '<a href="%s" %s>', esc_url( $uri ), $new_tab );
            $out .= sprintf( '<a href="%s" %s>', '', $new_tab );
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
            $out .= '</a>';

            ob_start();
            echo do_action( 'mcb_public_add_script', $contact['type'] );
            $out .= ob_get_contents();
            ob_end_clean();

            $out .= '</li>';
        }

        $out .= '</ul>';
        $out .= '</nav>';

        $out .= '</div>';

        return $out;
    }
}
