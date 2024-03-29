<?php

namespace MobileContactBar\Controllers;


final class NoticeController
{
    const USER_META_KEY = 'mobile_contact_bar_notices';
    const NOTICES = ['major', 'minor'];

    public $screens = [];


    public function __construct()
    {
        $this->screens = [
            'index.php',
            'dashboard',
            abmcb()->page_suffix,
        ];
    }


    /**
     * Loads styles and scripts for admin notices.
     *
     * @param  string $hook_suffix The specific admin page
     * @return void
     */
    public function admin_enqueue_scripts( $hook_suffix )
    {
        if ( $this->is_notice_on_screen( $hook_suffix ))
        {
            wp_enqueue_style(
                abmcb()->slug . '-notices',
                plugin_dir_url( abmcb()->file ) . 'assets/css/notices.css',
                [],
                abmcb()->version,
                'all'
            );
            wp_style_add_data( abmcb()->slug . '-notices', 'rtl', 'replace' );

            wp_enqueue_script(
                abmcb()->slug . '-notices',
                plugin_dir_url( abmcb()->file ) . 'assets/js/notices.js',
                ['jquery'],
                abmcb()->version,
                false
            );

            wp_localize_script(
                abmcb()->slug . '-notices',
                abmcb()->id,
                ['nonce' => wp_create_nonce( abmcb()->id  )]
            );
        }
    }


    public function admin_notices()
    {
        $this->display_page_notice();
        $this->display_major_notice();
        $this->display_minor_notice();
    }


    /**
     * @return void
     * 
     * @global $plugin_page
     */
    protected function display_page_notice()
    {
        global $plugin_page;

        if ( abmcb()->slug !== $plugin_page )
        {
            return;
        }

        if ( ! empty( $_GET['settings-updated'] ))
        {
            ?>
            <div class="updated notice notice-success is-dismissible">
                <p><strong><?php _e( 'Your changes have been saved.', 'mobile-contact-bar' ); ?></strong></p>
            </div>
            <?php
        }
    }


    protected function display_major_notice()
    {
        $current_screen = get_current_screen();

        if ( $this->is_notice_on_screen( $current_screen->base ) && current_user_can( abmcb()->capability ))
        {
            $user_meta = $this->get_user_meta();
            if ( ! $user_meta || ! isset( $user_meta['major'] ))
            {
                $message = sprintf(
                    _x( 'Thanks for installing Mobile Contact Bar v%s, we hope you love it!', 'admin-text', 'mobile-contact-bar' ),
                    abmcb()->version
                );
                $this->render_major_notice( $message );
            }
            elseif ( isset( $user_meta['major'] ) && ! $this->is_dismissed_notice( 'major' ))
            {
                $message = sprintf(
                    _x( 'Thanks for updating to Mobile Contact Bar v%s, we hope you love the changes!', 'admin-text', 'mobile-contact-bar' ),
                    abmcb()->version
                );
                $this->render_major_notice( $message );
            }
        }
    }


    protected function display_minor_notice()
    {
        $current_screen = get_current_screen();

        if ( $this->is_notice_on_screen( $current_screen->base ) && current_user_can( abmcb()->capability ))
        {
            $user_meta = $this->get_user_meta();
            if ( ! $user_meta || ! isset( $user_meta['minor'] ))
            {
                $this->update_user_meta( 'minor', abmcb()->version );
            }
            elseif ( isset( $user_meta['minor'] ) && ! $this->is_dismissed_notice( 'minor' ))
            {
                $this->update_user_meta( 'minor', abmcb()->version );

                $message = sprintf(
                    _x( 'Your Mobile Contact Bar settings have been updated successfully.', 'admin-text', 'mobile-contact-bar' ),
                    abmcb()->version
                );

                ?>
                <div class="updated notice notice-success is-dismissible mobile-contact-bar-notice" data-dismiss="minor">
                    <p><?php echo $message; ?></p>
                </div>
                <?php
            }
        }
    }


    /**
     * Renders major version notice.
     * 
     * @return void
     */
    private function render_major_notice( $message = '' )
    {
        ?>
        <div class="updated notice notice-success is-dismissible mobile-contact-bar-notice" data-dismiss="major">
            <p><?php echo $message; ?></p>
            <p>
                <a href="<?php echo esc_url( 'https://wordpress.org/plugins/mobile-contact-bar/#developers' ); ?>" target="_blank" rel="noopener" class="button mobile-contact-bar-whats-new">
                    <span class="mobile-contact-bar-whats-new-icon">
                        <?php include_once plugin_dir_path( abmcb()->file ) . 'assets/img/whats-new-icon.svg'; ?>
                    </span>
                    <span><?php echo _x( 'See What\'s New', 'admin-text', 'mobile-contact-bar' ); ?></span>
                </a>
            </p>
        </div>
        <?php
    }


    /**
     * 
     * @return array|void
     */
    public function ajax_dismiss_notice()
    {
        if ( ! isset( $_POST['nonce'] ) || ! wp_verify_nonce( $_POST['nonce'], abmcb()->id ))
        {
            wp_die();
        }

        if ( isset( $_POST['notice'] ) && in_array( $_POST['notice'], self::NOTICES ))
        {
            $this->update_user_meta( $_POST['notice'], abmcb()->version );
            wp_send_json_success();
        }

        wp_die();
    }


    private function is_notice_on_screen( $screen = '' )
    {
        return in_array( $screen, $this->screens );
    }


    private function is_dismissed_notice( $notice )
    {
        $user_meta = $this->get_user_meta();

        if ( isset( $user_meta[$notice] ) && version_compare(
            $this->version( $user_meta[$notice], $notice ),
            $this->version( abmcb()->version, $notice ), '<' ))
        {
            return false;
        }

        return true;
    }

    
    private function get_user_meta()
    {
        $user_id = get_current_user_id();
        $user_meta = get_user_meta( $user_id, self::USER_META_KEY, true );

        return ( ! $user_meta )
            ? []
            : (array) $user_meta;
    }


    private function update_user_meta( $key, $value )
    {
        $user_meta = $this->get_user_meta();
        $user_meta[$key] = $value;

        $user_id = get_current_user_id();
        // Instead of update_user_option using update_user_meta is deliberate
        update_user_meta( $user_id, self::USER_META_KEY, $user_meta );
    }


    /**
     * Extracts version level (major, minor, patch) from a version.
     * 
     * @param  string $version
     * @param  string $version_level
     * @return string
     */
    public function version( $version, $version_level = '' )
    {
        $pattern = '/^v?(\d{1,5})(\.\d++)?(\.\d++)?(.+)?$/i';
        preg_match( $pattern, $version, $matches );

        switch ( $version_level )
        {
            case 'major':
                $version = isset( $matches[1] ) ? $matches[1] : '';
                break;

            case 'minor':
                $version = isset( $matches[1], $matches[2] ) ? $matches[1] . $matches[2] : '';
                break;

            case 'patch':
                $version = isset( $matches[1], $matches[2], $matches[3] ) ? $matches[1] . $matches[2] . $matches[3] : '';
                break;
        }
        return empty( $version )
            ? abmcb()->version
            : $version;
    }
}
