<?php

namespace MobileContactBar\Migrations;

use MobileContactBar\File;
use MobileContactBar\ContactTypes;
use MobileContactBar\Helper;
use MobileContactBar\Settings;


final class Migrate_3_0_0
{
    public $option_bar_v2 = false;


    /**
     * @return bool
     */
    public function run()
    {
        $this->option_bar_v2 = get_option( abmcb()->id );
        $this->migrate_option_bar();
        $this->migrate_user_meta();

        abmcb( File::class )->create();

        return true;
    }


    /**
     * @return void
     */
    public function migrate_option_bar()
    {
        $settings = $this->migrate_settings();
        $contacts = $this->migrate_contacts();

        update_option( abmcb()->id, ['settings' => $settings, 'contacts' => $contacts] );
    }


    /**
     * @return array
     */
    private function migrate_settings()
    {
        $settings = [];

        if ( isset( $this->option_bar_v2['settings'] ) && is_array( $this->option_bar_v2['settings'] ))
        {
            $settings_v2 = $this->option_bar_v2['settings'];
            $settings = $settings_v2;

            $settings['icons_labels']['background_color']['secondary']       = '';
            $settings['icons_labels']['icon_color']['secondary']             = '';
            $settings['icons_labels']['label_color']['secondary']            = '';
            $settings['icons_labels']['border_color']['secondary']           = '';

            $settings['badges']['background_color']                          = [];
            $settings['badges']['font_color']                                = [];
            $settings['badges']['background_color']['secondary']             = '';
            $settings['badges']['font_color']['secondary']                   = '';

            if ( isset( $settings_v2['bar'] ))
            {
                if ( isset( $settings_v2['bar']['height'] ))
                {
                    $settings['bar']['shortest']                             = (int) $settings_v2['bar']['height'];    
                }
                if ( isset( $settings_v2['bar']['width'], $settings_v2['icons']['alignment'] ))
                {
                    if ( 100 === (int) $settings_v2['bar']['width'] && 'justified' === $settings_v2['icons']['alignment'] )
                    {
                        $settings['bar']['span']                             = 'stretch';
                    }
                    elseif ( 100 === (int) $settings_v2['bar']['width'] && 'centered' === $settings_v2['icons']['alignment'] )
                    {
                        $settings['bar']['span']                             = 'fix_max';
                    }
                    else
                    {
                        $settings['bar']['span']                             = 'fix_min';
                    }
                }
                if ( isset( $settings_v2['bar']['horizontal_position'] ))
                {
                    switch( $settings_v2['bar']['horizontal_position'] )
                    {
                        case 'left':
                            $settings['bar']['alignment']                    = 0;
                            break;

                        case 'center':
                            $settings['bar']['alignment']                    = 50;
                            break;

                        case 'right':
                            $settings['bar']['alignment']                    = 100;
                            break;
                    }
                    
                }
                if ( isset( $settings_v2['bar']['vertical_position'] ))
                {
                    $settings['bar']['position']                             = $settings_v2['bar']['vertical_position'];    
                }
                if ( isset( $settings_v2['bar']['vertical_position'], $settings_v2['bar']['is_border'] ))
                {
                    if ( 'top' === $settings_v2['bar']['vertical_position'] && 'one' === $settings_v2['bar']['is_border'] )
                    {
                        $settings['bar']['is_borders']['top']                = 0;
                        $settings['bar']['is_borders']['bottom']             = 1;
                    }
                    elseif ( 'bottom' === $settings_v2['bar']['vertical_position'] && 'one' === $settings_v2['bar']['is_border'] )
                    {
                        $settings['bar']['is_borders']['top']                 = 1;
                        $settings['bar']['is_borders']['bottom']              = 0;
                    }
                }
                if ( isset( $settings_v2['bar']['is_border'] ))
                {
                    if ( 'two' === $settings_v2['bar']['is_border'] )
                    {
                        $settings['bar']['is_borders']['top']                 = 1;
                        $settings['bar']['is_borders']['bottom']              = 1;
                    }
                }
                if ( isset( $settings_v2['bar']['color'] ))
                {
                    $settings['icons_labels']['background_color']['primary'] = $settings_v2['bar']['color'];
                }
                if ( isset( $settings_v2['bar']['space_height'] ))
                {
                    $settings['bar']['space']                                 = (int) $settings_v2['bar']['space_height'];
                }
            }

            if ( isset( $settings_v2['icons'] ))
            {
                if ( isset( $settings_v2['icons']['width'] ))
                {
                    $settings['icons_labels']['width']                       = (int) $settings_v2['icons']['width'];
                }
                if ( isset( $settings_v2['icons']['is_border'] ))
                {
                    if ( 'two' === $settings_v2['icons']['is_border'] )
                    {
                        $settings['icons_labels']['is_borders']['top']       = 0;
                        $settings['icons_labels']['is_borders']['right']     = 1;
                        $settings['icons_labels']['is_borders']['bottom']    = 0;
                        $settings['icons_labels']['is_borders']['left']      = 1;
                    }
                    elseif ( 'four' === $settings_v2['icons']['is_border'] )
                    {
                        $settings['icons_labels']['is_borders']['top']       = 1;
                        $settings['icons_labels']['is_borders']['right']     = 1;
                        $settings['icons_labels']['is_borders']['bottom']    = 1;
                        $settings['icons_labels']['is_borders']['left']      = 1;
                    }
                }
                if ( isset( $settings_v2['icons']['border_color'] ))
                {
                    $settings['icons_labels']['border_color']['primary']     = $settings_v2['icons']['border_color'];
                }
                if ( isset( $settings_v2['icons']['border_width'] ))
                {
                    $settings['icons_labels']['border_width']                = (int) $settings_v2['icons']['border_width'];
                }
                if ( isset( $settings_v2['icons']['size'] ))
                {
                    $settings['icons_labels']['icon_size']                   = $this->migrate_icon_size( $settings_v2['icons']['size'] );
                }
                if ( isset( $settings_v2['icons']['color'] ))
                {
                    $settings['icons_labels']['icon_color']['primary']       = $settings_v2['icons']['color'];
                    $settings['icons_labels']['label_color']['primary']      = $settings_v2['icons']['color'];
                    $settings['toggle']['font_color']['primary']             = $settings_v2['icons']['color'];
                }
            }

            if ( isset( $settings_v2['toggle'] ))
            {
                if ( isset( $settings_v2['toggle']['color'] ))
                {
                    $settings['toggle']['background_color']['primary']       = $settings_v2['toggle']['color'];
                }
                if ( isset( $settings_v2['toggle']['size'] ))
                {
                    $settings['toggle']['font_size']                         = (float) $settings_v2['toggle']['size'];
                }
            }

            if ( isset( $settings_v2['badges'] ))
            {
                if ( isset( $settings_v2['badges']['place'] ))
                {
                    $settings['badges']['position']                          = $settings_v2['badges']['place'];
                }
                if ( isset( $settings_v2['badges']['size'] ))
                {
                    $settings['badges']['size']                              = round( floor( $settings_v2['badges']['size'] * 1.25 * 200 ) / 10, 0, PHP_ROUND_HALF_DOWN ) / 20;
                }
                if ( isset( $settings_v2['badges']['background_color'] ))
                {
                    $settings['badges']['background_color']['primary']       = $settings_v2['badges']['background_color'];
                }
                if ( isset( $settings_v2['badges']['font_color'] ))
                {
                    $settings['badges']['font_color']['primary']             = $settings_v2['badges']['font_color'];
                }
            }
        }

        $default_settings = abmcb( Settings\Input::class )->default_settings();
        $refreshed_settings = Helper::array_intersect_key_recursive(
            array_replace_recursive( $default_settings, $settings ),
            $default_settings
        );

        return $refreshed_settings;
    }


    /**
     * @param  string $size_v2
     * @return float
     */
    private function migrate_icon_size( $size_v2 )
    {
        switch ( $size_v2 )
        {
            case 'xs':
                return 0.75;

            case 'sm':
                return 0.9;

            case 'lg':
                return 1.35;

            case '1x':
                return 1;

            case '2x':
                return 2;

            case '3x':
                return 3;

            case '4x':
                return 4;

            case '5x':
            case '6x':
            case '7x':
            case '8x':
            case '9x':
            case '10x':
                return 5;

            default:
                return 1.35;
        }
    }


    /**
     * @return array
     */
    private function migrate_contacts()
    {
        $contacts = [];

        if ( isset( $this->option_bar_v2['contacts'] ) && is_array( $this->option_bar_v2['contacts'] ))
        {
            $contacts_v2 = $this->option_bar_v2['contacts'];
            $default_customization = ContactTypes\ContactType::default_customization();

            foreach ( $contacts_v2 as $contact_v2 )
            {
                if ( ! isset( $contact_v2['type'], $contact_v2['title'], $contact_v2['uri'], $contact_v2['placeholder'] )
                    || ! in_array( $contact_v2['type'], ['Custom', 'Email', 'Sample', 'ScrollTop', 'Text', 'WhatsApp', 'WooCommerce'] ))
                {
                    continue;
                }

                $contact_type = $this->migrate_contact_type( strtolower( $contact_v2['type'] ), $contact_v2['uri'], $contact_v2['placeholder'] );
                if ( empty( $contact_type ))
                {
                    continue;
                }

                $contact            = [];
                $contact['type']    = $contact_type;
                $contact['id']      = '';
                $contact['checked'] = ( isset( $contact_v2['checked'] )) ? $contact_v2['checked'] : 0;
                $contact['brand']   = '';
                $contact['group']   = ''; 
                $contact['icon']    = '';
                if ( isset( $contact_v2['icon'] ))
                {
                    $group = $this->migrate_group( $contact_v2['icon'] );
                    $icon  = $this->migrate_icon( $contact_v2['icon'] );

                    if ( ! empty( $group ) && ! empty( $icon ))
                    {
                        $contact['brand'] = 'fa';
                        $contact['group'] = $group; 
                        $contact['icon']  = $icon;
                    }
                }
                $contact['label']   = '';
                $contact['text']    = $contact_v2['title'];
                $contact['uri']     = ( '#' === $contact_v2['uri'] ) ? '' : esc_url_raw( rawurldecode( $contact_v2['uri'] ), abmcb()->schemes );
                $contact['custom']  = $default_customization;

                if ( isset( $contact_v2['parameters'] ) && is_array( $contact_v2['parameters'] ))
                {
                    $contact['query'] = [];

                    foreach ( $contact_v2['parameters'] as $parameter_v2 )
                    {
                        $parameter          = [];
                        $parameter['key']   = ( isset( $parameter_v2['key'] ))   ? rawurlencode( rawurldecode( $parameter_v2['key'] ))   : '';
                        $parameter['value'] = ( isset( $parameter_v2['value'] )) ? rawurlencode( rawurldecode( $parameter_v2['value'] )) : '';

                        $contact['query'][] = $parameter;
                    }
                }

                if ( 'link' === $contact['type'] && ! isset( $contact['query'] ))
                {
                    $contact['query'] = [];
                }

                $contacts[] = $contact;
            }
        }

        return $contacts;
    }


    /**
     * @param  string $contact_type_v2
     * @param  string $uri_v2
     * @param  string $placeholder_v2
     * @return string
     */
    private function migrate_contact_type( $contact_type_v2, $uri_v2, $placeholder_v2 )
    {
        if ( 'whatsapp' === $contact_type_v2 || 'https://api.whatsapp.com/send' === ( untrailingslashit( $uri_v2 )))
        {
            return 'whatsapp';
        }

        switch ( $contact_type_v2 )
        {
            case 'email':
            case 'whatsapp':
            case 'woocommerce':
                return $contact_type_v2;

            case 'scrolltop':
                return 'scrolltotop';

            case 'text':
                return 'sms';

            case 'custom':
                return $this->migrate_general_contact_type( $uri_v2 );

            case 'sample':
                return $this->migrate_general_contact_type( $placeholder_v2 );

            default:
                return '';
        }
    }


    /**
     * @param  string $uri_v2
     * @return string
     */
    private function migrate_general_contact_type( $uri_v2 )
    {
        if ( 'https://api.whatsapp.com/send' === untrailingslashit( $uri_v2 ))
        {
            return 'whatsapp';
        }

        $scheme = array_reduce(
            abmcb()->schemes,
            function ( $acc, $scheme ) use ( $uri_v2 ) { return ( strpos( $uri_v2, $scheme ) > -1 ) ? $scheme : $acc; },
            ''
        );

        switch( $scheme )
        {
            case 'http':
            case 'https':
                return 'link';

            case 'mailto':
                return 'email';

            case 'skype':
                return 'skype';

            case 'sms':
                return 'sms';

            case 'tel':
                return 'tel';

            default:
                return '';
        }
    }


    /**
     * @param  string $icon_v2
     * @return string
     */
    private function migrate_group( $icon_v2 )
    {
        $names = preg_split( '/\s+/', $icon_v2, -1, PREG_SPLIT_NO_EMPTY );
        if ( ! is_array( $names ) || count( $names ) !== 2 )
        {
            return '';
        }

        switch ( $names[0] )
        {
            case 'fas':
                return 'solid';

            case 'far':
                return 'regular';

            case 'fab':
                return 'brands';

            default:
                return '';
        }
    }


    /**
     * @param  string $icon_v2
     * @return string
     */
    private function migrate_icon( $icon_v2 )
    {
        $names = preg_split( '/\s+/', $icon_v2, -1, PREG_SPLIT_NO_EMPTY );
        if ( ! is_array( $names ) || count( $names ) !== 2 )
        {
            return '';
        }

        return str_replace( 'fa-', '', $names[1] );
    }


    /**
     * @return void
     */
    private function migrate_user_meta()
    {
        $meta_boxes = array_merge(
            array_map( function ( $section ) { return 'mcb-meta-box-' . $section; }, abmcb( Settings\Input::class )->sections() ),
            ['mcb-meta-box-contacts']
        );

        if ( ! class_exists( 'WooCommerce' ))
        {
            $meta_boxes = array_diff( $meta_boxes, 'mcb-meta-box-badges' );
        }
        
        $user_id = get_current_user_id();

        // No hidden meta boxes
        update_user_meta( $user_id, 'metaboxhidden_' . abmcb()->page_suffix, [] );
        
        // Close all meta boxes
        $closed_meta_boxes = array_merge( $meta_boxes, ['mcb-meta-box-preview'] );
        update_user_meta( $user_id, 'closedpostboxes_' . abmcb()->page_suffix, $closed_meta_boxes );

        // Reorder meta boxes
        $order_meta_boxes = [];
        $order_meta_boxes['advanced'] = implode( ',', $meta_boxes );
        $order_meta_boxes['side'] = 'mcb-meta-box-preview';
        update_user_meta( $user_id, 'meta-box-order_' . abmcb()->page_suffix, $order_meta_boxes );

        // Set 2 column layout
        update_user_meta( $user_id, 'screen_layout_' . abmcb()->page_suffix, 2 );
    }
}
