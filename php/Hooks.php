<?php

namespace MobileContactBar;

use MobileContactBar\Controllers\AdminController;
use MobileContactBar\Controllers\AJAXController;
use MobileContactBar\Controllers\CronController;
use MobileContactBar\Controllers\IFrameController;
use MobileContactBar\Controllers\NoticeController;
use MobileContactBar\Controllers\PublicController;


final class Hooks
{
    /**
     * Controllers
     */
    protected $admin  = null;
    protected $ajax   = null;
    protected $cron   = null;
    protected $iframe = null;
    protected $notice = null;
    protected $public = null;


    /**
     * Hooks plugin into WordPress's actions and filters.
     * 
     * @return void
     */
    public function add()
    {
        $this->cron = abmcb( CronController::class );
        add_filter( 'cron_schedules', [$this->cron, 'cron_schedules'] );
        add_action( 'wp',  [$this->cron, 'wp'] );
        add_action( abmcb()->wp_cron_hook, [$this->cron, 'clear_stat_cache'] );

        if ( is_admin() )
        {
            $this->notice = abmcb( NoticeController::class );
            add_action( 'admin_enqueue_scripts', [$this->notice, 'admin_enqueue_scripts'] );
            add_action( 'admin_notices', [$this->notice, 'admin_notices'] );
            add_action( 'wp_ajax_mcb_ajax_dismiss_notice', [$this->notice, 'ajax_dismiss_notice'] );

            $this->ajax = abmcb( AJAXController::class );
            foreach ( $this->ajax->admin_actions as $admin_action )
            {
                add_action( 'wp_ajax_mcb_' . $admin_action, [$this->ajax, $admin_action], 1 );
            }

            $this->admin = abmcb( AdminController::class );
            add_action( 'admin_menu', [$this->admin, 'admin_menu'] );
            add_action( 'admin_init', [$this->admin, 'admin_init'] );
            add_action( 'add_meta_boxes', [$this->admin, 'add_meta_boxes'] );
            add_action( 'admin_enqueue_scripts', [$this->admin, 'admin_enqueue_scripts'] );
            add_action( 'in_admin_header', [$this->admin, 'in_admin_header'] );
            add_action( 'admin_footer', [$this->admin, 'admin_footer'] );
            add_filter( 'pre_update_option_' . abmcb()->id, [$this->admin, 'pre_update_option'], 10, 2 );
            add_filter( 'plugin_action_links_' . plugin_basename( abmcb()->file ), [$this->admin, 'plugin_action_links'] );
        }

        if ( isset( $_GET[abmcb()->slug . '-iframe'] ))
        {
            $this->iframe = abmcb( IFrameController::class );
            add_action( 'init', [$this->iframe, 'init'] );
        }

        if ( ! is_admin() && ! wp_doing_ajax() && ! isset( $_GET[abmcb()->slug . '-iframe'] ))
        {
            $this->public = abmcb( PublicController::class );
            add_action( 'init', [$this->public, 'init'] );
        }
    }
}
