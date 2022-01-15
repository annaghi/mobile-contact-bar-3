<?php

namespace MobileContactBar\Styles;


final class CSS
{
    /**
     * Outputs the base styles generated from 'settings' and 'contacts'.
     *
     * @param  array  $settings
     * @param  array  $contacts
     * @return string           CSS
     */
    public function output( $settings = [], $contacts = [] )
    {
        $styles = '';

        $bar    = $settings['bar'];
        $item   = $settings['icons_labels'];
        $toggle = $settings['toggle'];
        $badge  = $settings['badges'];

        $checked_contacts = array_filter( $contacts, function ( $contact ) { return $contact['checked']; } );
        $checked_contacts_count = count( $checked_contacts );


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
        $styles .= 'overflow:hidden;';
        $styles .= 'height:100%;';
        $styles .= '}';

        $styles .= $this->toggle( $bar, $toggle );

        $styles .= '#mobile-contact-bar ul{';
        // $styles .= 'box-sizing:border-box;';
        $styles .= 'list-style-type:none;';
        $styles .= 'margin:0;';
        $styles .= 'padding:0;';
        $styles .= 'overflow:hidden;';

        $styles .= $this->bar_border( $bar );

        $styles .= '}';


        // Item
        $styles .= '#mobile-contact-bar li{';
        $styles .= 'margin:0;';
        $styles .= 'padding:0;';
        $styles .= '}';

        $styles .= '.mobile-contact-bar-item{';
        $styles .= ( empty( $item['background_color']['primary'] )) ? '' : 'background-color:' . $item['background_color']['primary'] . ';';
        $styles .= 'text-decoration:none;';
        $styles .= 'outline:none;';
        $styles .= 'cursor:pointer;';
        $styles .= 'display:flex;';
        $styles .= 'flex-direction:column;';
        $styles .= 'justify-content:center;';
        $styles .= 'align-items:center;';
        $styles .= 'gap:' . $item['gap'] . 'em;';
        $styles .= 'height:100%;';
        $styles .= '}';

        $styles .= $this->item_border( $item );

        $styles .= '.mobile-contact-bar-icon{';
        $styles .= 'display:inline-flex;';
        $styles .= 'position:relative;';
        $styles .= 'line-height:50%;';
        $styles .= ( empty( $item['icon_color']['primary'] )) ? '' : 'color:' . $item['icon_color']['primary'] . ';';
        $styles .= 'padding:0 5px;';
        $styles .= '}';

        $styles .= '.mobile-contact-bar-icon svg{';
        $styles .= 'width:1em;';
        $styles .= 'height:1em;';
        $styles .= 'font-size:' . $item['icon_size'] . 'em;';
        $styles .= '}';

        $styles .= '.mobile-contact-bar-fa svg{';
        $styles .= 'fill:currentColor;';
        $styles .= '}';

        $styles .= '.mobile-contact-bar-label{';
        $styles .= ( empty( $item['label_color']['primary'] )) ? '' : 'color:' . $item['label_color']['primary'] . ';';
        $styles .= 'font-size:' . $item['label_size'] . 'em;';
        $styles .= 'line-height:1;';
        $styles .= '}';

        $styles .= $this->badge( $badge );

        $styles .= $this->item_pseudo_classes( $bar, $item, $toggle, $badge );


        $styles .= $this->bar_position( $bar, $toggle, $checked_contacts_count );


        // Item customization
        foreach ( $checked_contacts as $contact )
        {
            if ( $contact['id'] )
            {
                $styles .= $this->item_custom_colors( $item, $contact );
                $styles .= $this->item_pseudo_classes_custom_colors( $bar, $item, $contact );
            }
        }

        return $styles;
    }


    private function bar_position( $bar, $toggle, $checked_contacts_count )
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
                    $styles .= ( 'stretch' === $bar['span'] ) ? 'width:100%;' : 'width:' . $checked_contacts_count * $bar['shortest'] . 'px;';
                    $styles .= 'position:fixed;';
                    $styles .= 'top:0;';
                    if ( 'fix' === $bar['span'] )
                    {
                        $styles .= 'left:' . $bar['alignment'] . '%;';
                        $styles .= 'transform:translateX(-' . $bar['alignment'] . '%);';
                    }
                    else
                    {
                        $styles .= 'left:0;';
                    }
                    $styles .= '}';
                    $styles .= '#mobile-contact-bar ul{';
                    $styles .= 'display:grid;';
                    $styles .= ( 'stretch' === $bar['span'] )
                        ? 'grid-template-columns: repeat(' . $checked_contacts_count . ',1fr);'
                        : 'grid-template-columns: repeat(' . $checked_contacts_count . ',' . $bar['shortest'] . 'px);';
                    $styles .= 'grid-template-rows:' . $grid_template_size . 'px;';
                    $styles .= '}';

                    $styles .= '#wpadminbar ~ #mobile-contact-bar{';
                    $styles .= 'top:32px;';
                    $styles .= '}';

                    $styles .= '@media only screen and (max-width: 782px){';
                    $styles .= '#wpadminbar ~ #mobile-contact-bar{';
                    $styles .= 'top:46px;';
                    $styles .= '}';
                    $styles .= '}';

                    $styles .= '@media only screen and (max-width: 600px){';
                    $styles .= '#wpadminbar ~ #mobile-contact-bar{';
                    $styles .= 'position:sticky;';
                    $styles .= 'top:0;';
                    $styles .= 'margin-top:-' . $bar['shortest'] . 'px;';
                    if ( 'fix' === $bar['span'] )
                    {
                        $styles .= 'left:calc(' . $bar['alignment'] . '% - '. ( $checked_contacts_count * $bar['shortest'] / 2 ) . 'px);';
                        $styles .= 'transform:none;';
                    }
                    $styles .= '}';
                    $styles .= '}';
                    break;

                case 'bottom':
                    $styles .= '#mobile-contact-bar{';
                    $styles .= ( 'stretch' === $bar['span'] ) ? 'width:100%;' : 'width:' . $checked_contacts_count * $bar['shortest'] . 'px;';
                    $styles .= 'position:fixed;';
                    $styles .= 'bottom:0;';
                    if ( 'fix' === $bar['span'] )
                    {
                        $styles .= 'left:' . $bar['alignment'] . '%;';
                        $styles .= 'transform:translateX(-' . $bar['alignment'] . '%);';
                    }
                    else
                    {
                        $styles .= 'left:0;';
                    }
                    $styles .= '}';
                    $styles .= '#mobile-contact-bar ul{';
                    $styles .= 'display:grid;';
                    $styles .= ( 'stretch' === $bar['span'] )
                        ? 'grid-template-columns: repeat(' . $checked_contacts_count . ',1fr);'
                        : 'grid-template-columns: repeat(' . $checked_contacts_count . ',' . $bar['shortest'] . 'px);';
                    $styles .= 'grid-template-rows:' . $grid_template_size . 'px;';
                    $styles .= '}';
                    break;

                case 'left':
                    $styles .= '#mobile-contact-bar{';
                    $styles .= ( 'stretch' === $bar['span'] ) ? 'height:100%;' : 'height:' . $checked_contacts_count * $bar['shortest'] . 'px;';
                    $styles .= 'width:' . $bar['shortest'] . 'px;';
                    $styles .= 'position:fixed;';
                    $styles .= 'left:0;';
                    if ( 'fix' === $bar['span'] )
                    {
                        $styles .= 'top:' . $bar['alignment'] . '%;';
                        $styles .= 'transform:translateY(-' . $bar['alignment'] . '%);';
                    }
                    else
                    {
                        $styles .= 'top:0;';
                    }
                    $styles .= '}';
                    $styles .= '#mobile-contact-bar ul{';
                    $styles .= 'height:100%;';
                    $styles .= 'display:grid;';
                    $styles .= 'grid-template-rows:repeat(' . $checked_contacts_count . ',1fr);';
                    $styles .= 'grid-template-columns:' . $grid_template_size . 'px;';
                    $styles .= '}';

                    $styles .= '#wpadminbar ~ #mobile-contact-bar{';
                    $styles .= ( 'stretch' === $bar['span'] ) ? 'height:calc(100% - 32px);' : 'height:' . $checked_contacts_count * $bar['shortest'] . 'px;';
                    $styles .= 'top:32px;';
                    if ( 'fix' === $bar['span'] )
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
                    $styles .= '}';

                    $styles .= '@media only screen and (max-width: 782px){';
                    $styles .= '#wpadminbar ~ #mobile-contact-bar{';
                    $styles .= ( 'stretch' === $bar['span'] ) ? 'height:calc(100% - 46px);' : 'height:' . $checked_contacts_count * $bar['shortest'] . 'px;';
                    $styles .= 'top:46px;';
                    if ( 'fix' === $bar['span'] )
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
                    $styles .= '}';
                    $styles .= '}';
                    break;

                case 'right':
                    $styles .= '#mobile-contact-bar{';
                    $styles .= ( 'stretch' === $bar['span'] ) ? 'height:100%;' : 'height:' . $checked_contacts_count * $bar['shortest'] . 'px;';
                    $styles .= 'width:' . $bar['shortest'] . 'px;';
                    $styles .= 'position:fixed;';
                    $styles .= 'right:0;';
                    if ( 'fix' === $bar['span'] )
                    {
                        $styles .= 'top:' . $bar['alignment'] . '%;';
                        $styles .= 'transform:translateY(-' . $bar['alignment'] . '%);';
                    }
                    else
                    {
                        $styles .= 'top:0;';
                    }
                    $styles .= '}';
                    $styles .= '#mobile-contact-bar ul{';
                    $styles .= 'height:100%;';
                    $styles .= 'display:grid;';
                    $styles .= 'grid-template-rows:repeat(' . $checked_contacts_count . ',1fr);';
                    $styles .= 'grid-template-columns:' . $grid_template_size . 'px;';
                    $styles .= '}';

                    $styles .= '#wpadminbar ~ #mobile-contact-bar{';
                    $styles .= ( 'stretch' === $bar['span'] ) ? 'height:calc(100% - 32px);' : 'height:' . $checked_contacts_count * $bar['shortest'] . 'px;';
                    $styles .= 'top:32px;';
                    if ( 'fix' === $bar['span'] )
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
                    $styles .= '}';

                    $styles .= '@media only screen and (max-width: 782px){';
                    $styles .= '#wpadminbar ~ #mobile-contact-bar{';
                    $styles .= ( 'stretch' === $bar['span'] ) ? 'height:calc(100% - 46px);' : 'height:' . $checked_contacts_count * $bar['shortest'] . 'px;';
                    $styles .= 'top:46px;';
                    if ( 'fix' === $bar['span'] )
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
                    $styles .= ( 'stretch' === $bar['span'] ) ? 'width:100%;' : 'width:' . $checked_contacts_count * $bar['shortest'] . 'px;';
                    $styles .= 'position:absolute;';
                    $styles .= 'top:0;';
                    if ( 'fix' === $bar['span'] )
                    {
                        $styles .= 'left:' . $bar['alignment'] . '%;';
                        $styles .= 'transform:translateX(-' . $bar['alignment'] . '%);';
                    }
                    else
                    {
                        $styles .= 'left:0;';
                    }
                    $styles .= '}';
                    $styles .= '#mobile-contact-bar ul{';
                    $styles .= 'display:grid;';
                    $styles .= 'grid-template-columns:repeat(' . $checked_contacts_count . ',1fr);';
                    $styles .= 'grid-template-rows:' . $grid_template_size . 'px;';
                    $styles .= '}';

                    $styles .= '#wpadminbar ~ #mobile-contact-bar{';
                    $styles .= 'top:32px;';
                    $styles .= '}';

                    $styles .= '@media only screen and (max-width: 782px){';
                    $styles .= '#wpadminbar ~ #mobile-contact-bar{';
                    $styles .= 'top:46px;';
                    $styles .= '}';
                    $styles .= '}';
                    break;

                case 'bottom':
                    $styles .= '#mobile-contact-bar{';
                    $styles .= ( 'stretch' === $bar['span'] ) ? 'width:100%;' : 'width:' . $checked_contacts_count * $bar['shortest'] . 'px;';
                    $styles .= 'position:relative;';
                    if ( 'fix' === $bar['span'] )
                    {
                        $styles .= 'left:' . $bar['alignment'] . '%;';
                        $styles .= 'transform:translateX(-' . $bar['alignment'] . '%);';
                    }
                    else
                    {
                        $styles .= 'left:0;';
                    }
                    $styles .= '}';
                    $styles .= '#mobile-contact-bar ul{';
                    $styles .= 'display:grid;';
                    $styles .= 'grid-template-columns:repeat(' . $checked_contacts_count . ',1fr);';
                    $styles .= 'grid-template-rows:' . $grid_template_size . 'px;';
                    $styles .= '}';
                    break;

                case 'left':
                    $styles .= '#mobile-contact-bar{';
                    $styles .= ( 'stretch' === $bar['span'] ) ? 'height:100%;' : 'height:' . $checked_contacts_count * $bar['shortest'] . 'px;';
                    $styles .= 'width:' . $bar['shortest'] . 'px;';
                    $styles .= 'position:absolute;';
                    $styles .= 'left:0;';
                    $styles .= 'top:0;';
                    if ( 'fix' === $bar['span'] )
                    {
                        $styles .= 'top:' . $bar['alignment'] . '%;';
                        $styles .= 'transform:translateY(-' . $bar['alignment'] . '%);';
                    }
                    $styles .= '}';
                    $styles .= '#mobile-contact-bar ul{';
                    $styles .= 'height:100%;';
                    $styles .= 'display:grid;';
                    $styles .= 'grid-template-rows:repeat(' . $checked_contacts_count . ',1fr);';
                    $styles .= 'grid-template-columns:' . $grid_template_size . 'px;';
                    $styles .= '}';

                    $styles .= '#wpadminbar ~ #mobile-contact-bar{';
                    $styles .= ( 'stretch' === $bar['span'] ) ? 'height:calc(100% - 32px);' : 'height:' . $checked_contacts_count * $bar['shortest'] . 'px;';
                    $styles .= 'top:32px;';
                    if ( 'fix' === $bar['span'] )
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
                    $styles .= '}';

                    $styles .= '@media only screen and (max-width: 782px){';
                    $styles .= '#wpadminbar ~ #mobile-contact-bar{';
                    $styles .= ( 'stretch' === $bar['span'] ) ? 'height:calc(100% - 46px);' : 'height:' . $checked_contacts_count * $bar['shortest'] . 'px;';
                    $styles .= 'top:46px;';
                    if ( 'fix' === $bar['span'] )
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
                    $styles .= '}';
                    $styles .= '}';
                    break;
                case 'right':
                    $styles .= '#mobile-contact-bar{';
                    $styles .= ( 'stretch' === $bar['span'] ) ? 'height:100%;' : 'height:' . $checked_contacts_count * $bar['shortest'] . 'px;';
                    $styles .= 'width:' . $bar['shortest'] . 'px;';
                    $styles .= 'position:absolute;';
                    $styles .= 'right:0;';
                    $styles .= 'top:0;';
                    if ( 'fix' === $bar['span'] )
                    {
                        $styles .= 'top:' . $bar['alignment'] . '%;';
                        $styles .= 'transform:translateY(-' . $bar['alignment'] . '%);';
                    }
                    $styles .= '}';
                    $styles .= '#mobile-contact-bar ul{';
                    $styles .= 'height:100%;';
                    $styles .= 'display:grid;';
                    $styles .= 'grid-template-rows:repeat(' . $checked_contacts_count . ',1fr);';
                    $styles .= 'grid-template-columns:' . $grid_template_size . 'px;';
                    $styles .= '}';

                    $styles .= '#wpadminbar ~ #mobile-contact-bar{';
                    $styles .= ( 'stretch' === $bar['span'] ) ? 'height:calc(100% - 32px);' : 'height:' . $checked_contacts_count * $bar['shortest'] . 'px;';
                    $styles .= 'top:32px;';
                    if ( 'fix' === $bar['span'] )
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
                    $styles .= '}';

                    $styles .= '@media only screen and (max-width: 782px){';
                    $styles .= '#wpadminbar ~ #mobile-contact-bar{';
                    $styles .= ( 'stretch' === $bar['span'] ) ? 'height:calc(100% - 46px);' : 'height:' . $checked_contacts_count * $bar['shortest'] . 'px;';
                    $styles .= 'top:46px;';
                    if ( 'fix' === $bar['span'] )
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
                    $styles .= '}';
                    $styles .= '}';
                    break;
            }
        }

        return $styles;
    }


    private function bar_border( $bar )
    {
        $styles = '';

        $bar_border_color = empty( $bar['border_color'] ) ? 'transparent' : $bar['border_color'];

        if ( $bar['is_borders']['top'] )
        {
            $styles .= 'border-top:' . $bar['border_width'] . 'px solid ' . $bar_border_color . ';';
        }
        if ( $bar['is_borders']['bottom'] )
        {
            $styles .= 'border-bottom:' . $bar['border_width'] . 'px solid ' . $bar_border_color . ';';
        }
        if ( $bar['is_borders']['left'] )
        {
            $styles .= 'border-left:' . $bar['border_width'] . 'px solid ' . $bar_border_color . ';';
        }
        if ( $bar['is_borders']['right'] )
        {
            $styles .= 'border-right:' . $bar['border_width'] . 'px solid ' . $bar_border_color . ';';
        }

        return $styles;
    }


    private function item_border( $item )
    {
        $styles = '';

        $border_color = empty( $item['border_color']['primary'] ) ? 'transparent' : $item['border_color']['primary'];

        if ( $item['is_borders']['top'] )
        {
            $styles .= '.mobile-contact-bar-item{';
            $styles .= 'border-top:' . $item['border_width'] . 'px solid ' . $border_color . ';';
            $styles .= '}';
        }
        if ( $item['is_borders']['bottom'] )
        {
            $styles .= '.mobile-contact-bar-item{';
            $styles .= 'border-bottom:' . $item['border_width'] . 'px solid ' . $border_color . ';';
            $styles .= '}';
        }
        if ( $item['is_borders']['left'] && $item['is_borders']['right'] )
        {
            $styles .= '#mobile-contact-bar li{';
            $styles .= 'border-left:' . $item['border_width'] . 'px solid ' . $border_color . ';';
            $styles .= '}';

            $styles .= '#mobile-contact-bar li:last-child{';
            $styles .= 'border-right:' . $item['border_width'] . 'px solid ' . $border_color . ';';
            $styles .= '}';
        }
        if (( $item['is_borders']['left'] && ! $item['is_borders']['right'] ) || ( ! $item['is_borders']['left'] && $item['is_borders']['right'] ))
        {
            $styles .= '#mobile-contact-bar li{';
            $styles .= 'border-left:' . $item['border_width'] . 'px solid ' . $border_color . ';';
            $styles .= '}';

            $styles .= '#mobile-contact-bar li:first-child{';
            $styles .= 'border-left:0;';
            $styles .= '}';
        }

        return $styles;
    }


    private function toggle( $bar, $toggle )
    {
        $styles = '';

        if ( $toggle['is_render'] && $bar['is_fixed'] )
        {
            if ( $toggle['is_animation'] )
            {
                if ( 0 === $bar['space_height'] && 'bottom' === $bar['position'] )
                {
                    $styles .= '#mobile-contact-bar-nav{';
                    $styles .= 'transition:bottom 1s ease;';
                    $styles .= '}';

                    $styles .= '#mobile-contact-bar-toggle{';
                    $styles .= 'transition:bottom 1s ease;';
                    $styles .= '}';
                }
                elseif ( 0 === $bar['space_height'] && 'top' === $bar['position'] )
                {
                    $styles .= '#mobile-contact-bar-nav{';
                    $styles .= 'transition:top 1s ease;';
                    $styles .= '}';

                    $styles .= '#mobile-contact-bar-toggle{';
                    $styles .= 'transition:top 1s ease;';
                    $styles .= '}';
                }
                else
                {
                    $styles .= '#mobile-contact-bar-nav{';
                    $styles .= 'transition:height 1s ease;';
                    $styles .= '}';

                    $styles .= '.mobile-contact-bar-icon,';
                    $styles .= '.mobile-contact-bar-label,';
                    $styles .= '.mobile-contact-bar-badge{';
                    $styles .= 'opacity:1;';
                    $styles .= 'transition:opacity 0.5s ease;';
                    $styles .= '}';
                }
            }
            if ( 0 === $bar['space_height'] && 'bottom' === $bar['position'] )
            {
                $styles .= '#mobile-contact-bar-nav{';
                $styles .= 'position:relative;';
                $styles .= 'bottom:0;';
                $styles .= '}';

                $styles .= '#mobile-contact-bar-toggle{';
                $styles .= 'bottom:0;';
                $styles .= '}';

                $styles .= '#mobile-contact-bar-toggle-checkbox:checked ~ #mobile-contact-bar-nav{';
                $styles .= 'bottom:-' . $bar['shortest'] . 'px;';
                $styles .= '}';

                $styles .= '#mobile-contact-bar-toggle-checkbox:checked ~ #mobile-contact-bar-toggle{';
                $styles .= 'bottom:-' . $bar['shortest'] . 'px;';
                $styles .= '}';
            }
            elseif ( 0 === $bar['space_height'] && 'top' === $bar['position'] )
            {
                $styles .= '#mobile-contact-bar-nav{';
                $styles .= 'position:relative;';
                $styles .= 'top:0;';
                $styles .= '}';

                $styles .= '#mobile-contact-bar-toggle{';
                $styles .= 'top:' . $bar['shortest'] . 'px;';
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
                $styles .= '#mobile-contact-bar-toggle-checkbox:checked ~ #mobile-contact-bar-nav{';
                $styles .= 'height:0;';
                $styles .= '}';
    
                $styles .= '#mobile-contact-bar-toggle-checkbox:checked ~ #mobile-contact-bar-nav .mobile-contact-bar-icon,';
                $styles .= '#mobile-contact-bar-toggle-checkbox:checked ~ #mobile-contact-bar-nav .mobile-contact-bar-label,';
                $styles .= '#mobile-contact-bar-toggle-checkbox:checked ~ #mobile-contact-bar-nav .mobile-contact-bar-badge{';
                $styles .= 'opacity:0;';
                $styles .= '}';
            }

            $styles .= '#mobile-contact-bar-toggle-checkbox{';
            $styles .= 'display:none;';
            $styles .= 'position:absolute;';
            $styles .= '}';

            $styles .= '#mobile-contact-bar-toggle{';
            $styles .= 'display:table;';
            $styles .= ( empty( $toggle['background_color']['primary'] )) ? '' : 'color:' . $toggle['background_color']['primary'] . ';';
            $styles .= 'cursor:pointer;';
            $styles .= 'line-height:0;';
            $styles .= 'margin:0 auto;';
            $styles .= 'padding:0;';
            $styles .= 'position:relative;';
            $styles .= 'z-index:2;';
            $styles .= '}';

            $styles .= '#mobile-contact-bar-toggle span{';
            $styles .= 'display:inline-block;';
            $styles .= ( empty( $toggle['font_color']['primary'] )) ? '' : 'color:' . $toggle['font_color']['primary'] . ';';
            $styles .= 'font-size:' . $toggle['font_size'] . 'em;';
            $styles .= 'position:absolute;';
            $styles .= 'bottom:50%;';
            $styles .= 'left:50%;';
            $styles .= 'transform:translate(-50%);';
            $styles .= 'text-align:center;';
            $styles .= 'width:100%;';
            $styles .= 'z-index:2;';
            $styles .= '}';

            $styles .= '#mobile-contact-bar-toggle svg{';
            $styles .= 'display:inline-block;';
            $styles .= 'pointer-events:none;';
            $styles .= 'z-index:1;';
            $styles .= '}';
        }

        return $styles;
    }


    private function badge( $badge )
    {
        $styles = '';

        $styles .= '.mobile-contact-bar-badge{';
        $styles .= ( empty( $badge['background_color']['primary'] )) ? '' : 'background-color:' . $badge['background_color']['primary'] . ';';
        $styles .= ( empty( $badge['font_color']['primary'] )) ? '' : 'color:' . $badge['font_color']['primary'] . ';';
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

        switch ( $badge['position'] )
        {
            case 'top-right':
                $styles .= 'top:0;';
                $styles .= 'right:0;';
                $styles .= 'transform-origin:top right;';
                $styles .= 'transform:scale(' . $badge['size'] . ') translate(' . $badge['size'] . 'em,' . (-1) * $badge['size'] . 'em);';
                break;

            case 'bottom-right':
                $styles .= 'bottom:0;';
                $styles .= 'right:0;';
                $styles .= 'transform-origin:bottom right;';
                $styles .= 'transform:scale(' . $badge['size'] . ') translate(' . $badge['size'] . 'em,' . $badge['size'] . 'em);';
                break;

            case 'bottom-left':
                $styles .= 'bottom:0;';
                $styles .= 'left:0;';
                $styles .= 'transform-origin:bottom left;';
                $styles .= 'transform:scale(' . $badge['size'] . ') translate(' . (-1) * $badge['size'] . 'em,' . $badge['size'] . 'em);';
                break;

            case 'top-left':
                $styles .= 'top:0;';
                $styles .= 'left:0;';
                $styles .= 'transform-origin:top left;';
                $styles .= 'transform:scale(' . $badge['size'] . ') translate(' . (-1) * $badge['size'] . 'em,' . (-1) * $badge['size'] . 'em);';
                break;
        }
        $styles .= '}';

        return $styles;
    }


    private function item_pseudo_classes( $bar, $item, $toggle, $badge )
    {
        $styles = '';

        // Item
        if ( ! empty( $item['background_color']['secondary'] ))
        {
            if ( $bar['is_secondary_colors']['focus'] )
            {
                $styles .= '.mobile-contact-bar-item:focus{';
                $styles .= 'background-color:' . $item['background_color']['secondary'] . ';';
                $styles .= '}';
            }
            if ( $bar['is_secondary_colors']['hover'] )
            {
                $styles .= '.mobile-contact-bar-item:hover{';
                $styles .= 'background-color:' . $item['background_color']['secondary'] . ';';
                $styles .= '}';
            }
            if ( $bar['is_secondary_colors']['active'] )
            {
                $styles .= '.mobile-contact-bar-item.mobile-contact-bar-active{';
                $styles .= 'background-color:' . $item['background_color']['secondary'] . ';';
                $styles .= '}';
            }
        }

        if ( ! empty( $item['icon_color']['secondary'] ))
        {
            if ( $bar['is_secondary_colors']['focus'] )
            {
                $styles .= '.mobile-contact-bar-item:focus .mobile-contact-bar-icon svg{';
                $styles .= 'color:' . $item['icon_color']['secondary'] . ';';
                $styles .= '}';
            }
            if ( $bar['is_secondary_colors']['hover'] )
            {
                $styles .= '.mobile-contact-bar-item:hover .mobile-contact-bar-icon svg{';
                $styles .= 'color:' . $item['icon_color']['secondary'] . ';';
                $styles .= '}';
            }
            if ( $bar['is_secondary_colors']['active'] )
            {
                $styles .= '.mobile-contact-bar-item.mobile-contact-bar-active .mobile-contact-bar-icon svg{';
                $styles .= 'color:' . $item['icon_color']['secondary'] . ';';
                $styles .= '}';
            }
        }

        if ( ! empty( $item['label_color']['secondary'] ))
        {
            if ( $bar['is_secondary_colors']['focus'] )
            {
                $styles .= '.mobile-contact-bar-item:focus .mobile-contact-bar-label{';
                $styles .= 'color:' . $item['label_color']['secondary'] . ';';
                $styles .= '}';
            }
            if ( $bar['is_secondary_colors']['hover'] )
            {
                $styles .= '.mobile-contact-bar-item:hover .mobile-contact-bar-label{';
                $styles .= 'color:' . $item['label_color']['secondary'] . ';';
                $styles .= '}';
            }
            if ( $bar['is_secondary_colors']['active'] )
            {
                $styles .= '.mobile-contact-bar-item.mobile-contact-bar-active .mobile-contact-bar-label{';
                $styles .= 'color:' . $item['label_color']['secondary'] . ';';
                $styles .= '}';
            }
        }


        // Item borders
        $border_color = empty( $item['border_color']['secondary'] ) ? 'transparent' : $item['border_color']['secondary'];
        if ( $bar['is_secondary_colors']['focus'] )
        {
            if ( $item['is_borders']['top'] )
            {
                $styles .= '.mobile-contact-bar-item:focus{';
                $styles .= 'border-top:' . $item['border_width'] . 'px solid ' . $border_color . ';';
                $styles .= '}';
            }
            if ( $item['is_borders']['bottom'] )
            {
                $styles .= '.mobile-contact-bar-item:focus{';
                $styles .= 'border-bottom:' . $item['border_width'] . 'px solid ' . $border_color . ';';
                $styles .= '}';
            }            
        }
        if ( $bar['is_secondary_colors']['hover'] )
        {
            if ( $item['is_borders']['top'] )
            {
                $styles .= '.mobile-contact-bar-item:hover{';
                $styles .= 'border-top:' . $item['border_width'] . 'px solid ' . $border_color . ';';
                $styles .= '}';
            }
            if ( $item['is_borders']['bottom'] )
            {
                $styles .= '.mobile-contact-bar-item:hover{';
                $styles .= 'border-bottom:' . $item['border_width'] . 'px solid ' . $border_color . ';';
                $styles .= '}';
            }            
        }
        if ( $bar['is_secondary_colors']['active'] )
        {
            if ( $item['is_borders']['top'] )
            {
                $styles .= '.mobile-contact-bar-item.mobile-contact-bar-active{';
                $styles .= 'border-top:' . $item['border_width'] . 'px solid ' . $border_color . ';';
                $styles .= '}';
            }
            if ( $item['is_borders']['bottom'] )
            {
                $styles .= '.mobile-contact-bar-item.mobile-contact-bar-active{';
                $styles .= 'border-bottom:' . $item['border_width'] . 'px solid ' . $border_color . ';';
                $styles .= '}';
            }            
        }


        // Badge
        if ( ! empty( $badge['background_color']['secondary'] ))
        {
            if ( $bar['is_secondary_colors']['focus'] )
            {
                $styles .= '.mobile-contact-bar-item:focus .mobile-contact-bar-badge{';
                $styles .= 'background-color:' . $badge['background_color']['secondary'] . ';';
                $styles .= '}';
            }
            if ( $bar['is_secondary_colors']['hover'] )
            {
                $styles .= '.mobile-contact-bar-item:hover .mobile-contact-bar-badge{';
                $styles .= 'background-color:' . $badge['background_color']['secondary'] . ';';
                $styles .= '}';
            }
            if ( $bar['is_secondary_colors']['active'] )
            {
                $styles .= '.mobile-contact-bar-item.mobile-contact-bar-active .mobile-contact-bar-badge{';
                $styles .= 'background-color:' . $badge['background_color']['secondary'] . ';';
                $styles .= '}';
            }
        }
        if ( ! empty( $badge['font_color']['secondary'] ))
        {
            if ( $bar['is_secondary_colors']['focus'] )
            {
                $styles .= '.mobile-contact-bar-item:focus .mobile-contact-bar-badge{';
                $styles .= 'color:' . $badge['font_color']['secondary'] . ';';
                $styles .= '}';
            }
            if ( $bar['is_secondary_colors']['hover'] )
            {
                $styles .= '.mobile-contact-bar-item:hover .mobile-contact-bar-badge{';
                $styles .= 'color:' . $badge['font_color']['secondary'] . ';';
                $styles .= '}';
            }
            if ( $bar['is_secondary_colors']['active'] )
            {
                $styles .= '.mobile-contact-bar-item.mobile-contact-bar-active .mobile-contact-bar-badge{';
                $styles .= 'color:' . $badge['font_color']['secondary'] . ';';
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
                $styles .= '#mobile-contact-bar-toggle:focus span{';
                $styles .= 'color:' . $toggle['font_color']['secondary'] . ';';
                $styles .= '}';
            }
            if ( $bar['is_secondary_colors']['hover'] )
            {
                $styles .= '#mobile-contact-bar-toggle:hover span{';
                $styles .= 'color:' . $toggle['font_color']['secondary'] . ';';
                $styles .= '}';
            }
        }

        return $styles;
    }


    private function item_custom_colors( $item, $contact )
    {
        $styles = '';

        if ( ! empty( $contact['custom']['background_color']['primary'] ))
        {
            $styles .= '#' . $contact['id'] . ' .mobile-contact-bar-item{';
            $styles .= 'background-color:' . $contact['custom']['background_color']['primary'] . ';';
            $styles .= '}';
        }

        if ( ! empty( $contact['custom']['icon_color']['primary'] ))
        {
            $styles .= '#' . $contact['id'] . ' .mobile-contact-bar-icon{';
            $styles .= 'color:' . $contact['custom']['icon_color']['primary'] . ';';
            $styles .= '}';
        }

        if ( ! empty( $contact['custom']['label_color']['primary'] ))
        {
            $styles .= '#' . $contact['id'] . ' .mobile-contact-bar-label{';
            $styles .= 'color:' . $contact['custom']['label_color']['primary'] . ';';
            $styles .= '}';
        }

        if ( $item['is_borders']['top'] && ! empty( $contact['custom']['border_color']['primary'] ))
        {
            $styles .= '#' . $contact['id'] . ' .mobile-contact-bar-item{';
            $styles .= 'border-top-color:' . $contact['custom']['border_color']['primary'] . ';';
            $styles .= '}';
        }
        if ( $item['is_borders']['bottom'] && ! empty( $contact['custom']['border_color']['primary'] ))
        {
            $styles .= '#' . $contact['id'] . ' .mobile-contact-bar-item{';
            $styles .= 'border-bottom-color:' . $contact['custom']['border_color']['primary'] . ';';
            $styles .= '}';
        }

        return $styles;
    }


    private function item_pseudo_classes_custom_colors( $bar, $item, $contact )
    {
        $styles = '';

        if ( ! empty( $contact['custom']['background_color']['secondary'] ))
        {
            if ( $bar['is_secondary_colors']['focus'] )
            {
                $styles .= '#' . $contact['id'] . ' .mobile-contact-bar-item:focus{';
                $styles .= 'background-color:' . $contact['custom']['background_color']['secondary'] . ';';
                $styles .= '}';
            }
            if ( $bar['is_secondary_colors']['hover'] )
            {
                $styles .= '#' . $contact['id'] . ' .mobile-contact-bar-item:hover{';
                $styles .= 'background-color:' . $contact['custom']['background_color']['secondary'] . ';';
                $styles .= '}';
            }
            if ( $bar['is_secondary_colors']['active'] )
            {
                $styles .= '#' . $contact['id'] . ' .mobile-contact-bar-item.mobile-contact-bar-active{';
                $styles .= 'background-color:' . $contact['custom']['background_color']['secondary'] . ';';
                $styles .= '}';
            }
        }

        if ( ! empty( $contact['custom']['icon_color']['secondary'] ))
        {
            if ( $bar['is_secondary_colors']['focus'] )
            {
                $styles .= '#' . $contact['id'] . ' .mobile-contact-bar-item:focus .mobile-contact-bar-icon svg{';
                $styles .= 'color:' . $contact['custom']['icon_color']['secondary'] . ';';
                $styles .= '}';
            }
            if ( $bar['is_secondary_colors']['hover'] )
            {
                $styles .= '#' . $contact['id'] . ' .mobile-contact-bar-item:hover .mobile-contact-bar-icon svg{';
                $styles .= 'color:' . $contact['custom']['icon_color']['secondary'] . ';';
                $styles .= '}';
            }
            if ( $bar['is_secondary_colors']['active'] )
            {
                $styles .= '#' . $contact['id'] . ' .mobile-contact-bar-item.mobile-contact-bar-active .mobile-contact-bar-icon svg{';
                $styles .= 'color:' . $contact['custom']['icon_color']['secondary'] . ';';
                $styles .= '}';
            }
        }

        if ( ! empty( $contact['custom']['label_color']['secondary'] ))
        {
            if ( $bar['is_secondary_colors']['focus'] )
            {
                $styles .= '#' . $contact['id'] . ' .mobile-contact-bar-item:focus .mobile-contact-bar-label{';
                $styles .= 'color:' . $contact['custom']['label_color']['secondary'] . ';';
                $styles .= '}';
            }
            if ( $bar['is_secondary_colors']['hover'] )
            {
                $styles .= '#' . $contact['id'] . ' .mobile-contact-bar-item:hover .mobile-contact-bar-label{';
                $styles .= 'color:' . $contact['custom']['label_color']['secondary'] . ';';
                $styles .= '}';
            }
            if ( $bar['is_secondary_colors']['active'] )
            {
                $styles .= '#' . $contact['id'] . ' .mobile-contact-bar-item.mobile-contact-bar-active .mobile-contact-bar-label{';
                $styles .= 'color:' . $contact['custom']['label_color']['secondary'] . ';';
                $styles .= '}';
            }
        }

        if ( $bar['is_secondary_colors']['focus'] )
        {
            if ( $item['is_borders']['top'] && ! empty( $contact['custom']['border_color']['secondary'] ))
            {
                $styles .= '#' . $contact['id'] . ' .mobile-contact-bar-item:focus{';
                $styles .= 'border-top-color:' . $contact['custom']['border_color']['secondary'] . ';';
                $styles .= '}';
            }
            if ( $item['is_borders']['bottom'] && ! empty( $contact['custom']['border_color']['secondary'] ))
            {
                $styles .= '#' . $contact['id'] . ' .mobile-contact-bar-item:focus{';
                $styles .= 'border-bottom-color:' . $contact['custom']['border_color']['secondary'] . ';';
                $styles .= '}';
            }
        }

        if ( $bar['is_secondary_colors']['hover'] )
        {
            if ( $item['is_borders']['top'] && ! empty( $contact['custom']['border_color']['secondary'] ))
            {
                $styles .= '#' . $contact['id'] . ' .mobile-contact-bar-item:hover{';
                $styles .= 'border-top-color:' . $contact['custom']['border_color']['secondary'] . ';';
                $styles .= '}';
            }
            if ( $item['is_borders']['bottom'] && ! empty( $contact['custom']['border_color']['secondary'] ))
            {
                $styles .= '#' . $contact['id'] . ' .mobile-contact-bar-item:hover{';
                $styles .= 'border-bottom-color:' . $contact['custom']['border_color']['secondary'] . ';';
                $styles .= '}';
            }
        }

        if ( $bar['is_secondary_colors']['active'] )
        {
            if ( $item['is_borders']['top'] && ! empty( $contact['custom']['border_color']['secondary'] ))
            {
                $styles .= '#' . $contact['id'] . ' .mobile-contact-bar-item.mobile-contact-bar-active{';
                $styles .= 'border-top-color:' . $contact['custom']['border_color']['secondary'] . ';';
                $styles .= '}';
            }
            if ( $item['is_borders']['bottom'] && ! empty( $contact['custom']['border_color']['secondary'] ))
            {
                $styles .= '#' . $contact['id'] . ' .mobile-contact-bar-item.mobile-contact-bar-active{';
                $styles .= 'border-bottom-color:' . $contact['custom']['border_color']['secondary'] . ';';
                $styles .= '}';
            }
        }

        return $styles;
    }
}
