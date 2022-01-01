<?php

namespace MobileContactBar\Controllers;


final class CronController
{
    public function cron_schedules( $schedules )
    {
        if ( isset( $schedules['weekly'] ))
        {
            return $schedules;
        }

        $schedules['weekly'] = [
            'interval' => WEEK_IN_SECONDS,
            'display'  => __( 'Once Weekly', 'mobile-contact-bar' ),
        ];

        return $schedules;
    }


    public function wp()
    {
        if ( ! wp_next_scheduled( abmcb()->wp_cron_hook ))
        {
            wp_schedule_event( current_time( 'timestamp', true ), 'weekly', abmcb()->wp_cron_hook );
        }
    }


    public function clear_stat_cache()
    {
        clearstatcache();
    }
}
