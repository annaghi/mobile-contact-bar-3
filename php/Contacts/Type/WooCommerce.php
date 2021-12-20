<?php

namespace MobileContactBar\Contacts\Type;

use MobileContactBar\Contacts\Input;


final class WooCommerce extends TypeAbstract
{
    public function __construct()
    {
        if( class_exists( 'WooCommerce' ))
        {
            parent::__construct();
            add_filter( 'mcb_public_add_badge', [$this, 'mcb_public_add_badge'], 10, 2 );

            if ( defined( 'WC_VERSION' ) && version_compare( WC_VERSION, '2.3', '>=' ))
            {
                add_filter( 'woocommerce_add_to_cart_fragments', [$this, 'woocommerce_add_to_cart_fragments'] );
            }
            else
            {
                add_filter( 'add_to_cart_fragments', [$this, 'woocommerce_add_to_cart_fragments'] );
            }
        }
    }


    public function contact()
    {
        return [
            'type'        => $this->type,
            'title'       => __( 'WooCommerce Cart', 'mobile-contact-bar' ),
            'checked'     => 0,
            'brand'       => 'fa',
            'icon'        => 'solid shopping-cart',
            'label'       => __( 'Cart', 'mobile-contact-bar' ),
            'uri'         => get_site_url() . '/cart',
            'placeholder' => is_ssl() ? 'https://mysite.com/cart' : 'http://mysite.com/cart',
            'palette'     => abmcb( Input::class )->palette_defaults(),
            'desc_type'   => __( 'type desc', 'mobile-contact-bar' ),
            'desc_uri'    => __( 'URI desc', 'mobile-contact-bar' ),
        ];
    }


    public function mcb_public_add_badge( $badge = '', $type )
    {
        return ( $type === $this->type )
            ? $this->badge()
            : '';
    }


    public function woocommerce_add_to_cart_fragments( $fragments )
    {
        global $woocommerce;

        $fragments['.mobile-contact-bar-badge'] = $this->badge();

        return $fragments;
    }


    private function badge()
    {
        if ( class_exists( 'WooCommerce' ))
        {
            $count = ( empty ( WC()->cart )) ? 0 : wp_kses_data( WC()->cart->get_cart_contents_count() );
            return sprintf( '<span class="mobile-contact-bar-badge">%d</span>', $count );
        }

        return '';
    }
}