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


    public function weekly_events()
    {
        clearstatcache();
        $this->get_remote_post_test();
        $this->get_cloudflare_ips();
    }


    /**
     * @return string
     */
    public function get_remote_post_test()
    {
        $test = get_transient( abmcb()->id . '_remote_post_test' );

        if ( false === $test )
        {
            $response = wp_remote_post('https://api.wordpress.org/stats/php/1.0/');

            $test = ! is_wp_error( $response ) && in_array( $response['response']['code'], range( 200, 299 ))
                ? 'Works'
                : 'Does not work';
            set_transient( abmcb()->id . '_remote_post_test', $test, WEEK_IN_SECONDS );
        }

        return $test;
    }


    /**
     * @return array
     */
    public function get_cloudflare_ips()
    {
        $cloudflare_ips = get_transient( abmcb()->id . '_cloudflare_ips' );

        if ( false === $cloudflare_ips )
        {
            $cloudflare_ips = array_fill_keys( ['v4', 'v6'], [] );

            foreach ( array_keys( $cloudflare_ips ) as $version )
            {
                $url = 'https://www.cloudflare.com/ips-' . $version;
                $response = wp_remote_get( $url, ['sslverify' => false] );

                if ( is_wp_error( $response ))
                {
                    continue;
                }

                $status_code = wp_remote_retrieve_response_code( $response );
                if ( 200 !== $status_code )
                {
                    continue;
                }

                $cloudflare_ips[$version] = array_filter(
                    (array) preg_split( '/\R/', wp_remote_retrieve_body( $response ))
                );
            }

            set_transient( abmcb()->id . '_cloudflare_ips', $cloudflare_ips, WEEK_IN_SECONDS );
        }

        return $cloudflare_ips;
    }
}
