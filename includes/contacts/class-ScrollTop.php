<?php

defined( 'ABSPATH' ) or exit;


final class Mobile_Contact_Bar_Contact_ScrollTop
{

    private static $type = null;
    private static $icon = null;

    public static function plugins_loaded()
    {
        self::$type = substr( __CLASS__, 27 );
        self::$icon = 'fas fa-chevron-circle-up';

        if( is_admin() )
        {
            add_filter( 'mcb_admin_add_icon'    , array( __CLASS__, 'mcb_admin_add_icon' ));
            add_filter( 'mcb_admin_add_contact' , array( __CLASS__, 'mcb_admin_add_contact' ));
        }
    }



    public static function mcb_admin_add_icon( $icons )
    {
        $icons[25] = array(
            'type'  => self::$type,
            'icon'  => self::$icon,
            'title' => __( 'Add Scroll Top', 'mobile-contact-bar' ),
        );
        return $icons;
    }



    public static function mcb_admin_add_contact( $contacts )
    {
        $contacts[] = array(
            'checked'     => 0,
            'type'        => self::$type,
            'icon'        => self::$icon,
            'title'       => __( 'Scroll Top', 'mobile-contact-bar' ),
            'placeholder' => '',
            'uri'         => '#',
        );
        return $contacts;
    }

    public static function public_scripts()
    {
        ?>
        <script id="mobile-contact-bar-scroll">
        (function() {
            function scrollTo(to = 0, duration = 1000) {
                var start       = window.pageYOffset || document.documentElement.scrollTop || document.body.scrollTop || 0,
                    change      = to - start,
                    increment   = 20,
                    currentTime = 0;

                function animateScroll() {
                    currentTime += increment;
                    var val = Math.easeInOutQuad(currentTime, start, change, duration);

                    window.pageYOffset = val;
                    document.documentElement.scrollTop = val;
                    document.body.scrollTop = val;

                    if( currentTime < duration ) {
                        setTimeout(animateScroll, increment);
                    }
                }
                animateScroll();
            };

            Math.easeInOutQuad = function( t, b, c, d ) {
                t /= d/2;
                if (t < 1) return c/2*t*t + b;
                t--;
                return -c/2 * (t*(t-2) - 1) + b;
            };

            document.addEventListener('DOMContentLoaded', function() {
                document.scripts['mobile-contact-bar-scroll'].parentElement.firstChild.onclick = function( event ) {
                    event.preventDefault();
                    scrollTo(0, 300);
                }
            });
        })();
        </script>
        <?php
    }
}
