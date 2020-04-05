<?php

defined( 'ABSPATH' ) or exit;


final class Mobile_Contact_Bar_Contact_Sample
{

    private static $type = 'Sample';

    public static function plugins_loaded()
    {}



    /**
     * Defines the list of mobile contacts
     *
     * @since 0.1.0
     *
     * @return array Associative array of contacts
     */
    public static function mcb_admin_add_contact()
    {
        $user = wp_get_current_user();

        return array(
            array(
                'checked'     => 0,
                'type'        => self::$type,
                'icon'        => 'fas fa-phone',
                'title'       => __( 'Phone Number for calling', 'mobile-contact-bar' ),
                'placeholder' => 'tel:+15417543010',
                'uri'         => '',
            ),
            array(
                'checked'     => 0,
                'type'        => self::$type,
                'icon'        => 'far fa-comment',
                'title'       => __( 'Phone Number for texting', 'mobile-contact-bar' ),
                'placeholder' => 'sms:+15417543010',
                'uri'         => '',
                'parameters'  => array(
                    array(
                        'key'         => 'body',
                        'type'        => 'text',
                        'placeholder' => __( 'Message ...', 'mobile-contact-bar' ),
                        'value'       => '',
                    ),
                ),
            ),
            array(
                'checked'     => 1,
                'type'        => self::$type,
                'icon'        => 'far fa-envelope',
                'title'       => __( 'Email Address', 'mobile-contact-bar' ),
                'placeholder' => 'mailto:username@example.com',
                'uri'         => 'mailto:' . $user->data->user_email,
                'parameters'  => array(
                    array(
                        'key'         => 'subject',
                        'type'        => 'text',
                        'placeholder' => __( 'Subject ...', 'mobile-contact-bar' ),
                        'value'       => '',
                    ),
                    array(
                        'key'         => 'body',
                        'type'        => 'textarea',
                        'placeholder' => __( 'Text ...', 'mobile-contact-bar' ),
                        'value'       => '',
                    ),
                    array(
                        'key'         => 'cc',
                        'type'        => 'email',
                        'placeholder' => __( 'example@domain.com', 'mobile-contact-bar' ),
                        'value'       => '',
                    ),
                    array(
                        'key'         => 'bcc',
                        'type'        => 'email',
                        'placeholder' => __( 'example1@domain.com,example2@domain.net', 'mobile-contact-bar' ),
                        'value'       => '',
                    ),
                ),
            ),
            array(
                'checked'     => 0,
                'type'        => self::$type,
                'icon'        => 'fab fa-whatsapp',
                'title'       => 'WhatsApp',
                'placeholder' => '', // contact with empty placeholder has non-editable URI
                'uri'         => 'https://api.whatsapp.com/send',
                'parameters'  => array(
                    array(
                        'key'         => 'phone',
                        'type'        => 'text',
                        'placeholder' => __( '15417543010', 'mobile-contact-bar' ),
                        'value'       => '',
                    ),
                    array(
                        'key'         => 'text',
                        'type'        => 'text',
                        'placeholder' => __( 'Message ...', 'mobile-contact-bar' ),
                        'value'       => '',
                    ),
                ),
            ),
            array(
                'checked'     => 0,
                'type'        => self::$type,
                'icon'        => 'fas fa-map-marker-alt',
                'title'       => 'Google Maps',
                'placeholder' => 'https://google.com/maps/place/Dacre+St,+London+UK/',
                'uri'         => '',
            ),
            array(
                'checked'     => 0,
                'type'        => self::$type,
                'icon'        => 'fab fa-facebook-f',
                'title'       => 'Facebook',
                'placeholder' => 'https://www.facebook.com/username',
                'uri'         => '',
            ),
            array(
                'checked'     => 0,
                'type'        => self::$type,
                'icon'        => 'fab fa-linkedin-in',
                'title'       => 'LinkedIn',
                'placeholder' => 'https://www.linkedin.com/in/username',
                'uri'         => '',
            ),
            array(
                'checked'     => 0,
                'type'        => self::$type,
                'icon'        => 'fab fa-twitter',
                'title'       => 'Twitter',
                'placeholder' => 'https://twitter.com/username',
                'uri'         => '',
            ),
            array(
                'checked'     => 0,
                'type'        => self::$type,
                'icon'        => 'fab fa-google-plus-g',
                'title'       => 'Google+',
                'placeholder' => 'https://plus.google.com/username',
                'uri'         => '',
            ),
            array(
                'checked'     => 0,
                'type'        => self::$type,
                'icon'        => 'fab fa-instagram',
                'title'       => 'Instagram',
                'placeholder' => 'https://www.instagram.com/username',
                'uri'         => '',
            ),
            array(
                'checked'     => 0,
                'type'        => self::$type,
                'icon'        => 'fab fa-youtube',
                'title'       => 'YouTube',
                'placeholder' => 'https://www.youtube.com/user/username',
                'uri'         => '',
            ),
            array(
                'checked'     => 0,
                'type'        => self::$type,
                'icon'        => 'fab fa-pinterest-p',
                'title'       => 'Pinterest',
                'placeholder' => 'https://www.pinterest.com/username',
                'uri'         => '',
            ),
            array(
                'checked'     => 0,
                'type'        => self::$type,
                'icon'        => 'fab fa-tumblr',
                'title'       => 'Tumblr',
                'placeholder' => 'https://username.tumblr.com',
                'uri'         => '',
            ),
            array(
                'checked'     => 0,
                'type'        => self::$type,
                'icon'        => 'fab fa-vimeo-v',
                'title'       => 'Vimeo',
                'placeholder' => 'https://vimeo.com/username',
                'uri'         => '',
            ),
            array(
                'checked'     => 0,
                'type'        => self::$type,
                'icon'        => 'fab fa-flickr',
                'title'       => 'Flickr',
                'placeholder' => 'https://www.flickr.com/people/username',
                'uri'         => '',
            ),
            array(
                'checked'     => 0,
                'type'        => self::$type,
                'icon'        => 'fab fa-skype',
                'title'       => __( 'Skype for calling', 'mobile-contact-bar' ),
                'placeholder' => 'skype:username?call',
                'uri'         => '',
            ),
            array(
                'checked'     => 0,
                'type'        => self::$type,
                'icon'        => 'fab fa-skype',
                'title'       => __( 'Skype for chatting', 'mobile-contact-bar' ),
                'placeholder' => 'skype:username?chat',
                'uri'         => '',
            ),
        );
    }
}
