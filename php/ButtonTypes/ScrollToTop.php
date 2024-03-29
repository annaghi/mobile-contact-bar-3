<?php

namespace MobileContactBar\ButtonTypes;


final class ScrollToTop extends Button
{
    public function __construct()
    {
        parent::__construct();
    }


    public function field()
    {
        return [
            'type'        => $this->type,
            'id'          => '',
            'title'       => __( 'Scroll to Top', 'mobile-contact-bar' ),
            'checked'     => 0,
            'brand'       => 'fa',
            'group'       => 'solid',
            'icon'        => 'chevron-up',
            'label'       => '',
            'text'        => __( 'Scroll to top', 'mobile-contact-bar' ),
            'uri'         => '',
            'placeholder' => 'some placeholder',
            'custom'      => self::default_customization(),
            'desc_type'   => __( 'Inline JavaScript handles the scrolling.', 'mobile-contact-bar' ),
            'desc_uri'    => '',
        ];
    }


    public function script()
    {
        ?>
        <script id="<?php echo abmcb()->slug, '-', esc_attr( $this->type ); ?>">
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
                document.scripts['<?php echo abmcb()->slug, '-', esc_attr( $this->type ); ?>'].parentElement.firstChild.onclick = function( event ) {
                    event.preventDefault();
                    scrollTo(0, 300);
                }
            });
        })();
        </script>
        <?php
    }
}
