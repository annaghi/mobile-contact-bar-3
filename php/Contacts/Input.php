<?php

namespace MobileContactBar\Contacts;

use MobileContactBar\Settings;
use MobileContactBar\Helper;


final class Input
{
    /**
     * Defines sample 'contacts'.
     *
     * @return array
     */
    public function sample_contacts()
    {
        $default_customization = $this->default_customization();

        return
        [
            [
                'type'        => 'link',
                'id'          => '',
                'checked'     => 1,
                'brand'       => 'fa',
                'group'       => 'solid',
                'icon'        => 'home',
                'label'       => __( 'Home' ),
                'uri'         => get_site_url(),
                'parameters'  => [],
                'custom'      => $default_customization,
            ],
            [
                'type'        => 'email',
                'id'          => '',
                'checked'     => 1,
                'brand'       => 'fa',
                'group'       => 'regular',
                'icon'        => 'envelope',
                'label'       => __( 'Email' ),
                'uri'         => $this->email(),
                'parameters'  => [
                    [
                        'key'   => 'subject',
                        'value' => '',
                    ],
                    [
                        'key'   => 'body',
                        'value' => '',
                    ],
                    [
                        'key'   => 'cc',
                        'value' => '',
                    ],
                    [
                        'key'   => 'bcc',
                        'value' => '',
                    ],
                ],
                'custom'      => $default_customization,
            ],
            [
                'type'        => 'whatsapp',
                'id'          => '',
                'checked'     => 0,
                'brand'       => 'fa',
                'group'       => 'brands',
                'icon'        => 'whatsapp',
                'label'       => 'WhatsApp',
                'uri'         => 'https://api.whatsapp.com/send',
                'parameters'  => [
                    [
                        'key'   => 'phone',
                        'value' => '',
                    ],
                    [
                        'key'   => 'text',
                        'value' => '',
                    ],
                ],
                'custom'      => $default_customization,
            ],
            [
                'type'        => 'link',
                'id'          => '',
                'checked'     => 1,
                'brand'       => 'fa',
                'group'       => 'solid',
                'icon'        => 'map-marker-alt',
                'label'       => __( 'Map' ),
                'uri'         => 'https://google.com/maps/place/Dacre+St,+London+UK/',
                'parameters'  => [],
                'custom'      => $default_customization,
            ],
            [
                'type'        => 'scrolltotop',
                'id'          => '',
                'checked'     => 1,
                'brand'       => 'fa',
                'group'       => 'solid',
                'icon'        => 'chevron-up',
                'label'       => '',
                'uri'         => '',
                'custom'      => $default_customization,
            ],
        ];
    }


    /**
     * Retrieves the sample 'contacts' with unchecked items.
     *
     * @return array
     */
    public function unchecked_sample_contacts()
    {
        $sample_contacts = $this->sample_contacts();
        return array_map( function ( $contact ) { return array_replace( $contact, ['checked' => 0] ); }, $sample_contacts );
    }


    /**
     * Generates email address for sample input.
     *
     * @return string Email address
     */
    private function email()
    {
        if ( is_user_logged_in() )
        {
            $current_user = wp_get_current_user();
            return 'mailto:' . $current_user->data->user_email;
        }

        return 'mailto:username@example.com';
    }


    /**
     * Defines the input fields for custom settings fields.
     *
     * @return array Multidimensional array
     */
    public function custom_input_fields()
    {
        return
        [
            'background_color' => [
                'title'     => __( 'Background Color', 'mobile-contact-bar' ),
                'primary'   => [
                    'type'    => 'color-picker',
                    'default' => '',
                ],
                'secondary' => [
                    'type'    => 'color-picker',
                    'default' => '',
                ]
            ],
            'icon_color' => [
                'title'     => __( 'Icon Color', 'mobile-contact-bar' ),
                'primary'   => [
                    'type'    => 'color-picker',
                    'default' => '',
                ],
                'secondary' => [
                    'type'    => 'color-picker',
                    'default' => '',
                ]
            ],
            'label_color' => [
                'title'     => __( 'Label Color', 'mobile-contact-bar' ),
                'primary'   => [
                    'type'    => 'color-picker',
                    'default' => '',
                ],
                'secondary' => [
                    'type'    => 'color-picker',
                    'default' => '',
                ]
            ],
            'border_color' => [
                'title'     => __( 'Border Color', 'mobile-contact-bar' ),
                'primary'   => [
                    'type'    => 'color-picker',
                    'default' => '',
                ],
                'secondary' => [
                    'type'    => 'color-picker',
                    'default' => '',
                ]
            ],
        ]; 
    }


    /**
     * Retrieves the custom settings fields with default values.
     *
     * @return array
     */
    public function default_customization()
    {
        $defaults = [];
        $input_fields = $this->custom_input_fields();

        foreach ( $input_fields as $custom_key => $custom )
        {
            foreach ( $custom as $field_key => $field )
            {
                if ( isset( $field['default'] ))
                {
                    $defaults[$custom_key][$field_key] = $field['default'];
                }
            }
        }

        return $defaults;
    }


    /**
     * Retrieves the custom settings fields with empty default values.
     *
     * @return array
     */
    public function empty_default_customization()
    {
        $defaults = [];
        $input_fields = $this->custom_input_fields();

        foreach ( $input_fields as $custom_key => $custom )
        {
            foreach ( $custom as $field_key => $field )
            {
                if ( isset( $field['default'] ))
                {
                    $defaults[$custom_key][$field_key] = '';
                }
            }
        }

        return $defaults;
    }


    /**
     * Sanitizes the 'contacts'.
     *
     * @param  array $contacts
     * @return array
     */
    public function sanitize( $contacts = [] )
    {
        if ( ! is_array( $contacts ))
        {
            return $this->unchecked_sample_contacts();
        }

        $sanitized_contacts = [];

        $contact_types = abmcb()->contact_types;
        $contact_types_keys = array_keys( $contact_types );
        $empty_default_customization = $this->empty_default_customization();

        foreach ( $contacts as $contact_key => &$contact )
        {
            // remove contact if invalid 'type'
            if ( ! in_array( $contact['type'], $contact_types_keys ))
            {
                unset( $contacts[$contact_key] );
                continue;
            }

            // add empty 'checked'
            if ( ! isset( $contact['checked'] ))
            {
                $contacts[$contact_key]['checked'] = 0;
            }

            // add 'parameters' for 'link' contact type if it was empty
            if ( 'link' === $contact['type'] && ! isset( $contact['parameters'] ))
            {
                $contacts[$contact_key]['parameters'] = [];
            }

            $contacts[$contact_key]['custom'] = Helper::array_intersect_key_recursive(
                array_replace_recursive( $empty_default_customization, $contact['custom'] ),
                $empty_default_customization
            );

            $diff_contact = Helper::array_minus_key_recursive( Helper::array_keys_recursive( $contact ), $contact_types[$contact['type']]->keys());
            if ( ! empty( $diff_contact ) && count( $diff_contact ) > 1 && ! isset( $diff_contact['parameters'] ) && 'link' !== $contact['type'] )
            {
                unset( $contacts[$contact_key] );
                continue;
            }

            if ( isset( $diff_contact['parameters'] ))
            {
                $diff_parameters = array_filter(
                    $diff_contact['parameters'],
                    function( $parameter ) { return ( count( $parameter ) !== 2 || ! isset( $parameter['key'], $parameter['value'] )); }
                );
                if ( ! empty( $diff_parameters ))
                {
                    unset( $contacts[$contact_key] );
                    continue;
                }
            }

            // reindex 'parameters'
            if ( isset( $contact['parameters'] ) && ! empty( $contact['parameters'] ))
            {
                $contacts[$contact_key]['parameters'] = array_values( $contacts[$contact_key]['parameters'] );
            }

            // remove contact if invalid 'brand'
            if ( ! empty( $contact['brand'] ) && ! in_array( $contact['brand'], ['fa', 'ti'] ))
            {
                unset( $contacts[$contact_key] );
                continue;
            }

            // remove contact if 'brand' and 'group' does not match
            if ( 'ti' === $contact['brand'] && '' !== $contact['group'] )
            {
                unset( $contacts[$contact_key] );
                continue;
            }
            if ( 'fa' === $contact['brand'] && ! in_array( $contact['group'], ['regular', 'solid', 'brands'] ))
            {
                unset( $contacts[$contact_key] );
                continue;
            }

            // remove contact if 'icon' does not exist in FA or TI, but leave empty icons
            if ( ! empty( $contact['icon'] )
                && ! $this->ti_in_icons( $contact['icon'] )
                && ! $this->fa_in_icons( $contact['group'], $contact['icon'] ))
            {
                unset( $contacts[$contact_key] );
                continue;
            }
        }
        unset( $contact );


        // sanitize contacts
        foreach ( $contacts as $contact_key => $contact )
        {
            $sanitized_contact = [];

            // 'type' is already sanitized
            $sanitized_contact['type'] = $contact['type'];

            // sanitize 'checked'
            $sanitized_contact['checked'] = (int) $contact['checked'];

            // 'brand' is already sanitized
            $sanitized_contact['brand'] = $contact['brand'];

            // 'group' is already sanitized
            $sanitized_contact['group'] = $contact['group'];

            // 'icon' is already sanitized
            $sanitized_contact['icon'] = $contact['icon'];

            // sanitize 'label'
            $sanitized_contact['label'] = sanitize_text_field( $contact['label'] );

            // sanitize 'uri'
            $sanitized_contact['uri'] = Validator::sanitize_contact_uri( $contact['uri'] );

            // sanitize 'id'
            $is_any_color = array_filter(
                $contact['custom'],
                function ( $color ) { return ! empty( $color['primary'] || ! empty( $color['secondary'] )); }
            );

            $value = sanitize_key( str_replace( ['#', '.'], '', $contact['id'] ));
            if ( empty( $value ) && $is_any_color )
            {
                $sanitized_contact['id'] = 'mcb-sample-id-' . ( $this->max_key( $contacts ) + 1 );
            }
            else
            {
                $sanitized_contact['id'] = $value;
            }

            // sanitize customization
            foreach ( $contact['custom'] as $custom_key => $custom )
            {  
                foreach ( $custom as $setting_key => $setting )
                {
                    $sanitized_contact['custom'][$custom_key][$setting_key] =
                        abmcb( Settings\Input::class )->sanitize_color( $setting );
                }
            }

            // sanitize 'parameters'
            if ( isset( $contact['parameters'] ) && is_array( $contact['parameters'] ))
            {
                $sanitized_contact['parameters'] = [];

                $contact_type = $contact_types[$contact['type']]->contact();

                foreach ( $contact['parameters'] as $parameter_key => $parameter )
                {
                    if ( 'link' === $contact['type'] )
                    {
                        $field = 'text';
                    }
                    else
                    {
                        $parameter_index = array_search( $parameter['key'], array_column( $contact_type['parameters'], 'key' ));
                        $parameter_type = $contact_type['parameters'][$parameter_index];
                        $field = $parameter_type['field'];
                    }

                    // sanitize 'key'
                    $sanitized_contact['parameters'][$parameter_key]['key'] = sanitize_key( $parameter['key'] );

                    // santitize 'value'
                    $sanitized_contact['parameters'][$parameter_key]['value'] = Validator::sanitize_parameter_value( $parameter['value'], $field );
                }
            }
            $sanitized_contacts[$contact_key] = $sanitized_contact;
        }

        // reindex
        return array_values( $sanitized_contacts );
    }


    /**
     * @param  array $contacts
     * @return int
     */
    public function max_key( $contacts )
    {
        $key = -1;
        if ( 0 === count( $contacts ))
        {
            return $key;
        }
        else
        {
            $ids = array_column( $contacts, 'id' );

            foreach( $ids as $id )
            {
                $match = preg_match( '/^mcb-sample-id-([0-9]+)$/', $id, $matches );
                if ( 1 === $match )
                {
                    $key = max( $matches[1], $key );
                }
            }
            return $key;
        }
    }


    /**
     * Checks whether an icon name is a valid Font Awesome icon.
     *
     * @param  string $group Icon group, only for Font Awesome (regular, solid, brands)
     * @param  string $icon  Icon name
     * @return bool          Whether the icon exists or not
     */
    public function fa_in_icons( $group, $icon )
    {
        $icon_set = self::fa_icons();

        foreach ( $icon_set as $valid_group => $valid_icons )
        {
            if ( $group === $valid_group )
            {
                foreach ( $valid_icons as $valid_icon )
                {
                    if ( $icon === $valid_icon )
                    {
                        return true;
                    }
                }
            }
        }
        return false;
    }


    /**
     * Checks whether an icon name is a valid Tabler Icon.
     *
     * @param  string $icon Icon name, like 'envelope'
     * @return bool         Whether the icon exists or not
     */
    public function ti_in_icons( $icon )
    {
        $valid_icons = self::ti_icons();
        return in_array( $icon, $valid_icons );
    }


    /**
     * Defines the Font Awesome icons which are divided into sections: 'solid', 'regular', 'brands'.
     *
     * @return array Array of Font Awesome icon names
     */
    public static function fa_icons()
    {
        return
        [
            'solid' => ['ad', 'address-book', 'address-card', 'adjust', 'air-freshener', 'align-center', 'align-justify', 'align-left', 'align-right', 'allergies', 'ambulance', 'american-sign-language-interpreting', 'anchor', 'angle-double-down', 'angle-double-left', 'angle-double-right', 'angle-double-up', 'angle-down', 'angle-left', 'angle-right', 'angle-up', 'angry', 'ankh', 'apple-alt', 'archive', 'archway', 'arrow-alt-circle-down', 'arrow-alt-circle-left', 'arrow-alt-circle-right', 'arrow-alt-circle-up', 'arrow-circle-down', 'arrow-circle-left', 'arrow-circle-right', 'arrow-circle-up', 'arrow-down', 'arrow-left', 'arrow-right', 'arrow-up', 'arrows-alt', 'arrows-alt-h', 'arrows-alt-v', 'assistive-listening-systems', 'asterisk', 'at', 'atlas', 'atom', 'audio-description', 'award', 'baby', 'baby-carriage', 'backspace', 'backward', 'bacon', 'bacteria', 'bacterium', 'bahai', 'balance-scale', 'balance-scale-left', 'balance-scale-right', 'ban', 'band-aid', 'barcode', 'bars', 'baseball-ball', 'basketball-ball', 'bath', 'battery-empty', 'battery-full', 'battery-half', 'battery-quarter', 'battery-three-quarters', 'bed', 'beer', 'bell', 'bell-slash', 'bezier-curve', 'bible', 'bicycle', 'biking', 'binoculars', 'biohazard', 'birthday-cake', 'blender', 'blender-phone', 'blind', 'blog', 'bold', 'bolt', 'bomb', 'bone', 'bong', 'book', 'book-dead', 'book-medical', 'book-open', 'book-reader', 'bookmark', 'border-all', 'border-none', 'border-style', 'bowling-ball', 'box', 'box-open', 'box-tissue', 'boxes', 'braille', 'brain', 'bread-slice', 'briefcase', 'briefcase-medical', 'broadcast-tower', 'broom', 'brush', 'bug', 'building', 'bullhorn', 'bullseye', 'burn', 'bus', 'bus-alt', 'business-time', 'calculator', 'calendar', 'calendar-alt', 'calendar-check', 'calendar-day', 'calendar-minus', 'calendar-plus', 'calendar-times', 'calendar-week', 'camera', 'camera-retro', 'campground', 'candy-cane', 'cannabis', 'capsules', 'car', 'car-alt', 'car-battery', 'car-crash', 'car-side', 'caravan', 'caret-down', 'caret-left', 'caret-right', 'caret-square-down', 'caret-square-left', 'caret-square-right', 'caret-square-up', 'caret-up', 'carrot', 'cart-arrow-down', 'cart-plus', 'cash-register', 'cat', 'certificate', 'chair', 'chalkboard', 'chalkboard-teacher', 'charging-station', 'chart-area', 'chart-bar', 'chart-line', 'chart-pie', 'check', 'check-circle', 'check-double', 'check-square', 'cheese', 'chess', 'chess-bishop', 'chess-board', 'chess-king', 'chess-knight', 'chess-pawn', 'chess-queen', 'chess-rook', 'chevron-circle-down', 'chevron-circle-left', 'chevron-circle-right', 'chevron-circle-up', 'chevron-down', 'chevron-left', 'chevron-right', 'chevron-up', 'child', 'church', 'circle', 'circle-notch', 'city', 'clinic-medical', 'clipboard', 'clipboard-check', 'clipboard-list', 'clock', 'clone', 'closed-captioning', 'cloud', 'cloud-download-alt', 'cloud-meatball', 'cloud-moon', 'cloud-moon-rain', 'cloud-rain', 'cloud-showers-heavy', 'cloud-sun', 'cloud-sun-rain', 'cloud-upload-alt', 'cocktail', 'code', 'code-branch', 'coffee', 'cog', 'cogs', 'coins', 'columns', 'comment', 'comment-alt', 'comment-dollar', 'comment-dots', 'comment-medical', 'comment-slash', 'comments', 'comments-dollar', 'compact-disc', 'compass', 'compress', 'compress-alt', 'compress-arrows-alt', 'concierge-bell', 'cookie', 'cookie-bite', 'copy', 'copyright', 'couch', 'credit-card', 'crop', 'crop-alt', 'cross', 'crosshairs', 'crow', 'crown', 'crutch', 'cube', 'cubes', 'cut', 'database', 'deaf', 'democrat', 'desktop', 'dharmachakra', 'diagnoses', 'dice', 'dice-d20', 'dice-d6', 'dice-five', 'dice-four', 'dice-one', 'dice-six', 'dice-three', 'dice-two', 'digital-tachograph', 'directions', 'disease', 'divide', 'dizzy', 'dna', 'dog', 'dollar-sign', 'dolly', 'dolly-flatbed', 'donate', 'door-closed', 'door-open', 'dot-circle', 'dove', 'download', 'drafting-compass', 'dragon', 'draw-polygon', 'drum', 'drum-steelpan', 'drumstick-bite', 'dumbbell', 'dumpster', 'dumpster-fire', 'dungeon', 'edit', 'egg', 'eject', 'ellipsis-h', 'ellipsis-v', 'envelope', 'envelope-open', 'envelope-open-text', 'envelope-square', 'equals', 'eraser', 'ethernet', 'euro-sign', 'exchange-alt', 'exclamation', 'exclamation-circle', 'exclamation-triangle', 'expand', 'expand-alt', 'expand-arrows-alt', 'external-link-alt', 'external-link-square-alt', 'eye', 'eye-dropper', 'eye-slash', 'fan', 'fast-backward', 'fast-forward', 'faucet', 'fax', 'feather', 'feather-alt', 'female', 'fighter-jet', 'file', 'file-alt', 'file-archive', 'file-audio', 'file-code', 'file-contract', 'file-csv', 'file-download', 'file-excel', 'file-export', 'file-image', 'file-import', 'file-invoice', 'file-invoice-dollar', 'file-medical', 'file-medical-alt', 'file-pdf', 'file-powerpoint', 'file-prescription', 'file-signature', 'file-upload', 'file-video', 'file-word', 'fill', 'fill-drip', 'film', 'filter', 'fingerprint', 'fire', 'fire-alt', 'fire-extinguisher', 'first-aid', 'fish', 'fist-raised', 'flag', 'flag-checkered', 'flag-usa', 'flask', 'flushed', 'folder', 'folder-minus', 'folder-open', 'folder-plus', 'font', 'football-ball', 'forward', 'frog', 'frown', 'frown-open', 'funnel-dollar', 'futbol', 'gamepad', 'gas-pump', 'gavel', 'gem', 'genderless', 'ghost', 'gift', 'gifts', 'glass-cheers', 'glass-martini', 'glass-martini-alt', 'glass-whiskey', 'glasses', 'globe', 'globe-africa', 'globe-americas', 'globe-asia', 'globe-europe', 'golf-ball', 'gopuram', 'graduation-cap', 'greater-than', 'greater-than-equal', 'grimace', 'grin', 'grin-alt', 'grin-beam', 'grin-beam-sweat', 'grin-hearts', 'grin-squint', 'grin-squint-tears', 'grin-stars', 'grin-tears', 'grin-tongue', 'grin-tongue-squint', 'grin-tongue-wink', 'grin-wink', 'grip-horizontal', 'grip-lines', 'grip-lines-vertical', 'grip-vertical', 'guitar', 'h-square', 'hamburger', 'hammer', 'hamsa', 'hand-holding', 'hand-holding-heart', 'hand-holding-medical', 'hand-holding-usd', 'hand-holding-water', 'hand-lizard', 'hand-middle-finger', 'hand-paper', 'hand-peace', 'hand-point-down', 'hand-point-left', 'hand-point-right', 'hand-point-up', 'hand-pointer', 'hand-rock', 'hand-scissors', 'hand-sparkles', 'hand-spock', 'hands', 'hands-helping', 'hands-wash', 'handshake', 'handshake-alt-slash', 'handshake-slash', 'hanukiah', 'hard-hat', 'hashtag', 'hat-cowboy', 'hat-cowboy-side', 'hat-wizard', 'hdd', 'head-side-cough', 'head-side-cough-slash', 'head-side-mask', 'head-side-virus', 'heading', 'headphones', 'headphones-alt', 'headset', 'heart', 'heart-broken', 'heartbeat', 'helicopter', 'highlighter', 'hiking', 'hippo', 'history', 'hockey-puck', 'holly-berry', 'home', 'horse', 'horse-head', 'hospital', 'hospital-alt', 'hospital-symbol', 'hospital-user', 'hot-tub', 'hotdog', 'hotel', 'hourglass', 'hourglass-end', 'hourglass-half', 'hourglass-start', 'house-damage', 'house-user', 'hryvnia', 'i-cursor', 'ice-cream', 'icicles', 'icons', 'id-badge', 'id-card', 'id-card-alt', 'igloo', 'image', 'images', 'inbox', 'indent', 'industry', 'infinity', 'info', 'info-circle', 'italic', 'jedi', 'joint', 'journal-whills', 'kaaba', 'key', 'keyboard', 'khanda', 'kiss', 'kiss-beam', 'kiss-wink-heart', 'kiwi-bird', 'landmark', 'language', 'laptop', 'laptop-code', 'laptop-house', 'laptop-medical', 'laugh', 'laugh-beam', 'laugh-squint', 'laugh-wink', 'layer-group', 'leaf', 'lemon', 'less-than', 'less-than-equal', 'level-down-alt', 'level-up-alt', 'life-ring', 'lightbulb', 'link', 'lira-sign', 'list', 'list-alt', 'list-ol', 'list-ul', 'location-arrow', 'lock', 'lock-open', 'long-arrow-alt-down', 'long-arrow-alt-left', 'long-arrow-alt-right', 'long-arrow-alt-up', 'low-vision', 'luggage-cart', 'lungs', 'lungs-virus', 'magic', 'magnet', 'mail-bulk', 'male', 'map', 'map-marked', 'map-marked-alt', 'map-marker', 'map-marker-alt', 'map-pin', 'map-signs', 'marker', 'mars', 'mars-double', 'mars-stroke', 'mars-stroke-h', 'mars-stroke-v', 'mask', 'medal', 'medkit', 'meh', 'meh-blank', 'meh-rolling-eyes', 'memory', 'menorah', 'mercury', 'meteor', 'microchip', 'microphone', 'microphone-alt', 'microphone-alt-slash', 'microphone-slash', 'microscope', 'minus', 'minus-circle', 'minus-square', 'mitten', 'mobile', 'mobile-alt', 'money-bill', 'money-bill-alt', 'money-bill-wave', 'money-bill-wave-alt', 'money-check', 'money-check-alt', 'monument', 'moon', 'mortar-pestle', 'mosque', 'motorcycle', 'mountain', 'mouse', 'mouse-pointer', 'mug-hot', 'music', 'network-wired', 'neuter', 'newspaper', 'not-equal', 'notes-medical', 'object-group', 'object-ungroup', 'oil-can', 'om', 'otter', 'outdent', 'pager', 'paint-brush', 'paint-roller', 'palette', 'pallet', 'paper-plane', 'paperclip', 'parachute-box', 'paragraph', 'parking', 'passport', 'pastafarianism', 'paste', 'pause', 'pause-circle', 'paw', 'peace', 'pen', 'pen-alt', 'pen-fancy', 'pen-nib', 'pen-square', 'pencil-alt', 'pencil-ruler', 'people-arrows', 'people-carry', 'pepper-hot', 'percent', 'percentage', 'person-booth', 'phone', 'phone-alt', 'phone-slash', 'phone-square', 'phone-square-alt', 'phone-volume', 'photo-video', 'piggy-bank', 'pills', 'pizza-slice', 'place-of-worship', 'plane', 'plane-arrival', 'plane-departure', 'plane-slash', 'play', 'play-circle', 'plug', 'plus', 'plus-circle', 'plus-square', 'podcast', 'poll', 'poll-h', 'poo', 'poo-storm', 'poop', 'portrait', 'pound-sign', 'power-off', 'pray', 'praying-hands', 'prescription', 'prescription-bottle', 'prescription-bottle-alt', 'print', 'procedures', 'project-diagram', 'pump-medical', 'pump-soap', 'puzzle-piece', 'qrcode', 'question', 'question-circle', 'quidditch', 'quote-left', 'quote-right', 'quran', 'radiation', 'radiation-alt', 'rainbow', 'random', 'receipt', 'record-vinyl', 'recycle', 'redo', 'redo-alt', 'registered', 'remove-format', 'reply', 'reply-all', 'republican', 'restroom', 'retweet', 'ribbon', 'ring', 'road', 'robot', 'rocket', 'route', 'rss', 'rss-square', 'ruble-sign', 'ruler', 'ruler-combined', 'ruler-horizontal', 'ruler-vertical', 'running', 'rupee-sign', 'sad-cry', 'sad-tear', 'satellite', 'satellite-dish', 'save', 'school', 'screwdriver', 'scroll', 'sd-card', 'search', 'search-dollar', 'search-location', 'search-minus', 'search-plus', 'seedling', 'server', 'shapes', 'share', 'share-alt', 'share-alt-square', 'share-square', 'shekel-sign', 'shield-alt', 'shield-virus', 'ship', 'shipping-fast', 'shoe-prints', 'shopping-bag', 'shopping-basket', 'shopping-cart', 'shower', 'shuttle-van', 'sign', 'sign-in-alt', 'sign-language', 'sign-out-alt', 'signal', 'signature', 'sim-card', 'sink', 'sitemap', 'skating', 'skiing', 'skiing-nordic', 'skull', 'skull-crossbones', 'slash', 'sleigh', 'sliders-h', 'smile', 'smile-beam', 'smile-wink', 'smog', 'smoking', 'smoking-ban', 'sms', 'snowboarding', 'snowflake', 'snowman', 'snowplow', 'soap', 'socks', 'solar-panel', 'sort', 'sort-alpha-down', 'sort-alpha-down-alt', 'sort-alpha-up', 'sort-alpha-up-alt', 'sort-amount-down', 'sort-amount-down-alt', 'sort-amount-up', 'sort-amount-up-alt', 'sort-down', 'sort-numeric-down', 'sort-numeric-down-alt', 'sort-numeric-up', 'sort-numeric-up-alt', 'sort-up', 'spa', 'space-shuttle', 'spell-check', 'spider', 'spinner', 'splotch', 'spray-can', 'square', 'square-full', 'square-root-alt', 'stamp', 'star', 'star-and-crescent', 'star-half', 'star-half-alt', 'star-of-david', 'star-of-life', 'step-backward', 'step-forward', 'stethoscope', 'sticky-note', 'stop', 'stop-circle', 'stopwatch', 'stopwatch-20', 'store', 'store-alt', 'store-alt-slash', 'store-slash', 'stream', 'street-view', 'strikethrough', 'stroopwafel', 'subscript', 'subway', 'suitcase', 'suitcase-rolling', 'sun', 'superscript', 'surprise', 'swatchbook', 'swimmer', 'swimming-pool', 'synagogue', 'sync', 'sync-alt', 'syringe', 'table', 'table-tennis', 'tablet', 'tablet-alt', 'tablets', 'tachometer-alt', 'tag', 'tags', 'tape', 'tasks', 'taxi', 'teeth', 'teeth-open', 'temperature-high', 'temperature-low', 'tenge', 'terminal', 'text-height', 'text-width', 'th', 'th-large', 'th-list', 'theater-masks', 'thermometer', 'thermometer-empty', 'thermometer-full', 'thermometer-half', 'thermometer-quarter', 'thermometer-three-quarters', 'thumbs-down', 'thumbs-up', 'thumbtack', 'ticket-alt', 'times', 'times-circle', 'tint', 'tint-slash', 'tired', 'toggle-off', 'toggle-on', 'toilet', 'toilet-paper', 'toilet-paper-slash', 'toolbox', 'tools', 'tooth', 'torah', 'torii-gate', 'tractor', 'trademark', 'traffic-light', 'trailer', 'train', 'tram', 'transgender', 'transgender-alt', 'trash', 'trash-alt', 'trash-restore', 'trash-restore-alt', 'tree', 'trophy', 'truck', 'truck-loading', 'truck-monster', 'truck-moving', 'truck-pickup', 'tshirt', 'tty', 'tv', 'umbrella', 'umbrella-beach', 'underline', 'undo', 'undo-alt', 'universal-access', 'university', 'unlink', 'unlock', 'unlock-alt', 'upload', 'user', 'user-alt', 'user-alt-slash', 'user-astronaut', 'user-check', 'user-circle', 'user-clock', 'user-cog', 'user-edit', 'user-friends', 'user-graduate', 'user-injured', 'user-lock', 'user-md', 'user-minus', 'user-ninja', 'user-nurse', 'user-plus', 'user-secret', 'user-shield', 'user-slash', 'user-tag', 'user-tie', 'user-times', 'users', 'users-cog', 'users-slash', 'utensil-spoon', 'utensils', 'vector-square', 'venus', 'venus-double', 'venus-mars', 'vest', 'vest-patches', 'vial', 'vials', 'video', 'video-slash', 'vihara', 'virus', 'virus-slash', 'viruses', 'voicemail', 'volleyball-ball', 'volume-down', 'volume-mute', 'volume-off', 'volume-up', 'vote-yea', 'vr-cardboard', 'walking', 'wallet', 'warehouse', 'water', 'wave-square', 'weight', 'weight-hanging', 'wheelchair', 'wifi', 'wind', 'window-close', 'window-maximize', 'window-minimize', 'window-restore', 'wine-bottle', 'wine-glass', 'wine-glass-alt', 'won-sign', 'wrench', 'x-ray', 'yen-sign', 'yin-yang'],
            'regular' => ['address-book', 'address-card', 'angry', 'arrow-alt-circle-down', 'arrow-alt-circle-left', 'arrow-alt-circle-right', 'arrow-alt-circle-up', 'bell', 'bell-slash', 'bookmark', 'building', 'calendar', 'calendar-alt', 'calendar-check', 'calendar-minus', 'calendar-plus', 'calendar-times', 'caret-square-down', 'caret-square-left', 'caret-square-right', 'caret-square-up', 'chart-bar', 'check-circle', 'check-square', 'circle', 'clipboard', 'clock', 'clone', 'closed-captioning', 'comment', 'comment-alt', 'comment-dots', 'comments', 'compass', 'copy', 'copyright', 'credit-card', 'dizzy', 'dot-circle', 'edit', 'envelope', 'envelope-open', 'eye', 'eye-slash', 'file', 'file-alt', 'file-archive', 'file-audio', 'file-code', 'file-excel', 'file-image', 'file-pdf', 'file-powerpoint', 'file-video', 'file-word', 'flag', 'flushed', 'folder', 'folder-open', 'frown', 'frown-open', 'futbol', 'gem', 'grimace', 'grin', 'grin-alt', 'grin-beam', 'grin-beam-sweat', 'grin-hearts', 'grin-squint', 'grin-squint-tears', 'grin-stars', 'grin-tears', 'grin-tongue', 'grin-tongue-squint', 'grin-tongue-wink', 'grin-wink', 'hand-lizard', 'hand-paper', 'hand-peace', 'hand-point-down', 'hand-point-left', 'hand-point-right', 'hand-point-up', 'hand-pointer', 'hand-rock', 'hand-scissors', 'hand-spock', 'handshake', 'hdd', 'heart', 'hospital', 'hourglass', 'id-badge', 'id-card', 'image', 'images', 'keyboard', 'kiss', 'kiss-beam', 'kiss-wink-heart', 'laugh', 'laugh-beam', 'laugh-squint', 'laugh-wink', 'lemon', 'life-ring', 'lightbulb', 'list-alt', 'map', 'meh', 'meh-blank', 'meh-rolling-eyes', 'minus-square', 'money-bill-alt', 'moon', 'newspaper', 'object-group', 'object-ungroup', 'paper-plane', 'pause-circle', 'play-circle', 'plus-square', 'question-circle', 'registered', 'sad-cry', 'sad-tear', 'save', 'share-square', 'smile', 'smile-beam', 'smile-wink', 'snowflake', 'square', 'star', 'star-half', 'sticky-note', 'stop-circle', 'sun', 'surprise', 'thumbs-down', 'thumbs-up', 'times-circle', 'tired', 'trash-alt', 'user', 'user-circle', 'window-close', 'window-maximize', 'window-minimize', 'window-restore'],
            'brands' => ['500px', 'accessible-icon', 'accusoft', 'acquisitions-incorporated', 'adn', 'adversal', 'affiliatetheme', 'airbnb', 'algolia', 'alipay', 'amazon', 'amazon-pay', 'amilia', 'android', 'angellist', 'angrycreative', 'angular', 'app-store', 'app-store-ios', 'apper', 'apple', 'apple-pay', 'artstation', 'asymmetrik', 'atlassian', 'audible', 'autoprefixer', 'avianex', 'aviato', 'aws', 'bandcamp', 'battle-net', 'behance', 'behance-square', 'bimobject', 'bitbucket', 'bitcoin', 'bity', 'black-tie', 'blackberry', 'blogger', 'blogger-b', 'bluetooth', 'bluetooth-b', 'bootstrap', 'btc', 'buffer', 'buromobelexperte', 'buy-n-large', 'buysellads', 'canadian-maple-leaf', 'cc-amazon-pay', 'cc-amex', 'cc-apple-pay', 'cc-diners-club', 'cc-discover', 'cc-jcb', 'cc-mastercard', 'cc-paypal', 'cc-stripe', 'cc-visa', 'centercode', 'centos', 'chrome', 'chromecast', 'cloudflare', 'cloudscale', 'cloudsmith', 'cloudversify', 'codepen', 'codiepie', 'confluence', 'connectdevelop', 'contao', 'cotton-bureau', 'cpanel', 'creative-commons', 'creative-commons-by', 'creative-commons-nc', 'creative-commons-nc-eu', 'creative-commons-nc-jp', 'creative-commons-nd', 'creative-commons-pd', 'creative-commons-pd-alt', 'creative-commons-remix', 'creative-commons-sa', 'creative-commons-sampling', 'creative-commons-sampling-plus', 'creative-commons-share', 'creative-commons-zero', 'critical-role', 'css3', 'css3-alt', 'cuttlefish', 'd-and-d', 'd-and-d-beyond', 'dailymotion', 'dashcube', 'deezer', 'delicious', 'deploydog', 'deskpro', 'dev', 'deviantart', 'dhl', 'diaspora', 'digg', 'digital-ocean', 'discord', 'discourse', 'dochub', 'docker', 'draft2digital', 'dribbble', 'dribbble-square', 'dropbox', 'drupal', 'dyalog', 'earlybirds', 'ebay', 'edge', 'edge-legacy', 'elementor', 'ello', 'ember', 'empire', 'envira', 'erlang', 'ethereum', 'etsy', 'evernote', 'expeditedssl', 'facebook', 'facebook-f', 'facebook-messenger', 'facebook-square', 'fantasy-flight-games', 'fedex', 'fedora', 'figma', 'firefox', 'firefox-browser', 'first-order', 'first-order-alt', 'firstdraft', 'flickr', 'flipboard', 'fly', 'font-awesome', 'font-awesome-alt', 'font-awesome-flag', 'fonticons', 'fonticons-fi', 'fort-awesome', 'fort-awesome-alt', 'forumbee', 'foursquare', 'free-code-camp', 'freebsd', 'fulcrum', 'galactic-republic', 'galactic-senate', 'get-pocket', 'gg', 'gg-circle', 'git', 'git-alt', 'git-square', 'github', 'github-alt', 'github-square', 'gitkraken', 'gitlab', 'gitter', 'glide', 'glide-g', 'gofore', 'goodreads', 'goodreads-g', 'google', 'google-drive', 'google-pay', 'google-play', 'google-plus', 'google-plus-g', 'google-plus-square', 'google-wallet', 'gratipay', 'grav', 'gripfire', 'grunt', 'guilded', 'gulp', 'hacker-news', 'hacker-news-square', 'hackerrank', 'hips', 'hire-a-helper', 'hive', 'hooli', 'hornbill', 'hotjar', 'houzz', 'html5', 'hubspot', 'ideal', 'imdb', 'innosoft', 'instagram', 'instagram-square', 'instalod', 'intercom', 'internet-explorer', 'invision', 'ioxhost', 'itch-io', 'itunes', 'itunes-note', 'java', 'jedi-order', 'jenkins', 'jira', 'joget', 'joomla', 'js', 'js-square', 'jsfiddle', 'kaggle', 'keybase', 'keycdn', 'kickstarter', 'kickstarter-k', 'korvue', 'laravel', 'lastfm', 'lastfm-square', 'leanpub', 'less', 'line', 'linkedin', 'linkedin-in', 'linode', 'linux', 'lyft', 'magento', 'mailchimp', 'mandalorian', 'markdown', 'mastodon', 'maxcdn', 'mdb', 'medapps', 'medium', 'medium-m', 'medrt', 'meetup', 'megaport', 'mendeley', 'microblog', 'microsoft', 'mix', 'mixcloud', 'mixer', 'mizuni', 'modx', 'monero', 'napster', 'neos', 'nimblr', 'node', 'node-js', 'npm', 'ns8', 'nutritionix', 'octopus-deploy', 'odnoklassniki', 'odnoklassniki-square', 'old-republic', 'opencart', 'openid', 'opera', 'optin-monster', 'orcid', 'osi', 'page4', 'pagelines', 'palfed', 'patreon', 'paypal', 'penny-arcade', 'perbyte', 'periscope', 'phabricator', 'phoenix-framework', 'phoenix-squadron', 'php', 'pied-piper', 'pied-piper-alt', 'pied-piper-hat', 'pied-piper-pp', 'pied-piper-square', 'pinterest', 'pinterest-p', 'pinterest-square', 'playstation', 'product-hunt', 'pushed', 'python', 'qq', 'quinscape', 'quora', 'r-project', 'raspberry-pi', 'ravelry', 'react', 'reacteurope', 'readme', 'rebel', 'red-river', 'reddit', 'reddit-alien', 'reddit-square', 'redhat', 'renren', 'replyd', 'researchgate', 'resolving', 'rev', 'rocketchat', 'rockrms', 'rust', 'safari', 'salesforce', 'sass', 'schlix', 'scribd', 'searchengin', 'sellcast', 'sellsy', 'servicestack', 'shirtsinbulk', 'shopify', 'shopware', 'simplybuilt', 'sistrix', 'sith', 'sketch', 'skyatlas', 'skype', 'slack', 'slack-hash', 'slideshare', 'snapchat', 'snapchat-ghost', 'snapchat-square', 'soundcloud', 'sourcetree', 'speakap', 'speaker-deck', 'spotify', 'squarespace', 'stack-exchange', 'stack-overflow', 'stackpath', 'staylinked', 'steam', 'steam-square', 'steam-symbol', 'sticker-mule', 'strava', 'stripe', 'stripe-s', 'studiovinari', 'stumbleupon', 'stumbleupon-circle', 'superpowers', 'supple', 'suse', 'swift', 'symfony', 'teamspeak', 'telegram', 'telegram-plane', 'tencent-weibo', 'the-red-yeti', 'themeco', 'themeisle', 'think-peaks', 'tiktok', 'trade-federation', 'trello', 'tumblr', 'tumblr-square', 'twitch', 'twitter', 'twitter-square', 'typo3', 'uber', 'ubuntu', 'uikit', 'umbraco', 'uncharted', 'uniregistry', 'unity', 'unsplash', 'untappd', 'ups', 'usb', 'usps', 'ussunnah', 'vaadin', 'viacoin', 'viadeo', 'viadeo-square', 'viber', 'vimeo', 'vimeo-square', 'vimeo-v', 'vine', 'vk', 'vnv', 'vuejs', 'watchman-monitoring', 'waze', 'weebly', 'weibo', 'weixin', 'whatsapp', 'whatsapp-square', 'whmcs', 'wikipedia-w', 'windows', 'wix', 'wizards-of-the-coast', 'wodu', 'wolf-pack-battalion', 'wordpress', 'wordpress-simple', 'wpbeginner', 'wpexplorer', 'wpforms', 'wpressr', 'xbox', 'xing', 'xing-square', 'y-combinator', 'yahoo', 'yammer', 'yandex', 'yandex-international', 'yarn', 'yelp', 'yoast', 'youtube', 'youtube-square', 'zhihu']
        ];
    }


    /**
     * Defines the Tabler Icons.
     *
     * @return array Array of Tabler Icon names
     */
    public static function ti_icons()
    {
        return
        [
            '2fa','3d-cube-sphere','a-b','access-point','access-point-off','accessible','activity','ad','ad-2','adjustments','adjustments-alt','adjustments-horizontal','aerial-lift','affiliate','alarm','alert-circle','alert-octagon','alert-triangle','alien','align-center','align-justified','align-left','align-right','ambulance','anchor','angle','antenna-bars-1','antenna-bars-2','antenna-bars-3','antenna-bars-4','antenna-bars-5','aperture','apple','apps','archive','armchair','arrow-autofit-content','arrow-autofit-down','arrow-autofit-height','arrow-autofit-left','arrow-autofit-right','arrow-autofit-up','arrow-autofit-width','arrow-back','arrow-back-up','arrow-bar-down','arrow-bar-left','arrow-bar-right','arrow-bar-to-down','arrow-bar-to-left','arrow-bar-to-right','arrow-bar-to-up','arrow-bar-up','arrow-big-down','arrow-big-left','arrow-big-right','arrow-big-top','arrow-bottom-bar','arrow-bottom-circle','arrow-bottom-square','arrow-bottom-tail','arrow-down','arrow-down-circle','arrow-down-left','arrow-down-left-circle','arrow-down-right','arrow-down-right-circle','arrow-forward','arrow-forward-up','arrow-left','arrow-left-bar','arrow-left-circle','arrow-left-square','arrow-left-tail','arrow-loop-left','arrow-loop-right','arrow-narrow-down','arrow-narrow-left','arrow-narrow-right','arrow-narrow-up','arrow-ramp-left','arrow-ramp-right','arrow-right','arrow-right-bar','arrow-right-circle','arrow-right-square','arrow-right-tail','arrow-top-bar','arrow-top-circle','arrow-top-square','arrow-top-tail','arrow-up','arrow-up-circle','arrow-up-left','arrow-up-left-circle','arrow-up-right','arrow-up-right-circle','arrow-wave-left-down','arrow-wave-left-up','arrow-wave-right-down','arrow-wave-right-up','arrows-diagonal','arrows-diagonal-2','arrows-diagonal-minimize','arrows-diagonal-minimize-2','arrows-double-ne-sw','arrows-double-nw-se','arrows-double-se-nw','arrows-double-sw-ne','arrows-down','arrows-down-up','arrows-horizontal','arrows-join','arrows-join-2','arrows-left','arrows-left-down','arrows-left-right','arrows-maximize','arrows-minimize','arrows-right','arrows-right-down','arrows-right-left','arrows-sort','arrows-split','arrows-split-2','arrows-up','arrows-up-down','arrows-up-left','arrows-up-right','arrows-vertical','artboard','aspect-ratio','at','atom','atom-2','award','axe','axis-x','axis-y','backhoe','backpack','backspace','ball-american-football','ball-baseball','ball-basketball','ball-bowling','ball-football','ball-football-off','ball-tennis','ball-volleyball','ballon','ban','bandage','barcode','basket','bath','battery','battery-1','battery-2','battery-3','battery-4','battery-automotive','battery-charging','battery-charging-2','battery-eco','battery-off','beach','bed','beer','bell','bell-minus','bell-off','bell-plus','bell-ringing','bell-ringing-2','bell-x','bike','binary','biohazard','blockquote','bluetooth','bluetooth-connected','bluetooth-off','blur','bold','bolt','bolt-off','bone','book','bookmark','bookmark-off','bookmarks','border-all','border-bottom','border-horizontal','border-inner','border-left','border-none','border-outer','border-radius','border-right','border-style','border-style-2','border-top','border-vertical','bottle','box','box-margin','box-model','box-model-2','box-multiple','box-multiple-0','box-multiple-1','box-multiple-2','box-multiple-3','box-multiple-4','box-multiple-5','box-multiple-6','box-multiple-7','box-multiple-8','box-multiple-9','box-padding','braces','brackets','brand-airbnb','brand-airtable','brand-android','brand-angular','brand-apple','brand-apple-arcade','brand-appstore','brand-asana','brand-behance','brand-bing','brand-bitbucket','brand-booking','brand-bootstrap','brand-chrome','brand-codepen','brand-codesandbox','brand-css3','brand-cucumber','brand-debian','brand-deviantart','brand-discord','brand-disqus','brand-docker','brand-doctrine','brand-dribbble','brand-edge','brand-facebook','brand-figma','brand-firebase','brand-firefox','brand-flickr','brand-foursquare','brand-framer','brand-git','brand-github','brand-gitlab','brand-gmail','brand-google','brand-google-analytics','brand-google-drive','brand-google-play','brand-gravatar','brand-hipchat','brand-html5','brand-instagram','brand-javascript','brand-kickstarter','brand-kotlin','brand-linkedin','brand-loom','brand-mastercard','brand-medium','brand-messenger','brand-netbeans','brand-netflix','brand-notion','brand-nytimes','brand-open-source','brand-opera','brand-pagekit','brand-patreon','brand-paypal','brand-php','brand-pinterest','brand-pocket','brand-producthunt','brand-python','brand-react-native','brand-reddit','brand-safari','brand-sass','brand-sentry','brand-shazam','brand-sketch','brand-skype','brand-slack','brand-snapchat','brand-soundcloud','brand-spotify','brand-stackoverflow','brand-steam','brand-stripe','brand-sublime-text','brand-tabler','brand-tailwind','brand-telegram','brand-tidal','brand-tiktok','brand-tinder','brand-tumblr','brand-twitch','brand-twitter','brand-uber','brand-ubuntu','brand-unsplash','brand-vercel','brand-vimeo','brand-visual-studio','brand-vk','brand-whatsapp','brand-windows','brand-yahoo','brand-ycombinator','brand-youtube','bread','briefcase','brightness','brightness-2','brightness-down','brightness-half','brightness-up','browser','brush','bucket','bug','building','building-arch','building-bank','building-bridge','building-bridge-2','building-carousel','building-castle','building-church','building-community','building-cottage','building-factory','building-fortress','building-hospital','building-lighthouse','building-monument','building-pavilon','building-skyscraper','building-store','building-warehouse','bulb','bulb-off','bulldozer','bus','businessplan','calculator','calendar','calendar-event','calendar-minus','calendar-off','calendar-plus','calendar-stats','calendar-time','camera','camera-minus','camera-off','camera-plus','camera-rotate','camera-selfie','candy','capture','car','car-crane','car-crash','caravan','cardboards','caret-down','caret-left','caret-right','caret-up','cash','cash-banknote','cash-banknote-off','cast','ce','certificate','charging-pile','chart-arcs','chart-arcs-3','chart-area','chart-area-line','chart-arrows','chart-arrows-vertical','chart-bar','chart-bubble','chart-candle','chart-circles','chart-donut','chart-donut-2','chart-donut-3','chart-donut-4','chart-dots','chart-infographic','chart-line','chart-pie','chart-pie-2','chart-pie-3','chart-pie-4','chart-radar','check','checkbox','checks','checkup-list','cheese','chevron-down','chevron-down-left','chevron-down-right','chevron-left','chevron-right','chevron-up','chevron-up-left','chevron-up-right','chevrons-down','chevrons-down-left','chevrons-down-right','chevrons-left','chevrons-right','chevrons-up','chevrons-up-left','chevrons-up-right','christmas-tree','circle','circle-0','circle-1','circle-2','circle-3','circle-4','circle-5','circle-6','circle-7','circle-8','circle-9','circle-check','circle-dashed','circle-dotted','circle-half','circle-half-vertical','circle-minus','circle-off','circle-plus','circle-square','circle-x','circles','clear-all','clear-formatting','click','clipboard','clipboard-check','clipboard-list','clipboard-x','clock','cloud','cloud-download','cloud-fog','cloud-off','cloud-rain','cloud-snow','cloud-storm','cloud-upload','code','code-minus','code-plus','coffee','coin','color-picker','color-swatch','column-insert-left','column-insert-right','columns','comet','command','compass','components','confetti','container','contrast','cookie','copy','copyleft','copyright','corner-down-left','corner-down-left-double','corner-down-right','corner-down-right-double','corner-left-down','corner-left-down-double','corner-left-up','corner-left-up-double','corner-right-down','corner-right-down-double','corner-right-up','corner-right-up-double','corner-up-left','corner-up-left-double','corner-up-right','corner-up-right-double','cpu','crane','credit-card','credit-card-off','crop','cross','crosshair','crown','crown-off','crutches','cup','curly-loop','currency','currency-bahraini','currency-bath','currency-bitcoin','currency-cent','currency-dinar','currency-dirham','currency-dogecoin','currency-dollar','currency-dollar-australian','currency-dollar-canadian','currency-dollar-singapore','currency-ethereum','currency-euro','currency-forint','currency-frank','currency-krone-czech','currency-krone-danish','currency-krone-swedish','currency-leu','currency-lira','currency-litecoin','currency-naira','currency-pound','currency-real','currency-renminbi','currency-ripple','currency-riyal','currency-rubel','currency-rupee','currency-shekel','currency-taka','currency-tugrik','currency-won','currency-yen','currency-zloty','current-location','cursor-text','cut','dashboard','database','database-export','database-import','database-off','details','device-analytics','device-audio-tape','device-cctv','device-computer-camera','device-computer-camera-off','device-desktop','device-desktop-analytics','device-desktop-off','device-floppy','device-gamepad','device-laptop','device-mobile','device-mobile-message','device-mobile-rotated','device-mobile-vibration','device-speaker','device-tablet','device-tv','device-watch','device-watch-stats','device-watch-stats-2','devices','devices-2','devices-pc','diamond','dice','dimensions','direction','direction-horizontal','directions','disabled','disabled-2','disc','discount','discount-2','divide','dna','dna-2','dog-bowl','door','door-enter','door-exit','dots','dots-circle-horizontal','dots-diagonal','dots-diagonal-2','dots-vertical','download','drag-drop','drag-drop-2','drone','drone-off','droplet','droplet-filled','droplet-filled-2','droplet-half','droplet-half-2','droplet-off','ear','ear-off','edit','edit-circle','egg','emergency-bed','emphasis','engine','equal','equal-not','eraser','exchange','exposure','external-link','eye','eye-check','eye-off','eye-table','eyeglass','eyeglass-2','face-id','face-id-error','fall','feather','fence','file','file-alert','file-analytics','file-certificate','file-check','file-code','file-code-2','file-diff','file-digit','file-dislike','file-download','file-export','file-horizontal','file-import','file-info','file-invoice','file-like','file-minus','file-music','file-off','file-phone','file-plus','file-report','file-search','file-shredder','file-symlink','file-text','file-upload','file-x','file-zip','files','files-off','filter','filter-off','fingerprint','firetruck','first-aid-kit','fish','flag','flag-2','flag-3','flame','flare','flask','flask-2','flip-horizontal','flip-vertical','float-center','float-left','float-none','float-right','focus','focus-2','fold','fold-down','fold-up','folder','folder-minus','folder-off','folder-plus','folder-x','folders','forbid','forbid-2','forklift','forms','frame','friends','gas-station','gauge','gavel','geometry','ghost','gift','git-branch','git-commit','git-compare','git-fork','git-merge','git-pull-request','git-pull-request-closed','glass','glass-full','glass-off','globe','golf','gps','grain','grid-dots','grill','grip-horizontal','grip-vertical','growth','h-1','h-2','h-3','h-4','h-5','h-6','hammer','hand-click','hand-finger','hand-little-finger','hand-middle-finger','hand-move','hand-off','hand-ring-finger','hand-rock','hand-stop','hand-three-fingers','hand-two-fingers','hanger','hash','haze','heading','headphones','headphones-off','headset','heart','heart-broken','heart-rate-monitor','heartbeat','helicopter','helicopter-landing','help','hexagon','hexagon-off','hierarchy','hierarchy-2','highlight','history','home','home-2','hotel-service','hourglass','ice-cream','ice-cream-2','id','inbox','indent-decrease','indent-increase','infinity','info-circle','info-square','italic','jump-rope','karate','key','keyboard','keyboard-hide','keyboard-off','keyboard-show','lamp','language','language-hiragana','language-katakana','lasso','layers-difference','layers-intersect','layers-linked','layers-subtract','layers-union','layout','layout-2','layout-align-bottom','layout-align-center','layout-align-left','layout-align-middle','layout-align-right','layout-align-top','layout-board','layout-board-split','layout-bottombar','layout-cards','layout-columns','layout-distribute-horizontal','layout-distribute-vertical','layout-grid','layout-grid-add','layout-kanban','layout-list','layout-navbar','layout-rows','layout-sidebar','layout-sidebar-right','leaf','lego','lemon','lemon-2','letter-a','letter-b','letter-c','letter-case','letter-case-lower','letter-case-toggle','letter-case-upper','letter-d','letter-e','letter-f','letter-g','letter-h','letter-i','letter-j','letter-k','letter-l','letter-m','letter-n','letter-o','letter-p','letter-q','letter-r','letter-s','letter-spacing','letter-t','letter-u','letter-v','letter-w','letter-x','letter-y','letter-z','letters-case','license','lifebuoy','line','line-dashed','line-dotted','line-height','link','list','list-check','list-details','list-numbers','list-search','live-photo','live-view','loader','loader-quarter','location','lock','lock-access','lock-off','lock-open','lock-square','login','logout','luggage','lungs','macro','magnet','mail','mail-forward','mail-opened','mailbox','man','manual-gearbox','map','map-2','map-pin','map-pin-off','map-pins','map-search','markdown','marquee','marquee-2','mars','mask','mask-off','massage','math','math-function','math-symbols','maximize','meat','medal','medical-cross','medicine-syrup','menu','menu-2','message','message-2','message-circle','message-circle-2','message-circle-off','message-dots','message-language','message-off','message-plus','message-report','messages','messages-off','microphone','microphone-2','microphone-off','microscope','milk','minimize','minus','minus-vertical','mist','mood-boy','mood-confuzed','mood-crazy-happy','mood-cry','mood-empty','mood-happy','mood-kid','mood-nervous','mood-neutral','mood-sad','mood-smile','mood-suprised','mood-tongue','moon','moon-2','moon-stars','moped','motorbike','mountain','mouse','movie','mug','multiplier-0-5x','multiplier-1-5x','multiplier-1x','multiplier-2x','mushroom','music','new-section','news','nfc','note','notebook','notes','notification','number-0','number-1','number-2','number-3','number-4','number-5','number-6','number-7','number-8','number-9','nurse','octagon','octagon-off','old','olympics','omega','outlet','overline','package','pacman','page-break','paint','palette','panorama-horizontal','panorama-vertical','paperclip','parachute','parentheses','parking','peace','pencil','pennant','pepper','percentage','perspective','phone','phone-call','phone-calling','phone-check','phone-incoming','phone-off','phone-outgoing','phone-pause','phone-plus','phone-x','photo','photo-off','physotherapist','picture-in-picture','picture-in-picture-off','picture-in-picture-on','pig','pill','pills','pin','pinned','pinned-off','pizza','plane','plane-arrival','plane-departure','plane-inflight','planet','plant','plant-2','play-card','player-pause','player-play','player-record','player-skip-back','player-skip-forward','player-stop','player-track-next','player-track-prev','playlist','plug','plus','point','pokeball','polaroid','pool','power','pray','prescription','presentation','presentation-analytics','printer','prison','prompt','propeller','puzzle','puzzle-2','pyramid','qrcode','question-mark','radio','radioactive','radius-bottom-left','radius-bottom-right','radius-top-left','radius-top-right','rainbow','receipt','receipt-2','receipt-off','receipt-refund','receipt-tax','recharging','record-mail','rectangle','rectangle-vertical','recycle','refresh','refresh-alert','registered','relation-many-to-many','relation-one-to-many','relation-one-to-one','repeat','repeat-once','replace','report','report-analytics','report-medical','report-money','report-search','resize','ripple','road-sign','rocket','rotate','rotate-2','rotate-360','rotate-clockwise','rotate-clockwise-2','rotate-rectangle','route','router','row-insert-bottom','row-insert-top','rss','ruler','ruler-2','run','sailboat','salt','satellite','sausage','scale','scale-outline','scan','school','scissors','scooter','scooter-electric','screen-share','screen-share-off','scuba-mask','search','section','seeding','select','selector','send','separator','separator-horizontal','separator-vertical','server','servicemark','settings','settings-automation','shadow','shadow-off','shape','shape-2','shape-3','share','shield','shield-check','shield-checkered','shield-chevron','shield-lock','shield-off','shield-x','ship','shirt','shopping-cart','shopping-cart-discount','shopping-cart-off','shopping-cart-plus','shopping-cart-x','shredder','signature','sitemap','skateboard','sleigh','slice','slideshow','smart-home','smoking','smoking-no','snowflake','soccer-field','social','sock','sofa','sort-ascending','sort-ascending-2','sort-ascending-letters','sort-ascending-numbers','sort-descending','sort-descending-2','sort-descending-letters','sort-descending-numbers','soup','space','spacing-horizontal','spacing-vertical','speakerphone','speedboat','sport-billard','square','square-0','square-1','square-2','square-3','square-4','square-5','square-6','square-7','square-8','square-9','square-check','square-dot','square-forbid','square-forbid-2','square-minus','square-off','square-plus','square-root','square-root-2','square-rotated','square-rotated-off','square-toggle','square-toggle-horizontal','square-x','squares-diagonal','squares-filled','stack','stack-2','stack-3','stairs','stairs-down','stairs-up','star','star-half','star-off','stars','steering-wheel','step-into','step-out','stethoscope','sticker','strikethrough','submarine','subscript','subtask','sum','sun','sun-off','sunrise','sunset','superscript','swimming','switch','switch-2','switch-3','switch-horizontal','switch-vertical','table','table-export','table-import','table-off','tag','tags','tallymark-1','tallymark-2','tallymark-3','tallymark-4','tallymarks','tank','target','temperature','temperature-celsius','temperature-fahrenheit','temperature-minus','temperature-plus','template','tent','terminal','terminal-2','test-pipe','text-direction-ltr','text-direction-rtl','text-resize','text-wrap','text-wrap-disabled','thermometer','thumb-down','thumb-up','ticket','tilt-shift','tir','toggle-left','toggle-right','tool','tools','tools-kitchen','tools-kitchen-2','tornado','tournament','track','tractor','trademark','traffic-cone','traffic-lights','train','transfer-in','transfer-out','trash','trash-off','trash-x','tree','trees','trending-down','trending-down-2','trending-down-3','trending-up','trending-up-2','trending-up-3','triangle','triangle-off','triangle-square-circle','trident','trophy','truck','truck-delivery','truck-off','truck-return','typography','umbrella','underline','unlink','upload','urgent','user','user-check','user-circle','user-exclamation','user-minus','user-off','user-plus','user-search','user-x','users','vaccine','vaccine-bottle','variable','vector','vector-beizer-2','vector-bezier','vector-triangle','venus','versions','video','video-minus','video-off','video-plus','view-360','viewfinder','viewport-narrow','viewport-wide','virus','virus-off','virus-search','vocabulary','volume','volume-2','volume-3','walk','wall','wallet','wallpaper','wand','wave-saw-tool','wave-sine','wave-square','wifi','wifi-0','wifi-1','wifi-2','wifi-off','wind','windmill','window','wiper','wiper-wash','woman','world','world-download','world-latitude','world-longitude','world-upload','wrecking-ball','writing','writing-sign','x','yin-yang','zodiac-aquarius','zodiac-aries','zodiac-cancer','zodiac-capricorn','zodiac-gemini','zodiac-leo','zodiac-libra','zodiac-pisces','zodiac-sagittarius','zodiac-scorpio','zodiac-taurus','zodiac-virgo','zoom-cancel','zoom-check','zoom-in','zoom-money','zoom-out','zoom-question'
        ];
    }
}
