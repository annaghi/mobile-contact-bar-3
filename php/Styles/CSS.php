<?php

namespace MobileContactBar\Styles;


final class CSS
{
    /**
     * Outputs the public 'styles' generated from 'settings' and 'contacts'.
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
        $styles .= 'width:' . $bar['width'] . '%;';
        $styles .= 'z-index:9998;';
        $styles .= '}';

        $styles .= '#mobile-contact-bar:before,';
        $styles .= '#mobile-contact-bar:after{';
        $styles .= 'content:"";';
        $styles .= 'display:table;';
        $styles .= '}';

        $styles .= '#mobile-contact-bar:after{';
        $styles .= 'clear:both;';
        $styles .= '}';

        $styles .= '#mobile-contact-bar-nav{';
        $styles .= 'height:' . $bar['height'] . 'px;';
        $styles .= 'overflow:hidden;';
        $styles .= 'width:100%;';
        $styles .= '}';

        $styles .= $this->toggle( $bar, $toggle );

        $styles .= '#mobile-contact-bar ul{';
        $styles .= 'box-sizing:border-box;';
        $styles .= 'list-style-type:none;';
        $styles .= 'margin:0;';
        $styles .= 'padding:0;';
        $styles .= 'width:100%;';
        $styles .= 'height:100%;';
        $styles .= 'display:flex;';
        $styles .= 'flex-flow:row nowrap;';
        $styles .= 'justify-content:center;';
        if ( $bar['is_borders']['top'] )
        {
            $bar_border_color = empty( $bar['border_color'] ) ? 'transparent' : $bar['border_color'];
            $styles .= 'border-top:' . $bar['border_width'] . 'px solid ' . $bar_border_color . ';';
        }
        if ( $bar['is_borders']['bottom'] )
        {
            $bar_border_color = empty( $bar['border_color'] ) ? 'transparent' : $bar['border_color'];
            $styles .= 'border-bottom:' . $bar['border_width'] . 'px solid ' . $bar_border_color . ';';
        }
        $styles .= '}';


        // Item
        $styles .= '#mobile-contact-bar li{';
        $styles .= 'margin:0;';
        $styles .= 'padding:0;';
        switch ( $item['alignment'] )
        {
            case 'centered':
                $styles .= 'width:' . $item['width'] . 'px;';
                break;

            case 'justified':
                $styles .= ( $checked_contacts_count > 0 ) ? 'width:' . ( 100 / $checked_contacts_count ) . '%;' : 'width:100%;';
                break;
        }
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
        $styles .= 'font-size: 100%;';
        $styles .= 'line-height: 50%;';
        $styles .= ( empty( $item['icon_color']['primary'] )) ? '' : 'color:' . $item['icon_color']['primary'] . ';';
        $styles .= '}';

        $styles .= '.mobile-contact-bar-icon svg{';
        $styles .= 'width:1.5em;';
        $styles .= 'height:1.5em;';
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

        $styles .= $this->pseudo_classes( $bar, $item, $badge, $toggle );

       
        $styles .= $this->bar_position( $bar, $toggle );


        // Item customization
        foreach ( $checked_contacts as $contact )
        {
            if ( $contact['id'] )
            {
                $styles .= $this->custom_colors( $item, $contact );
                $styles .= $this->pseudo_classes_custom_colors( $bar, $item, $contact );
            }
        }

        return $styles;
    }


    private function bar_position( $bar, $toggle )
    {
        $styles = '';

        $placeholder_color = ( empty( $bar['placeholder_color'] )) ? 'transparent' : $bar['placeholder_color'];

        // bottom
        // fixed
        if ( 'bottom' === $bar['vertical_alignment'] && $bar['is_sticky'] )
        {
            if ( $bar['placeholder_height'] > 0 )
            {
                $styles .= 'body{';
                $styles .= 'border-bottom:' . $bar['placeholder_height'] . 'px solid ' . $placeholder_color . '!important;';
                $styles .= '}';
            }

            $styles .= '#mobile-contact-bar{';
            $styles .= 'position:fixed;';
            $styles .= 'left:0;';
            $styles .= ( $bar['space_height'] > 0 ) ? 'bottom:' . $bar['space_height'] . 'px;' : 'bottom:0;';
            $styles .= '}';
        }

        // top
        // fixed
        if ( 'top' === $bar['vertical_alignment'] && $bar['is_sticky'] )
        {
            if ( $bar['placeholder_height'] > 0 )
            {
                if ( is_admin_bar_showing() )
                {
                    
                }
            
                $styles .= 'body{';
                $styles .= 'border-top:' . $bar['placeholder_height'] . 'px solid ' . $placeholder_color . '!important;';
                $styles .= '}';
            }

                $styles .= '#mobile-contact-bar{';
                $styles .= 'position:fixed;';
                $styles .= 'left:0;';
                $styles .= ( $bar['space_height'] > 0 ) ? 'top:' . ($bar['space_height'] + 46) . 'px;' : 'top:46px;';
                $styles .= '}';

            if ( $toggle['is_render'] )
            {
                $styles .= '#mobile-contact-bar-toggle{';
                $styles .= 'position:absolute;';
                $styles .= 'bottom:-34px;';
                $styles .= 'left:50%;';
                $styles .= 'transform:translateX(-50%);';
                $styles .= '}';
            }
        }

        // bottom
        // not fixed
        if ( 'bottom' === $bar['vertical_alignment'] && ! $bar['is_sticky'] )
        {
            if ( $bar['placeholder_height'] > 0 )
            {
                $styles .= 'body{';
                $styles .= 'border-bottom:' . $bar['placeholder_height'] . 'px solid ' . $placeholder_color . '!important;';
                $styles .= '}';
            }

            $styles .= '#mobile-contact-bar{';
            $styles .= 'margin-top:-' . $bar['height'] . 'px;';
            $styles .= 'position:relative;';
            $styles .= 'left:0;';
            if ( $bar['placeholder_height'] > 0 )
            {
                $styles .= ( $bar['space_height'] > 0 ) ? 'bottom:' . ( $bar['space_height'] - $bar['placeholder_height'] ) . 'px;' : 'bottom:-' . $bar['placeholder_height'] . 'px;';
            }
            else
            {
                $styles .= ( $bar['space_height'] > 0 ) ? 'bottom:' . $bar['space_height'] . 'px;' : 'bottom:0;';
            }
            $styles .= '}';
        }

        // top
        // not fixed
        if ( 'top' === $bar['vertical_alignment'] && ! $bar['is_sticky'] )
        {
            if ( $bar['placeholder_height'] > 0 )
            {
                $styles .= 'body{';
                $styles .= 'border-top:' . $bar['placeholder_height'] . 'px solid ' . $placeholder_color . '!important;';
                $styles .= '}';
            }

            $styles .= '#mobile-contact-bar{';
            $styles .= 'position:absolute;';
            $styles .= 'left:0;';
            $styles .= ( $bar['space_height'] > 0 ) ? 'top:' . $bar['space_height'] . 'px;' : 'top:0;';
            $styles .= '}';
        }


        // Bar width
        if ( $bar['width'] < 100 )
        {
            switch ( $bar['horizontal_alignment'] )
            {
                case 'center':
                    $styles .= '#mobile-contact-bar{';
                    $styles .= 'left:50%;';
                    $styles .= 'transform:translateX(-50%);';
                    $styles .= '}';
                    break;

                case 'right':
                    $styles .= '#mobile-contact-bar{';
                    $styles .= 'left:100%;';
                    $styles .= 'transform:translateX(-100%);';
                    $styles .= '}';
                    break;
            }
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

        if ( $toggle['is_render'] && $bar['is_sticky'] )
        {
            if ( $toggle['is_animation'] )
            {
                $styles .= '#mobile-contact-bar-nav{';
                $styles .= 'transition:height 1s ease;';
                $styles .= '}';
            }

            $styles .= '#mobile-contact-bar-toggle-checkbox:checked ~ #mobile-contact-bar-nav{';
            $styles .= 'height:0;';
            $styles .= '}';

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
        $styles .= 'border-radius:100%;';
        $styles .= 'display:flex;';
        $styles .= 'align-items:center;';
        $styles .= 'justify-content:center;';
        $styles .= 'font-size:1em;';
        $styles .= 'height:1.5em;';
        $styles .= 'width:1.5em;';
        $styles .= 'line-height:1.5;';
        $styles .= 'text-indent:0;';
        $styles .= 'position:absolute;';

        switch ( $badge['position'] )
        {
            case 'top-right':
                $styles .= 'top:0;';
                $styles .= 'right:0;';
                $styles .= 'transform-origin:top right;';
                $styles .= 'transform:scale(' . $badge['font_size'] . ') translate(' . $badge['font_size'] . 'em,' . (-1) * $badge['font_size'] . 'em);';
                break;

            case 'bottom-right':
                $styles .= 'bottom:0;';
                $styles .= 'right:0;';
                $styles .= 'transform-origin:bottom right;';
                $styles .= 'transform:scale(' . $badge['font_size'] . ') translate(' . $badge['font_size'] . 'em,' . $badge['font_size'] . 'em);';
                break;

            case 'bottom-left':
                $styles .= 'bottom:0;';
                $styles .= 'left:0;';
                $styles .= 'transform-origin:bottom left;';
                $styles .= 'transform:scale(' . $badge['font_size'] . ') translate(' . (-1) * $badge['font_size'] . 'em,' . $badge['font_size'] . 'em);';

                break;
            case 'top-left':
                $styles .= 'top:0;';
                $styles .= 'left:0;';
                $styles .= 'transform-origin:top left;';
                $styles .= 'transform:scale(' . $badge['font_size'] . ') translate(' . (-1) * $badge['font_size'] . 'em,' . (-1) * $badge['font_size'] . 'em);';
                break;
        }
        $styles .= '}';

        return $styles;
    }


    private function pseudo_classes( $bar, $item, $badge, $toggle )
    {
        $styles = '';

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


    private function custom_colors( $item, $contact )
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


    private function pseudo_classes_custom_colors( $bar, $item, $contact )
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
