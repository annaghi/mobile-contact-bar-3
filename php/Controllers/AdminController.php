<?php

namespace MobileContactBar\Controllers;

use MobileContactBar\Options;
use MobileContactBar\Settings;
use MobileContactBar\Contacts;
use MobileContactBar\Styles;


final class AdminController
{
    /**
     * Multidimensional array of the plugin's option, divided into sections: 'settings', 'contacts', 'styles'.
     *
     * @var array
     */
    public $option_bar = [];

 
    /**
     * Adds option page to the admin menu.
     * Hooks the option page related screen tabs.
     * 
     * @return void
     */
    public function admin_menu()
    {
        add_options_page(
            __( 'Mobile Contact Bar', 'mobile-contact-bar' ),
            __( 'Mobile Contact Bar', 'mobile-contact-bar' ),
            abmcb()->capability,
            abmcb()->slug,
            [$this, 'callback_render_page']
        );

        add_action( 'load-' . abmcb()->page_suffix, [$this, 'load_screen_options'] );
        add_action( 'load-' . abmcb()->page_suffix, [$this, 'load_help'] );
    }


    /**
     * Renders the option page skeleton.
     * 
     * @return void
     */
    public function callback_render_page()
    {
        $checked_contacts = array_filter( $this->option_bar['contacts'], function( $contact ) { return $contact['checked']; });
        ?>
        <div class="wrap">
            <h2><?php esc_html_e( 'Mobile Contact Bar', 'mobile-contact-bar' ); ?></h2>

            <form id="mcb-form" action="options.php" method="post">
                <?php
                settings_fields( abmcb()->id . '_group' );
                wp_nonce_field( 'closedpostboxes', 'closedpostboxesnonce', false );
                wp_nonce_field( 'meta-box-order', 'meta-box-order-nonce', false );
                ?>

                <div id="poststuff">
                    <div id="post-body" class="metabox-holder columns-<?php echo ( 1 === get_current_screen()->get_columns() ) ? '1' : '2'; ?>">

                        <div id="postbox-container-2" class="postbox-container">
                            <?php do_meta_boxes( abmcb()->page_suffix, 'advanced', null ); ?>
                        </div><!-- #postbox-container-2 -->

                        <div id="postbox-container-1" class="postbox-container">
                            <?php do_meta_boxes( abmcb()->page_suffix, 'side', null ); ?>
                        </div><!-- #postbox-container-1 -->

                        <?php submit_button(); ?>

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
     * Adds sections and settings to the option page.
     * 
     * @return void
     */
    public function admin_init()
    {
        $this->option_bar = abmcb( Options::class )->get_option( abmcb()->id, 'default_option_bar', 'is_valid_option_bar' );

        register_setting(
            abmcb()->id . '_group',
            abmcb()->id,
            [$this, 'callback_sanitize_option']
        );

        abmcb( Settings\View::class )->add( $this->option_bar );
        abmcb( Contacts\View::class )->add( $this->option_bar );
    }


    /**
     * Sanitizes the settings and contacts.
     *
     * @param  array $input Multidimensional array of the bar-option
     * @return array        Sanitized bar-option
     */
    public function callback_sanitize_option( $input )
    {
        if ( empty( $input ) || ! is_array( $input ))
        {
            return $this->option_bar;
        }

        $settings = isset( $input['settings'] ) ? $input['settings'] : [];
        $contacts = isset( $input['contacts'] ) ? $input['contacts'] : [];

        $this->option_bar['settings'] = abmcb( Settings\Input::class )->sanitize( $settings );
        $this->option_bar['contacts'] = abmcb( Contacts\Input::class )->sanitize( $contacts );

        return $this->option_bar;
    }


    /**
     * Adds meta boxes to the option page.
     * Adjusts meta box classes.
     * 
     * @return void
     *
     * @global $wp_settings_sections
     */
    public function add_meta_boxes()
    {
        $screen = get_current_screen();
        if ( $screen->base !== abmcb()->page_suffix )
        {
            return;
        }

        global $wp_settings_sections;

        add_meta_box(
            'mcb-section-model',
            __( 'Real-time Model', 'mobile-contact-bar' ),
            [$this, 'callback_render_model'],
            abmcb()->page_suffix,
            'side',
            'default'
        );

        foreach ( $wp_settings_sections[abmcb()->id] as $section )
        {
            add_meta_box(
                $section['id'],
                $section['title'],
                [$this, 'callback_render_section'],
                abmcb()->page_suffix,
                'advanced',
                'default'
            );

            // Add all meta box classes to this class list except the Contact List meta box
            if ( 'mcb-section-contacts' !== $section['id'] )
            {
                add_filter( 'postbox_classes_' . abmcb()->page_suffix . '_' . $section['id'], [$this, 'postbox_classes'] );
            }
        }

        $user_id = get_current_user_id();
        $closed_meta_boxes = get_user_meta( 'closedpostboxes_' . abmcb()->page_suffix, $user_id );

        // Close meta boxes for the first time user
        if ( ! $closed_meta_boxes )
        {
            $meta_boxes = array_keys( $wp_settings_sections[abmcb()->id] );
            update_user_meta( $user_id, 'closedpostboxes_' . abmcb()->page_suffix, $meta_boxes, true );
        }
    }


    /**
     * Renders Real-time Model meta box.
     * 
     * @return void
     */
    public function callback_render_model()
    {
        ?>
        <div id="mcb-model">
            <?php include_once plugin_dir_path( abmcb()->file ) . 'assets/images/real-time-model/model.svg'; ?>
            <footer><em><?php _e( 'The model is an approximation. A lot depends on your active theme\'s styles.', 'mobile-contact-bar' ); ?></em></footer>
        </div>

        <div id="mcb-about">
            <h2><?php _e( 'Mobile Contact Bar', 'mobile-contact-bar' ); ?> <?php echo abmcb()->version; ?></h2>
            <p><?php _e( abmcb()->description, 'mobile-contact-bar' ); ?></p>
            <ul>
                <li><a href="<?php echo esc_url( abmcb()->plugin_uri . '#developers' ); ?>" target="_blank" rel="noopener"><?php _e( 'Changelog', 'mobile-contact-bar' ); ?></a></li>
                <li><a href="<?php echo esc_url( 'https://wordpress.org/support/plugin/mobile-contact-bar' ); ?>" target="_blank" rel="noopener"><?php _e( 'Forum', 'mobile-contact-bar' ); ?></a></li>
            </ul>
            <footer>
                <?php printf( __( 'Thank you for networking with <a href="%s">MCB</a>.', 'mobile-contact-bar' ), esc_url( abmcb()->plugin_uri )); ?>
            </footer>
        </div>
        <?php
    }


    /**
     * Renders a meta box.
     *
     * @param  object $object  null
     * @param  array  $section Passed from add_meta_box as sixth parameter
     * @return void
     */
    public function callback_render_section( $object, $section )
    {
        $table_id = str_replace( '-section-', '-table-', $section['id'] );

        if ( 'mcb-table-contacts' === $table_id )
        {
            echo abmcb( Contacts\View::class )->render_contact_list();
        }
        else
        {
            ?>
            <table id="<?php esc_attr_e( $table_id ); ?>" class="form-table">
                <tbody>
                    <?php do_settings_fields( abmcb()->id, $section['id'] ); ?>
                </tbody>
            </table>
            <?php
        }
    }

    
    /**
     * Adds classes to meta boxes.
     *
     * @param  array $classes Array of classes
     * @return array          Updated array of classes
     */
    public function postbox_classes( $classes )
    {
        $classes[] = 'mcb-settings';
        return $classes;
    }


    /**
     * Triggers add_meta_boxes on 'add_meta_boxes' hook.
     * Adds screen options tab.
     * 
     * @return void
     */
    public function load_screen_options()
    {
        do_action( 'add_meta_boxes', abmcb()->page_suffix, null );
        add_screen_option( 'layout_columns', ['max' => 2, 'default' => 2] );
    }


    /**
     * Adds contextual help menu.
     * 
     * @return void
     */
    public function load_help()
    {
        $screen = get_current_screen();

        $tabs =
        [
            [
                'title'    => __( 'Links', 'mobile-contact-bar' ),
                'id'       => 'mcb-link',
                'callback' => [$this, 'callback_render_help_tab_link'],
            ],
            [
                'title'    => __( 'Emails', 'mobile-contact-bar' ),
                'id'       => 'mcb-mailto',
                'callback' => [$this, 'callback_render_help_tab_mailto'],
            ],
            [
                'title'    => __( 'Phone calls', 'mobile-contact-bar' ),
                'id'       => 'mcb-tel',
                'callback' => [$this, 'callback_render_help_tab_tel'],
            ],
            [
                'title'    => __( 'SMS', 'mobile-contact-bar' ),
                'id'       => 'mcb-sms',
                'callback' => [$this, 'callback_render_help_tab_sms'],
            ],
            [
                'title'    => __( 'Skype', 'mobile-contact-bar' ),
                'id'       => 'mcb-skype',
                'callback' => [$this, 'callback_render_help_tab_skype'],
            ],
            [
                'title'    => __( 'Viber', 'mobile-contact-bar' ),
                'id'       => 'mcb-viber',
                'callback' => [$this, 'callback_render_help_tab_viber'],
            ],
        ];

        foreach ( $tabs as $tab )
        {
            $screen->add_help_tab( $tab );
        }

        $screen->set_help_sidebar( $this->output_help_sidebar() );
    }


    /**
     * Renders 'links' help tab.
     * 
     * @return void
     */
    public function callback_render_help_tab_link()
    {
        ?>
        <h4><?php _e( 'Linking to web pages on your or others websites', 'mobile-contact-bar' ); ?></h4>
        <code>http://domain.com</code> <?php _e( 'or', 'mobile-contact-bar' ); ?> <code>http://domain.com/path/to/page</code>
        <p><?php _e( 'For secure websites using SSL to encrypt data and authenticate the website use the <code>https</code> protocol:', 'mobile-contact-bar' ); ?></p>
        <code>https://domain.com</code> <?php _e( 'or', 'mobile-contact-bar' ); ?> <code>https://domain.com/path/to/page</code>
        <p><?php _e( 'You can append query parameters to URLs using the', 'mobile-contact-bar' ); ?> <span class="mcb-tab-button button">&nbsp;<?php _e( 'Add Parameter', 'mobile-contact-bar' ); ?></span> <?php _e( 'button', 'mobile-contact-bar' ); ?></p>
        <p class="mcb-tab-status-green"><?php _e( 'Standardised protocol', 'mobile-contact-bar' ); ?></p>
        <?php
    }


    /**
     * Renders 'mailto' help tab.
     * 
     * @return void
     */
    public function callback_render_help_tab_mailto()
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
     * Renders 'tel' help tab.
     * 
     * @return void
     */
    public function callback_render_help_tab_tel()
    {
        ?>
        <h4><?php _e( 'Initiating phone or mobile audio calls', 'mobile-contact-bar' ); ?></h4>
        <code>tel:15417543010</code> <?php _e( 'or', 'mobile-contact-bar' ); ?> <code>tel:+15417543010</code>
        <p><?php _e( 'Use the international dialing format: the plus sign (<code>+</code>), country code, area code, and number. You can separate each segment of the number with a hyphen (<code>-</code>) for easier reading.', 'mobile-contact-bar' ); ?></p>
        <p class="mcb-tab-status-green"><?php _e( 'Standardised protocol', 'mobile-contact-bar' ); ?></p>
        <?php
    }


    /**
     * Renders 'sms' help tab.
     * 
     * @return void
     */
    public function callback_render_help_tab_sms()
    {
        ?>
        <h4><?php _e( 'Sending text messages to mobile phones', 'mobile-contact-bar' ); ?></h4>
        <code>sms:15417543010</code> <?php _e( 'or', 'mobile-contact-bar' ); ?> <code>sms:+15417543010</code>
        <p><?php _e( 'Use the international dialing format: the plus sign (<code>+</code>), country code, area code, and number. You can separate each segment of the number with a hyphen (<code>-</code>) for easier reading.', 'mobile-contact-bar' ); ?></p>
        <p><?php _e( 'Optional query parameter:', 'mobile-contact-bar' ); ?></p>
        <ul class="mcb-query-parameters">
            <li>
                <span class="mcb-query-parameter-key">body</span>
                <span><?php _e( 'Text message to appear in the body of the message (it does not always work).', 'mobile-contact-bar' ); ?></span>
            </li>
        </ul>
        <p class="mcb-tab-status-yellow"><?php _e( 'Inconsistent protocol', 'mobile-contact-bar' ); ?></p>
        <?php
    }


    /**
     * Renders 'skype' help tab.
     * 
     * @return void
     */
    public function callback_render_help_tab_skype()
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
     * Renders 'viber' help tab.
     * 
     * @return void
     */
    public function callback_render_help_tab_viber()
    {
        ?>
        <h4><?php _e( 'Sending instant messages to other Viber users, phones, or mobiles', 'mobile-contact-bar' ); ?></h4>
        <code>viber://pa?chatURI=&lt;Chat URI&gt;</code>
        <p class="mcb-tab-status-yellow"><?php _e( 'Inconsistent protocol', 'mobile-contact-bar' ); ?></p>
        <?php
    }


    /**
     * Outputs help sidebar.
     * 
     * @return string HTML
     */
    public function output_help_sidebar()
    {
        $out  = '';
        $out .= '<h4>' . esc_html__( 'More info', 'mobile-contact-bar' ) . '</h4>';
        $out .= '<p><a href="'. esc_url( 'https://en.wikipedia.org/wiki/Uniform_Resource_Identifier' ) . '" target="_blank" rel="noopener">' . esc_html__( 'Uniform Resource Identifier', 'mobile-contact-bar' ) . '</a></p>';

        return $out;
    }


    /**
     * Loads styles and scripts for the option page.
     *
     * @param  string $hook_suffix The specific admin page
     * @return void
     */
    public function admin_enqueue_scripts( $hook_suffix )
    {
        if ( abmcb()->page_suffix === $hook_suffix )
        {
            // WordPress's postboxes logic
            wp_enqueue_script( 'postbox' );

            // WordPress's color picker styles and scripts
            wp_enqueue_style( 'wp-color-picker' );
            wp_enqueue_script( 'wp-color-picker' );

            wp_enqueue_style(
                'mcb-admin',
                plugin_dir_url( abmcb()->file ) . 'assets/css/admin.min.css',
                ['wp-color-picker'],
                abmcb()->version,
                'all'
            );

            wp_enqueue_script(
                'mcb-admin',
                plugin_dir_url( abmcb()->file ) . 'assets/js/admin.min.js',
                ['jquery', 'jquery-ui-slider', 'jquery-ui-sortable', 'postbox', 'wp-color-picker'],
                abmcb()->version,
                false
            );

            wp_localize_script(
                'mcb-admin',
                abmcb()->id,
                [
                    'nonce'    => wp_create_nonce( abmcb()->id ),
                    'page_url' => plugin_dir_url( abmcb()->file ),
                    'ti_icons' => Contacts\Input::ti_icons(),
                    'fa_icons' => Contacts\Input::fa_icons(),
                ]
            );

            wp_enqueue_script(
                'mobile-contact-bar',
                plugin_dir_url( abmcb()->file ) . 'assets/js/public.min.js',
                [],
                abmcb()->version,
                true
            );
        }
    }


    /**
     * Renders HTML template elements.
     * 
     * @return void
     */
    public function admin_footer()
    {
        abmcb( Contacts\View::class )->render_icon_picker_template();
    }


    /**
     * Injects re-generated styles to bar-option before update.
     *
     * @param  array $new_value The new value
     * @param  array $old_value The old value
     * @return array            The updated bar-option
     */
    public function pre_update_option( $new_value, $old_value = [] )
    {
        $new_value['styles'] = Styles\CSS::output( $new_value['settings'], $new_value['contacts'] );
        return $new_value;
    }


    /**
     * Adds 'Settings' link to the plugins overview page.
     *
     * @param  array $links Associative array of links
     * @return array        Updated links
     */
    public function plugin_action_links( $links )
    {
        return array_merge(
            ['settings' => '<a href="' . admin_url( 'options-general.php?page=' . abmcb()->slug ) . '">' . esc_html__( 'Settings' ) . '</a>'],
            $links
        );
    }
}
