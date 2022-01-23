<?php

namespace MobileContactBar\Controllers;

use MobileContactBar\File;
use MobileContactBar\Option;
use MobileContactBar\Settings;
use MobileContactBar\Buttons;
use MobileContactBar\Modules\Icons;
use MobileContactBar\Modules\SystemInfo;


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
            __( 'MCB Contact Bar', 'mobile-contact-bar' ),
            abmcb()->capability,
            abmcb()->slug,
            [$this, 'callback_render_page'],
            $mcb_icon
        );

        add_action( 'load-' . abmcb()->page_suffix, [$this, 'load_screen_options'] );
        add_action( 'load-' . abmcb()->page_suffix, [$this, 'load_help'] );
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

        $checked_buttons = array_filter( abmcb()->option_bar['buttons'], function ( $button ) { return $button['checked']; });
        $bar_device = ( 'none' === abmcb()->option_bar['settings']['bar']['device'] ) ? $this->l10n['disabled'] : $this->l10n['enabled'];
        $badge_length = ( 0 == count( $checked_buttons )) ? 'mcb-badge-disabled' : 'mcb-badge-enabled';
        $badge_display = ( 'none' === abmcb()->option_bar['settings']['bar']['device'] ) ? 'mcb-badge-disabled' : 'mcb-badge-enabled';

        ?>
        <div class="mcb-header">
            <h1 class="mcb-plugin-name"><?php esc_html_e( 'Mobile Contact Bar', 'mobile-contact-bar' ); ?></h1>
            <h1 class="mcb-plugin-initialism"><?php echo abmcb()->mcb; ?></h1>
            <span id="mcb-badge-length" class="<?php echo $badge_length; ?>"><?php echo count( $checked_buttons ); ?></span>
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
        abmcb( Buttons\View::class )->add();

        $this->download_system_info();
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

            if ( 'mcb-meta-box-builder' !== $section['id'] )
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
                $order_meta_boxes['advanced'] = 'mcb-meta-box-bar,mcb-meta-box-buttons,mcb-meta-box-badges,mcb-meta-box-toggle,mcb-meta-box-builder';
                $order_meta_boxes['side'] = 'mcb-meta-box-preview';
            }
            else
            {
                $order_meta_boxes['advanced'] = 'mcb-meta-box-bar,mcb-meta-box-buttons,mcb-meta-box-toggle,mcb-meta-box-builder';
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

        abmcb( Buttons\View::class )->render_icon_picker_template();
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
     * Downloads System Info in a txt file.
     * 
     * @return void
     */
    public function download_system_info()
    {
        $request = filter_input( INPUT_POST, abmcb()->mcb, FILTER_DEFAULT, FILTER_REQUIRE_ARRAY );
        if ( ! empty( $request['_action'] ) && empty( $request['_ajax_request'] ))
        {
            check_admin_referer( $request['_action'] );
            abmcb( File::class )->download( abmcb()->id . '-system-info.txt', abmcb( SystemInfo::class )->get() );
        }
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
     * Renders a meta box.
     *
     * @param  object $object  null
     * @param  array  $section Passed from add_meta_box as sixth parameter
     * @return void
     */
    public function callback_render_meta_box( $object, $section )
    {
        $section_name = str_replace( '-meta-box-', '-section-', $section['id'] );

        if ( 'mcb-section-builder' === $section_name )
        {
            echo abmcb( Buttons\View::class )->render_builder();
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
                'title'    => __( 'Troubleshooting', 'mobile-contact-bar' ),
                'id'       => 'mcb-troubleshooting',
                'callback' => [$this, 'callback_render_help_troubleshooting'],
            ],
            [
                'title'    => __( 'System Info', 'mobile-contact-bar' ),
                'id'       => 'mcb-system-info',
                'callback' => [$this, 'callback_render_help_system_info'],
            ],
            [
                'title'    => __( 'Contact Support', 'mobile-contact-bar' ),
                'id'       => 'mcb-contact-support',
                'callback' => [$this, 'callback_render_help_contact_support'],
            ],
        ];

        foreach ( $tabs as $tab )
        {
            $screen->add_help_tab( $tab );
        }

        $screen->set_help_sidebar( $this->output_help_sidebar() );
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
        $out .= '<p><span class="dashicons dashicons-info"></span> <a href="' . esc_url( 'https://wordpress.org/plugins/mobile-contact-bar/' ) . '" target="_blank">' . __( 'View details', 'mobile-contact-bar' ) . '</a></p>';
        $out .= '<p><span class="dashicons dashicons-wordpress"></span> <a href="' . esc_url( 'https://wordpress.org/support/plugin/mobile-contact-bar/' ) . '" target="_blank">' . __( 'Support Forum', 'mobile-contact-bar' ) . '</a></p>';

        return $out;
    }


    /**
     * Renders 'Troubleshooting' help tab.
     * 
     * @return void
     */
    public function callback_render_help_troubleshooting()
    {
        ?>
        <dl>
            <dt><strong><?php _e( '1. Make sure you are using the latest version of Mobile Contact Bar.', 'mobile-contact-bar' ); ?></strong></dt>
            <dd><?php _e( 'If you are not using the latest version and are experiencing problems, chances are good that your problem has already been addressed in the latest version.', 'mobile-contact-bar' ); ?></dd>
            <dt><strong><?php _e( '2. Deactivate and activate again the Mobile Contact Bar plugin.', 'mobile-contact-bar' ); ?></strong></dt>
            <dd><?php _e( 'During the activation the plugin tries to heal the settings, the buttons, and the CSS file.', 'mobile-contact-bar' ); ?></dd>
            <dt><strong><?php _e( '3. Temporarily switch to an official WordPress Theme.', 'mobile-contact-bar' ); ?></strong></dt>
            <dd><?php _e( 'Try switching to an official WordPress Theme (i.e. Twenty Twenty) and then see if you are still experiencing problems with the plugin. If this fixes the problem then there is a compatibility issue with your theme.', 'mobile-contact-bar' ); ?></dd>
            <dt><strong><?php _e( '4. Temporarily deactivate all of your plugins.', 'mobile-contact-bar' ); ?></strong></dt>
            <dd><?php _e( 'If switching to an official WordPress theme did not fix anything, the final thing to try is to deactivate all of your plugins except for Mobile Contact Bar. If this fixes the problem then there is a compatibility issue with one of your plugins.', 'mobile-contact-bar' ); ?>
            <br /><?php _e( 'To find out which plugin is incompatible with Mobile Contact Bar you will need to reactivate your plugins one-by-one until you find the plugin that is causing the problem. But keep continuing to test the rest of your plugins. Hopefully you won’t find any more but it’s always better to make sure.', 'mobile-contact-bar' ); ?></dd>
        </dl>
        <p><strong><?php _e( 'If you find an incompatible theme or plugin, please Contact Support so we can fix it.', 'mobile-contact-bar' ); ?></strong></p>
        <?php
    }


    /**
     * Renders 'System Info' help tab.
     * 
     * @return void
     */
    public function callback_render_help_system_info()
    {
        $system_info = abmcb( SystemInfo::class )->get();

        ?>
        <form id="mcb-system-info-form" method="post">
            <textarea class="large-text code mcb-tab-textarea" name="<?php echo abmcb()->mcb; ?>[system-info]" rows="20" onclick="this.select()" readonly><?php echo esc_attr( $system_info ); ?></textarea>
            <input type="hidden" name="<?php echo abmcb()->mcb; ?>[_action]" value="download-system-info">
            <?php wp_nonce_field( 'download-system-info' ); ?>
            <button type="submit" id="mcb-download-system-info" class="button button-secondary"><?php _e( 'Download System Info', 'mobile-contact-bar' ); ?></button>
        </form>
        <?php
    }


    /**
     * Renders 'Contact Support' help tab.
     * 
     * @return void
     */
    public function callback_render_help_contact_support()
    {
        ?>
            <p><strong><?php printf( __( 'Please send an email to %s and include the following details:', 'mobile-contact-bar' ), '<a href="mailto:support@mobilecontactbar.com?subject=Support%20request">support@mobilecontactbar.com</a>' ); ?></strong></p>
            <ul>
                <li><?php _e( 'A detailed description of the problem you are having and the steps to reproduce it.', 'mobile-contact-bar' ); ?></li>
                <li><?php _e( 'Download and attach the Mobile Contact Bar <strong>System Info</strong> report to the email.', 'mobile-contact-bar' ); ?></li>
                <li><?php _e( 'Please also include screenshots if they will help explain the problem.', 'mobile-contact-bar' ); ?></li>
            </ul>
            <p><span class="required"><?php _e( 'Please be aware that if your email does not include the Mobile Contact Bar System Info report (as requested above), the response may be delayed or possibly ignored altogether. Thank you for understanding.', 'mobile-contact-bar' ); ?></span></p>
        <?php
    }
}
