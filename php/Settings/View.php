<?php

namespace MobileContactBar\Settings;

use MobileContactBar\Settings;


final class View
{
    public $option_bar = [];


    /**
     * Adds meta box for each section in the 'settings'.
     * 
     * @param  array $option_bar
     * @return void
     */
    public function add( $option_bar = [] )
    {
        $this->option_bar = $option_bar;

        $input_fields = abmcb( Settings\Input::class )->input_fields();

        foreach ( $input_fields as $section_id => $section )
        {
            if ( 'badges' === $section_id && ! class_exists( 'WooCommerce' ))
            {
                continue;
            }

            $title = ucwords( str_replace( '_', ' & ', $section_id ), ' &');

            add_settings_section(
                'mcb-section-' . $section_id,
                __( $title, 'mobile-contact-bar' ),
                false,
                abmcb()->id
            );

            foreach ( $section as $setting_id => $setting )
            {
                $args = [
                    'class'      => sprintf( 'mcb-setting-%s-%s', esc_attr( $section_id ), esc_attr( $setting_id )),
                    'section_id' => $section_id,
                    'setting_id' => $setting_id,
                    'setting'    => $setting,
                ];

                if ( isset( $setting['parent'] ))
                {
                    $args['class'] = sprintf( 'mcb-setting-%1$s-%2$s hidden mcb-child mcb-parent-%1$s-%3$s',
                        esc_attr( $section_id ),
                        esc_attr( $setting_id ),
                        esc_attr( $setting['parent'])
                    );
                }
                elseif ( isset( $setting['trigger'] ))
                {
                    $args['class'] = sprintf( 'mcb-setting-%1$s-%2$s mcb-parent mcb-parent-%1$s-%2$s mcb-trigger-%3$s',
                        esc_attr( $section_id ),
                        esc_attr( $setting_id ),
                        esc_attr( $setting['trigger'])
                    );
                }

                add_settings_field(
                    $setting_id,
                    $this->output_setting_th( $section_id, $setting_id, $setting ),
                    [$this, 'callback_render_setting_td'],
                    abmcb()->id,
                    'mcb-section-' . $section_id,
                    $args
                );
            }
        }
    }


    /**
     * Outputs a setting input field's TH part.
     *
     * @param  string $section_id
     * @param  string $setting_id
     * @param  array  $setting
     * @return string              HTML
     */
    private function output_setting_th( $section_id, $setting_id, $setting )
    {
        switch ( $setting['type'] )
        {
            case 'radio':
            case 'checkbox':
            case 'checkbox-group':
            case 'color-picker':
                $out = esc_attr__( $setting['title'] );
                break;

            case 'text':
            case 'select':
            case 'number':
            case 'slider':
                $out = sprintf(
                    '<label for="mcb-%s-%s">%s</label>',
                    esc_attr( $section_id ),
                    esc_attr( $setting_id ),
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
     *         string $section_id
     *         string $setting_id
     *         array  $setting
     * @return void                HTML
     */
    public function callback_render_setting_td( $args )
    {
        extract( $args );

        $value = $this->option_bar['settings'][$section_id][$setting_id];

        switch ( $setting['type'] )
        {
            case 'color-picker':
                printf(
                    '<input type="text" class="color-picker" id="mcb-%1$s-%2$s" name="' . abmcb()->id . '[settings][%1$s][%2$s]" data-alpha-enabled="true" value="%3$s">',
                    esc_attr( $section_id ),
                    esc_attr( $setting_id ),
                    esc_attr(( $value === 'transparent' ) ? '' : $value )
                );
                break;

            case 'select':
                printf(
                    '<select class="mcb-regular-text" id="mcb-%1$s-%2$s" name="' . abmcb()->id . '[settings][%1$s][%2$s]">',
                    esc_attr( $section_id ),
                    esc_attr( $setting_id )
                );
                foreach ( $setting['options'] as $option_id => $option )
                {
                    printf(
                        '<option value="%s" %s>%s</option>',
                        esc_attr( $option_id ),
                        selected( $option_id, $value, false ),
                        esc_html( $option )
                    );
                }
                echo '</select>';
                break;

            case 'radio':
                printf(
                    '<fieldset class="mcb-radio-label-wrap" id="mcb-%s-%s">',
                    esc_attr( $section_id ),
                    esc_attr( $setting_id )
                );
                foreach ( $setting['options'] as $option_id => $option )
                {
                    printf(
                        '<label class="mcb-radio-label" for="mcb-%1$s-%2$s--%3$s">
                            <input type="radio" id="mcb-%1$s-%2$s--%3$s" name="' . abmcb()->id . '[settings][%1$s][%2$s]" value="%3$s" %4$s>%5$s</label>',
                        esc_attr( $section_id ),
                        esc_attr( $setting_id ),
                        esc_attr( $option_id ),
                        checked( $option_id, $value, false ),
                        esc_html( $option )
                    );
                }
                echo '</fieldset>';
                break;

            case 'checkbox':
                printf(
                    '<label for="mcb-%1$s-%2$s">
                        <input type="checkbox" id="mcb-%1$s-%2$s" name="' . abmcb()->id . '[settings][%1$s][%2$s]" %3$s value="1">%4$s</label>',
                    esc_attr( $section_id ),
                    esc_attr( $setting_id ),
                    checked( $value, 1, false ),
                    esc_html( $setting['label'] )
                );
                break;

            case 'checkbox-group':
                printf(
                    '<fieldset class="mcb-checkbox-label-wrap" id="mcb-%s-%s">',
                    esc_attr( $section_id ),
                    esc_attr( $setting_id )
                );
                foreach ( $setting['options'] as $option_id => $option )
                {
                    printf(
                        '<label class="mcb-checkbox-label" for="mcb-%1$s-%2$s--%3$s">
                            <input type="checkbox" id="mcb-%1$s-%2$s--%3$s" name="' . abmcb()->id . '[settings][%1$s][%2$s][%3$s]" value="1" %4$s>%5$s</label>',
                        esc_attr( $section_id ),
                        esc_attr( $setting_id ),
                        esc_attr( $option_id ),
                        checked( $value[$option_id], 1, false ),
                        esc_html( $option['label'] )
                    );
                }
                echo '</fieldset>';
                break;

            case 'text':
                printf(
                    '<input type="text" class="mcb-regular-text" id="mcb-%1$s-%2$s" name="' . abmcb()->id . '[settings][%1$s][%2$s]" value="%3$s">',
                    esc_attr( $section_id ),
                    esc_attr( $setting_id ),
                    esc_attr( $value )
                );
                break;

            case 'number':
                printf(
                    '<input type="number" class="mcb-regular-text" id="mcb-%1$s-%2$s" name="' . abmcb()->id . '[settings][%1$s][%2$s]" value="%3$d">
                    <span>%4$s</span>',
                    esc_attr( $section_id ),
                    esc_attr( $setting_id ),
                    esc_attr( $value ),
                    isset( $setting['postfix'] ) ? esc_html( $setting['postfix'] ) : ''
                );
                break;

            case 'slider':
                printf(
                    '<input type="range" class="mcb-slider-input" id="mcb-%1$s-%2$s" name="' . abmcb()->id . '[settings][%1$s][%2$s]" value="%3$s" min="%4$s" max="%5$s" step="%6$s" data-postfix="%7$s">
                    <span class="mcb-slider-value">%3$s %7$s</span>',
                    esc_attr( $section_id ),
                    esc_attr( $setting_id ),
                    esc_attr( $value ),
                    esc_attr( $setting['min'] ),
                    esc_attr( $setting['max'] ),
                    esc_attr( $setting['step'] ),
                    isset( $setting['postfix'] ) ? esc_html( $setting['postfix'] ) : ''
                );
                break;
        }

        if ( isset( $setting['desc'] ))
        {
            printf( '<p class="mcb-description">%s</p>', esc_html( $setting['desc'] ));
        }
    }
}
    