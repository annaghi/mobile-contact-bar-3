<?php

namespace MobileContactBar\Migrations;

use MobileContactBar\ContactTypes;


final class Migrate_3_0_0
{
    public $option_bar_v2 = false;


    /**
     * @return bool
     */
    public function run()
    {
        $this->option_bar_v2 = get_option( abmcb()->id );
        $this->migrate_bar();
        $this->migrate_user_meta();

        return true;
    }


    public function migrate_bar()
    {
        $settings = $this->migrate_settings();
        $contacts = $this->migrate_contacts();
        $styles   = '';

        $option_bar = [
            'settings' => $settings,
            'contacts' => $contacts,
            'styles'   => $styles,
        ];

        update_option( abmcb()->id, $option_bar );
    }


    // TODO migrate icon size
    // TODO migrate badge size
    private function migrate_settings()
    {
        $settings = [];

        if ( isset( $this->option_bar_v2['settings'] ) && is_array( $this->option_bar_v2['settings'] ))
        {
            $settings_v2 = $this->option_bar_v2['settings'];

            $settings['icons_labels']['background_color']['secondary']       = '';
            $settings['icons_labels']['icon_color']['secondary']             = '';
            $settings['icons_labels']['label_color']['secondary']            = '';
            $settings['icons_labels']['border_color']['secondary']           = '';
            
            $settings = array_replace_recursive( $settings, $settings_v2 );

            if ( isset( $settings_v2['bar'] ))
            {
                if ( isset( $settings_v2['bar']['is_fixed'] ))
                {
                    $settings['bar']['is_sticky']                            = $settings_v2['bar']['is_fixed'];
                }
                if ( isset( $settings_v2['bar']['horizontal_position'] ))
                {
                    $settings['bar']['horizontal_alignment']                 = $settings_v2['bar']['horizontal_position'];
                }
                if ( isset( $settings_v2['bar']['vertical_position'] ))
                {
                    $settings['bar']['vertical_alignment']                   = $settings_v2['bar']['vertical_position'];    
                }
                if ( isset( $settings_v2['bar']['placeholder_height'] ))
                {
                    $settings['bar']['placeholder_height']                   = $settings_v2['bar']['placeholder_height'];
                }
                if ( isset( $settings_v2['bar']['vertical_alignment'], $settings_v2['bar']['is_border'] ))
                {
                    if ( $settings_v2['bar']['vertical_alignment'] === 'top' && $settings_v2['bar']['is_border'] === 'one' )
                    {
                        $settings['bar']['is_borders']['top']                = 0;
                        $settings['bar']['is_borders']['bottom']             = 1;
                    }
                    elseif ( $settings_v2['bar']['vertical_alignment'] === 'bottom' && $settings_v2['bar']['is_border'] === 'one' )
                    {
                        $settings['bar']['is_borders']['top']                = 1;
                        $settings['bar']['is_borders']['bottom']             = 0;
                    }
                }
                if ( isset( $settings_v2['bar']['is_border'] ))
                {
                    if ( $settings_v2['bar']['is_border'] === 'two' )
                    {
                        $settings['bar']['is_borders']['top']                = 1;
                        $settings['bar']['is_borders']['bottom']             = 1;
                    }
                }
                if ( isset( $settings_v2['bar']['color'] ))
                {
                    $settings['icons_labels']['background_color']['primary'] = $settings_v2['bar']['color'];
                }
            }

            if ( isset( $settings_v2['icons'] ))
            {
                if ( isset( $settings_v2['icons']['alignment'] ))
                {
                    $settings['icons_labels']['alignment']                   = $settings_v2['icons']['alignment'];
                }
                if ( isset( $settings_v2['icons']['width'] ))
                {
                    $settings['icons_labels']['width']                       = $settings_v2['icons']['width'];
                }
                if ( isset( $settings_v2['icons']['is_border'] ))
                {
                    if ( $settings_v2['icons']['is_border'] === 'two' )
                    {
                        $settings['icons_labels']['is_borders']['top']       = 0;
                        $settings['icons_labels']['is_borders']['right']     = 1;
                        $settings['icons_labels']['is_borders']['bottom']    = 0;
                        $settings['icons_labels']['is_borders']['left']      = 1;
                    }
                    elseif ( $settings_v2['icons']['is_border'] === 'four' )
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
                    $settings['icons_labels']['border_width']                = $settings_v2['icons']['border_width'];
                }
                if ( isset( $settings_v2['icons']['size'] ))
                {
                    $settings['icons_labels']['icon_size']                   = $settings_v2['icons']['size'];
                }
                if ( isset( $settings_v2['icons']['color'] ))
                {
                    $settings['icons_labels']['icon_color']['primary']       = $settings_v2['icons']['color'];
                    $settings['icons_labels']['label_color']['primary']      = $settings_v2['icons']['color'];
                    $settings['toggle']['font_color']                        = $settings_v2['icons']['color'];
                }
            }

            if ( isset( $settings_v2['toggle'] ))
            {
                if ( isset( $settings_v2['toggle']['color'] ))
                {
                    $settings['toggle']['background_color']                  = $settings_v2['toggle']['color'];
                }
                if ( isset( $settings_v2['toggle']['size'] ))
                {
                    $settings['toggle']['font_size']                         = $settings_v2['toggle']['size'];
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
                    $settings['badges']['font_size']                         = $settings_v2['badges']['size'];
                }
            }
        }

        return $settings;
    }


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
                $contact['text']    = '';
                $contact['uri']     = ( $contact_v2['uri'] === '#' ) ? '' : $contact_v2['uri'];
                $contact['custom']  = $default_customization;

                if ( isset( $contact_v2['parameters'] ) && is_array( $contact_v2['parameters'] ))
                {
                    $contact['parameters'] = [];

                    foreach ( $contact_v2['parameters'] as $parameter_v2 )
                    {
                        $parameter          = [];
                        $parameter['key']   = ( isset( $parameter_v2['key'] )) ? $parameter_v2['key'] : '';
                        $parameter['value'] = ( isset( $parameter_v2['value'] )) ? $parameter_v2['value'] : '';

                        $contact['parameters'][] = $parameter;
                    }
                }

                if ( 'link' === $contact['type'] && ! isset( $contact['parameters'] ))
                {
                    $contact['parameters'] = [];
                }

                $contacts[] = $contact;
            }
        }

        return $contacts;
    }


    private function migrate_contact_type( $contact_type, $uri, $placeholder )
    {
        if ( $contact_type === 'whatsapp' || ( untrailingslashit( $uri ) === 'https://api.whatsapp.com/send' ))
        {
            return 'whatsapp';
        }

        switch ( $contact_type )
        {
            case 'email':
            case 'whatsapp':
            case 'woocommerce':
                return $contact_type;

            case 'scrolltop':
                return 'scrolltotop';

            case 'text':
                return 'sms';

            case 'custom':
                return $this->migrate_general_contact_type( $uri );

            case 'sample':
                return $this->migrate_general_contact_type( $placeholder );

            default:
                return '';
        }
    }


    private function migrate_general_contact_type( $uri )
    {
        if ( untrailingslashit( $uri ) === 'https://api.whatsapp.com/send' )
        {
            return 'whatsapp';
        }

        $scheme = array_reduce(
            abmcb()->schemes,
            function ( $acc, $scheme ) use ( $uri ) { return ( strpos( $uri, $scheme ) > -1 ) ? $scheme : $acc; },
            ''
        );

        switch( $scheme )
        {
            case 'tel':
                return 'tel';

            case 'sms':
                return 'sms';

            case 'skype':
                return 'skype';

            case 'mailto':
                return 'email';

            case 'http':
            case 'https':
                return 'link';

            default:
                return 'link';
        }
    }


    private function migrate_group( $icon )
    {
        $names = preg_split( '/\s+/', $icon, -1, PREG_SPLIT_NO_EMPTY );
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


    private function migrate_icon( $icon )
    {
        $names = preg_split( '/\s+/', $icon, -1, PREG_SPLIT_NO_EMPTY );
        if ( ! is_array( $names ) || count( $names ) !== 2 )
        {
            return '';
        }

        return str_replace( 'fa-', '', $names[1] );
    }


    private function migrate_user_meta()
    {
        $user_id = get_current_user_id();

        // No hidden meta box
        update_user_meta( $user_id, 'metaboxhidden_' . abmcb()->page_suffix, [] );
        
        // Update closed meta boxes
        $closed_meta_boxes = get_user_meta( $user_id, 'closedpostboxes_' . abmcb()->page_suffix, true );

        if ( $closed_meta_boxes )
        {
            $closed_meta_boxes = array_diff( $closed_meta_boxes, ['mcb-section-model'] );
            if ( in_array( 'mcb-section-icons', $closed_meta_boxes ))
            {
                $closed_meta_boxes = array_diff( $closed_meta_boxes, ['mcb-section-icons'] );
                $closed_meta_boxes = array_merge( $closed_meta_boxes, ['mcb-section-icons_labels'] );
            }
            update_user_meta( $user_id, 'closedpostboxes_' . abmcb()->page_suffix, $closed_meta_boxes );
        }

        // Reorder meta boxes
        $order_meta_boxes = [];
        if ( class_exists( 'WooCommerce' ))
        {
            $order_meta_boxes['advanced'] = 'mcb-section-bar,mcb-section-icons_labels,mcb-section-badges,mcb-section-toggle,mcb-section-contacts';
            $order_meta_boxes['side'] = 'mcb-section-live-preview';
        }
        else
        {
            $order_meta_boxes['advanced'] = 'mcb-section-bar,mcb-section-icons_labels,mcb-section-toggle,mcb-section-contacts';
            $order_meta_boxes['side'] = 'mcb-section-live-preview';
        }
        update_user_meta( $user_id, 'meta-box-order_' . abmcb()->page_suffix, $order_meta_boxes );
    }
}
