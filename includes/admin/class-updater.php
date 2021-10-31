<?php

defined( 'ABSPATH' ) || exit();


final class Mobile_Contact_Bar_Updater
{

    /**
     * Multidimensional array of custom contatct hooked by the 'mcb_admin_update_contacts' filter.
     * @var array
     */
    public static $used_contacts = array();



    /**
     * Creates new options.
     * Hooks WordPress's admin actions and filters.
     *
     * @since 2.0.0
     */
    public static function plugins_loaded()
    {
        add_action( 'init'                  , array( __CLASS__, 'init' ));
        add_action( 'admin_init'            , array( __CLASS__, 'admin_init' ));
        add_action( 'admin_notices'         , array( __CLASS__, 'admin_notices_plugin_update' ), 20 );
        add_action( 'admin_notices'         , array( __CLASS__, 'admin_notices_plugin_updated_warning' ));
        add_action( 'admin_notices'         , array( __CLASS__, 'admin_notices_plugin_updated_success' ));
        add_action( 'admin_enqueue_scripts' , array( __CLASS__, 'admin_enqueue_scripts' ));
    }



    /**
     * Applies 'mcb_admin_update_contacts' filter.
     *
     * @since 2.0.0
     */
    public static function init()
    {
        $default_bar = Mobile_Contact_Bar_Page::default_bar();
        add_option( MOBILE_CONTACT_BAR__NAME . '_version', MOBILE_CONTACT_BAR__VERSION );
        add_option( MOBILE_CONTACT_BAR__NAME, $default_bar );

        self::$used_contacts = apply_filters( 'mcb_admin_update_contacts', array() );
    }



    /**
     * Renders plugin update notice.
     *
     * @since 2.0.0
     */
    public static function admin_notices_plugin_update()
    {
        $screen = get_current_screen();
        $show_on_screens = array(
            'dashboard',
            'plugins',
            Mobile_Contact_Bar_Page::$page_suffix,
        );

        if( get_option( MOBILE_CONTACT_BAR__NAME . '_update' ) && in_array( $screen->base, $show_on_screens ))
        {
            ?>
            <div class="notice notice-info">
                <p><strong><?php _e( 'Mobile Contact Bar', 'mobile-contact-bar' ); ?></strong> &#8226; <?php _e( 'The plugin would like to migrate your contacts and settings to version 2, and upgrade Font Awesome to version 5.', 'mobile-contact-bar' ); ?></p>
                <p class="submit">
                    <a href="<?php echo esc_url( add_query_arg( 'do_update_mobile_contact_bar', 'true', admin_url( 'options-general.php?page=' . MOBILE_CONTACT_BAR__SLUG ))); ?>" class="mcb-update-now button-primary"><?php _e( 'Run the updater', 'mobile-contact-bar' ); ?></a>
                </p>
            </div>
            <script type="text/javascript">
                jQuery( '.mcb-update-now' ).click( 'click', function() {
                    return window.confirm( '<?php echo esc_js( __( 'It is strongly recommended that you backup your database before proceeding. Are you sure you wish to run the updater now?', 'mobile-contact-bar' )); ?>' );
                });
            </script>
            <?php
        }
    }



    /**
     * Renders success notice after update.
     *
     * @since 2.0.0
     */
    public static function admin_notices_plugin_updated_success()
    {
        if( get_transient( MOBILE_CONTACT_BAR__NAME . '_updated_succes' ))
        {
            ?>
            <div class="updated notice is-dismissible">
                <p><?php _e( 'Settings, contacts, and Font Awesome are updated. Thank you for updating to the latest version!', 'mobile-contact-bar' ); ?></p>
                <p><?php _e( 'The bar rendering method has been changed, please check the "Display on Devices" setting in the "Bar" box.', 'mobile-contact-bar' ); ?></p>
            </div>
            <?php

            self::close_metaboxes();

            delete_transient( MOBILE_CONTACT_BAR__NAME . '_updated_succes' );
            delete_option( MOBILE_CONTACT_BAR__NAME . '_update' );
        }
    }



    /**
     * Renders warning notice on migration needed.
     *
     * @since 2.0.0
     */
    public static function admin_notices_plugin_updated_warning()
    {
        if( get_transient( MOBILE_CONTACT_BAR__NAME . '_updated_warning' ))
        {
            $message_filter = str_replace(
                array( '[hook]', '[file]' ),
                array(
                    '<code>mcb_admin_update_contacts</code>',
                    '<code>functions.php</code>'
                ),
                __( 'We have removed the [hook] filter, so you too can safely remove the unused code from your [file] file.', 'mobile-contact-bar' )
            );
            $used_contacts_count = count( self::$used_contacts );

            $message_icon = str_replace(
                '[icon]',
                '<span style="color:#228ae6;border: 1px solid #ccc;border-radius: 2px;padding:3px;"><i class="fab fa-font-awesome-flag fa-fw" aria-hidden="true"></i></span>',
                _n( 'Please set manually the Font Awesome 5 icon by clicking on the [icon] button.', 'Please set manually the Font Awesome 5 icons by clicking on the [icon] button in the "Contact List" box.', $used_contacts_count, 'mobile-contact-bar')
            );

            ?>
            <div class="notice notice-warning is-dismissible">
                <p>
                    <p><?php echo $message_filter; ?></p>
                    <p>
                        <b><?php printf( _n( 'There is a contact added by you using that filter.', 'There are %d contacts added by you using that filter.', $used_contacts_count, 'mobile-contact-bar' ), $used_contacts_count ); ?></b>
                        <?php printf( $message_icon, $used_contacts_count ); ?></span>
                    </p>
                </p>
            </div>
            <?php

            self::close_metaboxes();

            delete_transient( MOBILE_CONTACT_BAR__NAME . '_updated_warning' );
            delete_option( MOBILE_CONTACT_BAR__NAME . '_update' );
        }
    }



    /**
     * Updates new bar-option with old values.
     * Deletes old bar-option.
     *
     * @since 2.0.0
     */
    public static function admin_init()
    {
        if( isset( $_GET['do_update_mobile_contact_bar'] ))
        {
            $old_bar = get_option( 'mcb_option' );
            $new_bar = get_option( MOBILE_CONTACT_BAR__NAME );

            $old_settings = ( isset( $old_bar['settings'] ) && $old_bar['settings'] ) ? $old_bar['settings'] : null;
            $old_contacts = ( isset( $old_bar['contacts'] ) && $old_bar['contacts'] ) ? $old_bar['contacts'] : array();

            $new_settings = array();
            $new_contacts = array();


            // SETTINGS

            if( $old_settings && isset( $old_bar['styles'] ))
            {
                $new_settings['bar']['device']              = ( $old_settings['bar_max_screen_width'] > 1400 ) ? 'both' : 'mobile';
                $new_settings['bar']['device']              = ( ! $old_settings['bar_is_active'] || ! $old_contacts ) ? 'none' : $new_settings['bar']['device'];
                $new_settings['bar']['is_new_tab']          = ( isset( $old_settings['bar_is_new_tab'] )) ? $old_settings['bar_is_new_tab'] : 0;
                $new_settings['bar']['color']               = $old_settings['bar_color'];
                $new_settings['bar']['opacity']             = $old_settings['bar_opacity'];
                $new_settings['bar']['height']              = $old_settings['bar_height'];
                $new_settings['bar']['width']               = $old_settings['bar_width'];
                $new_settings['bar']['horizontal_position'] = ( isset( $old_settings['bar_horizontal_align'] )) ? $old_settings['bar_horizontal_align'] : 'center';
                $new_settings['bar']['vertical_position']   = $old_settings['bar_position'];
                $new_settings['bar']['is_fixed']            = $old_settings['bar_is_fixed'];
                $new_settings['bar']['space_height']        = $new_bar['settings']['bar']['space_height'];
                $new_settings['bar']['placeholder_height']  = 0;
                $new_settings['bar']['placeholder_color']   = $new_bar['settings']['bar']['placeholder_color'];
                $new_settings['bar']['is_border']           = $new_bar['settings']['bar']['is_border'];
                $new_settings['bar']['border_color']        = $new_bar['settings']['bar']['border_color'];
                $new_settings['bar']['border_width']        = $new_bar['settings']['bar']['border_width'];

                $new_settings['icons']['size']              = $old_settings['icon_size'];
                $new_settings['icons']['color']             = $old_settings['icon_color'];
                $new_settings['icons']['alignment']         = $new_bar['settings']['icons']['alignment'];
                $new_settings['icons']['width']             = $new_bar['settings']['icons']['width'];
                $new_settings['icons']['is_border']         = ! $old_settings['icon_is_border'] ? 'none' : 'around';
                $new_settings['icons']['border_color']      = $old_settings['icon_border_color'];
                $new_settings['icons']['border_width']      = $old_settings['icon_border_width'];

                $new_settings['toggle']['is_render']        = $old_settings['bar_is_toggle'];
                $new_settings['toggle']['is_closed']        = 0;
                $new_settings['toggle']['is_cookie']        = $new_bar['settings']['toggle']['is_cookie'];
                $new_settings['toggle']['shape']            = $new_bar['settings']['toggle']['shape'];
                $new_settings['toggle']['color']            = $old_settings['bar_toggle_color'];
                $new_settings['toggle']['label']            = '';
                $new_settings['toggle']['size']             = $new_bar['settings']['toggle']['size'];
                $new_settings['toggle']['is_animation']     = $new_bar['settings']['toggle']['is_animation'];

                $new_settings['badges']                     = $new_bar['settings']['badges'];
            }
            else
            {
                $new_settings = $new_bar['settings'];
            }


            // CONTACTS

            $manual_update = false;

            // merge old active contacts
            foreach( $old_contacts as $old_id => $old_contact )
            {
                $new_contact = array();

                $new_contact['checked'] = 1;

                $uri = self::build_uri( $old_contact['protocol'], $old_contact['resource'] );
                $new_contact['uri'] = Mobile_Contact_Bar_Validator::sanitize_contact_uri( $uri );

                switch( $old_id )
                {
                    case 'phone':
                        $new_contact['type']        = 'Sample';
                        $new_contact['icon']        = 'fas fa-phone';
                        $new_contact['title']       = 'Phone Number for calling';
                        $new_contact['placeholder'] = 'tel:15417543010 or tel:+15417543010';
                        break;

                    case 'text':
                        $new_contact['type']        = 'Sample';
                        $new_contact['icon']        = 'far fa-comment';
                        $new_contact['title']       = 'Phone Number for texting';
                        $new_contact['placeholder'] = 'sms:15417543010 or sms:+15417543010';
                        $new_contact['parameters']  = array(
                            array(
                                'key'         => 'body',
                                'type'        => 'text',
                                'placeholder' => 'Message ...',
                                'value'       => '',
                            ),
                        );

                        if( isset( $old_contact['parameters'] ) && isset( $old_contact['parameters']['body'] ))
                        {
                            $value = urldecode( $old_contact['parameters']['body'] );
                            $new_contact['parameters'][0]['value'] = Mobile_Contact_Bar_Validator::sanitize_parameter_value( $value, 'text' );
                        }
                        break;

                    case 'email':
                        $new_contact['type']        = 'Sample';
                        $new_contact['icon']        = 'far fa-envelope';
                        $new_contact['title']       = 'Email Address';
                        $new_contact['placeholder'] = 'mailto:username@example.com';
                        $new_contact['parameters']  = array(
                            array(
                                'key'         => 'subject',
                                'type'        => 'text',
                                'placeholder' => 'Subject ...',
                                'value'       => '',
                            ),
                            array(
                                'key'         => 'body',
                                'type'        => 'textarea',
                                'placeholder' => 'Text ...',
                                'value'       => '',
                            ),
                            array(
                                'key'         => 'cc',
                                'type'        => 'email',
                                'placeholder' => 'example@domain.com',
                                'value'       => '',
                            ),
                            array(
                                'key'         => 'bcc',
                                'type'        => 'email',
                                'placeholder' => 'example1@domain.com,example2@domain.net',
                                'value'       => '',
                            ),
                        );

                        foreach( $new_contact['parameters'] as $parameter_id => &$parameter )
                        {
                            $key = $parameter['key'];

                            if( isset( $old_contact['parameters'] ) && isset( $old_contact['parameters'][$key] ))
                            {
                                $value = urldecode( $old_contact['parameters'][$key] );
                                $parameter['value'] = Mobile_Contact_Bar_Validator::sanitize_parameter_value( $value, $parameter['type'] );
                            }
                        }
                        unset( $parameter );
                        break;

                    case 'skype':
                        $new_contact['type']        = 'Sample';
                        $new_contact['icon']        = 'fab fa-skype';
                        $new_contact['title']       = 'Skype for calling';
                        $new_contact['placeholder'] = 'skype:username?call';
                        break;

                    case 'address':
                        $new_contact['type']        = 'Sample';
                        $new_contact['icon']        = 'fas fa-map-marker-alt';
                        $new_contact['title']       = 'Google Maps';
                        $new_contact['placeholder'] = 'https://google.com/maps/place/Dacre+St,+London+UK/';
                        break;

                    case 'facebook':
                        $new_contact['type']        = 'Sample';
                        $new_contact['icon']        = 'fab fa-facebook-f';
                        $new_contact['title']       = 'Facebook';
                        $new_contact['placeholder'] = 'https://www.facebook.com/username';
                        break;

                    case 'twitter':
                        $new_contact['type']        = 'Sample';
                        $new_contact['icon']        = 'fab fa-twitter';
                        $new_contact['title']       = 'Twitter';
                        $new_contact['placeholder'] = 'https://twitter.com/username';
                        break;

                    case 'googleplus':
                        $new_contact['type']        = 'Sample';
                        $new_contact['icon']        = 'fab fa-google-plus-g';
                        $new_contact['title']       = 'Google+';
                        $new_contact['placeholder'] = 'https://plus.google.com/username';
                        break;

                    case 'instagram':
                        $new_contact['type']        = 'Sample';
                        $new_contact['icon']        = 'fab fa-instagram';
                        $new_contact['title']       = 'Instagram';
                        $new_contact['placeholder'] = 'https://www.instagram.com/username';
                        break;

                    case 'youtube':
                        $new_contact['type']        = 'Sample';
                        $new_contact['icon']        = 'fab fa-youtube';
                        $new_contact['title']       = 'YouTube';
                        $new_contact['placeholder'] = 'https://www.youtube.com/user/username';
                        break;

                    case 'pinterest':
                        $new_contact['type']        = 'Sample';
                        $new_contact['icon']        = 'fab fa-pinterest-p';
                        $new_contact['title']       = 'Pinterest';
                        $new_contact['placeholder'] = 'https://www.pinterest.com/username';
                        break;

                    case 'tumblr':
                        $new_contact['type']        = 'Sample';
                        $new_contact['icon']        = 'fab fa-tumblr';
                        $new_contact['title']       = 'Tumblr';
                        $new_contact['placeholder'] = 'https://username.tumblr.com';
                        break;

                    case 'linkedin':
                        $new_contact['type']        = 'Sample';
                        $new_contact['icon']        = 'fab fa-linkedin-in';
                        $new_contact['title']       = 'LinkedIn';
                        $new_contact['placeholder'] = 'https://www.linkedin.com/in/username';
                        break;

                    case 'vimeo':
                        $new_contact['type']        = 'Sample';
                        $new_contact['icon']        = 'fab fa-vimeo-v';
                        $new_contact['title']       = 'Vimeo';
                        $new_contact['placeholder'] = 'https://vimeo.com/username';
                        break;

                    case 'flickr':
                        $new_contact['type']        = 'Sample';
                        $new_contact['icon']        = 'fab fa-flickr';
                        $new_contact['title']       = 'Flickr';
                        $new_contact['placeholder'] = 'https://www.flickr.com/people/username';
                        break;

                    // migrate hooked active old contacts to Custom contacts
                    default:
                        $manual_update = $manual_update || true;

                        $new_contact['checked']     = 0;
                        $new_contact['type']        = 'Custom';
                        $new_contact['icon']        = '';
                        $new_contact['title']       = ( isset( self::$used_contacts[$old_id] ) && isset( self::$used_contacts[$old_id]['title'] )) ? self::$used_contacts[$old_id]['title'] : '';
                        $new_contact['placeholder'] = ( isset( self::$used_contacts[$old_id] ) && isset( self::$used_contacts[$old_id]['placeholder'] )) ? self::$used_contacts[$old_id]['placeholder'] : '';
                        break;
                }

                $new_contacts[] = $new_contact;
            }


            // merge hooked but inactive old contacts
            $old_ids = array_keys( $old_contacts );
            foreach( self::$used_contacts as $id => $contact )
            {
                if( ! in_array( $id, $old_ids ))
                {
                    $new_contact['checked']     = 0;
                    $new_contact['type']        = 'Custom';
                    $new_contact['icon']        = '';
                    $new_contact['title']       = ( isset( self::$used_contacts[$id]['title'] )) ? self::$used_contacts[$id]['title'] : '';
                    $new_contact['placeholder'] = ( isset( self::$used_contacts[$id]['placeholder'] )) ? self::$used_contacts[$id]['placeholder'] : '';
                    $new_contact['uri']         = '';

                    $new_contacts[] = $new_contact;
                }
            }

            // merge new contacts
            foreach( $new_bar['contacts'] as &$contact )
            {
                // reset 'checked'
                $contact['checked'] = 0;
                $new_contacts[]     = $contact;
            }
            unset( $contact );

            $new_bar['settings'] = $new_settings;
            $new_bar['contacts'] = $new_contacts;
            $new_bar['styles']   = Mobile_Contact_Bar_Option::styles( $new_settings, $new_contacts );

            update_option( MOBILE_CONTACT_BAR__NAME, $new_bar );

            delete_option( 'mcb_option' );

            set_transient( MOBILE_CONTACT_BAR__NAME . '_updated_succes', 1 );

            if( $manual_update || self::$used_contacts )
            {
                set_transient( MOBILE_CONTACT_BAR__NAME . '_updated_warning', 1 );
            }

            wp_safe_redirect( remove_query_arg( array( 'do_update_mobile_contact_bar', )));
            exit;
        }
    }



    /**
     * Loads styles and scripts for plugin option page.
     *
     * @since 2.0.0
     *
     * @param string $hook_suffix The specific admin page.
     */
    public static function admin_enqueue_scripts( $hook_suffix )
    {
        if( Mobile_Contact_Bar_Page::$page_suffix == $hook_suffix && self::$used_contacts )
        {
            wp_enqueue_script(
                'mcb-upgrade',
                plugins_url( 'assets/js/upgrade.min.js', MOBILE_CONTACT_BAR__PATH ),
                array( 'jquery', ),
                MOBILE_CONTACT_BAR__VERSION,
                false
            );
        }
    }



    /**
     * Creates new URI.
     *
     * @since 2.0.0
     *
     * @param  string $protocol [description]
     * @param  string $resource [description]
     * @return string           [description]
     */
    private static function build_uri( $protocol, $resource )
    {
        $uri = '';

        switch( $protocol )
        {
            case 'tel':
            case 'sms':
            case 'mailto':
                $uri = $protocol . ':' . $resource;
                break;

            case 'skype':
                $uri = $protocol . ':' . $resource . '?chat';
                break;

            case 'http':
            case 'https':
                $uri = $resource;
                break;
        }

        return $uri;
    }



    /**
     * Closes meta boxes except Contact List.
     *
     * @since 2.0.0
     *
     * @global $wp_settings_sections
     */
    public static function close_metaboxes()
    {
        global $wp_settings_sections;

        $user              = wp_get_current_user();
        $contacts_meta_box = 'mcb-section-contacts';
        $meta_boxes        = array_keys( $wp_settings_sections[MOBILE_CONTACT_BAR__NAME] );

        $meta_boxes = array_flip( $meta_boxes );
        unset( $meta_boxes[$contacts_meta_box] );
        $meta_boxes = array_flip( $meta_boxes );

        update_user_option( $user->ID, 'closedpostboxes_' . Mobile_Contact_Bar_Page::$page_suffix, $meta_boxes, true );
    }
}
