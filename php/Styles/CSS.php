<?php

namespace MobileContactBar\Styles;

final class CSS
{
    /**
     * Generates the public styles from settings and contacts.
     *
     * @param  array $settings Multidimensional array of bar settings
     * @param  array $contacts Multidimensional array of bar contacts
     * @return array           The generated bar styles
     */
    public static function generate( $settings = [], $contacts = [] )
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
        switch ( $bar['is_border'] )
        {
            case 'one':
                switch ( $bar['vertical_alignment'] )
                {
                    case 'top':
                        $styles .= 'border-bottom:' . $bar['border_width'] . 'px solid ' . $bar['border_color'] . ';';
                        break;

                    case 'bottom':
                        $styles .= 'border-top:' . $bar['border_width'] . 'px solid ' . $bar['border_color'] . ';';
                        break;
                }
                break;

            case 'two':
                $styles .= 'border-top:' . $bar['border_width'] . 'px solid ' . $bar['border_color'] . ';';
                $styles .= 'border-bottom:' . $bar['border_width'] . 'px solid ' . $bar['border_color'] . ';';
                break;
        }
        $styles .= '}';


        // Items
        $styles .= '.mobile-contact-bar-item{';
        $styles .= 'background-color:' . $items['background_color'] . ';';
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
        switch ( $bar['is_border'] )
        {
            case 'one':
                // $styles .= 'height:' . ( $bar['height'] - $bar['border_width'] ) . 'px;';				
                break;

            case 'two':
                // $styles .= 'height:' . ( $bar['height'] - 2 * $bar['border_width'] ) . 'px;';
                break;
        // 	case 'none':
        // 		$styles .= 'height:' . $bar['height'] . 'px;';
        // 		break;
        // }
        }
        $styles .= '}';

        if ( $items['borders']['top'] )
        {
            $styles .= '.mobile-contact-bar-item{';
            $styles .= 'border-top:' . $items['border_width'] . 'px solid ' . $items['border_color'] . ';';
            $styles .= '}';
        }
        if ( $items['borders']['bottom'] )
        {
            $styles .= '.mobile-contact-bar-item{';
            $styles .= 'border-bottom:' . $items['border_width'] . 'px solid ' . $items['border_color'] . ';';
            $styles .= '}';
        }
        if ( $items['borders']['left'] && $items['borders']['right'] )
        {
            $styles .= '.mobile-contact-bar-item{';
            $styles .= 'border-left:' . $items['border_width'] . 'px solid ' . $items['border_color'] . ';';
            $styles .= '}';

            $styles .= '.mobile-contact-bar-item:last-child{';
            $styles .= 'border-right:' . $items['border_width'] . 'px solid ' . $items['border_color'] . ';';
            $styles .= '}';
        }
        if (( $items['borders']['left'] && ! $items['borders']['right'] ) || ( ! $items['borders']['left'] && $items['borders']['right'] ))
        {
            $styles .= '.mobile-contact-bar-item{';
            $styles .= 'border-left:' . $items['border_width'] . 'px solid ' . $items['border_color'] . ';';
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
        $styles .= '}';

        $styles .= '.mobile-contact-bar-icon i{';
        $styles .= 'color:' . $items['icon_color'] . ';';
        $styles .= 'font-size:' . $items['icon_size'] . 'em;';
        $styles .= '}';

        $styles .= '.mobile-contact-bar-label{';
        $styles .= 'color:' . $items['label_color'] . ';';
        $styles .= 'font-size:' . $items['label_size'] . 'em;';
        $styles .= 'line-height:1;';
        $styles .= '}';


        // Hover over item, active item
        if ( ! empty( $items['secondary_background_color'] ) && $items['secondary_background_color'] !== 'transparent' )
        {
            if ( $items['secondary_colors']['focus'] )
            {
                $styles .= '.mobile-contact-bar-item:focus{';
                $styles .= 'background-color:' . $items['secondary_background_color'] . ';';
                $styles .= '}';
            }
            if ( $items['secondary_colors']['hover'] )
            {
                $styles .= '.mobile-contact-bar-item:hover{';
                $styles .= 'background-color:' . $items['secondary_background_color'] . ';';
                $styles .= '}';
            }
            if ( $items['secondary_colors']['active'] )
            {
                $styles .= '.mobile-contact-bar-item.mobile-contact-bar-active{';
                $styles .= 'background-color:' . $items['secondary_background_color'] . ';';
                $styles .= '}';
            }
        }

        if ( ! empty( $items['secondary_icon_color'] ) && $items['secondary_icon_color'] !== 'transparent' )
        {
            if ( $items['secondary_colors']['focus'] )
            {
                $styles .= '.mobile-contact-bar-item:focus .mobile-contact-bar-icon i{';
                $styles .= 'color:' . $items['secondary_icon_color'] . ';';
                $styles .= '}';
            }
            if ( $items['secondary_colors']['hover'] )
            {
                $styles .= '.mobile-contact-bar-item:hover .mobile-contact-bar-icon i{';
                $styles .= 'color:' . $items['secondary_icon_color'] . ';';
                $styles .= '}';
            }
            if ( $items['secondary_colors']['active'] )
            {
                $styles .= '.mobile-contact-bar-item.mobile-contact-bar-active .mobile-contact-bar-icon i{';
                $styles .= 'color:' . $items['secondary_icon_color'] . ';';
                $styles .= '}';
            }
        }

        if ( ! empty( $items['secondary_label_color'] ) && $items['secondary_label_color'] !== 'transparent' )
        {
            if ( $items['secondary_colors']['focus'] )
            {
                $styles .= '.mobile-contact-bar-item:focus .mobile-contact-bar-label{';
                $styles .= 'color:' . $items['secondary_label_color'] . ';';
                $styles .= '}';
            }
            if ( $items['secondary_colors']['hover'] )
            {
                $styles .= '.mobile-contact-bar-item:hover .mobile-contact-bar-label{';
                $styles .= 'color:' . $items['secondary_label_color'] . ';';
                $styles .= '}';
            }
            if ( $items['secondary_colors']['active'] )
            {
                $styles .= '.mobile-contact-bar-item.mobile-contact-bar-active .mobile-contact-bar-label{';
                $styles .= 'color:' . $items['secondary_label_color'] . ';';
                $styles .= '}';
            }
        }


        // Toggle
        if ( !! $toggle && $toggle['is_render'] && $bar['is_fixed'] )
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
            $styles .= 'color:' . $toggle['font_color'] . ';';
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
            $styles .= 'fill:' . $toggle['background_color'] . ';';
            $styles .= 'z-index:1;';
            $styles .= '}';
        } // endif is_toggle


        // Badges
        if ( !! $badges )
        {
            $styles .= '.mobile-contact-bar-badge{';
            $styles .= 'background-color:' . $badges['background_color'] . ';';
            $styles .= 'border-radius:100%;';
            $styles .= 'color:' . $badges['font_color'] . ';';
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
        if ( 'bottom' === $bar['vertical_alignment'] && $bar['is_fixed'] )
        {
            if ( $bar['placeholder_height'] > 0 )
            {
                $styles .= 'body{';
                $styles .= 'border-bottom:' . $bar['placeholder_height'] . 'px solid ' . $bar['placeholder_color'] . '!important;';
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
        if ( 'top' === $bar['vertical_alignment'] && $bar['is_fixed'] )
        {
            if ( $bar['placeholder_height'] > 0 )
            {
                $styles .= 'body{';
                $styles .= 'border-top:' . $bar['placeholder_height'] . 'px solid ' . $bar['placeholder_color'] . '!important;';
                $styles .= '}';
            }

                $styles .= '#mobile-contact-bar{';
                $styles .= 'position:fixed;';
                $styles .= 'left:0;';
                $styles .= ( $bar['space_height'] > 0 ) ? 'top:' . $bar['space_height'] . 'px;' : 'top:0;';
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
        if ( 'bottom' === $bar['vertical_alignment'] && ! $bar['is_fixed'] )
        {
            if ( $bar['placeholder_height'] > 0 )
            {
                $styles .= 'body{';
                $styles .= 'border-bottom:' . $bar['placeholder_height'] . 'px solid ' . $bar['placeholder_color'] . '!important;';
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
        if ( 'top' === $bar['vertical_alignment'] && ! $bar['is_fixed'] )
        {
            if ( $bar['placeholder_height'] > 0 )
            {
                $styles .= 'body{';
                $styles .= 'border-top:' . $bar['placeholder_height'] . 'px solid ' . $bar['placeholder_color'] . '!important;';
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
            if ( !! $contact['id'] )
            {
                $styles .= '#' . $contact['id'] . '.mobile-contact-bar-item{';
                $styles .= 'background-color:' . $contact['palette']['background_color'] . ';';
                $styles .= '}';

                $styles .= '#' . $contact['id'] . ' .mobile-contact-bar-icon i{';
                $styles .= 'color:' . $contact['palette']['icon_color'] . ';';
                $styles .= '}';

                $styles .= '#' . $contact['id'] . ' .mobile-contact-bar-label{';
                $styles .= 'color:' . $contact['palette']['font_color'] . ';';
                $styles .= '}';

                if ( $items['borders']['top'] )
                {
                    $styles .= '#' . $contact['id'] . '.mobile-contact-bar-item{';
                    $styles .= 'border-top-color:' . $contact['palette']['border_color'] . ';';
                    $styles .= '}';
                }
                if ( $items['borders']['bottom'] )
                {
                    $styles .= '#' . $contact['id'] . '.mobile-contact-bar-item{';
                    $styles .= 'border-bottom-color:' . $contact['palette']['border_color'] . ';';
                    $styles .= '}';
                }
            }
        }

        return $styles;
    }
}
