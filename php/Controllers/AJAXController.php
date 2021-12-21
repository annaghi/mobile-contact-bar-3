<?php

namespace MobileContactBar\Controllers;

use MobileContactBar\Contacts;


final class AJAXController
{
    /**
     * Accepted administrative AJAX actions.
     *
     * @var array
     */
    public $admin_actions = [
        'ajax_add_contact',
        'ajax_add_parameter',
        'ajax_change_contact_type',
        'ajax_get_icon'
    ];


    /**
     * Sends HTML for a new contact with type 'link'.
     * 
     * @return void
     *
     * @uses $_POST
     */
    public function ajax_add_contact()
    {
        if ( $this->verify_nonce() )
        {
            $data = abmcb( Contacts\View::class )->ajax_add_contact();
            $response = json_encode( $data );

            if ( $response )
            {
                echo $response;
            }
        }
        wp_die();
    }


    /**
     * Sends HTML for a parameter with key-value inputs.
     * 
     * @return void
     *
     * @uses $_POST
     */
    public function ajax_add_parameter()
    {
        if ( $this->verify_nonce() )
        {
            $data = abmcb( Contacts\View::class )->ajax_add_parameter();
            $response = json_encode( $data );

            if ( $response )
            {
                echo $response;
            }
        }
        wp_die();
    }


    /**
     * Sends HTML and contact_type for the selected contact type.
     * 
     * @return void
     *
     * @uses $_POST
     */
    public function ajax_change_contact_type()
    {
        if ( $this->verify_nonce() )
        {
            $data = abmcb( Contacts\View::class )->ajax_change_contact_type();
            $response = json_encode( $data );

            if ( $response )
            {
                echo $response;
            }
        }
        wp_die();
    }


    /**
     * Sends SVG for the selected icon.
     * 
     * @return void
     *
     * @uses $_POST
     */
    public function ajax_get_icon()
    {
        if ( $this->verify_nonce() )
        {
            $data = abmcb( Contacts\View::class )->ajax_get_icon();
            $response = json_encode( $data );

            if ( $response )
            {
                echo $response;
            }
        }
        wp_die();
    }


    /**
     * Verifies nonce for the AJAX request.
     * 
     * @return bool|void
     *
     * @uses $_POST
     */
    private function verify_nonce()
    {
        if ( ! empty( $_POST['nonce'] ) && ! empty( $_POST['action'] ) && wp_verify_nonce( sanitize_key( $_POST['nonce'] ), abmcb()->id ))
        {
            return true;
        }
        wp_die();
    }
}
