<?php

namespace MobileContactBar\ContactTypes;


final class HistoryForward extends ContactType
{
    public function __construct()
    {
        parent::__construct();
        add_action( 'mcb_public_add_script', [$this, 'mcb_public_add_script'] );
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


    public function mcb_public_add_script( $type )
    {
        if ( $type !== $this->type )
        {
            return;
        }

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

