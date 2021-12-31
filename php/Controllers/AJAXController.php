<?php

namespace MobileContactBar\Controllers;

use MobileContactBar\Icons;
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
        'ajax_get_contact_field',
        'ajax_get_icon',
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
            && isset( $_POST['contact_key'] )
            && (int) $_POST['contact_key'] >= 0 )
        {
            $data = [];

            $contact_key = (int) $_POST['contact_key'];
            $contact_field = abmcb()->contact_types['link']->field();
    
            $data['summary'] = abmcb( Contacts\View::class )->output_summary(
                [
                    'contact_key' => $contact_key,
                    'contact'     => $contact_field,
                ]
            );

            $data['details'] = abmcb( Contacts\View::class )->output_details(
                [
                    'contact_key' => $contact_key,
                    'contact'     => $contact_field,
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
            && isset( $_POST['contact_key'], $_POST['parameter_key'] )
            && (int) $_POST['contact_key'] >= 0
            && (int) $_POST['parameter_key'] >= 0 )
        {
            $contact_key = (int) $_POST['contact_key'];
            $parameter_key = (int) $_POST['parameter_key'];

            $data = abmcb( Contacts\View::class )->output_link_parameter(
                [
                    'contact_key'    => $contact_key,
                    'parameter_type' => ['field' => 'text'],
                    'parameter_key'  => $parameter_key,
                    'parameter'      => ['key' => '', 'value' => ''],
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
    public function ajax_get_contact_field()
    {
        $contact_types = abmcb()->contact_types;

        if ( $this->verify_nonce()
            && isset( $_POST['contact_key'], $_POST['contact_type'] )
            && (int) $_POST['contact_key'] >= 0
            && in_array( $_POST['contact_type'], array_keys( $contact_types )))
        {
            $data = [];

            $contact_key = (int) $_POST['contact_key'];
            $contact_field = $contact_types[$_POST['contact_type']]->field();

            $data['contact_field'] = $contact_field;
            $data['uri'] = abmcb( Contacts\View::class )->output_details_uri(
                [
                    'contact_key'   => $contact_key,
                    'contact'       => $contact_field,
                    'contact_field' => $contact_field,
                ]
            );

            $data['parameters'] = abmcb( Contacts\View::class )->output_parameters(
                [
                    'contact_key'   => $contact_key,
                    'contact'       => $contact_field,
                    'contact_field' => $contact_field,
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
        if ( $this->verify_nonce() && isset( $_POST['brand'], $_POST['group'], $_POST['icon'] ))
        {
            clearstatcache();

            if ( 'ti' === $_POST['brand'] && '' === $_POST['group']
                && Icons::is_ti_icon( $_POST['icon'] )
                && file_exists( plugin_dir_path( abmcb()->file ) . 'assets/icons/ti/icons/'. $_POST['icon'] . '.svg' ))
            {
                $response = json_encode( file_get_contents( plugin_dir_path( abmcb()->file ) . 'assets/icons/ti/icons/'. $_POST['icon'] . '.svg' ));
                if ( $response )
                {
                    echo $response;
                }
            }
            elseif ( 'fa' === $_POST['brand']
                && Icons::is_fa_icon( $_POST['group'], $_POST['icon'] )
                && file_exists( plugin_dir_path( abmcb()->file ) . 'assets/icons/fa/svgs/' . $_POST['group'] . '/' . $_POST['icon'] . '.svg' ))
            {
                $response = json_encode( file_get_contents(  plugin_dir_path( abmcb()->file ) . 'assets/icons/fa/svgs/'. $_POST['group'] . '/' . $_POST['icon'] . '.svg' ));
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
