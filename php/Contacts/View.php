<?php

namespace MobileContactBar\Contacts;

use MobileContactBar\Renderer;
use MobileContactBar\Helper;


final class View
{
    public $option_bar = [];

    /**
     * Adds Contact List metabox to the options page.
     * 
     * @param array $option_bar
     */
    public function add( $option_bar = [] )
    {
        $this->option_bar = $option_bar;

        add_settings_section(
            'mcb-section-contacts',
            __( 'Contact List', 'mobile-contact-bar' ),
            false,
            abmcb()->id
        );
    }


    /**
     * Renders template HTML elements for the Icon Picker.
     */
    public function icon_picker_html_template()
    {
        ?>
        <script type="text/html" id="mcb-tmpl-icon-picker">
            <div id="mcb-icon-picker-container">
                <div class="mcb-icon-picker-brands">
                    <button type="button" data-brand="fa" class="button">Font Awesome</button>
                    <button type="button" data-brand="ti" class="button mcb-icon-brand-active">Tabler Icons</button>
                </div>
                <div class="icon-picker-control">
                    <a data-direction="back" href="#">
                        <i class="fas fa-angle-left fa-lg"></i>
                    </a>
                    <input type="text" class="" placeholder="<?php esc_attr_e( 'Search', 'mobile-contact-bar' ); ?>">
                    <a data-direction="forward" href="#">
                        <i class="fas fa-angle-right fa-lg"></i>
                    </a>
                </div>
                <!-- <ul data-brand="fa">
                    <?php
                    $path = plugin_dir_url( abmcb()->file ) . 'dist/icons/fa/sprites/';
                    $icons = Input::fa_icons();
                    foreach ( $icons as $section_id => $section ) :
                        foreach ( $section as $icon ) :
                            $title = $section_id . ' fa-' . $icon;
                            ?>
                            <li data-icon="<?php echo $icon; ?>">
                                <a href="#" title="<?php echo $icon; ?>">
                                    <svg class="mcb-icon">
                                        <use xlink:href="<?php echo $path . $section_id; ?>.svg#<?php echo $icon; ?>"></use>
                                    </svg>
                                </a>
                            </li>
                            <?php
                        endforeach;
                    endforeach;
                    ?>
                </ul> -->
                <ul>
                    <?php
                    $path = plugin_dir_url( abmcb()->file ) . 'dist/icons/ti/tabler-sprite.svg';
                    $icons = array_slice( Input::ti_icons(), 0, 30 );
                    foreach ( $icons as $icon ) :
                        $title = 'ti ti-' . $icon;
                        ?>
                        <li data-icon="<?php echo $icon; ?>">
                            <a href="#" title="<?php echo $icon; ?>">
                                <svg class="mcb-icon">
                                    <use xlink:href="<?php echo $path; ?>#<?php echo 'tabler-' . $icon; ?>"></use>
                                </svg>
                            </a>
                        </li>
                        <?php
                    endforeach;
                    ?>
                </ul>
            </div>
        </script>
        <?php
    }


    /**
     * Renders the contact list.
     */
    public function output_contact_list()
    {
        $settings = $this->option_bar['settings'];
        $contacts = $this->option_bar['contacts'];

        ?>
        <div id="mcb-table-contacts">
            <div id="mcb-contacts">
            <?php foreach ( $this->option_bar['contacts'] as $contact_id => $contact ) { ?>
                <div class="mcb-contact" data-contact-id="<?php echo $contact_id; ?>">
                <?php
                    echo $this->output_summary( ['contact_id' => $contact_id, 'contact' => $contact] );
                    echo $this->output_details( ['contact_id' => $contact_id, 'contact' => $contact] );
                ?>
                </div>
            <?php } ?>
            </div>
            <div id="mcb-footer-contacts">
                <button type="button" id="mcb-add-contact" title="<?php echo esc_attr__( 'Add New Contact', 'mobile-contact-bar' ); ?>">
                    <i class="fas fa-plus fa-fw" aria-hidden="true"></i>
                    <span class="mcb-add-contact-label"><?php echo esc_attr__( 'New Contact', 'mobile-contact-bar' ); ?></span>
                </button>
            </div>
        </div>
        <?php
    }


    /**
     * Outputs contact summary.
     *
     * @param  array  $args
     * 	       string $contact_id
     * 	       array  $contact
     * @return string             HTML
     */
    private function output_summary( $args )
    {
        extract( $args );

        $out = '';

        $prefix = abmcb()->id . '[contacts][' . esc_attr( $contact_id ) . ']';

        $out .= sprintf(
            '<div class="mcb-summary%s">',
            ( $contact['checked'] ) ? ' mcb-checked' : ''
        );
        
        $out .= '<div class="mcb-left-actions">';

        // draggable
        $out .= sprintf(
            '<div class="mcb-sortable-draggable ui-sortable-handle" title="%s">
                <i class="fas fa-grip-vertical"></i>
            </div>',
            esc_attr__( 'Drag and drop to reorder', 'mobile-contact-bar' )
        );

        // 'checkbox' input
        $out .= sprintf(
            '<div class="mcb-summary-checkbox">
                <input type="checkbox" name="' . $prefix . '[checked]" value="1" %s>
            </div>',
            ( $contact['checked'] ) ? checked( $contact['checked'], 1, false ) : ''
        );

        $out .= '</div>';

        $out .= '<div class="mcb-summary-icon-label">';

        // 'icon'
        if ( 'fa' === $contact['brand'] && abmcb( Input::class )->in_fa_icons( $contact['icon'] ))
        {
            $names = explode( ' ', $contact['icon'] );
            $path = plugin_dir_url( abmcb()->file ) . 'dist/icons/fa/svgs/' . $names[0] . '/' . $names[1] . '.svg';
            $svg = file_get_contents( $path );
            $sanititzed_svg = preg_replace( '/<!--[^>]*-->/', '', $svg );
            $out .= sprintf( '<div class="mcb-summary-icon">%s</div>', $sanititzed_svg );
        }
        elseif ( 'ti' === $contact['brand'] && abmcb( Input::class )->in_ti_icons( $contact['icon'] ))
        {
            $path = plugin_dir_url( abmcb()->file ) . 'dist/icons/ti/icons/'. $contact['icon'] . '.svg';
            $svg = file_get_contents( $path );
            $out .= sprintf( '<div class="mcb-summary-icon">%s</div>', $svg );
        }
        else
        {
            $out .= '<div class="mcb-summary-icon mcb-blank-icon">---</div>';
        }

        // 'label'
        $out .= sprintf( '<div class="mcb-summary-label">%s</div>', esc_attr( $contact['label'] ));

        $out .= '</div>';

        // 'URI'
        $out .= sprintf(
            '<div class="mcb-summary-uri">%s</div>',
            Validator::escape_contact_uri( $contact['uri'] )
        );

        $out .= '<div class="mcb-right-actions">';

        $out .= sprintf(
            '<button type="button" class="mcb-action-icon mcb-action-toggle-details" title="%1$s">
                <i class="fas fa-edit" aria-expanded="false" aria-hidden="true"></i>
                <span class="screen-reader-text">%1$s</span>
            </button>',
            esc_attr__( 'Edit contact details', 'mobile-contact-bar' )
        );

        $out .= sprintf(
            '<button type="button" class="mcb-action-icon mcb-action-delete-contact" title="%1$s">
                <i class="fas fa-times" aria-hidden="true"></i>
                <span class="screen-reader-text">%1$s</span>
            </button>',
            esc_attr__( 'Delete this contact', 'mobile-contact-bar' )
        );

        $out .= sprintf(
            '<button type="button" class="mcb-action-icon mcb-action-order-higher" title="%1$s">
                <i class="fas fa-angle-up" aria-hidden="true"></i>
                <span class="screen-reader-text">%1$s</span>
            </button>',
            esc_attr__( 'Order contact higher', 'mobile-contact-bar' )
        );

        $out .= sprintf(
            '<button type="button" class="mcb-action-icon mcb-action-order-lower" title="%1$s">
                <i class="fas fa-angle-down" aria-hidden="true"></i>
                <span class="screen-reader-text">%1$s</span>
            </button>',
            esc_attr__( 'Order contact lower', 'mobile-contact-bar' )
        );

        $out .= '</div>';
        $out .= '</div>';

        return $out;
    }


    /**
     * Outputs editable contact details.
     *
     * @param  array  $args
     * 	       string $contact_id
     * 	       array  $contact
     * @return string             HTML
     */
    private function output_details( $args )
    {
        extract( $args );

        $out = '';

        $prefix = abmcb()->id . '[contacts][' . esc_attr( $contact_id ) . ']';

        $contact_types = apply_filters( 'mcb_admin_contact_types', [] );
        $contact_type  = $contact_types[$contact['type']];

        $out .= '<div class="mcb-details">';

        // 'brand' hidden
        $out .= sprintf( '<input type="hidden" name="' . $prefix . '[brand]" value="%s">', esc_attr( $contact['brand'] ));

        // 'icon' hidden
        $out .= sprintf( '<input type="hidden" name="' . $prefix . '[icon]" value="%s">', esc_attr( $contact['icon'] ));

        // 'type' input
        $select = '<select name="' . $prefix . '[type]" id="' . $prefix . '[type]">';
        foreach ( $contact_types as $contact_type_id => $contact_types )
        {
            $select .= sprintf(
                '<option value="%s" %s>%s</option>',
                esc_attr( $contact_type_id ),
                selected( $contact_type_id, $contact['type'], false ),
                esc_attr( $contact_types['title'] )
            );
        }
        $select .= '</select>';
        
        $out .= sprintf(
            '<div class="mcb-row mcb-details-type">
                <div class="mcb-label">
                    <label for="' . $prefix . '[type]">%s</label>
                    <p class="mcb-description">%s</p>
                </div>
                <div class="mcb-input">%s</div>
            </div>',
            esc_attr__( 'Contact Type', 'mobile-contact-bar' ),
            esc_attr( $contact_type['desc_type'] ),
            $select,
            esc_attr( $contact['type'] )
        );

        // 'icon' visible
        if ( 'fa' === $contact['brand'] && abmcb( Input::class )->in_fa_icons( $contact['icon'] ))
        {
            $meta = explode( ' ', $contact['icon'] );
            $path = plugin_dir_url( abmcb()->file ) . 'dist/icons/fa/svgs/' . $meta[0] . '/' . $meta[1] . '.svg';
            $svg = file_get_contents( $path );
            $sanititzed_svg = preg_replace( '/<!--[^>]*-->/', '', $svg );
            $icon = sprintf( '<span>%s</span>', $sanititzed_svg );
        }
        elseif ( 'ti' === $contact['brand'] && abmcb( Input::class )->in_ti_icons( $contact['icon'] ))
        {
            $path = plugin_dir_url( abmcb()->file ) . 'dist/icons/ti/icons/'. $contact['icon'] . '.svg';
            $svg = file_get_contents( $path );
            $icon = sprintf( '<span>%s</span>', $svg );
        }
        else
        {
            $icon = '<span class="mcb-blank-icon">---</span>';
        }

        // select & clear 'icon' button
        $out .= sprintf(
            '<div class="mcb-row mcb-details-icon">
                <div class="mcb-label">
                    <label>%1$s</label>
                </div>
                <div class="mcb-input">
                    %2$s
                    <button type="button" class="button action mcb-action-pick-icon" title="%3$s">%3$s</button>
                    <button type="button" class="button action mcb-action-clear-icon" title="%4$s">%4$s</button>
                </div>
            </div>',
            esc_attr__( 'Contact Icon', 'mobile-contact-bar' ),
            $icon,
            esc_attr__( 'Select Icon', 'mobile-contact-bar' ),
            esc_attr__( 'Clear Icon', 'mobile-contact-bar' )
        );

        // 'label' input
        $out .= sprintf(
            '<div class="mcb-row mcb-details-label">
                <div class="mcb-label">
                    <label for="' . $prefix . '[label]">%s</label>
                    <p class="mcb-description">%s</p>
                </div>
                <div class="mcb-input">
                    <input type="text" name="' . $prefix . '[label]" id="' . $prefix . '[label]" value="%s">
                </div>
            </div>',
            esc_attr__( 'Contact Label', 'mobile-contact-bar' ),
            esc_attr__( 'Use \n for new lines' ),
            esc_html( $contact['label'] )
        );

        $out .= $this->output_details_uri( ['contact_id' => $contact_id, 'contact' => $contact, 'contact_type' => $contact_type] );
        $out .= $this->output_parameters( ['contact_id' => $contact_id, 'contact' => $contact, 'contact_type' => $contact_type] );
        $out .= $this->output_palette( ['contact_id' => $contact_id, 'contact' => $contact, 'contact_type' => $contact_type] );

        $out .= '</div>';

        return $out;
    }



    private function output_details_uri( $args )
    {
        extract( $args );

        $out = '';

        $prefix = abmcb()->id . '[contacts][' . esc_attr( $contact_id ) . ']';

        if ( 'scrolltotop' === $contact['type'] )
        {
            // 'URI' hidden
            $out .= sprintf(
                '<input type="hidden" name="' . $prefix . '[uri]" value="%1$s">',
                Validator::escape_contact_uri( $contact['uri'] )
            );

            // 'URI' visible
            $out .= sprintf(
                '<div class="mcb-row mcb-details-uri">
                    <div class="mcb-label">
                        <label>%s</label>
                        <p class="mcb-description">%s</p>
                    </div>
                    <div class="mcb-input">#</div>
                </div>',
                esc_attr__( 'Contact URI', 'mobile-contact-bar' ),
                esc_attr( $contact_type['desc_uri'] )
            );
        }
        else
        {
            // 'URI' input
            $out .= sprintf(
                '<div class="mcb-row mcb-details-uri">
                    <div class="mcb-label">
                        <label for="' . $prefix . '[uri]">%s</label>
                        <p class="mcb-description">%s</p>
                    </div>
                    <div class="mcb-input">
                        <input type="text" name="' . $prefix . '[uri]" id="' . $prefix . '[uri]" placeholder="%s" value="%s">
                    </div>
                </div>',
                esc_attr__( 'Contact URI', 'mobile-contact-bar' ),
                esc_attr( $contact_type['desc_uri'] ),
                esc_attr( $contact_type['placeholder'] ),
                Validator::escape_contact_uri( $contact['uri'] )
            );
        }

        return $out;
    }


    /**
     * Outputs parameters head row.
     *
     * @param  array  $args
     * 	       string $contact_id
     * 	       array  $contact
     * @return string             HTML
     */
    private function output_parameters( $args )
    {
        extract( $args );

        $out = '';

        if ( 'link' === $contact['type'] && isset( $contact['parameters'] ) && is_array( $contact['parameters'] ))
        {
            $out .= sprintf(
                '<div class="mcb-row mcb-custom-parameters">
                    <div class="mcb-label">
                        <label>%s</label>
                    </div>
                    <div class="mcb-input">
                        <button type="button" class="button action mcb-add-parameter" title="%2$s">%2$s</button>
                    </div>
                </div>',
                esc_attr__( 'Query String Parameters', 'mobile-contact-bar' ),
                esc_attr__( 'Add Parameter', 'mobile-contact-bar' )
            );
            
            foreach ( $contact['parameters'] as $parameter_id => $parameter )
            {
                $out .= $this->output_link_parameter(
                    [
                        'contact_id'     => $contact_id,
                        'parameter_type' => ['field' => 'text'],
                        'parameter_id'   => $parameter_id,
                        'parameter'      => $parameter
                    ]
                );
            }
        }
        elseif ( isset( $contact['parameters'] ) && is_array( $contact['parameters'] ))
        {
            $out .= sprintf(
                '<div class="mcb-row mcb-builtin-parameters">
                    <div class="mcb-label">
                        <label>%s</label>
                    </div>
                    <div class="mcb-input"></div>
                </div>',
                esc_attr__( 'Query String Parameters', 'mobile-contact-bar' )
            );

            foreach ( $contact['parameters'] as $parameter_id => $parameter )
            {
                $parameter_index = array_search( $parameter['key'], array_column( $contact_type['parameters'], 'key' ));
                $parameter_type = $contact_type['parameters'][$parameter_index];
                $out .= $this->output_builtin_parameter(
                    [
                        'contact_id'     => $contact_id,
                        'parameter_type' => $parameter_type,
                        'parameter_id'   => $parameter_id,
                        'parameter'      => $parameter
                    ]
                );
            }
        }

        return $out;
    }

    
    /**
     * Outputs parameter with editable key-value.
     *
     * @param  array  $args
     *         string $contact_id
     * 	       string $parameter_id
     * 	       array  $parameter_key
     * @return string                HTML
     */
    private function output_link_parameter( $args )
    {
        extract( $args );

        $out = '';

        $prefix = abmcb()->id . '[contacts][' . esc_attr( $contact_id ) . '][parameters][' . esc_attr( $parameter_id ) . ']';

        $out .= sprintf(
            '<div class="mcb-custom-parameter" data-parameter-id="%d">',
            esc_attr( $parameter_id )
        );
    
        // 'key' input
        $out .= sprintf(
            '<div class="mcb-row mcb-parameter-key">
                <div class="mcb-label">
                    <label for="' . $prefix . '[key]">%s</label>
                    <p class="mcb-description">%s</p>
                </div>
                <div class="mcb-input">
                    <input type="text" name="' . $prefix . '[key]" id="' . $prefix . '[key]" placeholder="%s" value="%s">
                    <button type="button" class="mcb-action-icon mcb-action-delete-parameter" title="%5$s">
                        <i class="fas fa-times" aria-hidden="true"></i>
                        <span class="screen-reader-text">%5$s</span>
                    </button>
                </div>
            </div>',
            esc_attr__( 'Query String Key', 'mobile-contact-bar' ),
            esc_attr__( 'Query String Key', 'mobile-contact-bar' ),
            esc_attr__( 'key', 'mobile-contact-bar' ),
            esc_attr( $parameter['key'] ),
            esc_attr__( 'Delete this parameter', 'mobile-contact-bar' )
        );

        // 'value' input
        switch ( $parameter_type['field'] )
        {
            case 'text':
            case 'email':
                $out .= sprintf(
                    '<div class="mcb-row mcb-parameter-value">
                        <div class="mcb-label">
                            <label for="' . $prefix . '[value]">%s</label>
                            <p class="mcb-description">%s</p>
                        </div>
                        <div class="mcb-input">
                            <input type="text" name="' . $prefix . '[value]" id="' . $prefix . '[value]" placeholder="%s" value="%s">
                        </div>
                    </div>',
                    esc_attr__( 'Query String Value', 'mobile-contact-bar' ),
                    esc_attr__( 'Query String Value', 'mobile-contact-bar' ),
                    esc_attr__( 'value', 'mobile-contact-bar' ),
                    esc_attr( $parameter['value'] )
                );
                break;

            case 'textarea':
                $out .= sprintf(
                    '<div class="mcb-row mcb-parameter-value">
                        <div class="mcb-label">
                            <label for="' . $prefix . '[value]">%s</label>
                            <p class="mcb-description">%s</p>
                        </div>
                        <div class="mcb-input">
                            <textarea name="' . $prefix . '[value]" id="' . $prefix . '[value]" placeholder="%s">%s</textarea>
                        </div>
                    </div>',
                    esc_attr__( 'Query String Value', 'mobile-contact-bar' ),
                    esc_attr__( 'Query String Value', 'mobile-contact-bar' ),
                    esc_attr__( 'value', 'mobile-contact-bar' ),
                    esc_textarea( $parameter['value'] )
                );
                break;
        }

        $out .= '</div>';

        return $out;
    }


    /**
     * Outputs parameter with editable value.
     *
     * @param  array  $args
     *         string $contact_id
     * 	       string $parameter_id
     * 	       array  $parameter_key
     * @return string                HTML
     */
    private function output_builtin_parameter( $args )
    {
        extract( $args );

        $out = '';

        $prefix = abmcb()->id . '[contacts][' . esc_attr( $contact_id ) . '][parameters][' . esc_attr( $parameter_id ) . ']';

        $out .= '<div class="mcb-builtin-parameter">';

        // 'key' hidden
        $out .= sprintf(
            '<input type="hidden" name="' . $prefix . '[key]" value="%s">',
            esc_attr( $parameter['key'] )
        );

        // 'value' input
        switch ( $parameter_type['field'] )
        {
            case 'text':
            case 'email':
                $out .= sprintf(
                    '<div class="mcb-row mcb-parameter-value">
                        <div class="mcb-label">
                            <label for="' . $prefix . '[value]">%s</label>
                            <p class="mcb-description">%s</p>
                        </div>
                        <div class="mcb-input">
                            <span>%s</span>
                            <input type="text" name="' . $prefix . '[value]" id="' . $prefix . '[value]" placeholder="%s" value="%s">
                        </div>
                    </div> ',
                    esc_attr__( 'Query String Value', 'mobile-contact-bar' ),
                    esc_attr__( 'Query String Value', 'mobile-contact-bar' ),
                    esc_attr( $parameter['key'] ),
                    esc_attr( $parameter_type['placeholder'] ),
                    esc_attr( $parameter['value'] )
                );
                break;

            case 'textarea':
                $out .= sprintf(
                    '<div class="mcb-row mcb-parameter-value">
                        <div class="mcb-label">
                            <label for="' . $prefix . '[value]">%s</label>
                            <p class="mcb-description">%s</p>
                        </div>
                        <div class="mcb-input">
                            <span>%s</span>
                            <textarea name="' . $prefix . '[value]" id="' . $prefix . '[value]" placeholder="%s">%s</textarea>
                        </div>
                    </div>',
                    esc_attr__( 'Query String Value', 'mobile-contact-bar' ),
                    esc_attr__( 'Query String Value', 'mobile-contact-bar' ),
                    esc_attr( $parameter['key'] ),
                    esc_attr( $parameter_type['placeholder'] ),
                    esc_textarea( $parameter['value'] )
                );
                break;
        }

        $out .= '</div>';

        return $out;
    }


    /**
     * Outputs the CSS ID selector input and custom colors of the contact.
     *
     * @param  array  $args
     * 	       string $contact_id
     * 	       array  $contact
     * @return string             HTML
     */
    private function output_palette( $args )
    {
        extract( $args );

        $out = '';

        $prefix = abmcb()->id . '[contacts][' . esc_attr( $contact_id ) . ']';
        $palette_fields = abmcb( Input::class )->palette();

        // 'id' input
        $out .= sprintf(
            '<div class="mcb-row mcb-details-id">
                <div class="mcb-label">
                    <label for="' . $prefix . '[id]">%s</label>
                    <p class="mcb-description">%s</p>
                </div>
                <div class="mcb-input">
                    <input type="text" name="' . $prefix . '[id]" id="' . $prefix . '[id]" value="%s">
                </div>
            </div>',
            esc_attr__( 'CSS ID selector', 'mobile-contact-bar' ),
            preg_match( '/^mcb-sample-id-[0-9]+/', $contact['id'] )
                ? esc_attr__( 'This is a generated ID, do not rely on unique numbers. Change it to your needs.', 'mobile-contact-bar' )
                : esc_attr__( 'Unique identifier. Used when colors are specified.', 'mobile-contact-bar' ),
            esc_attr( $contact['id'] )
        );

        // 'color' inputs
        foreach( $palette_fields as $section_id => $section )
        {
            $out .= sprintf(
                '<div class="mcb-row mcb-palette mcb-palette-%4$s">
                    <div class="mcb-label">
                        <label>%1$s</label>
                        <p class="mcb-description">%2$s</p>
                    </div>
                    <div class="mcb-input">
                        <div class="mcb-palette-colors">
                            <span>%3$s</span>
                            <input type="text" class="color-picker" name="' . $prefix . '[palette][%4$s][primary]" data-alpha-enabled="true" value="%5$s">
                        </div>
                        <div class="mcb-palette-colors">
                            <span>%6$s</span>
                            <input type="text" class="color-picker" name="' . $prefix . '[palette][%4$s][secondary]" data-alpha-enabled="true" value="%7$s">
                        </div>
                    </div>
                </div>',
                esc_attr( $section['title'] ),
                esc_attr__( 'Long', 'mobile-contact-bar' ),
                esc_attr__( 'primary', 'mobile-contact-bar' ),
                esc_attr( $section_id ),
                esc_attr( $contact['palette'][$section_id]['primary'] ),
                esc_attr__( 'secondary', 'mobile-contact-bar' ),
                esc_attr( $contact['palette'][$section_id]['secondary'] )
            );
        }

        return $out;
    }


    /**
     * Renders a contact.
     *
     * @uses $_POST
     */
    public function ajax_add_contact()
    {
        if ( isset( $_POST['contact_id'] ) && (int) $_POST['contact_id'] >= 0 )
        {
            $data = [];

            $contact_types = apply_filters( 'mcb_admin_contact_types', [] );
            $contact = $contact_types['link'];
    
            $data['summary'] = $this->output_summary( ['contact_id' => $_POST['contact_id'], 'contact' => $contact] );
            $data['details'] = $this->output_details( ['contact_id' => $_POST['contact_id'], 'contact' => $contact] );
    
            return $data;	
        }

        wp_die();
    }


    /**
     * Renders a parameter.
     *
     * @uses $_POST
     */
    public function ajax_add_parameter()
    {
        if ( isset( $_POST['contact_id'], $_POST['parameter_id'] ) && (int) $_POST['contact_id'] >= 0 && (int) $_POST['parameter_id'] >= 0 )
        {
            return $this->output_link_parameter(
                [
                    'contact_id'     => $_POST['contact_id'],
                    'parameter_type' => ['field' => 'text'],
                    'parameter_id'   => $_POST['parameter_id'],
                    'parameter'      => ['key' => '', 'value' => '']
                ]
            );
        }

        wp_die();		
    }


    /**
     * Renders contact type related parameters.
     *
     * @uses $_POST
     */
    public function ajax_change_contact_type()
    {
        $contact_types = array_keys( abmcb()->contact_types );

        if ( isset( $_POST['contact_id'], $_POST['contact_type'] ) && (int) $_POST['contact_id'] >= 0 && in_array( $_POST['contact_type'], $contact_types ))
        {
            $data = [];

            $contact_type = abmcb()->contact_types[$_POST['contact_type']]->contact();

            $data['contact'] = $this->output_details_uri( ['contact_id' => $_POST['contact_id'], 'contact' => $contact_type, 'contact_type' => $contact_type] );
            $data['parameters'] = $this->output_parameters( ['contact_id' => $_POST['contact_id'], 'contact' => $contact_type, 'contact_type' => $contact_type] );

            return $data;
        }

        wp_die();
    }


    /**
     * Renders contact type related parameters.
     *
     * @uses $_POST
     */
    public function ajax_get_icon()
    {
        if ( isset( $_POST['brand'], $_POST['icon'] ) && in_array( $_POST['brand'], ['fa', 'ti'] ))
        {
            if ( 'ti' === $_POST['brand'] && abmcb( Input::class )->in_ti_icons( $_POST['icon'] ))
            {
                $path = plugin_dir_url( abmcb()->file ) . 'dist/icons/ti/icons/'. $_POST['icon'] . '.svg';
                $svg = file_get_contents( $path );

                return $svg;
            }
        }

        wp_die();
    }
}
