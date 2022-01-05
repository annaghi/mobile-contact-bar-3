<?php

namespace MobileContactBar\Settings;


final class View
{
    /**
     * Adds meta box for each section in the 'settings'.
     * 
     * @return void
     */
    public function add()
    {
        $fields = abmcb( Input::class )->fields();

        foreach ( $fields as $section_key => $section )
        {
            if ( 'badges' === $section_key && ! class_exists( 'WooCommerce' ))
            {
                continue;
            }

            $title = ucwords( str_replace( '_', ' & ', $section_key ), ' &');

            add_settings_section(
                'mcb-meta-box-' . $section_key,
                __( $title, 'mobile-contact-bar' ),
                false,
                abmcb()->id
            );

            foreach ( $section as $setting_key => $setting )
            {
                $args = [
                    'class'       => sprintf( 'mcb-setting-%s-%s', esc_attr( $section_key ), esc_attr( $setting_key )),
                    'section_key' => $section_key,
                    'setting_key' => $setting_key,
                    'setting'     => $setting,
                ];

                if ( isset( $setting['parent'] ))
                {
                    $args['class'] = sprintf( 'mcb-setting-%1$s-%2$s hidden mcb-child mcb-parent-%1$s-%3$s',
                        esc_attr( $section_key ),
                        esc_attr( $setting_key ),
                        esc_attr( $setting['parent'])
                    );
                }
                elseif ( isset( $setting['trigger'] ))
                {
                    $args['class'] = sprintf( 'mcb-setting-%1$s-%2$s mcb-parent mcb-parent-%1$s-%2$s mcb-trigger-%3$s',
                        esc_attr( $section_key ),
                        esc_attr( $setting_key ),
                        esc_attr( $setting['trigger'])
                    );
                }

                add_settings_field(
                    $setting_key,
                    $this->output_setting_th( $section_key, $setting_key, $setting ),
                    [$this, 'callback_render_setting_td'],
                    abmcb()->id,
                    'mcb-meta-box-' . $section_key,
                    $args
                );
            }
        }
    }


    /**
     * Outputs a setting input field's TH part.
     *
     * @param  string $section_key
     * @param  string $setting_key
     * @param  array  $setting
     * @return string              HTML
     */
    private function output_setting_th( $section_key, $setting_key, $setting )
    {
        $out = '';

        switch ( $setting['type'] )
        {
            case 'radio':
            case 'checkbox':
            case 'checkbox-group':
            case 'color-picker':
            case 'color-picker-group':
                $out = esc_attr__( $setting['title'] );
                break;

            case 'text':
            case 'select':
            case 'number':
            case 'slider':
                $out = sprintf(
                    '<label for="mcb-%s-%s">%s</label>',
                    esc_attr( $section_key ),
                    esc_attr( $setting_key ),
                    esc_attr__( $setting['title'] )
                );
                break;
        }

        return $out;
    }


    /**
     * Renders a setting input field's TD part.
     *
     * @param  array  $args
     *         string $section_key
     *         string $setting_key
     *         array  $setting
     * @return void                HTML
     */
    public function callback_render_setting_td( $args )
    {
        extract( $args );

        $prefix = abmcb()->id . '[settings]';

        $value = abmcb()->option_bar['settings'][$section_key][$setting_key];

        switch ( $setting['type'] )
        {
            case 'color-picker':
                printf(
                    '<input type="text" class="color-picker" id="mcb-%1$s-%2$s" name="' . $prefix . '[%1$s][%2$s]" data-alpha-enabled="true" value="%3$s">',
                    esc_attr( $section_key ),
                    esc_attr( $setting_key ),
                    esc_attr( $value )
                );
                break;

            case 'color-picker-group':
                foreach ( $setting['options'] as $option_key => $option )
                {
                    printf(
                        '<fieldset class="mcb-color-picker-group" id="mcb-%s-%s--%s">',
                        esc_attr( $section_key ),
                        esc_attr( $setting_key ),
                        esc_attr( $option_key )
                    );
                    printf(
                        '<label class="mcb-color-picker-label">%4$s</label>
                        <input type="text" class="color-picker" name="' . $prefix . '[%1$s][%2$s][%3$s]" data-alpha-enabled="true" value="%5$s">',
                        esc_attr( $section_key ),
                        esc_attr( $setting_key ),
                        esc_attr( $option_key ),
                        esc_html( $option['desc'] ),
                        esc_attr( $value[$option_key] )
                    );
                    echo '</fieldset>';
                }
                break;

            case 'select':
                printf(
                    '<select class="mcb-regular-text" id="mcb-%1$s-%2$s" name="' . $prefix . '[%1$s][%2$s]">',
                    esc_attr( $section_key ),
                    esc_attr( $setting_key )
                );
                foreach ( $setting['options'] as $option_key => $option )
                {
                    printf(
                        '<option value="%s" %s>%s</option>',
                        esc_attr( $option_key ),
                        selected( $option_key, $value, false ),
                        esc_html( $option )
                    );
                }
                echo '</select>';
                break;

            case 'radio':
                printf(
                    '<fieldset class="mcb-radio-group" id="mcb-%s-%s">',
                    esc_attr( $section_key ),
                    esc_attr( $setting_key )
                );
                foreach ( $setting['options'] as $option_key => $option )
                {
                    printf(
                        '<label class="mcb-radio-label" for="mcb-%1$s-%2$s--%3$s">
                            <input type="radio" id="mcb-%1$s-%2$s--%3$s" name="' . $prefix . '[%1$s][%2$s]" value="%3$s" %4$s>%5$s</label>',
                        esc_attr( $section_key ),
                        esc_attr( $setting_key ),
                        esc_attr( $option_key ),
                        checked( $option_key, $value, false ),
                        esc_html( $option )
                    );
                }
                echo '</fieldset>';
                break;

            case 'checkbox':
                printf(
                    '<label for="mcb-%1$s-%2$s">
                        <input type="checkbox" id="mcb-%1$s-%2$s" name="' . $prefix . '[%1$s][%2$s]" %3$s value="1">%4$s</label>',
                    esc_attr( $section_key ),
                    esc_attr( $setting_key ),
                    checked( $value, 1, false ),
                    esc_html( $setting['label'] )
                );
                break;

            case 'checkbox-group':
                printf(
                    '<fieldset class="mcb-checkbox-group" id="mcb-%s-%s">',
                    esc_attr( $section_key ),
                    esc_attr( $setting_key )
                );
                foreach ( $setting['options'] as $option_key => $option )
                {
                    printf(
                        '<label class="mcb-checkbox-label" for="mcb-%1$s-%2$s--%3$s">
                            <input type="checkbox" id="mcb-%1$s-%2$s--%3$s" name="' . $prefix . '[%1$s][%2$s][%3$s]" value="1" %4$s>%5$s</label>',
                        esc_attr( $section_key ),
                        esc_attr( $setting_key ),
                        esc_attr( $option_key ),
                        checked( $value[$option_key], 1, false ),
                        esc_html( $option['label'] )
                    );
                }
                echo '</fieldset>';
                break;

            case 'text':
                printf(
                    '<input type="text" class="mcb-regular-text" id="mcb-%1$s-%2$s" name="' . $prefix . '[%1$s][%2$s]" value="%3$s">',
                    esc_attr( $section_key ),
                    esc_attr( $setting_key ),
                    esc_attr( $value )
                );
                break;

            case 'number':
                printf(
                    '<input type="number" class="mcb-regular-text" id="mcb-%1$s-%2$s" name="' . $prefix . '[%1$s][%2$s]" value="%3$d">
                    <span>%4$s</span>',
                    esc_attr( $section_key ),
                    esc_attr( $setting_key ),
                    esc_attr( $value ),
                    isset( $setting['postfix'] ) ? esc_html( $setting['postfix'] ) : ''
                );
                break;

            case 'slider':
                printf(
                    '<input type="range" class="mcb-slider-input" id="mcb-%1$s-%2$s" name="' . $prefix . '[%1$s][%2$s]" value="%3$s" min="%4$s" max="%5$s" step="%6$s" data-postfix="%7$s">
                    <span class="mcb-slider-value">%3$s %7$s</span>',
                    esc_attr( $section_key ),
                    esc_attr( $setting_key ),
                    esc_attr( $value ),
                    esc_attr( $setting['min'] ),
                    esc_attr( $setting['max'] ),
                    esc_attr( $setting['step'] ),
                    isset( $setting['postfix'] ) ? esc_html( $setting['postfix'] ) : ''
                );
                break;
        }

        if ( ! empty( $setting['desc'] ))
        {
            printf( '<p class="mcb-description">%s</p>', esc_html( $setting['desc'] ));
        }
    }
}
