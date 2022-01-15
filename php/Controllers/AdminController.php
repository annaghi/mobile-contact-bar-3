<?php

namespace MobileContactBar\Controllers;

use MobileContactBar\Icons;
use MobileContactBar\File;
use MobileContactBar\Option;
use MobileContactBar\Settings;
use MobileContactBar\Contacts;


final class AdminController
{
    public $l10n = [];


    /**
     * Adds main plugin page to the admin menu.
     * Hooks the page related screen tabs.
     * 
     * @return void
     */
    public function admin_menu()
    {
        $this->l10n = [
            'disabled' => __( 'disabled', 'mobile-contact-bar' ),
            'enabled'  => __( 'enabled', 'mobile-contact-bar' ),
            'no_URI'   => __( '(no URI)', 'mobile-contact-bar' ),
        ];

        $mcb_icon = 'data:image/svg+xml;base64,PHN2ZyB4bWxucz0iaHR0cDovL3d3dy53My5vcmcvMjAwMC9zdmciIHZpZXdCb3g9IjAgMCA1MTIgNTEyIj48cGF0aCBmaWxsPSJjdXJyZW50Q29sb3IiIGQ9Ik0gNDAwIDAgaCAtMjg4IGMgLTI2LjUxIDAgLTQ4IDIxLjQ5IC00OCA0OCB2IDQxNiBjIDAgMjYuNTEgMjEuNDkgNDggNDggNDggaCAyODggYyAyNi41MSAwIDQ4IC0yMS40OSA0OCAtNDggdiAtNDE2IGMgMCAtMjYuNTEgLTIxLjQ5IC00OCAtNDggLTQ4IHogbSA4IDQ2NCBjIDAgNC40MTEgLTMuNTg5IDggLTggOCBoIC0yODggYyAtNC40MTEgMCAtOCAtMy41ODkgLTggLTggdiAtNDE2IGMgMCAtNC40MTEgMy41ODkgLTggOCAtOCBoIDI4OCBjIDQuNDExIDAgOCAzLjU4OSA4IDggdiA0MTYgeiBtIC0yMjIgLTMyNCB2IC0yOCBjIDAgLTYuNjI3IC01LjM3MyAtMTIgLTEyIC0xMiBoIC0yOCBjIC02LjYyNyAwIC0xMiA1LjM3MyAtMTIgMTIgdiAyOCBjIDAgNi42MjcgNS4zNzMgMTIgMTIgMTIgaCAyOCBjIDYuNjI3IDAgMTIgLTUuMzczIDEyIC0xMiB6IG0gOTYgMCB2IC0yOCBjIDAgLTYuNjI3IC01LjM3MyAtMTIgLTEyIC0xMiBoIC0yOCBjIC02LjYyNyAwIC0xMiA1LjM3MyAtMTIgMTIgdiAyOCBjIDAgNi42MjcgNS4zNzMgMTIgMTIgMTIgaCAyOCBjIDYuNjI3IDAgMTIgLTUuMzczIDEyIC0xMiB6IG0gOTYgMCB2IC0yOCBjIDAgLTYuNjI3IC01LjM3MyAtMTIgLTEyIC0xMiBoIC0yOCBjIC02LjYyNyAwIC0xMiA1LjM3MyAtMTIgMTIgdiAyOCBjIDAgNi42MjcgNS4zNzMgMTIgMTIgMTIgaCAyOCBjIDYuNjI3IDAgMTIgLTUuMzczIDEyIC0xMiB6Ij48L3BhdGg+PC9zdmc+';

        add_menu_page(
            __( 'Mobile Contact Bar', 'mobile-contact-bar' ),
            __( 'Mobile Contact Bar', 'mobile-contact-bar' ),
            abmcb()->capability,
            abmcb()->slug,
            [$this, 'callback_render_page'],
            $mcb_icon
        );

        add_action( 'load-' . abmcb()->page_suffix, [$this, 'load_screen_options'] );
        add_action( 'load-' . abmcb()->page_suffix, [$this, 'load_help'] );
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
     * Renders the plugin's page header.
     * 
     * @global $plugin_page
     * 
     * @return void
     */
    public function in_admin_header()
    {
        global $plugin_page;

        if ( abmcb()->slug !== $plugin_page )
        {
            return;
        }

        $checked_contacts = array_filter( abmcb()->option_bar['contacts'], function ( $contact ) { return $contact['checked']; });
        $bar_device = ( 'none' === abmcb()->option_bar['settings']['bar']['device'] ) ? $this->l10n['disabled'] : $this->l10n['enabled'];
        $badge_length = ( 0 == count( $checked_contacts )) ? 'mcb-badge-disabled' : 'mcb-badge-enabled';
        $badge_display = ( 'none' === abmcb()->option_bar['settings']['bar']['device'] ) ? 'mcb-badge-disabled' : 'mcb-badge-enabled';

        ?>
        <div class="mcb-header">
            <h1 class="mcb-plugin-name"><?php esc_html_e( 'Mobile Contact Bar', 'mobile-contact-bar' ); ?></h1>
            <h1 class="mcb-plugin-initialism"><?php echo abmcb()->mcb; ?></h1>
            <span id="mcb-badge-length" class="<?php echo $badge_length; ?>"><?php echo count( $checked_contacts ); ?></span>
            <span id="mcb-badge-display" class="<?php echo $badge_display; ?>"><?php echo esc_html( $bar_device ); ?></span>
            <?php submit_button( null, 'primary large', 'submit', false, ['form' => 'mcb-form'] ); ?>
        </div>
        <?php
    }


    /**
     * Adds sections and settings to the plugin page.
     * 
     * @return void
     */
    public function admin_init()
    {
        register_setting( abmcb()->id . '_group', abmcb()->id, [$this, 'callback_sanitize_option'] );

        abmcb( Settings\View::class )->add();
        abmcb( Contacts\View::class )->add();
    }


    /**
     * Adds meta boxes to the plugin page.
     * Adjusts meta box classes.
     *
     * @global $plugin_page
     * @global $wp_settings_sections
     * 
     * @return void
     */
    public function add_meta_boxes()
    {
        global $plugin_page, $wp_settings_sections;
        
        if ( abmcb()->slug !== $plugin_page )
        {
            return;
        }

        add_meta_box(
            'mcb-meta-box-preview',
            __( 'Preview', 'mobile-contact-bar' ),
            [$this, 'callback_render_meta_box_preview'],
            abmcb()->page_suffix,
            'side',
            'default'
        );

        foreach ( $wp_settings_sections[abmcb()->id] as $section )
        {
            add_meta_box(
                $section['id'],
                $section['title'],
                [$this, 'callback_render_meta_box'],
                abmcb()->page_suffix,
                'advanced',
                'default'
            );

            if ( 'mcb-meta-box-contacts' !== $section['id'] )
            {
                add_filter( 'postbox_classes_' . abmcb()->page_suffix . '_' . $section['id'], [$this, 'postbox_classes_mcb_settings'] );
            }
        }

        // Close all meta boxes for the first time user
        $user_id = get_current_user_id();
        $closed_meta_boxes = get_user_meta( $user_id, 'closedpostboxes_' . abmcb()->page_suffix, true );
        if ( ! $closed_meta_boxes )
        {
            $closed_meta_boxes = array_merge( array_keys( $wp_settings_sections[abmcb()->id] ), ['mcb-meta-box-preview'] );
            update_user_meta( $user_id, 'closedpostboxes_' . abmcb()->page_suffix, $closed_meta_boxes );
        }

        // Define meta box order for the first time user
        $order_meta_boxes = get_user_meta( $user_id, 'meta-box-order_' . abmcb()->page_suffix, true );
        if ( ! $order_meta_boxes )
        {
            $order_meta_boxes = [];
            if ( class_exists( 'WooCommerce' ))
            {
                $order_meta_boxes['advanced'] = 'mcb-meta-box-bar,mcb-meta-box-icons_labels,mcb-meta-box-badges,mcb-meta-box-toggle,mcb-meta-box-contacts';
                $order_meta_boxes['side'] = 'mcb-meta-box-preview';
            }
            else
            {
                $order_meta_boxes['advanced'] = 'mcb-meta-box-bar,mcb-meta-box-icons_labels,mcb-meta-box-toggle,mcb-meta-box-contacts';
                $order_meta_boxes['side'] = 'mcb-meta-box-preview';
            }
            update_user_meta( $user_id, 'meta-box-order_' . abmcb()->page_suffix, $order_meta_boxes );
        }
    }


    /**
     * Adds 'mcb-settings' class to meta boxes.
     *
     * @param  array $classes Array of classes
     * @return array          Updated array of classes
     */
    public function postbox_classes_mcb_settings( $classes )
    {
        $classes[] = 'mcb-settings';
        return $classes;
    }


    /**
     * Loads styles and scripts for the plugin page.
     *
     * @param  string $hook_suffix The specific admin page
     * @return void
     */
    public function admin_enqueue_scripts( $hook_suffix )
    {
        if ( abmcb()->page_suffix === $hook_suffix )
        {
            // WordPress's postboxes scripts
            wp_enqueue_script( 'postbox' );

            // WordPress's color picker styles and scripts
            wp_enqueue_style( 'wp-color-picker' );
            wp_enqueue_script( 'wp-color-picker' );

            wp_enqueue_style(
                abmcb()->slug . '-admin',
                plugin_dir_url( abmcb()->file ) . 'assets/css/admin.css',
                ['wp-color-picker'],
                abmcb()->version,
                'all'
            );
            wp_style_add_data( abmcb()->slug . '-admin', 'rtl', 'replace' );

            wp_enqueue_script(
                abmcb()->slug . '-admin',
                plugin_dir_url( abmcb()->file ) . 'assets/js/admin.js',
                ['jquery', 'jquery-ui-slider', 'jquery-ui-sortable', 'postbox', 'wp-color-picker'],
                abmcb()->version,
                false
            );

            wp_localize_script(
                abmcb()->slug . '-admin',
                abmcb()->mcb,
                [
                    'nonce'      => wp_create_nonce( abmcb()->id ),
                    'plugin_url' => plugin_dir_url( abmcb()->file ),
                    'l10n'       => $this->l10n,
                    'ti_icons'   => Icons::ti_icons(),
                    'fa_icons'   => Icons::fa_icons(),
                    
                ]
            );
        }
    }


    /**
     * Renders HTML template elements.
     * 
     * @global $plugin_page
     * 
     * @return void
     */
    public function admin_footer()
    {
        global $plugin_page;

        if ( abmcb()->slug !== $plugin_page )
        {
            return;
        }

        abmcb( Contacts\View::class )->render_icon_picker_template();
    }


    /**
     * Writes generated styles to the uploads/ folder.
     *
     * @param  array $new_value The new value
     * @param  array $old_value The old value
     * @return array
     */
    public function pre_update_option( $new_value, $old_value = [] )
    {
        abmcb( File::class )->write( $new_value );
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
            ['settings' => '<a href="' . admin_url( 'admin.php?page=' . abmcb()->slug ) . '">' . esc_html__( 'Settings', 'mobile-contact-bar' ) . '</a>'],
            $links
        );
    }


    /**
     * Sanitizes the bar option before writing it to the database.
     *
     * @param  array $input Multidimensional array of the bar-option
     * @return array
     */
    public function callback_sanitize_option( $input )
    {
        return abmcb( Option::class )->sanitize_option_bar( $input, 'encode' );
    }


    /**
     * Outputs help sidebar.
     * 
     * @return string HTML
     */
    public function output_help_sidebar()
    {
        $out  = '';
        $out .= '<h4>' . esc_html__( 'About', 'mobile-contact-bar' ) . '</h4>';
        $out .= '<p><span class="dashicons dashicons-admin-plugins"></span> ' . sprintf( __( 'Version %s', 'mobile-contact-bar' ), abmcb()->version ) . '</p>';
        $out .= '<p><span class="dashicons dashicons-wordpress"></span> <a href="' . esc_url( abmcb()->plugin_uri ) . '" target="_blank">' . __( 'View details', 'mobile-contact-bar' ) . '</a></p>';

        return $out;
    }


    /**
     * Renders the plugin page content.
     * 
     * @return void
     */
    public function callback_render_page()
    {
        ?>
        <div class="wrap">
            <hr class="wp-header-end">

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
     * Renders Preview meta box.
     * 
     * @return void
     */
    public function callback_render_meta_box_preview()
    {
        ?>
        <div id="mcb-section-preview">
            <iframe src="<?php echo add_query_arg( [abmcb()->slug . '-iframe' => true], get_home_url() ); ?>" title="<?php esc_attr_e( 'Preview', 'mobile-contact-bar' ); ?>"></iframe>
            <script>
            (function() {
                jQuery('#mcb-section-preview iframe').on('load', function () {
                    var iframe = jQuery(this).contents();
                    iframe.find('html').css({ 'pointer-events': 'none' });
                    iframe.find('body').css({ 'pointer-events': 'none' });
                    iframe.find('#mobile-contact-bar').css({ 'pointer-events': 'all' });
                    iframe.attr('src', iframe.attr('src'));
                });
            })(jQuery);
            </script>
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
    public function callback_render_meta_box( $object, $section )
    {
        $section_name = str_replace( '-meta-box-', '-section-', $section['id'] );

        if ( 'mcb-section-contacts' === $section_name )
        {
            echo abmcb( Contacts\View::class )->render_contacts();
        }
        else
        {
            ?>
            <table id="<?php esc_attr_e( $section_name ); ?>" class="form-table">
                <tbody>
                    <?php do_settings_fields( abmcb()->id, $section['id'] ); ?>
                </tbody>
            </table>
            <?php
        }
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
        <p><?php _e( 'You can append query string parameters to URLs using the', 'mobile-contact-bar' ); ?> <span class="mcb-tab-button button">&nbsp;<?php _e( 'Add Parameter', 'mobile-contact-bar' ); ?></span> <?php _e( 'button', 'mobile-contact-bar' ); ?></p>
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
        <ul>
            <li>
                <span class="mcb-tab-query-parameter-key">subject</span>
                <span><?php _e( 'Text to appear in the subject line of the message.', 'mobile-contact-bar' ); ?></span>
            </li>
            <li>
                <span class="mcb-tab-query-parameter-key">body</span>
                <span><?php _e( 'Text to appear in the body of the message.', 'mobile-contact-bar' ); ?></span>
            </li>
            <li>
                <span class="mcb-tab-query-parameter-key">cc</span>
                <span><?php _e( 'Addresses to be included in the carbon copy section of the message. Separate addresses with commas.', 'mobile-contact-bar' ); ?></span>
            </li>
            <li>
                <span class="mcb-tab-query-parameter-key">bcc</span>
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
        <ul>
            <li>
                <span class="mcb-tab-query-parameter-key">body</span>
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
}
