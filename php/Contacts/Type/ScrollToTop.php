<?php

namespace MobileContactBar\Contacts\Type;

use MobileContactBar\Contacts\Input;


final class ScrollToTop extends TypeAbstract
{
    public function __construct()
    {
        parent::__construct();
        add_action( 'mcb_public_add_script', [$this, 'mcb_public_add_script'] );
    }


    public function contact()
    {
        return [
            'type'        => $this->type,
            'title'       => __( 'Scroll to Top', 'mobile-contact-bar' ),
            'checked'     => 0,
            'brand'       => 'fa',
            'icon'        => 'solid chevron-up',
            'label'       => __( 'Scroll To Top', 'mobile-contact-bar' ),
            'uri'         => '',
            'placeholder' => 'some placeholder',
            'palette'     => abmcb( Input::class )->palette_defaults(),
            'desc_type'   => __( 'Inline JavaScript handles the scrolling.', 'mobile-contact-bar' ),
            'desc_uri'    => '',
        ];
    }


    public function mcb_public_add_script( $type )
    {
        if ( $type !== $this->type )
        {
            return;
        }

        ?>
        <script id="mobile-contact-bar-<?php echo esc_attr( $this->type ); ?>">
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

                    if ( currentTime < duration ) {
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
                document.scripts['mobile-contact-bar-<?php echo esc_attr( $this->type ); ?>'].parentElement.firstChild.onclick = function( event ) {
                    event.preventDefault();
                    scrollTo(0, 300);
                }
            });
        })();
        </script>
        <?php
    }
}