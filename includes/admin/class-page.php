<?php

defined( 'ABSPATH' ) or exit;


final class Mobile_Contact_Bar_Page
{

    /**
     * Option page's hook.
     * @var string
     */
    public static $page = null;



    /**
     * Prevents activating on old versions of PHP or WordPress.
     * Creates the default option (settings, contacts, styles) during the plugin activation.
     *
     * @since 0.1.0
     *
     * @param bool $network_wide Whether to enable the plugin for all sites in the network or just for the current site.
     *
     * @global $wp_version
     * @global $wpdb
     */
    public static function on_activation( $network_wide = false )
    {
        global $wp_version;

        $readme_data = get_file_data( plugin_dir_path( MOBILE_CONTACT_BAR__PATH ) . 'readme.txt',
            array(
                'Requires PHP'      => 'Requires PHP',
                'Requires at least' => 'Requires at least',
            )
        );

        if( version_compare( PHP_VERSION, $readme_data['Requires PHP'], '<' ))
        {
            deactivate_plugins( basename( MOBILE_CONTACT_BAR__PATH ));
            wp_die(
                sprintf( __( 'Mobile Contact Bar requires at least PHP version %s. You are running version %s. Please upgrade and try again.', 'mobile-contact-bar' ), $readme_data['Requires PHP'], PHP_VERSION ),
                'Plugin Activation Error',
                array( 'back_link' => true, )
            );
        }
        elseif( version_compare( $wp_version, $readme_data['Requires at least'], '<' ))
        {
            deactivate_plugins( basename( MOBILE_CONTACT_BAR__PATH ));
            wp_die(
                sprintf( __( 'Mobile Contact Bar requires at least WordPress version %s. You are running version %s. Please upgrade and try again.', 'mobile-contact-bar' ), $readme_data['Requires at least'], $wp_version ),
                'Plugin Activation Error',
                array( 'back_link' => true, )
            );
        }
        else
        {
            $default_option = self::default_option();

            if( $network_wide )
            {
                global $wpdb;

                $blog_ids = $wpdb->get_col( "SELECT blog_id FROM $wpdb->blogs" );

                foreach( $blog_ids as $blog_id )
                {
                    switch_to_blog( $blog_id );

                    self::update_plugin_options( $default_option );

                    restore_current_blog();
                }
            }
            else
            {
                self::update_plugin_options( $default_option );
            }
        }
    }



    /**
     * Hooks WordPress's admin actions and filters.
     *
     * @since 0.1.0
     */
    public static function plugins_loaded()
    {
        $basename = plugin_basename( MOBILE_CONTACT_BAR__PATH );

        load_plugin_textdomain( 'mobile-contact-bar', false, dirname( $basename ) . '/languages' );

        add_action( 'init'                  , array( __CLASS__, 'init' ));
        add_action( 'wpmu_new_blog'         , array( __CLASS__, 'wpmu_new_blog' ));
        add_action( 'admin_menu'            , array( __CLASS__, 'admin_menu' ));
        add_action( 'add_meta_boxes'        , array( __CLASS__, 'add_meta_boxes' ));
        add_action( 'admin_enqueue_scripts' , array( __CLASS__, 'admin_enqueue_scripts' ));

        add_filter( 'plugin_action_links_' . $basename, array( __CLASS__, 'plugin_action_links' ));
    }



    /**
     * Updates plugin version
     * Restores option
     *
     * @since 2.0.1
     */
    public static function init()
    {
        $version = get_option( MOBILE_CONTACT_BAR__NAME . '_version' );

        if( version_compare( $version, MOBILE_CONTACT_BAR__VERSION, '<' ))
        {
            $default_option = self::default_option();
            self::update_plugin_options( $default_option );
        }
    }



    /**
     * Creates default option on blog creation.
     *
     * @since 1.0.0
     *
     * @param int $blog_id Blog ID of the newly created blog.
     */
    public static function wpmu_new_blog( $blog_id )
    {
        add_blog_option( $blog_id, MOBILE_CONTACT_BAR__NAME . '_version', MOBILE_CONTACT_BAR__VERSION );
        add_blog_option( $blog_id, MOBILE_CONTACT_BAR__NAME, self::default_option() );
    }



    /**
     * Adds 'Settings' link to the plugins overview page.
     *
     * @since 0.1.0
     *
     * @param  array $links Associative array of links.
     * @return array        Updated links.
     */
    public static function plugin_action_links( $links )
    {
        return array_merge(
            $links,
            array( 'settings' => '<a href="' . admin_url( 'options-general.php?page=' . MOBILE_CONTACT_BAR__SLUG ) . '">' . esc_html__( 'Settings' ) . '</a>' )
        );
    }



    /**
     * Adds option page to the admin menu.
     * Hooks the option page related screen tabs.
     *
     * @since 0.1.0
     */
    public static function admin_menu()
    {
        self::$page = add_options_page(
            __( 'Mobile Contact Bar', 'mobile-contact-bar' ),
            __( 'Mobile Contact Bar', 'mobile-contact-bar' ),
            'manage_options',
            MOBILE_CONTACT_BAR__SLUG,
            array( __CLASS__, 'callback_render_page' )
        );

        add_action( 'load-' . self::$page, array( __CLASS__, 'load_screen_options' ));
        add_action( 'load-' . self::$page, array( __CLASS__, 'load_help' ));
    }



    /**
     * Renders the option page skeleton.
     *
     * @since 0.1.0
     */
    public static function callback_render_page()
    {
        ?>
        <div class="wrap">
            <h2><?php esc_html_e( 'Mobile Contact Bar', 'mobile-contact-bar' ); ?></h2>

            <form id="mcb-form" action="options.php" method="post">
                <?php
                settings_fields( MOBILE_CONTACT_BAR__NAME . '_group' );
                wp_nonce_field( 'closedpostboxes', 'closedpostboxesnonce', false );
                wp_nonce_field( 'meta-box-order', 'meta-box-order-nonce', false );
                ?>

                <div id="poststuff">
                    <div id="post-body" class="metabox-holder columns-<?php echo ( 1 == get_current_screen()->get_columns() ) ? '1' : '2'; ?>">

                        <div id="postbox-container-2" class="postbox-container">
                            <?php do_meta_boxes( self::$page, 'advanced', null ); ?>
                        </div><!-- #postbox-container-2 -->

                        <div id="postbox-container-1" class="postbox-container">
                            <?php do_meta_boxes( self::$page, 'side', null ); ?>
                        </div><!-- #postbox-container-1 -->

                        <div id="post-body-content">
                            <?php submit_button(); ?>
                        </div>

                    </div><!-- #post-body -->
                    <br class="clear">

                </div><!-- #poststuff -->
                <br class="clear">

            </form><!-- #mcb-form -->
            <div class="clear"></div>

        </div>
        <div class="clear"></div>
        <?php
    }



    /**
     * Triggers the 'add_meta_boxes' hooks.
     * Adds screen options.
     *
     * @since 2.0.0
     */
    public static function load_screen_options()
    {
        //do_action( 'add_meta_boxes_' . self::$page, null );
        do_action( 'add_meta_boxes', self::$page, null );
        add_screen_option( 'layout_columns', array( 'max' => 2, 'default' => 2 ));
    }



    /**
     * Adds contextual help menu.
     *
     * @since 2.0.0
     */
    public static function load_help()
    {
        $screen = get_current_screen();

        $tabs = array(
            array(
                'title'    => __( 'Phone calls', 'mobile-contact-bar' ),
                'id'       => 'mcb-tel',
                'callback' => array( __CLASS__, 'render_help_tab_tel' ),
            ),
            array(
                'title'    => __( 'Mobile texts', 'mobile-contact-bar' ),
                'id'       => 'mcb-sms',
                'callback' => array( __CLASS__, 'render_help_tab_sms' ),
            ),
            array(
                'title'    => __( 'Emails', 'mobile-contact-bar' ),
                'id'       => 'mcb-mailto',
                'callback' => array( __CLASS__, 'render_help_tab_mailto' ),
            ),
            array(
                'title'    => __( 'Links', 'mobile-contact-bar' ),
                'id'       => 'mcb-http',
                'callback' => array( __CLASS__, 'render_help_tab_http' ),
            ),
            array(
                'title'    => __( 'Skype', 'mobile-contact-bar' ),
                'id'       => 'mcb-skype',
                'callback' => array( __CLASS__, 'render_help_tab_skype' ),
            ),
        );

        foreach( $tabs as $tab )
        {
            $screen->add_help_tab( $tab );
        }

        $screen->set_help_sidebar( self::output_help_sidebar() );
    }



    /**
     * Adds meta boxes to the option page.
     * Adjusts meta box classes.
     *
     * @since 1.2.0
     *
     * @global $wp_settings_sections
     */
    public static function add_meta_boxes()
    {
        global $wp_settings_sections;

        add_meta_box(
            'mcb-section-model',
            __( 'Real-time Model <sup>*</sup>', 'mobile-contact-bar' ),
            array( __CLASS__, 'callback_render_model' ),
            self::$page,
            'side',
            'default'
        );

        foreach( $wp_settings_sections[MOBILE_CONTACT_BAR__NAME] as $section )
        {
            add_meta_box(
                $section['id'],
                $section['title'],
                array( 'Mobile_Contact_Bar_Option', 'callback_render_section' ),
                self::$page,
                'advanced',
                'default'
            );

            // add 'mcb-settings' class to meta boxes except Contact List
            if( 'mcb-section-contacts' != $section['id'] )
            {
                add_filter( 'postbox_classes_' . self::$page . '_' . $section['id'], array( __CLASS__, 'metabox_classes_mcb_settings' ));
            }
        }

        $user = wp_get_current_user();
        $closed_meta_boxes = get_user_option( 'closedpostboxes_' . self::$page, $user->ID );

        // close meta boxes for the first time
        if( ! $closed_meta_boxes )
        {
            $meta_boxes = array_keys( $wp_settings_sections[MOBILE_CONTACT_BAR__NAME] );
            update_user_option( $user->ID, 'closedpostboxes_' . self::$page, $meta_boxes, true );
        }
    }



    /**
     * Adds classes to meta boxes.
     *
     * @since 2.0.0
     *
     * @param  array $classes Array of classes.
     * @return array          Updated array of classes.
     */
    public static function metabox_classes_close( $classes )
    {
        $classes[] = 'closed';
        return $classes;
    }



    /**
     * Adds classes to meta boxes.
     *
     * @since 2.0.0
     *
     * @param  array $classes Array of classes.
     * @return array          Updated array of classes.
     */
    public static function metabox_classes_mcb_settings( $classes )
    {
        $classes[] = 'mcb-settings';
        return $classes;
    }



    /**
     * Renders Real-time Model and Plugin Info meta box
     *
     * @since 2.0.0
     */
    public static function callback_render_model()
    {
        $plugin_data = get_file_data( MOBILE_CONTACT_BAR__PATH,
            array(
                'Description' => 'Description',
                'Plugin URI'  => 'Plugin URI',
                'Author URI'  => 'Author URI',
            )
        );

        ?>
        <div id="mcb-model">
            <?php include_once plugin_dir_path( MOBILE_CONTACT_BAR__PATH ) . 'assets/images/settings/real-time-model/model.svg'; ?>
            <footer><em><sup>*</sup> <?php _e( 'The model is an approximation. A lot depends on your active theme"s styles.', 'mobile-contact-bar' ); ?></em></footer>
        </div>

        <div id="mcb-about">
            <h2><?php _e( 'Mobile Contact Bar', 'mobile-contact-bar' ); ?> <?php echo MOBILE_CONTACT_BAR__VERSION; ?></h2>
            <p><?php _e( $plugin_data['Description'], 'mobile-contact-bar' ); ?></p>
            <ul>
                <li><a href="<?php echo esc_url( $plugin_data['Plugin URI'] . '#developers' ); ?>" target="_blank"><?php _e( 'Changelog', 'mobile-contact-bar' ); ?></a></li>
                <li><a href="<?php echo esc_url( 'https://wordpress.org/support/plugin/mobile-contact-bar' ); ?>" target="_blank"><?php _e( 'Forum', 'mobile-contact-bar' ); ?></a></li>
                <li><a href="<?php echo esc_url( 'https://wordpress.org/support/plugin/mobile-contact-bar' ); ?>" target="_blank"><?php _e( 'Requests', 'mobile-contact-bar' ); ?></a></li>
            </ul>
            <footer>
                <?php printf( __( 'Thank you for networking with <a href="%s">MCB</a>.', 'mobile-contact-bar' ), esc_url( $plugin_data['Plugin URI'] )); ?>
            </footer>
        </div>
        <?php
    }



    /**
     * Loads styles and scripts for plugin option page.
     *
     * @since 0.1.0
     *
     * @param string $hook The specific admin page.
     */
    public static function admin_enqueue_scripts( $hook )
    {
        if( self::$page == $hook )
        {
            // WordPress's postboxes logic
            wp_enqueue_script( 'postbox' );

            // WordPress's color picker styles and scripts
            wp_enqueue_style(  'wp-color-picker' );
            wp_enqueue_script( 'wp-color-picker' );

            wp_enqueue_style(
                'mcb-admin',
                plugins_url( 'assets/css/admin.min.css', MOBILE_CONTACT_BAR__PATH ),
                array( 'wp-color-picker', ),
                MOBILE_CONTACT_BAR__VERSION,
                'all'
            );

            wp_enqueue_script(
                'mcb-admin',
                plugins_url( 'assets/js/admin.min.js', MOBILE_CONTACT_BAR__PATH ),
                array( 'jquery', 'jquery-ui-slider', 'jquery-ui-sortable', 'postbox', 'wp-color-picker', ),
                MOBILE_CONTACT_BAR__VERSION,
                false
            );

            wp_localize_script(
                'mcb-admin',
                MOBILE_CONTACT_BAR__NAME,
                array(
                    'nonce' => wp_create_nonce( MOBILE_CONTACT_BAR__NAME ),
                )
            );
        }
    }



    /**
     * Renders 'tel' help tab
     *
     * @since 2.0.0
     */
    public static function render_help_tab_tel()
    {
        ?>
        <h4><?php _e( 'Initiating phone or mobile audio calls', 'mobile-contact-bar' ); ?></h4>
        <code>tel:+1-541-754-3010</code> <?php _e( 'or', 'mobile-contact-bar' ); ?> <code>tel:+15417543010</code>
        <p><?php _e( 'Use the international dialing format: the plus sign (<code>+</code>), country code, area code, and number. You can separate each segment of the number with a hyphen (<code>-</code>) for easier reading.', 'mobile-contact-bar' ); ?></p>
        <p class="mcb-tab-status-green"><?php _e( 'Standardised protocol', 'mobile-contact-bar' ); ?></p>
        <?php
    }



    /**
     * Renders 'sms' help tab
     *
     * @since 2.0.0
     */
    public static function render_help_tab_sms()
    {
        ?>
        <h4><?php _e( 'Sending text messages to mobile phones', 'mobile-contact-bar' ); ?></h4>
        <code>sms:+1-541-754-3010</code> <?php _e( 'or', 'mobile-contact-bar' ); ?> <code>sms:+15417543010</code>
        <p><?php _e( 'Use the international dialing format: the plus sign (<code>+</code>), country code, area code, and number. You can separate each segment of the number with a hyphen (<code>-</code>) for easier reading.', 'mobile-contact-bar' ); ?></p>
        <p><?php _e( 'Optional query parameter:', 'mobile-contact-bar' ); ?></p>
        <ul class="mcb-query-parameters">
            <li>
                <span class="mcb-query-parameter-key">body</span>
                <span><?php _e( 'Text to appear in the body of the message (it does not always work).', 'mobile-contact-bar' ); ?></span>
            </li>
        </ul>
        <p class="mcb-tab-status-yellow"><?php _e( 'Inconsistent protocol', 'mobile-contact-bar' ); ?></p>
        <?php
    }



    /**
     * Renders 'mailto' help tab
     *
     * @since 2.0.0
     */
    public static function render_help_tab_mailto()
    {
        ?>
        <h4><?php _e( 'Sending emails to email addresses', 'mobile-contact-bar' ); ?></h4>
        <code>mailto:someone@domain.com</code>
        <p><?php _e( 'Optional query parameters:', 'mobile-contact-bar' ); ?></p>
        <ul class="mcb-query-parameters">
            <li>
                <span class="mcb-query-parameter-key">subject</span>
                <span><?php _e( 'Text to appear in the subject line of the message.', 'mobile-contact-bar' ); ?></span>
            </li>
            <li>
                <span class="mcb-query-parameter-key">body</span>
                <span><?php _e( 'Text to appear in the body of the message.', 'mobile-contact-bar' ); ?></span>
            </li>
            <li>
                <span class="mcb-query-parameter-key">cc</span>
                <span><?php _e( 'Addresses to be included in the carbon copy section of the message. Separate addresses with commas.', 'mobile-contact-bar' ); ?></span>
            </li>
            <li>
                <span class="mcb-query-parameter-key">bcc</span>
                <span><?php _e( 'Addresses to be included in the blind carbon copy section of the message. Separate addresses with commas.', 'mobile-contact-bar' ); ?></span>
            </li>
        </ul>
        <p class="mcb-tab-status-green"><?php _e( 'Standardised protocol', 'mobile-contact-bar' ); ?></p>
        <?php
    }



    /**
     * Renders 'http' help tab
     *
     * @since 2.0.0
     */
    public static function render_help_tab_http()
    {
        ?>
        <h4><?php _e( 'Linking to web pages on your or others websites', 'mobile-contact-bar' ); ?></h4>
        <code>http://domain.com</code> <?php _e( 'or', 'mobile-contact-bar' ); ?> <code>http://domain.com/path/to/page</code>
        <p><?php _e( 'For secure websites using SSL to encrypt data and authenticate the website use the <code>https</code> protocol:', 'mobile-contact-bar' ); ?></p>
        <code>https://domain.com</code> <?php _e( 'or', 'mobile-contact-bar' ); ?> <code>https://domain.com/path/to/page</code>
        <p><?php _e( 'You can append query parameters to URLs using the', 'mobile-contact-bar' ); ?> <span class="mcb-tab-button button"><i class="fas fa-plus fa-sm" aria-hidden="true"></i></span> <?php _e( 'button', 'mobile-contact-bar' ); ?></p>
        <p class="mcb-tab-status-green"><?php _e( 'Standardised protocol', 'mobile-contact-bar' ); ?></p>
        <?php
    }



    /**
     * Renders 'skype' help tab
     *
     * @since 2.0.0
     */
    public static function render_help_tab_skype()
    {
        ?>
        <h4><?php _e( 'Sending instant messages to other Skype users, phones, or mobiles', 'mobile-contact-bar' ); ?></h4>
        <code>skype:username?chat</code> <?php _e( 'or', 'mobile-contact-bar' ); ?> <code>skype:+phone-number?chat</code>
        <h4><?php _e( 'Initiating audio calls to other Skype users, phones, or mobiles', 'mobile-contact-bar' ); ?></h4>
        <code>skype:username?call</code> <?php _e( 'or', 'mobile-contact-bar' ); ?> <code>skype:+phone-number?call</code>
        <p class="mcb-tab-status-yellow"><?php _e( 'Inconsistent protocol', 'mobile-contact-bar' ); ?></p>
        <?php
    }



    /**
     * Outputs help sidebar
     *
     * @since 2.0.0
     */
    public static function output_help_sidebar()
    {
        $out  = '';
        $out .= '<h4>' . __( 'More info', 'mobile-contact-bar' ) . '</h4>';
        $out .= '<p><a href="'. esc_url( 'https://en.wikipedia.org/wiki/Uniform_Resource_Identifier' ) . '" target="_blank">' . __( 'Uniform Resource Identifier', 'mobile-contact-bar' ) . '</a></p>';

        return $out;
    }



    /**
     * Checks whether an icon exists or not.
     *
     * @since 2.0.0
     *
     * @param  string $classes Font Awesome icon classes.
     * @return bool            Whether the icon exists or not.
     */
    public static function in_icons( $classes )
    {
        $class_list = explode( ' ', $classes );
        $name       = substr( $class_list[1], 3 );
        $icons      = self::icons();

        foreach( $icons as $id => $section )
        {
            if( $class_list[0] == $id )
            {
                foreach( $section as $icon )
                {
                    if( $name == $icon )
                    {
                        return true;
                    }
                }
            }
        }
        return false;
    }



    /**
     * Defines the multidimensional array of icons divided into sections: 'fas', 'far', 'fab'.
     *
     * @since 2.0.0
     *
     * @return array Array of Fontawesome Icon names.
     */
    public static function icons()
    {
       return array(
           'fas' => array('address-book','address-card','adjust','align-center','align-justify','align-left','align-right','allergies','ambulance','american-sign-language-interpreting','anchor','angle-double-down','angle-double-left','angle-double-right','angle-double-up','angle-down','angle-left','angle-right','angle-up','archive','arrow-alt-circle-down','arrow-alt-circle-left','arrow-alt-circle-right','arrow-alt-circle-up','arrow-circle-down','arrow-circle-left','arrow-circle-right','arrow-circle-up','arrow-down','arrow-left','arrow-right','arrow-up','arrows-alt','arrows-alt-h','arrows-alt-v','assistive-listening-systems','asterisk','at','audio-description','backward','balance-scale','ban','band-aid','barcode','bars','baseball-ball','basketball-ball','bath','battery-empty','battery-full','battery-half','battery-quarter','battery-three-quarters','bed','beer','bell','bell-slash','bicycle','binoculars','birthday-cake','blind','bold','bolt','bomb','book','bookmark','bowling-ball','box','box-open','boxes','braille','briefcase','briefcase-medical','bug','building','bullhorn','bullseye','burn','bus','calculator','calendar','calendar-alt','calendar-check','calendar-minus','calendar-plus','calendar-times','camera','camera-retro','capsules','car','caret-down','caret-left','caret-right','caret-square-down','caret-square-left','caret-square-right','caret-square-up','caret-up','cart-arrow-down','cart-plus','certificate','chart-area','chart-bar','chart-line','chart-pie','check','check-circle','check-square','chess','chess-bishop','chess-board','chess-king','chess-knight','chess-pawn','chess-queen','chess-rook','chevron-circle-down','chevron-circle-left','chevron-circle-right','chevron-circle-up','chevron-down','chevron-left','chevron-right','chevron-up','child','circle','circle-notch','clipboard','clipboard-check','clipboard-list','clock','clone','closed-captioning','cloud','cloud-download-alt','cloud-upload-alt','code','code-branch','coffee','cog','cogs','columns','comment','comment-alt','comment-dots','comment-slash','comments','compass','compress','copy','copyright','couch','credit-card','crop','crosshairs','cube','cubes','cut','database','deaf','desktop','diagnoses','dna','dollar-sign','dolly','dolly-flatbed','donate','dot-circle','dove','download','edit','eject','ellipsis-h','ellipsis-v','envelope','envelope-open','envelope-square','eraser','euro-sign','exchange-alt','exclamation','exclamation-circle','exclamation-triangle','expand','expand-arrows-alt','external-link-alt','external-link-square-alt','eye','eye-dropper','eye-slash','fast-backward','fast-forward','fax','female','fighter-jet','file','file-alt','file-archive','file-audio','file-code','file-excel','file-image','file-medical','file-medical-alt','file-pdf','file-powerpoint','file-video','file-word','film','filter','fire','fire-extinguisher','first-aid','flag','flag-checkered','flask','folder','folder-open','font','font-awesome-logo-full','football-ball','forward','frown','futbol','gamepad','gavel','gem','genderless','gift','glass-martini','globe','golf-ball','graduation-cap','h-square','hand-holding','hand-holding-heart','hand-holding-usd','hand-lizard','hand-paper','hand-peace','hand-point-down','hand-point-left','hand-point-right','hand-point-up','hand-pointer','hand-rock','hand-scissors','hand-spock','hands','hands-helping','handshake','hashtag','hdd','heading','headphones','heart','heartbeat','history','hockey-puck','home','hospital','hospital-alt','hospital-symbol','hourglass','hourglass-end','hourglass-half','hourglass-start','i-cursor','id-badge','id-card','id-card-alt','image','images','inbox','indent','industry','info','info-circle','italic','key','keyboard','language','laptop','leaf','lemon','level-down-alt','level-up-alt','life-ring','lightbulb','link','lira-sign','list','list-alt','list-ol','list-ul','location-arrow','lock','lock-open','long-arrow-alt-down','long-arrow-alt-left','long-arrow-alt-right','long-arrow-alt-up','low-vision','magic','magnet','male','map','map-marker','map-marker-alt','map-pin','map-signs','mars','mars-double','mars-stroke','mars-stroke-h','mars-stroke-v','medkit','meh','mercury','microchip','microphone','microphone-slash','minus','minus-circle','minus-square','mobile','mobile-alt','money-bill-alt','moon','motorcycle','mouse-pointer','music','neuter','newspaper','notes-medical','object-group','object-ungroup','outdent','paint-brush','pallet','paper-plane','paperclip','parachute-box','paragraph','paste','pause','pause-circle','paw','pen-square','pencil-alt','people-carry','percent','phone','phone-slash','phone-square','phone-volume','piggy-bank','pills','plane','play','play-circle','plug','plus','plus-circle','plus-square','podcast','poo','portrait','pound-sign','power-off','prescription-bottle','prescription-bottle-alt','print','procedures','puzzle-piece','qrcode','question','question-circle','quidditch','quote-left','quote-right','random','recycle','redo','redo-alt','registered','reply','reply-all','retweet','ribbon','road','rocket','rss','rss-square','ruble-sign','rupee-sign','save','search','search-minus','search-plus','seedling','server','share','share-alt','share-alt-square','share-square','shekel-sign','shield-alt','ship','shipping-fast','shopping-bag','shopping-basket','shopping-cart','shower','sign','sign-in-alt','sign-language','sign-out-alt','signal','sitemap','sliders-h','smile','smoking','snowflake','sort','sort-alpha-down','sort-alpha-up','sort-amount-down','sort-amount-up','sort-down','sort-numeric-down','sort-numeric-up','sort-up','space-shuttle','spinner','square','square-full','star','star-half','step-backward','step-forward','stethoscope','sticky-note','stop','stop-circle','stopwatch','street-view','strikethrough','subscript','subway','suitcase','sun','superscript','sync','sync-alt','syringe','table','table-tennis','tablet','tablet-alt','tablets','tachometer-alt','tag','tags','tape','tasks','taxi','terminal','text-height','text-width','th','th-large','th-list','thermometer','thermometer-empty','thermometer-full','thermometer-half','thermometer-quarter','thermometer-three-quarters','thumbs-down','thumbs-up','thumbtack','ticket-alt','times','times-circle','tint','toggle-off','toggle-on','trademark','train','transgender','transgender-alt','trash','trash-alt','tree','trophy','truck','truck-loading','truck-moving','tty','tv','umbrella','underline','undo','undo-alt','universal-access','university','unlink','unlock','unlock-alt','upload','user','user-alt','user-alt-slash','user-astronaut','user-check','user-circle','user-clock','user-cog','user-edit','user-friends','user-graduate','user-lock','user-md','user-minus','user-ninja','user-plus','user-secret','user-shield','user-slash','user-tag','user-tie','user-times','users','users-cog','utensil-spoon','utensils','venus','venus-double','venus-mars','vial','vials','video','video-slash','volleyball-ball','volume-down','volume-off','volume-up','warehouse','weight','wheelchair','wifi','window-close','window-maximize','window-minimize','window-restore','wine-glass','won-sign','wrench','x-ray','yen-sign'),
           'far' => array('address-book','address-card','arrow-alt-circle-down','arrow-alt-circle-left','arrow-alt-circle-right','arrow-alt-circle-up','bell','bell-slash','bookmark','building','calendar','calendar-alt','calendar-check','calendar-minus','calendar-plus','calendar-times','caret-square-down','caret-square-left','caret-square-right','caret-square-up','chart-bar','check-circle','check-square','circle','clipboard','clock','clone','closed-captioning','comment','comment-alt','comment-dots','comments','compass','copy','copyright','credit-card','dot-circle','edit','envelope','envelope-open','eye','eye-slash','file','file-alt','file-archive','file-audio','file-code','file-excel','file-image','file-pdf','file-powerpoint','file-video','file-word','flag','folder','folder-open','font-awesome-logo-full','frown','futbol','gem','hand-lizard','hand-paper','hand-peace','hand-point-down','hand-point-left','hand-point-right','hand-point-up','hand-pointer','hand-rock','hand-scissors','hand-spock','handshake','hdd','heart','hospital','hourglass','id-badge','id-card','image','images','keyboard','lemon','life-ring','lightbulb','list-alt','map','meh','minus-square','money-bill-alt','moon','newspaper','object-group','object-ungroup','paper-plane','pause-circle','play-circle','plus-square','question-circle','registered','save','share-square','smile','snowflake','square','star','star-half','sticky-note','stop-circle','sun','thumbs-down','thumbs-up','times-circle','trash-alt','user','user-circle','window-close','window-maximize','window-minimize','window-restore'),
           'fab' => array('500px','accessible-icon','accusoft','adn','adversal','affiliatetheme','algolia','amazon','amazon-pay','amilia','android','angellist','angrycreative','angular','app-store','app-store-ios','apper','apple','apple-pay','asymmetrik','audible','autoprefixer','avianex','aviato','aws','bandcamp','behance','behance-square','bimobject','bitbucket','bitcoin','bity','black-tie','blackberry','blogger','blogger-b','bluetooth','bluetooth-b','btc','buromobelexperte','buysellads','cc-amazon-pay','cc-amex','cc-apple-pay','cc-diners-club','cc-discover','cc-jcb','cc-mastercard','cc-paypal','cc-stripe','cc-visa','centercode','chrome','cloudscale','cloudsmith','cloudversify','codepen','codiepie','connectdevelop','contao','cpanel','creative-commons','creative-commons-by','creative-commons-nc','creative-commons-nc-eu','creative-commons-nc-jp','creative-commons-nd','creative-commons-pd','creative-commons-pd-alt','creative-commons-remix','creative-commons-sa','creative-commons-sampling','creative-commons-sampling-plus','creative-commons-share','css3','css3-alt','cuttlefish','d-and-d','dashcube','delicious','deploydog','deskpro','deviantart','digg','digital-ocean','discord','discourse','dochub','docker','draft2digital','dribbble','dribbble-square','dropbox','drupal','dyalog','earlybirds','ebay','edge','elementor','ember','empire','envira','erlang','ethereum','etsy','expeditedssl','facebook','facebook-f','facebook-messenger','facebook-square','firefox','first-order','first-order-alt','firstdraft','flickr','flipboard','fly','font-awesome','font-awesome-alt','font-awesome-flag','font-awesome-logo-full','fonticons','fonticons-fi','fort-awesome','fort-awesome-alt','forumbee','foursquare','free-code-camp','freebsd','fulcrum','galactic-republic','galactic-senate','get-pocket','gg','gg-circle','git','git-square','github','github-alt','github-square','gitkraken','gitlab','gitter','glide','glide-g','gofore','goodreads','goodreads-g','google','google-drive','google-play','google-plus','google-plus-g','google-plus-square','google-wallet','gratipay','grav','gripfire','grunt','gulp','hacker-news','hacker-news-square','hips','hire-a-helper','hooli','hotjar','houzz','html5','hubspot','imdb','instagram','internet-explorer','ioxhost','itunes','itunes-note','java','jedi-order','jenkins','joget','joomla','js','js-square','jsfiddle','keybase','keycdn','kickstarter','kickstarter-k','korvue','laravel','lastfm','lastfm-square','leanpub','less','line','linkedin','linkedin-in','linode','linux','lyft','magento','mandalorian','mastodon','maxcdn','medapps','medium','medium-m','medrt','meetup','microsoft','mix','mixcloud','mizuni','modx','monero','napster','nintendo-switch','node','node-js','npm','ns8','nutritionix','odnoklassniki','odnoklassniki-square','old-republic','opencart','openid','opera','optin-monster','osi','page4','pagelines','palfed','patreon','paypal','periscope','phabricator','phoenix-framework','phoenix-squadron','php','pied-piper','pied-piper-alt','pied-piper-hat','pied-piper-pp','pinterest','pinterest-p','pinterest-square','playstation','product-hunt','pushed','python','qq','quinscape','quora','r-project','ravelry','react','readme','rebel','red-river','reddit','reddit-alien','reddit-square','rendact','renren','replyd','researchgate','resolving','rocketchat','rockrms','safari','sass','schlix','scribd','searchengin','sellcast','sellsy','servicestack','shirtsinbulk','simplybuilt','sistrix','sith','skyatlas','skype','slack','slack-hash','slideshare','snapchat','snapchat-ghost','snapchat-square','soundcloud','speakap','spotify','stack-exchange','stack-overflow','staylinked','steam','steam-square','steam-symbol','sticker-mule','strava','stripe','stripe-s','studiovinari','stumbleupon','stumbleupon-circle','superpowers','supple','teamspeak','telegram','telegram-plane','tencent-weibo','themeisle','trade-federation','trello','tripadvisor','tumblr','tumblr-square','twitch','twitter','twitter-square','typo3','uber','uikit','uniregistry','untappd','usb','ussunnah','vaadin','viacoin','viadeo','viadeo-square','viber','vimeo','vimeo-square','vimeo-v','vine','vk','vnv','vuejs','weibo','weixin','whatsapp','whatsapp-square','whmcs','wikipedia-w','windows','wolf-pack-battalion','wordpress','wordpress-simple','wpbeginner','wpexplorer','wpforms','xbox','xing','xing-square','y-combinator','yahoo','yandex','yandex-international','yelp','yoast','youtube','youtube-square')
       );
    }



    /**
     * Returns the default option.
     *
     * @since 1.0.0
     *
     * @return array Option initialized with version number, default settings, and contacts.
     */
    public static function default_option()
    {
       $option = array();

       $option['settings'] = Mobile_Contact_Bar_Settings::get_defaults();
       $option['contacts'] = Mobile_Contact_Bar_Contact_Sample::mcb_admin_add_contact();
       $option/* styles */ = Mobile_Contact_Bar_Option::pre_update_option( $option );

       return array(
           'settings' => $option['settings'],
           'contacts' => $option['contacts'],
           'styles'   => $option['styles'],
       );
    }



    /**
     * Updates version, repairs or creates plugin option.
     *
     * @since 2.0.0
     *
     * @param array $default_option Default option.
     */
    private static function update_plugin_options( $default_option )
    {
        $option = get_option( MOBILE_CONTACT_BAR__NAME );

        if( $option )
        {
            $damaged = false;

            // repair 'settings'
            foreach( $default_option['settings'] as $section_id => $section )
            {
                foreach( $section as $setting_id => $setting )
                {
                    if( ! isset( $option['settings'][$section_id][$setting_id] ))
                    {
                        $option['settings'][$section_id][$setting_id] = $setting;
                        $damaged = true;
                    }
                }
            }

            // repair 'styles'
            if( ! isset( $option['styles'] ) || ! $option['styles'] || $damaged )
            {
                $option = Mobile_Contact_Bar_Option::pre_update_option( $option );
            }
            update_option( MOBILE_CONTACT_BAR__NAME, $option );
        }
        else
        {
            add_option( MOBILE_CONTACT_BAR__NAME, $default_option );
        }

        update_option( MOBILE_CONTACT_BAR__NAME . '_version', MOBILE_CONTACT_BAR__VERSION );
    }

}
