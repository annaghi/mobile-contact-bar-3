<?php

namespace MobileContactBar\Controllers;

use MobileContactBar\Contacts;
use MobileContactBar\Icons;
use MobileContactBar\Option;
use stdClass;


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
        'ajax_post_option_bar',
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

            $data['query'] = abmcb( Contacts\View::class )->output_query(
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
            $ti_svg = plugin_dir_path( abmcb()->file ) . 'assets/svg/ti/icons/'. $_POST['icon'] . '.svg';
            $fa_svg = plugin_dir_path( abmcb()->file ) . 'assets/svg/fa/svgs/' . $_POST['group'] . '/' . $_POST['icon'] . '.svg';

            if ( 'ti' === $_POST['brand'] && '' === $_POST['group'] && Icons::is_ti_icon( $_POST['icon'] ) && file_exists( $ti_svg ))
            {
                $response = json_encode( file_get_contents( $ti_svg ));
                if ( $response )
                {
                    echo $response;
                }
            }
            elseif ( 'fa' === $_POST['brand'] && Icons::is_fa_icon( $_POST['group'], $_POST['icon'] ) && file_exists( $fa_svg ))
            {
                $response = json_encode( file_get_contents( $fa_svg ));
                if ( $response )
                {
                    echo $response;
                }
            }
        }
        wp_die();
    }


    /**
     * Updates option_bar.
     * 
     * @return void
     *
     * @uses $_POST
     */
    public function ajax_post_option_bar()
    {
        if ( $this->verify_nonce() && isset( $_POST['fields'] ))
        {
            $data = json_decode( stripslashes( $_POST['fields'] ),true );

	        if ( ! is_null( $data ) && $data )
            {
                $fields = [];
		        foreach ( $data as $datum )
                {
                    preg_match( '#([^\[]*)(\[(.+)\])?#', $datum['name'], $matches );

                    $array_bits = [];
                    
                    if ( isset( $matches[3] ))
                    {
                        $array_bits = explode( '][', $matches[3] );
                    }

                    $new_datum = [];

                    for ( $i = count( $array_bits ) - 1; $i >= 0; $i-- )
                    {
                        if ( count( $array_bits ) - 1 === $i )
                        {
                            $new_datum[$array_bits[$i]] = wp_slash( $datum['value'] );
                        }
                        else
                        {
                            $new_datum = [$array_bits[$i] => $new_datum];
                        }
                    }

                    $fields = array_replace_recursive( $fields, $new_datum );
                }
            }

            abmcb( Option::class )->update_option( $fields, abmcb()->id, 'sanitize_option_bar' );
            $option_bar = abmcb( Option::class )->get_option( abmcb()->id, 'sanitize_option_bar' );

            wp_send_json_success( $option_bar );
        }
        wp_send_json_error();
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
