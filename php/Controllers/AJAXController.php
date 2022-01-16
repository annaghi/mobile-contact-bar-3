<?php

namespace MobileContactBar\Controllers;

use MobileContactBar\Buttons;
use MobileContactBar\Icons;


final class AJAXController
{
    /**
     * Accepted administrative AJAX actions.
     *
     * @var array
     */
    public $admin_actions = [
        'ajax_get_button',
        'ajax_get_parameter',
        'ajax_get_button_field',
        'ajax_get_icon',
    ];


    /**
     * Sends HTML for a new button with type 'link'.
     * 
     * @return void
     *
     * @uses $_POST
     */
    public function ajax_get_button()
    {
        if ( $this->verify_nonce()
            && isset( $_POST['button_key'] )
            && (int) $_POST['button_key'] >= 0 )
        {
            $data = [];

            $button_key = (int) $_POST['button_key'];
            $button_field = abmcb()->button_types['link']->field();
    
            $data['summary'] = abmcb( Buttons\View::class )->output_summary(
                [
                    'button_key' => $button_key,
                    'button'     => $button_field,
                ]
            );

            $data['details'] = abmcb( Buttons\View::class )->output_details(
                [
                    'button_key' => $button_key,
                    'button'     => $button_field,
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
            && isset( $_POST['button_key'], $_POST['parameter_key'] )
            && (int) $_POST['button_key'] >= 0
            && (int) $_POST['parameter_key'] >= 0 )
        {
            $button_key = (int) $_POST['button_key'];
            $parameter_key = (int) $_POST['parameter_key'];

            $data = abmcb( Buttons\View::class )->output_link_parameter(
                [
                    'button_key'     => $button_key,
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
     * Sends HTML and button_type for the selected button type.
     * 
     * @return void
     *
     * @uses $_POST
     */
    public function ajax_get_button_field()
    {
        $button_types = abmcb()->button_types;

        if ( $this->verify_nonce()
            && isset( $_POST['button_key'], $_POST['button_type'] )
            && (int) $_POST['button_key'] >= 0
            && in_array( $_POST['button_type'], array_keys( $button_types )))
        {
            $data = [];

            $button_key = (int) $_POST['button_key'];
            $button_field = $button_types[$_POST['button_type']]->field();

            $data['button_field'] = $button_field;
            $data['uri'] = abmcb( Buttons\View::class )->output_details_uri(
                [
                    'button_key'   => $button_key,
                    'button'       => $button_field,
                    'button_field' => $button_field,
                ]
            );

            $data['query'] = abmcb( Buttons\View::class )->output_query(
                [
                    'button_key'   => $button_key,
                    'button'       => $button_field,
                    'button_field' => $button_field,
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
