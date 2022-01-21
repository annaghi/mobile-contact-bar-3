<?php

namespace MobileContactBar\Styles;


final class CSS
{
    /**
     * Outputs the base styles generated from 'settings' and 'buttons'.
     *
     * @param  array  $settings
     * @param  array  $buttons
     * @return string           CSS
     */
    public function output( $settings = [], $buttons = [] )
    {
        $styles = '';

        $bar         = $settings['bar'];
        $general_btn = $settings['buttons'];
        $toggle      = $settings['toggle'];
        $badges      = $settings['badges'];

        $checked_buttons = array_filter( $buttons, function ( $button ) { return $button['checked']; } );
        $checked_buttons_count = count( $checked_buttons );

        // Bar
        $styles .= '#mobile-contact-bar{';
        $styles .= 'box-sizing:border-box;';
        $styles .= 'display:block;';
        $styles .= 'font-size:100%;';
        $styles .= 'font-size:1rem;';
        $styles .= 'opacity:' . $bar['opacity'] . ';';
        $styles .= 'z-index:9998;';
        $styles .= '}';

        // Clearfix
        $styles .= '#mobile-contact-bar:before,';
        $styles .= '#mobile-contact-bar:after{';
        $styles .= 'content:"";';
        $styles .= 'display:table;';
        $styles .= '}';
        $styles .= '#mobile-contact-bar:after{';
        $styles .= 'clear:both;';
        $styles .= '}';

        $styles .= '#mobile-contact-bar-nav{';
        // $styles .= 'overflow:hidden;';
        $styles .= 'height:100%;';
        $styles .= 'width:100%;';
        $styles .= '}';

        $styles .= $this->toggle( $bar, $toggle );

        $styles .= '#mobile-contact-bar-nav ul{';
        $styles .= 'list-style-type:none;';
        $styles .= 'margin:0;';
        $styles .= 'padding:0;';
        $styles .= 'overflow:hidden;';
        $styles .= $this->bar_borders( $bar );
        $styles .= '}';


        // Item
        $styles .= '#mobile-contact-bar-nav li{';
        $styles .= 'margin:0;';
        $styles .= 'padding:0;';
        $styles .= '}';

        $styles .= '.mobile-contact-bar-item{';
        $styles .= ( empty( $general_btn['background_color']['primary'] )) ? '' : 'background-color:' . $general_btn['background_color']['primary'] . ';';
        $styles .= 'text-decoration:none;';
        $styles .= 'outline:none;';
        $styles .= 'cursor:pointer;';
        $styles .= 'box-sizing:border-box;';
        $styles .= 'display:flex;';
        $styles .= 'flex-direction:column;';
        $styles .= 'justify-content:center;';
        $styles .= 'align-items:center;';
        $styles .= 'gap:' . $general_btn['gap'] . 'em;';
        $styles .= 'height:100%;';
        $styles .= '}';

        $styles .= $this->button_borders( $bar, $general_btn );

        $styles .= '.mobile-contact-bar-icon{';
        $styles .= 'display:inline-flex;';
        $styles .= 'position:relative;';
        $styles .= 'line-height:50%;';
        $styles .= ( empty( $general_btn['icon_color']['primary'] )) ? '' : 'color:' . $general_btn['icon_color']['primary'] . ';';
        $styles .= 'padding:0 5px;';
        $styles .= '}';

        $styles .= '.mobile-contact-bar-icon svg{';
        $styles .= 'width:1em;';
        $styles .= 'height:1em;';
        $styles .= 'font-size:' . $general_btn['icon_size'] . 'em;';
        $styles .= '}';

        $styles .= '.mobile-contact-bar-fa svg{';
        $styles .= 'fill:currentColor;';
        $styles .= '}';

        $styles .= '.mobile-contact-bar-label{';
        $styles .= ( empty( $general_btn['label_color']['primary'] )) ? '' : 'color:' . $general_btn['label_color']['primary'] . ';';
        $styles .= 'font-size:' . $general_btn['label_size'] . 'em;';
        $styles .= 'line-height:1;';
        $styles .= '}';

        $styles .= $this->badge( $badges );

        $styles .= $this->button_pseudo_classes( $bar, $general_btn, $toggle, $badges );

        $styles .= $this->bar_position( $bar, $general_btn, $checked_buttons_count );


        // Item customization
        foreach ( $checked_buttons as $custom_btn )
        {
            if ( $custom_btn['id'] )
            {
                $styles .= $this->custom_button( $general_btn, $custom_btn );
                $styles .= $this->custom_button_borders( $bar, $general_btn, $custom_btn );
                $styles .= $this->custom_button_pseudo_classes( $bar, $general_btn, $custom_btn );
            }
        }

        return $styles;
    }


    private function bar_borders( $bar )
    {
        $styles = '';

        $is_bar_border = $bar['border_width'] > 0 && ! empty( $bar['border_color'] );

        if ( $bar['is_borders']['top'] && $is_bar_border )
        {
            $styles .= 'border-top:' . $bar['border_width'] . 'px solid ' . $bar['border_color'] . ';';
        }
        if ( $bar['is_borders']['bottom'] && $is_bar_border )
        {
            $styles .= 'border-bottom:' . $bar['border_width'] . 'px solid ' . $bar['border_color'] . ';';
        }
        if ( $bar['is_borders']['left'] && $is_bar_border )
        {
            $styles .= 'border-left:' . $bar['border_width'] . 'px solid ' . $bar['border_color'] . ';';
        }
        if ( $bar['is_borders']['right'] && $is_bar_border )
        {
            $styles .= 'border-right:' . $bar['border_width'] . 'px solid ' . $bar['border_color'] . ';';
        }

        return $styles;
    }


    private function bar_position( $bar, $general_btn, $checked_buttons_count )
    {
        $styles = '';

        $grid_template_size = $bar['shortest'];
        switch ( $bar['position'] )
        {
            case 'top':
            case 'bottom':
                if ( $bar['is_borders']['top'] )
                {
                    $grid_template_size = $grid_template_size - $bar['border_width'];
                }
                if ( $bar['is_borders']['bottom'] )
                {
                    $grid_template_size = $grid_template_size - $bar['border_width'];
                }
                break;

            case 'left':
            case 'right':
                if ( $bar['is_borders']['left'] )
                {
                    $grid_template_size = $grid_template_size - $bar['border_width'];
                }
                if ( $bar['is_borders']['right'] )
                {
                    $grid_template_size = $grid_template_size - $bar['border_width'];
                }
                break;
        }

        if ( $bar['is_fixed'] )
        {
            switch ( $bar['position'] )
            {
                case 'top':
                    $styles .= '#mobile-contact-bar{';
                    $styles .= 'will-change:transform;';
                    $styles .= ( 'fix_min' === $bar['span'] ) ? 'width:' . $checked_buttons_count * $bar['shortest'] . 'px;' : 'width:100%;';
                    $styles .= 'position:fixed;';
                    $styles .= 'top:' . $bar['space'] . 'px;';
                    if ( 'fix_min' === $bar['span'] )
                    {
                        $styles .= 'left:' . $bar['alignment'] . '%;';
                        $styles .= 'transform:translateX(-' . $bar['alignment'] . '%);';
                    }
                    else
                    {
                        $styles .= 'left:0;';
                    }
                    $styles .= '}';

                    if ( 'fix_max' === $bar['span'] )
                    {
                        $styles .= '#mobile-contact-bar-nav{';
                        $styles .= 'background-color:' . $general_btn['background_color']['primary'] . ';';
                        $styles .= '}';
                    }

                    $styles .= '#mobile-contact-bar-nav ul{';
                    $styles .= ( 'stretch' === $bar['span'] ) ? 'width:100%;' : 'width:' . $checked_buttons_count * $bar['shortest'] . 'px;';
                    $styles .= 'position:relative;';
                    $styles .= 'left:' . $bar['alignment'] . '%;';
                    $styles .= 'transform:translateX(-' . $bar['alignment'] . '%);';
                    $styles .= 'display:grid;';
                    $styles .= ( 'stretch' === $bar['span'] )
                        ? 'grid-template-columns: repeat(' . $checked_buttons_count . ',1fr);'
                        : 'grid-template-columns: repeat(' . $checked_buttons_count . ',' . $bar['shortest'] . 'px);';
                    $styles .= 'grid-template-rows:' . $grid_template_size . 'px;';
                    $styles .= '}';

                    $styles .= '#wpadminbar ~ #mobile-contact-bar{';
                    $styles .= 'top:' . ( 32 + $bar['space'] ) . 'px;';
                    $styles .= '}';

                    $styles .= '@media only screen and (max-width: 782px){';
                    $styles .= '#wpadminbar ~ #mobile-contact-bar{';
                    $styles .= 'top:' . ( 46 + $bar['space'] ) . 'px;';
                    $styles .= '}';
                    $styles .= '}';

                    $styles .= '@media only screen and (max-width: 600px){';
                    $styles .= '#wpadminbar ~ #mobile-contact-bar{';
                    $styles .= 'position:sticky;';
                    $styles .= 'top:' . $bar['space'] . 'px;';
                    $styles .= 'margin-top:' . ( $bar['space'] - $bar['shortest'] ) . 'px;';
                    if ( 'fix_min' === $bar['span'] )
                    {
                        $styles .= 'left:calc(' . $bar['alignment'] . '% - '. ( $checked_buttons_count * $bar['shortest'] / 2 ) . 'px);';
                        $styles .= 'transform:none;';
                    }
                    $styles .= '}';
                    $styles .= '}';
                    break;

                case 'bottom':
                    $styles .= '#mobile-contact-bar{';
                    $styles .= ( 'fix_min' === $bar['span'] ) ? 'width:' . $checked_buttons_count * $bar['shortest'] . 'px;' : 'width:100%;';
                    $styles .= 'position:fixed;';
                    $styles .= 'bottom:' . $bar['space'] . 'px;';
                    if ( 'fix_min' === $bar['span'] )
                    {
                        $styles .= 'left:' . $bar['alignment'] . '%;';
                        $styles .= 'transform:translateX(-' . $bar['alignment'] . '%);';
                    }
                    else
                    {
                        $styles .= 'left:0;';
                    }
                    $styles .= '}';

                    if ( 'fix_max' === $bar['span'] )
                    {
                        $styles .= '#mobile-contact-bar-nav{';
                        $styles .= 'background-color:' . $general_btn['background_color']['primary'] . ';';
                        $styles .= '}';
                    }

                    $styles .= '#mobile-contact-bar-nav ul{';
                    $styles .= ( 'stretch' === $bar['span'] ) ? 'width:100%;' : 'width:' . $checked_buttons_count * $bar['shortest'] . 'px;';
                    $styles .= 'position:relative;';
                    $styles .= 'left:' . $bar['alignment'] . '%;';
                    $styles .= 'transform:translateX(-' . $bar['alignment'] . '%);';
                    $styles .= 'display:grid;';
                    $styles .= ( 'stretch' === $bar['span'] )
                        ? 'grid-template-columns: repeat(' . $checked_buttons_count . ',1fr);'
                        : 'grid-template-columns: repeat(' . $checked_buttons_count . ',' . $bar['shortest'] . 'px);';
                    $styles .= 'grid-template-rows:auto;';
                    $styles .= '}';

                    $styles .= '#mobile-contact-bar-nav li{';
                    $styles .= 'height:' . $grid_template_size . 'px;';
                    $styles .= '}';
                    break;

                case 'left':
                case 'right':
                    $styles .= '#mobile-contact-bar{';
                    $styles .= ( 'fix_min' === $bar['span'] ) ? 'height:' . $checked_buttons_count * $bar['shortest'] . 'px;' : 'height:100%;';
                    $styles .= 'width:' . $bar['shortest'] . 'px;';
                    $styles .= 'position:fixed;';
                    $styles .= $bar['position'] . ':' . $bar['space'] . 'px;';
                    if ( 'fix_min' === $bar['span'] )
                    {
                        $styles .= 'top:' . $bar['alignment'] . '%;';
                        $styles .= 'transform:translateY(-' . $bar['alignment'] . '%);';
                    }
                    else
                    {
                        $styles .= 'top:0;';
                    }
                    $styles .= '}';

                    if ( 'fix_max' === $bar['span'] )
                    {
                        $styles .= '#mobile-contact-bar-nav{';
                        $styles .= 'background-color:' . $general_btn['background_color']['primary'] . ';';
                        $styles .= '}';
                    }

                    $styles .= '#mobile-contact-bar-nav ul{';
                    $styles .= ( 'stretch' === $bar['span'] ) ? 'height:100%;' : 'height:' . $checked_buttons_count * $bar['shortest'] . 'px;';
                    $styles .= 'position:absolute;';
                    $styles .= 'top:' . $bar['alignment'] . '%;';
                    $styles .= 'transform:translateY(-' . $bar['alignment'] . '%);';
                    $styles .= 'display:grid;';
                    $styles .= 'grid-template-rows:repeat(' . $checked_buttons_count . ',1fr);';
                    $styles .= 'grid-template-columns:auto;';
                    $styles .= '}';

                    $styles .= '#mobile-contact-bar-nav li{';
                    $styles .= 'width:' . $grid_template_size . 'px;';
                    $styles .= '}';

                    $styles .= '#wpadminbar ~ #mobile-contact-bar{';
                    $styles .= ( 'fix_min' === $bar['span'] ) ? 'height:' . $checked_buttons_count * $bar['shortest'] . 'px;' : 'height:calc(100% - 32px);';
                    if ( 'fix_min' === $bar['span'] )
                    {
                        if ( $bar['alignment'] < 50 )
                        {
                            $styles .= 'top:calc(32px + ' . $bar['alignment'] . '%);';
                        }
                        else
                        {
                            $styles .= 'top:' . $bar['alignment'] . '%;';
                        }
                    }
                    else
                    {
                        $styles .= 'top:32px;';
                    }
                    $styles .= '}';

                    $styles .= '@media only screen and (max-width: 782px){';
                    $styles .= '#wpadminbar ~ #mobile-contact-bar{';
                    $styles .= ( 'fix_min' === $bar['span'] ) ? 'height:' . $checked_buttons_count * $bar['shortest'] . 'px;' : 'height:calc(100% - 46px);';
                    if ( 'fix_min' === $bar['span'] )
                    {
                        if ( $bar['alignment'] < 50 )
                        {
                            $styles .= 'top:calc(46px + ' . $bar['alignment'] . '%);';
                        }
                        else
                        {
                            $styles .= 'top:' . $bar['alignment'] . '%;';
                        }
                    }
                    else
                    {
                        $styles .= 'top:46px;';
                    }
                    $styles .= '}';
                    $styles .= '}';
                    break;
            }
        }
        else
        {
            switch ( $bar['position'] )
            {
                case 'top':
                    $styles .= '#mobile-contact-bar{';
                    $styles .= ( 'fix_min' === $bar['span'] ) ? 'width:' . $checked_buttons_count * $bar['shortest'] . 'px;' : 'width:100%;';
                    $styles .= 'position:absolute;';
                    $styles .= 'top:' . $bar['space'] . 'px;';
                    if ( 'fix_min' === $bar['span'] )
                    {
                        $styles .= 'left:' . $bar['alignment'] . '%;';
                        $styles .= 'transform:translateX(-' . $bar['alignment'] . '%);';
                    }
                    else
                    {
                        $styles .= 'left:0;';
                    }
                    $styles .= '}';

                    if ( 'fix_max' === $bar['span'] )
                    {
                        $styles .= '#mobile-contact-bar-nav{';
                        $styles .= 'background-color:' . $general_btn['background_color']['primary'] . ';';
                        $styles .= '}';
                    }

                    $styles .= '#mobile-contact-bar-nav ul{';
                    $styles .= ( 'stretch' === $bar['span'] ) ? 'width:100%;' : 'width:' . $checked_buttons_count * $bar['shortest'] . 'px;';
                    $styles .= 'position:relative;';
                    $styles .= 'left:' . $bar['alignment'] . '%;';
                    $styles .= 'transform:translateX(-' . $bar['alignment'] . '%);';
                    $styles .= 'display:grid;';
                    $styles .= 'grid-template-columns:repeat(' . $checked_buttons_count . ',1fr);';
                    $styles .= 'grid-template-rows:' . $grid_template_size . 'px;';
                    $styles .= '}';

                    $styles .= '#wpadminbar ~ #mobile-contact-bar{';
                    $styles .= 'top:' . ( 32 + $bar['space'] ) . 'px;';
                    $styles .= '}';

                    $styles .= '@media only screen and (max-width: 782px){';
                    $styles .= '#wpadminbar ~ #mobile-contact-bar{';
                    $styles .= 'top:' . ( 46 + $bar['space'] ) . 'px;';
                    $styles .= '}';
                    $styles .= '}';
                    break;

                case 'bottom':
                    $styles .= '#mobile-contact-bar{';
                    $styles .= ( 'fix_min' === $bar['span'] ) ? 'width:' . $checked_buttons_count * $bar['shortest'] . 'px;' : 'width:100%;';
                    $styles .= 'position:relative;';
                    $styles .= 'bottom:' . $bar['space'] . 'px;';
                    if ( 'fix_min' === $bar['span'] )
                    {
                        $styles .= 'left:' . $bar['alignment'] . '%;';
                        $styles .= 'transform:translateX(-' . $bar['alignment'] . '%);';
                    }
                    else
                    {
                        $styles .= 'left:0;';
                    }
                    $styles .= '}';

                    if ( 'fix_max' === $bar['span'] )
                    {
                        $styles .= '#mobile-contact-bar-nav{';
                        $styles .= 'background-color:' . $general_btn['background_color']['primary'] . ';';
                        $styles .= '}';
                    }

                    $styles .= '#mobile-contact-bar-nav ul{';
                    $styles .= ( 'stretch' === $bar['span'] ) ? 'width:100%;' : 'width:' . $checked_buttons_count * $bar['shortest'] . 'px;';
                    $styles .= 'position:relative;';
                    $styles .= 'left:' . $bar['alignment'] . '%;';
                    $styles .= 'transform:translateX(-' . $bar['alignment'] . '%);';
                    $styles .= 'display:grid;';
                    $styles .= 'grid-template-columns:repeat(' . $checked_buttons_count . ',1fr);';
                    $styles .= 'grid-template-rows:' . $grid_template_size . 'px;';
                    $styles .= '}';
                    break;

                case 'left':
                case 'right':
                    $styles .= '#mobile-contact-bar{';
                    $styles .= ( 'fix_min' === $bar['span'] ) ? 'height:' . $checked_buttons_count * $bar['shortest'] . 'px;' : 'height:100%;';
                    $styles .= 'width:' . $bar['shortest'] . 'px;';
                    $styles .= 'position:absolute;';
                    $styles .= $bar['position'] . ':' . $bar['space'] . 'px;';
                    if ( 'fix_min' === $bar['span'] )
                    {
                        $styles .= 'top:' . $bar['alignment'] . '%;';
                        $styles .= 'transform:translateY(-' . $bar['alignment'] . '%);';
                    }
                    else
                    {
                        $styles .= 'top:0;';
                    }
                    $styles .= '}';

                    if ( 'fix_max' === $bar['span'] )
                    {
                        $styles .= '#mobile-contact-bar-nav{';
                        $styles .= 'background-color:' . $general_btn['background_color']['primary'] . ';';
                        $styles .= '}';
                    }

                    $styles .= '#mobile-contact-bar-nav ul{';
                    $styles .= ( 'stretch' === $bar['span'] ) ? 'height:100%;' : 'height:' . $checked_buttons_count * $bar['shortest'] . 'px;';
                    $styles .= 'position:absolute;';
                    $styles .= 'top:' . $bar['alignment'] . '%;';
                    $styles .= 'transform:translateY(-' . $bar['alignment'] . '%);';
                    $styles .= 'display:grid;';
                    $styles .= 'grid-template-rows:repeat(' . $checked_buttons_count . ',1fr);';
                    $styles .= 'grid-template-columns:' . $grid_template_size . 'px;';
                    $styles .= '}';

                    $styles .= '#wpadminbar ~ #mobile-contact-bar{';
                    $styles .= ( 'fix_min' === $bar['span'] ) ? 'height:' . $checked_buttons_count * $bar['shortest'] . 'px;' : 'height:calc(100% - 32px);';
                    if ( 'fix_min' === $bar['span'] )
                    {
                        if ( $bar['alignment'] < 50 )
                        {
                            $styles .= 'top:calc(' . $bar['alignment'] . '% + 32px);';
                        }
                        else
                        {
                            $styles .= 'top:' . $bar['alignment'] . '%;';
                        }
                    }
                    else
                    {
                        $styles .= 'top:32px;';
                    }
                    $styles .= '}';

                    $styles .= '@media only screen and (max-width: 782px){';
                    $styles .= '#wpadminbar ~ #mobile-contact-bar{';
                    $styles .= ( 'fix_min' === $bar['span'] ) ? 'height:' . $checked_buttons_count * $bar['shortest'] . 'px;' : 'height:calc(100% - 46px);';
                    if ( 'fix_min' === $bar['span'] )
                    {
                        if ( $bar['alignment'] < 50 )
                        {
                            $styles .= 'top:calc(' . $bar['alignment'] . '% + 46px);';
                        }
                        else
                        {
                            $styles .= 'top:' . $bar['alignment'] . '%;';
                        }
                    }
                    else
                    {
                        $styles .= 'top:46px;';
                    }
                    $styles .= '}';
                    $styles .= '}';
                    break;
            }
        }

        return $styles;
    }


    private function toggle( $bar, $toggle )
    {
        $styles = '';

        // 55 = 110 / 2 half of toggle longest
        // 34                   toggle shortest

        if ( $toggle['is_render'] && $bar['is_fixed'] )
        {
            $styles .= '#mobile-contact-bar-inner{';
            $styles .= 'position:relative;';
            $styles .= 'height:100%;';
            $styles .= '}';

            $styles .= '#mobile-contact-bar-toggle{';
            $styles .= 'display:table;';
            $styles .= ( empty( $toggle['background_color']['primary'] )) ? '' : 'color:' . $toggle['background_color']['primary'] . ';';
            $styles .= 'cursor:pointer;';
            $styles .= 'line-height:0;';
            $styles .= 'margin:0;';
            $styles .= 'padding:0;';
            $styles .= 'position:absolute;';
            $styles .= 'z-index:2;';
            $styles .= '}';

            $styles .= '#mobile-contact-bar-toggle div{';
            $styles .= ( empty( $toggle['font_color']['primary'] )) ? '' : 'color:' . $toggle['font_color']['primary'] . ';';
            $styles .= 'font-size:' . $toggle['font_size'] . 'em;';
            $styles .= 'position:absolute;';
            $styles .= 'bottom:50%;';
            $styles .= 'left:50%;';
            $styles .= 'text-align:center;';
            $styles .= 'width:100%;';
            $styles .= 'z-index:2;';
            $styles .= '}';

            $styles .= '#mobile-contact-bar-toggle-checkbox{';
            $styles .= 'display:none;';
            $styles .= 'position:absolute;';
            $styles .= '}';

            switch ( $bar['position'] )
            {
                case 'top':
                    $styles .= '#mobile-contact-bar-toggle{';
                    $styles .= 'transform:rotateX(180deg);';
                    $styles .= 'top:' . $bar['shortest']. 'px;';
                    $styles .= 'left:calc(50% - 55px);';
                    $styles .= ( $toggle['is_animation'] ) ? 'transition:top 500ms ease;' : '';
                    $styles .= '}';

                    $styles .= '#mobile-contact-bar-toggle div{';
                    $styles .= 'transform:translateX(-50%) scaleY(-1);';
                    $styles .= '}';

                    if ( 0 === $bar['space'] )
                    {
                        $styles .= '#mobile-contact-bar-nav{';
                        $styles .= 'position:relative;';
                        $styles .= 'top:0;';
                        $styles .= ( $toggle['is_animation'] ) ? 'transition:top 500ms ease;' : '';
                        $styles .= '}';

                        $styles .= '#mobile-contact-bar-toggle-checkbox:checked ~ #mobile-contact-bar-nav{';
                        $styles .= 'top:-' . $bar['shortest'] . 'px;';
                        $styles .= '}';

                        $styles .= '#mobile-contact-bar-toggle-checkbox:checked ~ #mobile-contact-bar-toggle{';
                        $styles .= 'top:0;';
                        $styles .= '}';
                    }
                    else
                    {
                        $styles .= '#mobile-contact-bar-nav{';
                        $styles .= 'transform:scaleY(1);';
                        $styles .= 'transform-origin:top;';
                        $styles .= 'transition:all 500ms ease;';
                        $styles .= '}';

                        $styles .= '#mobile-contact-bar-toggle-checkbox:checked ~ #mobile-contact-bar-nav{';
                        $styles .= 'transform:scaleY(0);';
                        $styles .= '}';

                        $styles .= '#mobile-contact-bar-toggle-checkbox:checked ~ #mobile-contact-bar-toggle{';
                        $styles .= 'top:0;';
                        $styles .= '}';
                    }
                    break;

                case 'bottom':
                    $styles .= '#mobile-contact-bar-toggle{';
                    $styles .= 'top:-34px;';
                    $styles .= 'left:calc(50% - 55px);';
                    $styles .= ( $toggle['is_animation'] ) ? 'transition:top 500ms ease;' : '';
                    $styles .= '}';

                    $styles .= '#mobile-contact-bar-toggle div{';
                    $styles .= 'transform:translateX(-50%);';
                    $styles .= '}';

                    if ( 0 === $bar['space'] )
                    {
                        $styles .= '#mobile-contact-bar-nav{';
                        $styles .= 'position:relative;';
                        $styles .= 'bottom:0;';
                        $styles .= ( $toggle['is_animation'] ) ? 'transition:bottom 500ms ease;' : '';
                        $styles .= '}';

                        $styles .= '#mobile-contact-bar-toggle-checkbox:checked ~ #mobile-contact-bar-nav{';
                        $styles .= 'bottom:-' . $bar['shortest'] . 'px;';
                        $styles .= '}';

                        $styles .= '#mobile-contact-bar-toggle-checkbox:checked ~ #mobile-contact-bar-toggle{';
                        $styles .= 'top:' . ( $bar['shortest'] - 34 ) . 'px;';
                        $styles .= '}';
                    }
                    else
                    {
                        $styles .= '#mobile-contact-bar-nav{';
                        $styles .= 'transform:scaleY(1);';
                        $styles .= 'transform-origin:bottom;';
                        $styles .= 'transition:all 500ms ease;';
                        $styles .= '}';

                        $styles .= '#mobile-contact-bar-toggle-checkbox:checked ~ #mobile-contact-bar-nav{';
                        $styles .= 'transform:scaleY(0);';
                        $styles .= '}';

                        $styles .= '#mobile-contact-bar-toggle-checkbox:checked ~ #mobile-contact-bar-toggle{';
                        $styles .= 'top:' . ( $bar['shortest'] - 34 ) . 'px;';
                        $styles .= '}';
                    }
                    break;

                case 'left':
                    $styles .= '#mobile-contact-bar-toggle{';
                    $styles .= 'transform-origin:top left;';
                    $styles .= 'transform:rotateZ(90deg);';
                    $styles .= 'top:calc(50% - 55px);';
                    $styles .= 'left:' . ( 34 + $bar['shortest'] ) . 'px;';
                    $styles .= ( $toggle['is_animation'] ) ? 'transition:left 500ms ease;' : '';
                    $styles .= '}';

                    $styles .= '#mobile-contact-bar-toggle div{';
                    $styles .= 'transform:translateX(-50%);';
                    $styles .= '}';

                    if ( 0 === $bar['space'] )
                    {
                        $styles .= '#mobile-contact-bar-nav{';
                        $styles .= 'position:relative;';
                        $styles .= 'left:0;';
                        $styles .= ( $toggle['is_animation'] ) ? 'transition:left 500ms ease;' : '';
                        $styles .= '}';

                        $styles .= '#mobile-contact-bar-toggle-checkbox:checked ~ #mobile-contact-bar-nav{';
                        $styles .= 'left:-' . $bar['shortest'] . 'px;';
                        $styles .= '}';

                        $styles .= '#mobile-contact-bar-toggle-checkbox:checked ~ #mobile-contact-bar-toggle{';
                        $styles .= 'left:34px;';
                        $styles .= '}';
                    }
                    else
                    {
                        $styles .= '#mobile-contact-bar-nav{';
                        $styles .= 'transform:scaleX(1);';
                        $styles .= 'transform-origin:left;';
                        $styles .= 'transition:all 500ms ease;';
                        $styles .= '}';

                        $styles .= '#mobile-contact-bar-toggle-checkbox:checked ~ #mobile-contact-bar-nav{';
                        $styles .= 'transform:scaleX(0);';
                        $styles .= '}';

                        $styles .= '#mobile-contact-bar-toggle-checkbox:checked ~ #mobile-contact-bar-toggle{';
                        $styles .= 'left:34px;';
                        $styles .= '}';
                    }
                    break;

                case 'right':
                    $styles .= '#mobile-contact-bar-toggle{';
                    $styles .= 'transform-origin:top left;';
                    $styles .= 'transform:rotateZ(90deg) scaleY(-1);';
                    $styles .= 'top:calc(50% - 55px);';
                    $styles .= 'left:-34px;';
                    $styles .= ( $toggle['is_animation'] ) ? 'transition:left 500ms ease;' : '';
                    $styles .= '}';

                    $styles .= '#mobile-contact-bar-toggle div{';
                    $styles .= 'transform:translateX(-50%) scaleX(-1);';
                    $styles .= '}';

                    if ( 0 === $bar['space'] )
                    {
                        $styles .= '#mobile-contact-bar-nav{';
                        $styles .= 'position:relative;';
                        $styles .= 'left:0;';
                        $styles .= ( $toggle['is_animation'] ) ? 'transition:left 500ms ease;' : '';
                        $styles .= '}';

                        $styles .= '#mobile-contact-bar-toggle-checkbox:checked ~ #mobile-contact-bar-nav{';
                        $styles .= 'left:' . $bar['shortest'] . 'px;';
                        $styles .= '}';

                        $styles .= '#mobile-contact-bar-toggle-checkbox:checked ~ #mobile-contact-bar-toggle{';
                        $styles .= 'left:' . ( $bar['shortest'] - 34 ) . 'px;';
                        $styles .= '}';
                    }
                    else
                    {
                        $styles .= '#mobile-contact-bar-nav{';
                        $styles .= 'transform:scaleX(1);';
                        $styles .= 'transform-origin:right;';
                        $styles .= 'transition:all 500ms ease;';
                        $styles .= '}';

                        $styles .= '#mobile-contact-bar-toggle-checkbox:checked ~ #mobile-contact-bar-nav{';
                        $styles .= 'transform:scaleX(0);';
                        $styles .= '}';

                        $styles .= '#mobile-contact-bar-toggle-checkbox:checked ~ #mobile-contact-bar-toggle{';
                        $styles .= 'left:' . ( $bar['shortest'] - 34 ) . 'px;';
                        $styles .= '}';   
                    }
                    break;
            }
        }

        return $styles;
    }


    private function badge( $badges )
    {
        $styles = '';

        $styles .= '.mobile-contact-bar-badge{';
        $styles .= ( empty( $badges['background_color']['primary'] )) ? '' : 'background-color:' . $badges['background_color']['primary'] . ';';
        $styles .= ( empty( $badges['font_color']['primary'] )) ? '' : 'color:' . $badges['font_color']['primary'] . ';';
        $styles .= 'border-radius:2em;';
        $styles .= 'display:flex;';
        $styles .= 'align-items:center;';
        $styles .= 'justify-content:center;';
        $styles .= 'font-size:1em;';
        $styles .= 'height:1.75em;';
        $styles .= 'min-width:1.75em;';
        $styles .= 'padding:0 5px;';
        $styles .= 'text-indent:0;';
        $styles .= 'position:absolute;';

        switch ( $badges['position'] )
        {
            case 'top-right':
                $styles .= 'top:0;';
                $styles .= 'right:0;';
                $styles .= 'transform-origin:top right;';
                $styles .= 'transform:scale(' . $badges['size'] . ') translate(' . $badges['size'] . 'em,' . (-1) * $badges['size'] . 'em);';
                break;

            case 'bottom-right':
                $styles .= 'bottom:0;';
                $styles .= 'right:0;';
                $styles .= 'transform-origin:bottom right;';
                $styles .= 'transform:scale(' . $badges['size'] . ') translate(' . $badges['size'] . 'em,' . $badges['size'] . 'em);';
                break;

            case 'bottom-left':
                $styles .= 'bottom:0;';
                $styles .= 'left:0;';
                $styles .= 'transform-origin:bottom left;';
                $styles .= 'transform:scale(' . $badges['size'] . ') translate(' . (-1) * $badges['size'] . 'em,' . $badges['size'] . 'em);';
                break;

            case 'top-left':
                $styles .= 'top:0;';
                $styles .= 'left:0;';
                $styles .= 'transform-origin:top left;';
                $styles .= 'transform:scale(' . $badges['size'] . ') translate(' . (-1) * $badges['size'] . 'em,' . (-1) * $badges['size'] . 'em);';
                break;
        }
        $styles .= '}';

        return $styles;
    }


    private function button_borders( $bar, $general_btn )
    {
        $styles = '';

        $is_primary_border = $general_btn['border_width'] > 0 && ! empty( $general_btn['border_color']['primary'] );

        switch ( $bar['position'] )
        {
            case 'top':
            case 'bottom':
                if ( $general_btn['is_borders']['top'] && $is_primary_border )
                {
                    $styles .= '.mobile-contact-bar-item{';
                    $styles .= 'border-top:' . $general_btn['border_width'] . 'px solid ' . $general_btn['border_color']['primary'] . ';';
                    $styles .= '}';
                }
                if ( $general_btn['is_borders']['bottom'] && $is_primary_border )
                {
                    $styles .= '.mobile-contact-bar-item{';
                    $styles .= 'border-bottom:' . $general_btn['border_width'] . 'px solid ' . $general_btn['border_color']['primary'] . ';';
                    $styles .= '}';
                }
                if ( $general_btn['is_borders']['left'] && $general_btn['is_borders']['right'] && $is_primary_border )
                {
                    $styles .= '.mobile-contact-bar-item{';
                    $styles .= 'border-left:' . $general_btn['border_width'] . 'px solid ' . $general_btn['border_color']['primary'] . ';';
                    $styles .= '}';

                    $styles .= '#mobile-contact-bar-nav li:last-child .mobile-contact-bar-item{';
                    $styles .= 'border-right:' . $general_btn['border_width'] . 'px solid ' . $general_btn['border_color']['primary'] . ';';
                    $styles .= '}';
                }
                if ((( $general_btn['is_borders']['left'] && ! $general_btn['is_borders']['right'] ) || ( ! $general_btn['is_borders']['left'] && $general_btn['is_borders']['right'] )) && $is_primary_border )
                {
                    $styles .= '.mobile-contact-bar-item{';
                    $styles .= 'border-left:' . $general_btn['border_width'] . 'px solid ' . $general_btn['border_color']['primary'] . ';';
                    $styles .= '}';

                    $styles .= '#mobile-contact-bar-nav li:first-child .mobile-contact-bar-item{';
                    $styles .= 'border-left:0;';
                    $styles .= '}';
                }
                break;

            case 'left':
            case 'right':
                if ( $general_btn['is_borders']['left'] && $is_primary_border )
                {
                    $styles .= '.mobile-contact-bar-item{';
                    $styles .= 'border-left:' . $general_btn['border_width'] . 'px solid ' . $general_btn['border_color']['primary'] . ';';
                    $styles .= '}';
                }
                if ( $general_btn['is_borders']['right'] && $is_primary_border )
                {
                    $styles .= '.mobile-contact-bar-item{';
                    $styles .= 'border-right:' . $general_btn['border_width'] . 'px solid ' . $general_btn['border_color']['primary'] . ';';
                    $styles .= '}';
                }
                if ( $general_btn['is_borders']['top'] && $general_btn['is_borders']['bottom'] && $is_primary_border )
                {
                    $styles .= '.mobile-contact-bar-item{';
                    $styles .= 'border-top:' . $general_btn['border_width'] . 'px solid ' . $general_btn['border_color']['primary'] . ';';
                    $styles .= '}';
        
                    $styles .= '#mobile-contact-bar-nav li:last-child .mobile-contact-bar-item{';
                    $styles .= 'border-bottom:' . $general_btn['border_width'] . 'px solid ' . $general_btn['border_color']['primary'] . ';';
                    $styles .= '}';
                }
                if ((( $general_btn['is_borders']['top'] && ! $general_btn['is_borders']['bottom'] ) || ( ! $general_btn['is_borders']['top'] && $general_btn['is_borders']['bottom'] )) && $is_primary_border )
                {
                    $styles .= '.mobile-contact-bar-item{';
                    $styles .= 'border-top:' . $general_btn['border_width'] . 'px solid ' . $general_btn['border_color']['primary'] . ';';
                    $styles .= '}';
        
                    $styles .= '#mobile-contact-bar-nav li:first-child .mobile-contact-bar-item{';
                    $styles .= 'border-top:0;';
                    $styles .= '}';
                }
                break;
        }


        $is_secondary_border = $general_btn['border_width'] > 0 && ! empty( $general_btn['border_color']['secondary'] );

        switch ( $bar['position'] )
        {
            case 'top':
            case 'bottom':
                // Focus
                if ( $general_btn['is_borders']['top'] && $is_secondary_border && $bar['is_secondary_colors']['focus'] )
                {
                    $styles .= '.mobile-contact-bar-item:focus{';
                    $styles .= 'border-top:' . $general_btn['border_width'] . 'px solid ' . $general_btn['border_color']['secondary'] . ';';
                    $styles .= '}';
                }
                if ( $general_btn['is_borders']['bottom'] && $is_secondary_border && $bar['is_secondary_colors']['focus'] )
                {
                    $styles .= '.mobile-contact-bar-item:focus{';
                    $styles .= 'border-bottom:' . $general_btn['border_width'] . 'px solid ' . $general_btn['border_color']['secondary'] . ';';
                    $styles .= '}';
                }
                // Hover
                if ( $general_btn['is_borders']['top'] && $is_secondary_border && $bar['is_secondary_colors']['hover'] )
                {
                    $styles .= '.mobile-contact-bar-item:hover{';
                    $styles .= 'border-top:' . $general_btn['border_width'] . 'px solid ' . $general_btn['border_color']['secondary'] . ';';
                    $styles .= '}';
                }
                if ( $general_btn['is_borders']['bottom'] && $is_secondary_border && $bar['is_secondary_colors']['hover'] )
                {
                    $styles .= '.mobile-contact-bar-item:hover{';
                    $styles .= 'border-bottom:' . $general_btn['border_width'] . 'px solid ' . $general_btn['border_color']['secondary'] . ';';
                    $styles .= '}';
                }
                // Active
                if ( $general_btn['is_borders']['top'] && $is_secondary_border && $bar['is_secondary_colors']['active'] )
                {
                    $styles .= '.mobile-contact-bar-item.mobile-contact-bar-active{';
                    $styles .= 'border-top:' . $general_btn['border_width'] . 'px solid ' . $general_btn['border_color']['secondary'] . ';';
                    $styles .= '}';
                }
                if ( $general_btn['is_borders']['bottom'] && $is_secondary_border && $bar['is_secondary_colors']['active'] )
                {
                    $styles .= '.mobile-contact-bar-item.mobile-contact-bar-active{';
                    $styles .= 'border-bottom:' . $general_btn['border_width'] . 'px solid ' . $general_btn['border_color']['secondary'] . ';';
                    $styles .= '}';
                }
                break;

            case 'left':
            case 'right':
                // Focus
                if ( $general_btn['is_borders']['left'] && $is_secondary_border && $bar['is_secondary_colors']['focus'] )
                {
                    $styles .= '.mobile-contact-bar-item:focus{';
                    $styles .= 'border-left:' . $general_btn['border_width'] . 'px solid ' . $general_btn['border_color']['secondary'] . ';';
                    $styles .= '}';
                }
                if ( $general_btn['is_borders']['right'] && $is_secondary_border && $bar['is_secondary_colors']['focus'] )
                {
                    $styles .= '.mobile-contact-bar-item:focus{';
                    $styles .= 'border-right:' . $general_btn['border_width'] . 'px solid ' . $general_btn['border_color']['secondary'] . ';';
                    $styles .= '}';
                }
                // Hover
                if ( $general_btn['is_borders']['left'] && $is_secondary_border && $bar['is_secondary_colors']['hover'] )
                {
                    $styles .= '.mobile-contact-bar-item:hover{';
                    $styles .= 'border-left:' . $general_btn['border_width'] . 'px solid ' . $general_btn['border_color']['secondary'] . ';';
                    $styles .= '}';
                }
                if ( $general_btn['is_borders']['right'] && $is_secondary_border && $bar['is_secondary_colors']['hover'] )
                {
                    $styles .= '.mobile-contact-bar-item:hover{';
                    $styles .= 'border-right:' . $general_btn['border_width'] . 'px solid ' . $general_btn['border_color']['secondary'] . ';';
                    $styles .= '}';
                }
                // Active
                if ( $general_btn['is_borders']['left'] && $is_secondary_border && $bar['is_secondary_colors']['active'] )
                {
                    $styles .= '.mobile-contact-bar-item.mobile-contact-bar-active{';
                    $styles .= 'border-left:' . $general_btn['border_width'] . 'px solid ' . $general_btn['border_color']['secondary'] . ';';
                    $styles .= '}';
                }
                if ( $general_btn['is_borders']['right'] && $is_secondary_border && $bar['is_secondary_colors']['active'] )
                {
                    $styles .= '.mobile-contact-bar-item.mobile-contact-bar-active{';
                    $styles .= 'border-right:' . $general_btn['border_width'] . 'px solid ' . $general_btn['border_color']['secondary'] . ';';
                    $styles .= '}';
                }
                break;
        }

        return $styles;
    }


    private function button_pseudo_classes( $bar, $general_btn, $toggle, $badges )
    {
        $styles = '';

        if ( ! empty( $general_btn['background_color']['secondary'] ))
        {
            if ( $bar['is_secondary_colors']['focus'] )
            {
                $styles .= '.mobile-contact-bar-item:focus{';
                $styles .= 'background-color:' . $general_btn['background_color']['secondary'] . ';';
                $styles .= '}';
            }
            if ( $bar['is_secondary_colors']['hover'] )
            {
                $styles .= '.mobile-contact-bar-item:hover{';
                $styles .= 'background-color:' . $general_btn['background_color']['secondary'] . ';';
                $styles .= '}';
            }
            if ( $bar['is_secondary_colors']['active'] )
            {
                $styles .= '.mobile-contact-bar-item.mobile-contact-bar-active{';
                $styles .= 'background-color:' . $general_btn['background_color']['secondary'] . ';';
                $styles .= '}';
            }
        }

        if ( ! empty( $general_btn['icon_color']['secondary'] ))
        {
            if ( $bar['is_secondary_colors']['focus'] )
            {
                $styles .= '.mobile-contact-bar-item:focus .mobile-contact-bar-icon svg{';
                $styles .= 'color:' . $general_btn['icon_color']['secondary'] . ';';
                $styles .= '}';
            }
            if ( $bar['is_secondary_colors']['hover'] )
            {
                $styles .= '.mobile-contact-bar-item:hover .mobile-contact-bar-icon svg{';
                $styles .= 'color:' . $general_btn['icon_color']['secondary'] . ';';
                $styles .= '}';
            }
            if ( $bar['is_secondary_colors']['active'] )
            {
                $styles .= '.mobile-contact-bar-item.mobile-contact-bar-active .mobile-contact-bar-icon svg{';
                $styles .= 'color:' . $general_btn['icon_color']['secondary'] . ';';
                $styles .= '}';
            }
        }

        if ( ! empty( $general_btn['label_color']['secondary'] ))
        {
            if ( $bar['is_secondary_colors']['focus'] )
            {
                $styles .= '.mobile-contact-bar-item:focus .mobile-contact-bar-label{';
                $styles .= 'color:' . $general_btn['label_color']['secondary'] . ';';
                $styles .= '}';
            }
            if ( $bar['is_secondary_colors']['hover'] )
            {
                $styles .= '.mobile-contact-bar-item:hover .mobile-contact-bar-label{';
                $styles .= 'color:' . $general_btn['label_color']['secondary'] . ';';
                $styles .= '}';
            }
            if ( $bar['is_secondary_colors']['active'] )
            {
                $styles .= '.mobile-contact-bar-item.mobile-contact-bar-active .mobile-contact-bar-label{';
                $styles .= 'color:' . $general_btn['label_color']['secondary'] . ';';
                $styles .= '}';
            }
        }


        // Badge
        if ( ! empty( $badges['background_color']['secondary'] ))
        {
            if ( $bar['is_secondary_colors']['focus'] )
            {
                $styles .= '.mobile-contact-bar-item:focus .mobile-contact-bar-badge{';
                $styles .= 'background-color:' . $badges['background_color']['secondary'] . ';';
                $styles .= '}';
            }
            if ( $bar['is_secondary_colors']['hover'] )
            {
                $styles .= '.mobile-contact-bar-item:hover .mobile-contact-bar-badge{';
                $styles .= 'background-color:' . $badges['background_color']['secondary'] . ';';
                $styles .= '}';
            }
            if ( $bar['is_secondary_colors']['active'] )
            {
                $styles .= '.mobile-contact-bar-item.mobile-contact-bar-active .mobile-contact-bar-badge{';
                $styles .= 'background-color:' . $badges['background_color']['secondary'] . ';';
                $styles .= '}';
            }
        }
        if ( ! empty( $badges['font_color']['secondary'] ))
        {
            if ( $bar['is_secondary_colors']['focus'] )
            {
                $styles .= '.mobile-contact-bar-item:focus .mobile-contact-bar-badge{';
                $styles .= 'color:' . $badges['font_color']['secondary'] . ';';
                $styles .= '}';
            }
            if ( $bar['is_secondary_colors']['hover'] )
            {
                $styles .= '.mobile-contact-bar-item:hover .mobile-contact-bar-badge{';
                $styles .= 'color:' . $badges['font_color']['secondary'] . ';';
                $styles .= '}';
            }
            if ( $bar['is_secondary_colors']['active'] )
            {
                $styles .= '.mobile-contact-bar-item.mobile-contact-bar-active .mobile-contact-bar-badge{';
                $styles .= 'color:' . $badges['font_color']['secondary'] . ';';
                $styles .= '}';
            }
        }


        // Toggle
        if ( ! empty( $toggle['background_color']['secondary'] ))
        {
            if ( $bar['is_secondary_colors']['focus'] )
            {
                $styles .= '#mobile-contact-bar-toggle:focus{';
                $styles .= 'color:' . $toggle['background_color']['secondary'] . ';';
                $styles .= '}';
            }
            if ( $bar['is_secondary_colors']['hover'] )
            {
                $styles .= '#mobile-contact-bar-toggle:hover{';
                $styles .= 'color:' . $toggle['background_color']['secondary'] . ';';
                $styles .= '}';
            }
        }
        if ( ! empty( $toggle['font_color']['secondary'] ))
        {
            if ( $bar['is_secondary_colors']['focus'] )
            {
                $styles .= '#mobile-contact-bar-toggle:focus div{';
                $styles .= 'color:' . $toggle['font_color']['secondary'] . ';';
                $styles .= '}';
            }
            if ( $bar['is_secondary_colors']['hover'] )
            {
                $styles .= '#mobile-contact-bar-toggle:hover div{';
                $styles .= 'color:' . $toggle['font_color']['secondary'] . ';';
                $styles .= '}';
            }
        }

        return $styles;
    }


    private function custom_button( $general_btn, $custom_btn )
    {
        $styles = '';

        if ( ! empty( $custom_btn['custom']['background_color']['primary'] ))
        {
            $styles .= '#' . $custom_btn['id'] . ' .mobile-contact-bar-item{';
            $styles .= 'background-color:' . $custom_btn['custom']['background_color']['primary'] . ';';
            $styles .= '}';
        }

        if ( ! empty( $custom_btn['custom']['icon_color']['primary'] ))
        {
            $styles .= '#' . $custom_btn['id'] . ' .mobile-contact-bar-icon{';
            $styles .= 'color:' . $custom_btn['custom']['icon_color']['primary'] . ';';
            $styles .= '}';
        }

        if ( ! empty( $custom_btn['custom']['label_color']['primary'] ))
        {
            $styles .= '#' . $custom_btn['id'] . ' .mobile-contact-bar-label{';
            $styles .= 'color:' . $custom_btn['custom']['label_color']['primary'] . ';';
            $styles .= '}';
        }

        return $styles;
    }


    private function custom_button_borders( $bar, $general_btn, $custom_btn )
    {
        $styles = '';

        $is_primary_border = $general_btn['border_width'] > 0 && ! empty( $custom_btn['custom']['border_color']['primary'] );

        switch ( $bar['position'] )
        {
            case 'top':
            case 'bottom':
                if ( $general_btn['is_borders']['top'] && $is_primary_border )
                {
                    $styles .= '#' . $custom_btn['id'] . ' .mobile-contact-bar-item{';
                    $styles .= 'border-top:' . $general_btn['border_width'] . 'px solid ' . $custom_btn['custom']['border_color']['primary'] . ';';
                    $styles .= '}';
                }
                if ( $general_btn['is_borders']['bottom'] && $is_primary_border )
                {
                    $styles .= '#' . $custom_btn['id'] . ' .mobile-contact-bar-item{';
                    $styles .= 'border-bottom:' . $general_btn['border_width'] . 'px solid ' . $custom_btn['custom']['border_color']['primary'] . ';';
                    $styles .= '}';
                }
                break;

            case 'left':
            case 'right':
                if ( $general_btn['is_borders']['left'] && $is_primary_border )
                {
                    $styles .= '#' . $custom_btn['id'] . ' .mobile-contact-bar-item{';
                    $styles .= 'border-left:' . $general_btn['border_width'] . 'px solid ' . $custom_btn['custom']['border_color']['primary'] . ';';
                    $styles .= '}';
                }
                if ( $general_btn['is_borders']['right'] && $is_primary_border )
                {
                    $styles .= '#' . $custom_btn['id'] . ' .mobile-contact-bar-item{';
                    $styles .= 'border-right:' . $general_btn['border_width'] . 'px solid ' . $custom_btn['custom']['border_color']['primary'] . ';';
                    $styles .= '}';
                }
                break;
        }


        $is_secondary_border = $general_btn['border_width'] > 0 && ! empty( $custom_btn['custom']['border_color']['secondary'] );

        switch ( $bar['position'] )
        {
            case 'top':
            case 'bottom':
                // Focus
                if ( $general_btn['is_borders']['top'] && $is_secondary_border && $bar['is_secondary_colors']['focus'] )
                {
                    $styles .= '#' . $custom_btn['id'] . ' .mobile-contact-bar-item:focus{';
                    $styles .= 'border-top:' . $general_btn['border_width'] . 'px solid ' . $custom_btn['custom']['border_color']['secondary'] . ';';
                    $styles .= '}';
                }
                if ( $general_btn['is_borders']['bottom'] && $is_secondary_border && $bar['is_secondary_colors']['focus'] )
                {
                    $styles .= '#' . $custom_btn['id'] . ' .mobile-contact-bar-item:focus{';
                    $styles .= 'border-bottom:' . $general_btn['border_width'] . 'px solid ' . $custom_btn['custom']['border_color']['secondary'] . ';';
                    $styles .= '}';
                }
                // Hover
                if ( $general_btn['is_borders']['top'] && $is_secondary_border && $bar['is_secondary_colors']['hover'] )
                {
                    $styles .= '#' . $custom_btn['id'] . ' .mobile-contact-bar-item:hover{';
                    $styles .= 'border-top:' . $general_btn['border_width'] . 'px solid ' . $custom_btn['custom']['border_color']['secondary'] . ';';
                    $styles .= '}';
                }
                if ( $general_btn['is_borders']['bottom'] && $is_secondary_border && $bar['is_secondary_colors']['hover'] )
                {
                    $styles .= '#' . $custom_btn['id'] . ' .mobile-contact-bar-item:hover{';
                    $styles .= 'border-bottom:' . $general_btn['border_width'] . 'px solid ' . $custom_btn['custom']['border_color']['secondary'] . ';';
                    $styles .= '}';
                }
                // Active
                if ( $general_btn['is_borders']['top'] && $is_secondary_border && $bar['is_secondary_colors']['active'] )
                {
                    $styles .= '#' . $custom_btn['id'] . ' .mobile-contact-bar-item.mobile-contact-bar-active{';
                    $styles .= 'border-top:' . $general_btn['border_width'] . 'px solid ' . $custom_btn['custom']['border_color']['secondary'] . ';';
                    $styles .= '}';
                }
                if ( $general_btn['is_borders']['bottom'] && $is_secondary_border && $bar['is_secondary_colors']['active'] )
                {
                    $styles .= '#' . $custom_btn['id'] . ' .mobile-contact-bar-item.mobile-contact-bar-active{';
                    $styles .= 'border-bottom:' . $general_btn['border_width'] . 'px solid ' . $custom_btn['custom']['border_color']['secondary'] . ';';
                    $styles .= '}';
                }
                break;

            case 'left':
            case 'right':
                // Focus
                if ( $general_btn['is_borders']['left'] && $is_secondary_border && $bar['is_secondary_colors']['focus'] )
                {
                    $styles .= '#' . $custom_btn['id'] . ' .mobile-contact-bar-item:focus{';
                    $styles .= 'border-left:' . $general_btn['border_width'] . 'px solid ' . $custom_btn['custom']['border_color']['secondary'] . ';';
                    $styles .= '}';
                }
                if ( $general_btn['is_borders']['right'] && $is_secondary_border && $bar['is_secondary_colors']['focus'] )
                {
                    $styles .= '#' . $custom_btn['id'] . ' .mobile-contact-bar-item:focus{';
                    $styles .= 'border-right:' . $general_btn['border_width'] . 'px solid ' . $custom_btn['custom']['border_color']['secondary'] . ';';
                    $styles .= '}';
                }
                // Hover
                if ( $general_btn['is_borders']['left'] && $is_secondary_border && $bar['is_secondary_colors']['hover'] )
                {
                    $styles .= '#' . $custom_btn['id'] . ' .mobile-contact-bar-item:hover{';
                    $styles .= 'border-left:' . $general_btn['border_width'] . 'px solid ' . $custom_btn['custom']['border_color']['secondary'] . ';';
                    $styles .= '}';
                }
                if ( $general_btn['is_borders']['right'] && $is_secondary_border && $bar['is_secondary_colors']['hover'] )
                {
                    $styles .= '#' . $custom_btn['id'] . ' .mobile-contact-bar-item:hover{';
                    $styles .= 'border-right:' . $general_btn['border_width'] . 'px solid ' . $custom_btn['custom']['border_color']['secondary'] . ';';
                    $styles .= '}';
                }
                // Active
                if ( $general_btn['is_borders']['left'] && $is_secondary_border && $bar['is_secondary_colors']['active'] )
                {
                    $styles .= '#' . $custom_btn['id'] . ' .mobile-contact-bar-item.mobile-contact-bar-active{';
                    $styles .= 'border-left:' . $general_btn['border_width'] . 'px solid ' . $custom_btn['custom']['border_color']['secondary'] . ';';
                    $styles .= '}';
                }
                if ( $general_btn['is_borders']['right'] && $is_secondary_border && $bar['is_secondary_colors']['active'] )
                {
                    $styles .= '#' . $custom_btn['id'] . ' .mobile-contact-bar-item.mobile-contact-bar-active{';
                    $styles .= 'border-right:' . $general_btn['border_width'] . 'px solid ' . $custom_btn['custom']['border_color']['secondary'] . ';';
                    $styles .= '}';
                }
                break;
        }

        return $styles;
    }


    private function custom_button_pseudo_classes( $bar, $general_btn, $custom_btn )
    {
        $styles = '';

        if ( ! empty( $custom_btn['custom']['background_color']['secondary'] ))
        {
            if ( $bar['is_secondary_colors']['focus'] )
            {
                $styles .= '#' . $custom_btn['id'] . ' .mobile-contact-bar-item:focus{';
                $styles .= 'background-color:' . $custom_btn['custom']['background_color']['secondary'] . ';';
                $styles .= '}';
            }
            if ( $bar['is_secondary_colors']['hover'] )
            {
                $styles .= '#' . $custom_btn['id'] . ' .mobile-contact-bar-item:hover{';
                $styles .= 'background-color:' . $custom_btn['custom']['background_color']['secondary'] . ';';
                $styles .= '}';
            }
            if ( $bar['is_secondary_colors']['active'] )
            {
                $styles .= '#' . $custom_btn['id'] . ' .mobile-contact-bar-item.mobile-contact-bar-active{';
                $styles .= 'background-color:' . $custom_btn['custom']['background_color']['secondary'] . ';';
                $styles .= '}';
            }
        }

        if ( ! empty( $custom_btn['custom']['icon_color']['secondary'] ))
        {
            if ( $bar['is_secondary_colors']['focus'] )
            {
                $styles .= '#' . $custom_btn['id'] . ' .mobile-contact-bar-item:focus .mobile-contact-bar-icon svg{';
                $styles .= 'color:' . $custom_btn['custom']['icon_color']['secondary'] . ';';
                $styles .= '}';
            }
            if ( $bar['is_secondary_colors']['hover'] )
            {
                $styles .= '#' . $custom_btn['id'] . ' .mobile-contact-bar-item:hover .mobile-contact-bar-icon svg{';
                $styles .= 'color:' . $custom_btn['custom']['icon_color']['secondary'] . ';';
                $styles .= '}';
            }
            if ( $bar['is_secondary_colors']['active'] )
            {
                $styles .= '#' . $custom_btn['id'] . ' .mobile-contact-bar-item.mobile-contact-bar-active .mobile-contact-bar-icon svg{';
                $styles .= 'color:' . $custom_btn['custom']['icon_color']['secondary'] . ';';
                $styles .= '}';
            }
        }

        if ( ! empty( $custom_btn['custom']['label_color']['secondary'] ))
        {
            if ( $bar['is_secondary_colors']['focus'] )
            {
                $styles .= '#' . $custom_btn['id'] . ' .mobile-contact-bar-item:focus .mobile-contact-bar-label{';
                $styles .= 'color:' . $custom_btn['custom']['label_color']['secondary'] . ';';
                $styles .= '}';
            }
            if ( $bar['is_secondary_colors']['hover'] )
            {
                $styles .= '#' . $custom_btn['id'] . ' .mobile-contact-bar-item:hover .mobile-contact-bar-label{';
                $styles .= 'color:' . $custom_btn['custom']['label_color']['secondary'] . ';';
                $styles .= '}';
            }
            if ( $bar['is_secondary_colors']['active'] )
            {
                $styles .= '#' . $custom_btn['id'] . ' .mobile-contact-bar-item.mobile-contact-bar-active .mobile-contact-bar-label{';
                $styles .= 'color:' . $custom_btn['custom']['label_color']['secondary'] . ';';
                $styles .= '}';
            }
        }

        return $styles;
    }
}
