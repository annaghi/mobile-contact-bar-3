<?php

namespace MobileContactBar\Contacts\Type;

use MobileContactBar\Contacts\Input;


final class HistoryBack extends TypeAbstract
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
            'title'       => __( 'History Back', 'mobile-contact-bar' ),
            'checked'     => 0,
            'brand'       => 'fa',
            'icon'        => 'solid long-arrow-alt-left',
            'label'       => '',
            'uri'         => '',
            'placeholder' => '',
            'palette'     => abmcb( Input::class )->palette_defaults(),
            'desc_type'   => __( 'Inline JavaScript runs the history.back() method.', 'mobile-contact-bar' ),
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
            document.addEventListener('DOMContentLoaded', function() {
                document.scripts['mobile-contact-bar-<?php echo esc_attr( $this->type ); ?>'].parentElement.firstChild.onclick = function( event ) {
                    event.preventDefault();
                    history.back();
                }
            });
        })();
        </script>
        <?php
    }
}

