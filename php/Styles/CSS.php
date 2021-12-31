<?php

namespace MobileContactBar\Styles;


final class CSS
{
    /**
     * Outputs the public 'styles' generated from 'settings' and 'contacts'.
     *
     * @param  array  $settings
     * @param  array  $contacts
     * @return string           HTML
     */
    public static function output( $settings = [], $contacts = [] )
    {
        $styles = '';

        $bar = ( isset( $settings['bar'] ) && is_array( $settings['bar'] )) ? $settings['bar'] : false;
        $items = ( isset( $settings['icons_labels'] ) && is_array( $settings['icons_labels'] )) ? $settings['icons_labels'] : false;
        $toggle = ( isset( $settings['toggle'] ) && is_array( $settings['toggle'] )) ? $settings['toggle'] : false;
        $badges = ( isset( $settings['badges'] ) && is_array( $settings['badges'] )) ? $settings['badges'] : false;


        if ( ! $bar || ! $items )
        {
            return $styles;
        }

        $checked_contacts = array_filter(
            $contacts,
            function ( $contact ) { return $contact['checked']; }
        );
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
        $styles .= 'box-sizing:border-box;';
        $styles .= 'height:' . $bar['height'] . 'px;';
        $styles .= 'overflow:hidden;';
        $styles .= 'width:100%;';
        $styles .= '}';

        $styles .= '#mobile-contact-bar ul{';
        $styles .= 'box-sizing:border-box;';
        $styles .= 'list-style-type:none;';
        $styles .= 'margin:0;';
        $styles .= 'padding:0;';
        $styles .= 'width:100%;';
        $styles .= 'height:100%;';
        $styles .= 'display:flex;';
        $styles .= 'flex-flow: row nowrap;';
        if ( $bar['is_borders']['top'] )
        {
            $bar_border_color = empty( $bar['border_color'] ) ? 'transparent' : $bar['border_color'];
            $styles .= 'border-top:' . $bar['border_width'] . 'px solid ' . $bar_border_color . ';';
        }
        if ( $bar['is_borders']['bottom'] && ! empty( $bar['border_color'] ))
        {
            $bar_border_color = empty( $bar['border_color'] ) ? 'transparent' : $bar['border_color'];
            $styles .= 'border-bottom:' . $bar['border_width'] . 'px solid ' . $bar_border_color . ';';
        }
        $styles .= '}';


        // Items
        $styles .= '.mobile-contact-bar-item{';
        $styles .= ( empty( $items['background_color']['primary'] )) ? '' : 'background-color:' . $items['background_color']['primary'] . ';';
        // $styles .= 'box-sizing:border-box;';
        $styles .= 'margin:0;';
        $styles .= 'padding:0;';
        // $styles .= 'text-align:center;';
        switch ( $items['alignment'] )
        {
            case 'centered':
                $styles .= 'width:' . $items['width'] . 'px;';
                break;

            case 'justified':
                $styles .= ( $checked_contacts_count > 0 ) ? 'width:' . ( 100 / $checked_contacts_count ) . '%;' : 'width:100%;';
                break;
        }
        // switch ( $bar['is_border'] )
        // {
        //     case 'one':
        //         // $styles .= 'height:' . ( $bar['height'] - $bar['border_width'] ) . 'px;';				
        //         break;

        //     case 'two':
        //         // $styles .= 'height:' . ( $bar['height'] - 2 * $bar['border_width'] ) . 'px;';
        //         break;
        // // 	case 'none':
        // // 		$styles .= 'height:' . $bar['height'] . 'px;';
        // // 		break;
        // // }
        // }
        $styles .= '}';

        $items_border_color = empty( $items['border_color']['primary'] ) ? 'transparent' : $items['border_color']['primary'];
        if ( $items['is_borders']['top'] )
        {
            $styles .= '.mobile-contact-bar-item{';
            $styles .= 'border-top:' . $items['border_width'] . 'px solid ' . $items_border_color . ';';
            $styles .= '}';
        }
        if ( $items['is_borders']['bottom'] )
        {
            $styles .= '.mobile-contact-bar-item{';
            $styles .= 'border-bottom:' . $items['border_width'] . 'px solid ' . $items_border_color . ';';
            $styles .= '}';
        }
        if ( $items['is_borders']['left'] && $items['is_borders']['right'] )
        {
            $styles .= '.mobile-contact-bar-item{';
            $styles .= 'border-left:' . $items['border_width'] . 'px solid ' . $items_border_color . ';';
            $styles .= '}';

            $styles .= '.mobile-contact-bar-item:last-child{';
            $styles .= 'border-right:' . $items['border_width'] . 'px solid ' . $items_border_color . ';';
            $styles .= '}';
        }
        if (( $items['is_borders']['left'] && ! $items['is_borders']['right'] ) || ( ! $items['is_borders']['left'] && $items['is_borders']['right'] ))
        {
            $styles .= '.mobile-contact-bar-item{';
            $styles .= 'border-left:' . $items['border_width'] . 'px solid ' . $items_border_color . ';';
            $styles .= '}';

            $styles .= '.mobile-contact-bar-item:first-child{';
            $styles .= 'border-left:0;';
            $styles .= '}';
        }

        $styles .= '.mobile-contact-bar-item a{';
        $styles .= 'text-decoration:none;';
        $styles .= 'cursor:pointer;';
        $styles .= 'display:flex;';
        $styles .= 'flex-direction:column;';
        $styles .= 'justify-content:center;';
        $styles .= 'align-items:center;';
        $styles .= 'gap:' . $items['gap'] . 'em;';
        $styles .= 'height:100%;';
        // $styles .= 'z-index:9998;';
        $styles .= '}';

        $styles .= '.mobile-contact-bar-item a:active,';
        $styles .= '.mobile-contact-bar-item a:focus{';
        // $styles .= 'outline:none;';
        $styles .= '}';

        $styles .= '.mobile-contact-bar-icon{';
        $styles .= 'display:inline-flex;';
        $styles .= 'position:relative;';
        $styles .= 'font-size: 100%;';
        $styles .= 'line-height: 50%;';
        $styles .= '}';

        $styles .= '.mobile-contact-bar-icon svg{';
        $styles .= 'width:2em;';
        $styles .= 'height:2em;';
        // $styles .= 'font-size:' . $items['icon_size'] . 'em;';
        $styles .= '}';

        $styles .= '.mobile-contact-bar-fa svg{';
        $styles .= 'fill:currentColor;';
        $styles .= '}';

        $styles .= '.mobile-contact-bar-icon{';
        $styles .= ( empty( $items['icon_color']['primary'] )) ? '' : 'color:' . $items['icon_color']['primary'] . ';';
        $styles .= '}';

        $styles .= '.mobile-contact-bar-label{';
        $styles .= ( empty( $items['label_color']['primary'] )) ? '' : 'color:' . $items['label_color']['primary'] . ';';
        $styles .= 'font-size:' . $items['label_size'] . 'em;';
        $styles .= 'line-height:1;';
        $styles .= '}';


        // Hover over item, active item
        if ( ! empty( $items['background_color']['secondary'] ))
        {
            if ( $items['is_secondary_colors']['focus'] )
            {
                $styles .= '.mobile-contact-bar-item:focus{';
                $styles .= 'background-color:' . $items['background_color']['secondary'] . ';';
                $styles .= '}';
            }
            if ( $items['is_secondary_colors']['hover'] )
            {
                $styles .= '.mobile-contact-bar-item:hover{';
                $styles .= 'background-color:' . $items['background_color']['secondary'] . ';';
                $styles .= '}';
            }
            if ( $items['is_secondary_colors']['active'] )
            {
                $styles .= '.mobile-contact-bar-item.mobile-contact-bar-active{';
                $styles .= 'background-color:' . $items['background_color']['secondary'] . ';';
                $styles .= '}';
            }
        }

        if ( ! empty( $items['icon_color']['secondary'] ))
        {
            if ( $items['is_secondary_colors']['focus'] )
            {
                $styles .= '.mobile-contact-bar-item:focus .mobile-contact-bar-icon svg{';
                $styles .= 'color:' . $items['icon_color']['secondary'] . ';';
                $styles .= '}';
            }
            if ( $items['is_secondary_colors']['hover'] )
            {
                $styles .= '.mobile-contact-bar-item:hover .mobile-contact-bar-icon svg{';
                $styles .= 'color:' . $items['icon_color']['secondary'] . ';';
                $styles .= '}';
            }
            if ( $items['is_secondary_colors']['active'] )
            {
                $styles .= '.mobile-contact-bar-item.mobile-contact-bar-active .mobile-contact-bar-icon svg{';
                $styles .= 'color:' . $items['icon_color']['secondary'] . ';';
                $styles .= '}';
            }
        }

        if ( ! empty( $items['label_color']['secondary'] ))
        {
            if ( $items['is_secondary_colors']['focus'] )
            {
                $styles .= '.mobile-contact-bar-item:focus .mobile-contact-bar-label{';
                $styles .= 'color:' . $items['label_color']['secondary'] . ';';
                $styles .= '}';
            }
            if ( $items['is_secondary_colors']['hover'] )
            {
                $styles .= '.mobile-contact-bar-item:hover .mobile-contact-bar-label{';
                $styles .= 'color:' . $items['label_color']['secondary'] . ';';
                $styles .= '}';
            }
            if ( $items['is_secondary_colors']['active'] )
            {
                $styles .= '.mobile-contact-bar-item.mobile-contact-bar-active .mobile-contact-bar-label{';
                $styles .= 'color:' . $items['label_color']['secondary'] . ';';
                $styles .= '}';
            }
        }


        // Toggle
        if ( $toggle && $toggle['is_render'] && $bar['is_sticky'] )
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
            $styles .= 'cursor:pointer;';
            $styles .= 'display:table;';
            $styles .= 'line-height:0;';
            $styles .= 'margin:0 auto;';
            $styles .= 'padding:0;';
            $styles .= 'position:relative;';
            $styles .= 'z-index:2;';
            $styles .= '}';

            $styles .= '#mobile-contact-bar-toggle span{';
            $styles .= 'display:inline-block;';
            $styles .= ( empty( $toggle['font_color'] )) ? '' : 'color:' . $toggle['font_color'] . ';';
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
            $styles .= ( empty( $toggle['background_color'] )) ? '' : 'fill:' . $toggle['background_color'] . ';';
            $styles .= 'z-index:1;';
            $styles .= '}';
        } // endif is_toggle


        // Badges
        if ( $badges )
        {
            $styles .= '.mobile-contact-bar-badge{';
            $styles .= ( empty( $badges['background_color'] )) ? '' : 'background-color:' . $badges['background_color'] . ';';
            $styles .= 'border-radius:100%;';
            $styles .= ( empty( $badges['font_color'] )) ? '' : 'color:' . $badges['font_color'] . ';';
            $styles .= 'display:flex;';
            $styles .= 'align-items:center;';
            $styles .= 'justify-content:center;';
            $styles .= 'font-size:1em;';
            $styles .= 'height:1.5em;';
            $styles .= 'width:1.5em;';
            $styles .= 'line-height:1.5;';
            $styles .= 'text-indent:0;';
            $styles .= 'position:absolute;';

            switch ( $badges['position'] )
            {
                case 'top-right':
                    $styles .= 'top:0;';
                    $styles .= 'right:0;';
                    $styles .= 'transform-origin:top right;';
                    $styles .= 'transform:scale(' . $badges['font_size'] . ') translate(' . $badges['font_size'] . 'em,' . (-1) * $badges['font_size'] . 'em);';
                    break;

                case 'bottom-right':
                    $styles .= 'bottom:0;';
                    $styles .= 'right:0;';
                    $styles .= 'transform-origin:bottom right;';
                    $styles .= 'transform:scale(' . $badges['font_size'] . ') translate(' . $badges['font_size'] . 'em,' . $badges['font_size'] . 'em);';
                    break;

                case 'bottom-left':
                    $styles .= 'bottom:0;';
                    $styles .= 'left:0;';
                    $styles .= 'transform-origin:bottom left;';
                    $styles .= 'transform:scale(' . $badges['font_size'] . ') translate(' . (-1) * $badges['font_size'] . 'em,' . $badges['font_size'] . 'em);';

                    break;
                case 'top-left':
                    $styles .= 'top:0;';
                    $styles .= 'left:0;';
                    $styles .= 'transform-origin:top left;';
                    $styles .= 'transform:scale(' . $badges['font_size'] . ') translate(' . (-1) * $badges['font_size'] . 'em,' . (-1) * $badges['font_size'] . 'em);';
                    break;
            }
            $styles .= '}';
        }

        // bottom
        // fixed
        if ( 'bottom' === $bar['vertical_alignment'] && $bar['is_sticky'] )
        {
            if ( $bar['placeholder_height'] > 0 )
            {
                $placeholder_color = ( empty( $bar['placeholder_color'] )) ? 'transparent' : $bar['placeholder_color'];
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
                $placeholder_color = ( empty( $bar['placeholder_color'] )) ? 'transparent' : $bar['placeholder_color'];
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

            if ( ! empty( $toggle ) && $toggle['is_render'] )
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
                $placeholder_color = ( empty( $bar['placeholder_color'] )) ? 'transparent' : $bar['placeholder_color'];
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
                $placeholder_color = ( empty( $bar['placeholder_color'] )) ? 'transparent' : $bar['placeholder_color'];
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


        foreach ( $contacts as $contact )
        {
            if ( $contact['id'] )
            {
                if ( ! empty( $contact['custom']['background_color']['primary'] ))
                {
                    $styles .= '#' . $contact['id'] . '.mobile-contact-bar-item{';
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

                if ( $items['is_borders']['top'] && ! empty( $contact['custom']['border_color']['primary'] ))
                {
                    $styles .= '#' . $contact['id'] . '.mobile-contact-bar-item{';
                    $styles .= 'border-top-color:' . $contact['custom']['border_color']['primary'] . ';';
                    $styles .= '}';
                }
                if ( $items['is_borders']['bottom'] && ! empty( $contact['custom']['border_color']['primary'] ))
                {
                    $styles .= '#' . $contact['id'] . '.mobile-contact-bar-item{';
                    $styles .= 'border-bottom-color:' . $contact['custom']['border_color']['primary'] . ';';
                    $styles .= '}';
                }
            }
        }

        return $styles;
    }
}
