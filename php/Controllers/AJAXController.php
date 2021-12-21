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
        'ajax_get_contact',
        'ajax_get_parameter',
        'ajax_get_contact_type',
        'ajax_get_icon'
    ];


    /**
     * Sends HTML for a new contact with type 'link'.
     * 
     * @return void
     *
     * @uses $_POST
     */
    public function ajax_get_contact()
    {
        if ( $this->verify_nonce()
            && isset( $_POST['contact_id'] )
            && (int) $_POST['contact_id'] >= 0 )
        {
            $data = [];

            $contact_types = apply_filters( 'mcb_admin_contact_types', [] );
            $contact = $contact_types['link'];
    
            $data['summary'] = abmcb( Contacts\View::class )->output_summary(
                [
                    'contact_id' => $_POST['contact_id'],
                    'contact' => $contact
                ]
            );

            $data['details'] = abmcb( Contacts\View::class )->output_details(
                [
                    'contact_id' => $_POST['contact_id'],
                    'contact' => $contact
                ]
            );
    
            $response = json_encode( $data );
            if ( $response )
            {
                echo $response;
            }
        }
        wp_die();
    }


    /**
     * Sends HTML for a new parameter with key-value inputs.
     * 
     * @return void
     *
     * @uses $_POST
     */
    public function ajax_get_parameter()
    {
        if ( $this->verify_nonce()
            && isset( $_POST['contact_id'], $_POST['parameter_id'] )
            && (int) $_POST['contact_id'] >= 0
            && (int) $_POST['parameter_id'] >= 0 )
        {
            $data = abmcb( Contacts\View::class )->output_link_parameter(
                [
                    'contact_id'     => $_POST['contact_id'],
                    'parameter_type' => ['field' => 'text'],
                    'parameter_id'   => $_POST['parameter_id'],
                    'parameter'      => ['key' => '', 'value' => '']
                ]
            );

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
    public function ajax_get_contact_type()
    {
        $contact_types = abmcb()->contact_types;

        if ( $this->verify_nonce()
            && isset( $_POST['contact_id'], $_POST['contact_type'] )
            && (int) $_POST['contact_id'] >= 0
            && in_array( $_POST['contact_type'], array_keys( $contact_types )))
        {
            $data = [];

            $contact_type = $contact_types[$_POST['contact_type']]->contact();

            $data['contact_type'] = $contact_type;
            $data['uri'] = abmcb( Contacts\View::class )->output_details_uri(
                [
                    'contact_id' => $_POST['contact_id'],
                    'contact' => $contact_type,
                    'contact_type' => $contact_type
                ]
            );

            $data['parameters'] = abmcb( Contacts\View::class )->output_parameters(
                [
                    'contact_id' => $_POST['contact_id'],
                    'contact' => $contact_type,
                    'contact_type' => $contact_type
                ]
            );
    
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
        if ( $this->verify_nonce()
            && isset( $_POST['brand'], $_POST['icon'] )
            && in_array( $_POST['brand'], ['fa', 'ti'] ))
        {
            if ( 'ti' === $_POST['brand'] && abmcb( Contacts\Input::class )->in_ti_icons( $_POST['icon'] ))
            {
                $path = plugin_dir_url( abmcb()->file ) . 'assets/icons/ti/icons/'. $_POST['icon'] . '.svg';
                $data = file_get_contents( $path );

                $response = json_encode( $data );
                if ( $response )
                {
                    echo $response;
                }
            }
        }
        wp_die();
    }


    /**
     * Verifies nonce for an AJAX request.
     * 
     * @return bool|void
     *
     * @uses $_POST
     */
    private function verify_nonce()
    {
        if ( isset( $_POST['nonce'] ) && wp_verify_nonce( $_POST['nonce'], abmcb()->id ))
        {
            return true;
        }
        wp_die();
    }
}
