<?php

namespace MobileContactBar\ButtonTypes;


final class HistoryForward extends Button
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
            'title'       => __( 'History Forward', 'mobile-contact-bar' ),
            'checked'     => 0,
            'brand'       => 'fa',
            'group'       => 'solid',
            'icon'        => 'long-arrow-alt-right',
            'label'       => '',
            'text'        => __( 'Go forward one page', 'mobile-contact-bar' ),
            'uri'         => '',
            'placeholder' => '',
            'custom'      => self::default_customization(),
            'desc_type'   => __( 'Inline JavaScript runs the history.forward() method.', 'mobile-contact-bar' ),
            'desc_uri'    => '',
        ];
    }


    public function script()
    {
        ?>
        <script id="<?php echo abmcb()->slug, '-', esc_attr( $this->type ); ?>">
        (function() {
            document.addEventListener('DOMContentLoaded', function() {
                document.scripts['<?php echo abmcb()->slug, '-', esc_attr( $this->type ); ?>'].parentElement.firstChild.onclick = function( event ) {
                    event.preventDefault();
                    window.history.forward();
                }
            });
        })();
        </script>
        <?php
    }
}
