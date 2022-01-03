<?php

namespace MobileContactBar\Contacts;

use MobileContactBar\Icons;
use MobileContactBar\ContactTypes;


final class View
{
    /**
     * Adds Contact List meta box to the options page.
     * 
     * @return void
     */
    public function add()
    {
        add_settings_section(
            'mcb-meta-box-contacts',
            __( 'Contact List', 'mobile-contact-bar' ),
            false,
            abmcb()->id
        );
    }


    /**
     * Renders template HTML for the Icon Picker.
     * 
     * @return void
     */
    public function render_icon_picker_template()
    {
        ?>
        <script type="text/html" id="mcb-tmpl-icon-picker">
            <div id="mcb-icon-picker-container">
                <div class="mcb-icon-picker-brands">
                    <button type="button" data-brand="ti" class="button mcb-brand-active">Tabler Icons</button>
                    <button type="button" data-brand="fa" class="button">Font Awesome</button>
                </div>
                <div class="icon-picker-control">
                    <a data-direction="back" href="#">
                        <span class="dashicons dashicons-arrow-left-alt2" aria-hidden="true"></span>
                    </a>
                    <input type="text" class="" placeholder="<?php esc_attr_e( 'Search', 'mobile-contact-bar' ); ?>">
                    <a data-direction="forward" href="#">
                        <span class="dashicons dashicons-arrow-right-alt2" aria-hidden="true"></span>
                    </a>
                </div>
                <ul>
                    <?php
                    $sprite = plugin_dir_url( abmcb()->file ) . 'assets/svg/ti/tabler-sprite.svg';
                    $icons = array_slice( Icons::ti_icons(), 0, 30 );
                    foreach ( $icons as $icon ) :
                    ?>
                        <li data-icon="<?php echo $icon; ?>">
                            <a href="#" title="<?php echo $icon; ?>">
                                <svg class="mcb-icon">
                                    <use xlink:href="<?php echo $sprite, '#', 'tabler-', $icon; ?>"></use>
                                </svg>
                            </a>
                        </li>
                    <?php endforeach; ?>
                </ul>
            </div>
        </script>
        <?php
    }


    /**
     * Renders the contact list.
     * 
     * @return void
     */
    public function render_contacts()
    {
        $settings = abmcb()->option_bar['settings'];
        $contacts = abmcb()->option_bar['contacts'];

        ?>
        <div id="mcb-section-contacts">
            <div id="mcb-contacts">
            <?php foreach ( $contacts as $contact_key => $contact ) : ?>
                <div class="mcb-contact<?php echo ( $contact['checked'] ) ? ' mcb-checked' : ''?>" data-contact-key="<?php echo $contact_key; ?>">
                <?php
                    echo $this->output_summary( ['contact_key' => $contact_key, 'contact' => $contact] );
                    echo $this->output_details( ['contact_key' => $contact_key, 'contact' => $contact] );
                ?>
                </div>
            <?php endforeach; ?>
            </div>
            <div id="mcb-footer-contacts">
                <button type="button" class="button button-primary" id="mcb-add-contact" title="<?php echo esc_attr__( 'Add New Contact', 'mobile-contact-bar' ); ?>">
                    <span class="dashicons dashicons-plus-alt2" aria-hidden="true"></span>
                    <span><?php echo esc_attr__( 'New Contact', 'mobile-contact-bar' ); ?></span>
                </button>
            </div>
        </div>
        <?php
    }


    /**
     * Outputs contact summary.
     *
     * @param  array  $args
     * 	       string $contact_key
     * 	       array  $contact
     * @return string              HTML
     */
    public function output_summary( $args )
    {
        extract( $args );

        $out = '';

        $prefix = abmcb()->id . '[contacts][' . esc_attr( $contact_key ) . ']';

        $out .= '<div class="mcb-summary">';

        $out .= '<div class="mcb-left-actions">';

        // draggable
        $out .= sprintf(
            '<div class="mcb-sortable-draggable ui-sortable-handle" title="%s">
                <span class="dashicons dashicons-move" aria-hidden="true"></span>
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

        $out .= '<div class="mcb-summary-cta">';

        // 'icon'
        if ( 'ti' === $contact['brand'] )
        {
            $out .= sprintf(
                '<div class="mcb-summary-brand">%s</div><div class="mcb-summary-icon">%s</div>',
                strtoupper( $contact['brand'] ),
                file_get_contents( plugin_dir_path( abmcb()->file ) . 'assets/svg/ti/icons/'. $contact['icon'] . '.svg' )
            );
        }
        elseif ( 'fa' === $contact['brand'] )
        {
            $out .= sprintf(
                '<div class="mcb-summary-brand">%s</div><div class="mcb-summary-icon mcb-fa">%s</div>',
                strtoupper( $contact['brand'] ),
                file_get_contents( plugin_dir_path( abmcb()->file ) . 'assets/svg/fa/svgs/' . $contact['group'] . '/' . $contact['icon'] . '.svg' )
            );
        }
        else
        {
            $out .= '<div class="mcb-summary-brand mcb-blank-icon">--</div><div class="mcb-summary-icon mcb-blank-icon">--</div>';
        }

        // 'label'
        $out .= sprintf(
            '<div class="mcb-summary-label">%s</div>',
            esc_attr( $contact['label'] )
        );

        $out .= '</div>';

        // 'URI'
        if ( in_array( $contact['type'], ['historyback', 'historyforward', 'scrolltotop'] ))
        {
            $out .= '<div class="mcb-summary-uri mcb-monospace">#</div>';
        }
        else
        {
            $uri = esc_attr( $contact['uri'] );
            $out .= sprintf(
                '<div class="mcb-summary-uri%s">%s</div>',
                empty( $uri ) ? '' : ' mcb-monospace',
                empty( $uri ) ? esc_attr__( '(no URI)', 'mobile-contact-bar' ) : $uri
            );
        }

        $out .= '<div class="mcb-right-actions">';

        $out .= sprintf(
            '<button type="button" class="mcb-action-icon mcb-action-delete-contact" title="%1$s">
                <span class="dashicons dashicons-no-alt" aria-hidden="true"></span>
                <span class="screen-reader-text">%1$s</span>
            </button>',
            esc_attr__( 'Delete this contact', 'mobile-contact-bar' )
        );

        $out .= sprintf(
            '<button type="button" class="mcb-action-icon mcb-action-order-higher" title="%1$s">
                <span class="dashicons dashicons-arrow-up-alt2" aria-hidden="true"></span>
                <span class="screen-reader-text">%1$s</span>
            </button>',
            esc_attr__( 'Order contact higher', 'mobile-contact-bar' )
        );

        $out .= sprintf(
            '<button type="button" class="mcb-action-icon mcb-action-order-lower" title="%1$s">
                <span class="dashicons dashicons-arrow-down-alt2" aria-hidden="true"></span>
                <span class="screen-reader-text">%1$s</span>
            </button>',
            esc_attr__( 'Order contact lower', 'mobile-contact-bar' )
        );

        $out .= sprintf(
            '<button type="button" class="mcb-action-icon mcb-action-toggle-details" title="%1$s" aria-expanded="false">
                <span class="dashicons dashicons-admin-tools" aria-hidden="true"></span>
                <span class="screen-reader-text">%1$s</span>
            </button>',
            esc_attr__( 'Edit contact', 'mobile-contact-bar' )
        );

        $out .= '</div>';
        $out .= '</div>';

        return $out;
    }


    /**
     * Outputs editable contact details.
     *
     * @param  array  $args
     * 	       string $contact_key
     * 	       array  $contact
     * @return string              HTML
     */
    public function output_details( $args )
    {
        extract( $args );

        $out = '';

        $prefix = abmcb()->id . '[contacts][' . esc_attr( $contact_key ) . ']';

        $contact_field = abmcb()->contact_types[$contact['type']]->field();

        $out .= '<div class="mcb-details">';

        // 'brand' hidden
        $out .= sprintf( '<input type="hidden" name="' . $prefix . '[brand]" value="%s">', esc_attr( $contact['brand'] ));

        // 'group' hidden
        $out .= sprintf( '<input type="hidden" name="' . $prefix . '[group]" value="%s">', esc_attr( $contact['group'] ));

        // 'icon' hidden
        $out .= sprintf( '<input type="hidden" name="' . $prefix . '[icon]" value="%s">', esc_attr( $contact['icon'] ));

        // 'type' input
        $select = '<select name="' . $prefix . '[type]" id="' . $prefix . '[type]">';
        foreach ( abmcb()->contact_types as $contact_type )
        {
            $field = $contact_type->field();
            $select .= sprintf(
                '<option value="%s" %s>%s</option>',
                esc_attr( $field['type'] ),
                selected( $field['type'], $contact['type'], false ),
                esc_attr( $field['title'] )
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
            esc_attr( $contact_field['desc_type'] ),
            $select,
            esc_attr( $contact['type'] )
        );

        // 'icon' visible
        if ( 'ti' === $contact['brand'] )
        {
            $icon = sprintf(
                '<span class="mcb-details-brand">%s</span><span class="mcb-details-icon">%s</span>',
                strtoupper( $contact['brand'] ),
                file_get_contents( plugin_dir_path( abmcb()->file ) . 'assets/svg/ti/icons/'. $contact['icon'] . '.svg' )
            );
        }
        elseif ( 'fa' === $contact['brand'] )
        {
            $icon = sprintf(
                '<span class="mcb-details-brand">%s</span><span class="mcb-details-icon mcb-fa">%s</span>',
                strtoupper( $contact['brand'] ),
                file_get_contents( plugin_dir_path( abmcb()->file ) . 'assets/svg/fa/svgs/' . $contact['group'] . '/' . $contact['icon'] . '.svg' )
            );
        }
        else
        {
            $icon = '<span class="mcb-details-brand mcb-blank-icon">--</span><span class="mcb-details-icon mcb-blank-icon">--</span>';
        }

        // select & clear 'icon' button
        $out .= sprintf(
            '<div class="mcb-row mcb-details-cta">
                <div class="mcb-label">
                    <label>%1$s</label>
                </div>
                <div class="mcb-input">
                    %2$s
                    <button type="button" class="button mcb-action-pick-icon" title="%3$s">%3$s</button>
                    <button type="button" class="button mcb-action-clear-icon" title="%4$s">%4$s</button>
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

        // 'text' input
        $out .= sprintf(
            '<div class="mcb-row mcb-details-text">
                <div class="mcb-label">
                    <label for="' . $prefix . '[text]">%s</label>
                </div>
                <div class="mcb-input">
                    <input type="text" name="' . $prefix . '[text]" id="' . $prefix . '[text]" value="%s">
                </div>
            </div>',
            esc_attr__( 'Screen Reader Text', 'mobile-contact-bar' ),
            esc_html( $contact['text'] )
        );

        $out .= $this->output_details_uri( ['contact_key' => $contact_key, 'contact' => $contact, 'contact_field' => $contact_field] );
        $out .= $this->output_parameters( ['contact_key' => $contact_key, 'contact' => $contact, 'contact_field' => $contact_field] );
        $out .= $this->output_customization( ['contact_key' => $contact_key, 'contact' => $contact, 'contact_field' => $contact_field] );

        // Close details button
        $out .= sprintf(
            '<div class="mcb-row mcb-close-details">
                <div class="mcb-label"></div>
                <div class="mcb-input">
                    <button type="button" class="button mcb-action-close-details" title="%1$s">%1$s</button>
                </div>
            </div>',
            esc_attr__( 'Close details', 'mobile-contact-bar' )
        );

        $out .= '</div>';

        return $out;
    }


    public function output_details_uri( $args )
    {
        extract( $args );

        $out = '';

        $prefix = abmcb()->id . '[contacts][' . esc_attr( $contact_key ) . ']';

        if ( in_array( $contact['type'], ['historyback', 'historyforward', 'scrolltotop'] ))
        {
            // 'URI' hidden
            $out .= sprintf(
                '<input type="hidden" name="' . $prefix . '[uri]" value="%1$s">',
                esc_attr( $contact['uri'] )
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
                esc_attr( $contact_field['desc_uri'] )
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
                    <div class="mcb-input mcb-monospace">
                        <input type="text" name="' . $prefix . '[uri]" id="' . $prefix . '[uri]" placeholder="%s" value="%s">
                    </div>
                </div>',
                esc_attr__( 'Contact URI', 'mobile-contact-bar' ),
                esc_attr( $contact_field['desc_uri'] ),
                esc_attr( $contact_field['placeholder'] ),
                esc_attr( $contact['uri'] )
            );
        }

        return $out;
    }


    /**
     * Outputs parameters head row.
     *
     * @param  array  $args
     * 	       string $contact_key
     * 	       array  $contact
     * @return string              HTML
     */
    public function output_parameters( $args )
    {
        extract( $args );

        $out = '';

        if ( 'link' === $contact['type'] )
        {
            $out .= sprintf(
                '<div class="mcb-row mcb-link-parameters">
                    <div class="mcb-label">
                        <label>%s</label>
                    </div>
                    <div class="mcb-input">
                        <button type="button" class="button mcb-action-add-parameter" title="%2$s">%2$s</button>
                    </div>
                </div>',
                esc_attr__( 'Query String Parameters', 'mobile-contact-bar' ),
                esc_attr__( 'Add Parameter', 'mobile-contact-bar' )
            );
            
            foreach ( $contact['parameters'] as $parameter_key => $parameter )
            {
                $out .= $this->output_link_parameter(
                    [
                        'contact_key'    => $contact_key,
                        'parameter_type' => ['field' => 'text'],
                        'parameter_key'  => $parameter_key,
                        'parameter'      => $parameter
                    ]
                );
            }
        }
        elseif ( isset( $contact['parameters'] ))
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

            foreach ( $contact['parameters'] as $parameter_key => $parameter )
            {
                $parameter_index = array_search( $parameter['key'], array_column( $contact_field['parameters'], 'key' ));
                $parameter_type = $contact_field['parameters'][$parameter_index];
                $out .= $this->output_builtin_parameter(
                    [
                        'contact_key'    => $contact_key,
                        'parameter_type' => $parameter_type,
                        'parameter_key'  => $parameter_key,
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
     *         string $contact_key
     * 	       string $parameter_key
     * 	       array  $parameter_key
     * @return string                 HTML
     */
    public function output_link_parameter( $args )
    {
        extract( $args );

        $out = '';

        $prefix = abmcb()->id . '[contacts][' . esc_attr( $contact_key ) . '][parameters][' . esc_attr( $parameter_key ) . ']';

        $out .= sprintf(
            '<div class="mcb-link-parameter" data-parameter-key="%d">',
            esc_attr( $parameter_key )
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
                        <span class="dashicons dashicons-no-alt" aria-hidden="true"></span>
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
     *         string $contact_key
     * 	       string $parameter_key
     * 	       array  $parameter_key
     * @return string                 HTML
     */
    public function output_builtin_parameter( $args )
    {
        extract( $args );

        $out = '';

        $prefix = abmcb()->id . '[contacts][' . esc_attr( $contact_key ) . '][parameters][' . esc_attr( $parameter_key ) . ']';

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
     * 	       string $contact_key
     * 	       array  $contact
     * @return string              HTML
     */
    public function output_customization( $args )
    {
        extract( $args );

        $out = '';

        $prefix = abmcb()->id . '[contacts][' . esc_attr( $contact_key ) . ']';
        $custom_fields = ContactTypes\ContactType::custom_fields();

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
                ? esc_attr__( 'This is a generated ID, do not rely on it. Change it to your needs.', 'mobile-contact-bar' )
                : esc_attr__( 'Used when custom colors are specified.', 'mobile-contact-bar' ),
            esc_attr( $contact['id'] )
        );

        // 'custom' inputs
        foreach( $custom_fields as $custom_key => $custom )
        {
            $out .= sprintf(
                '<div class="mcb-row mcb-custom mcb-custom-%s">
                    <div class="mcb-label">
                        <label>%s</label>
                        <p class="mcb-description">%s</p>
                    </div>
                    <div class="mcb-input">',
                esc_attr( $custom_key ),
                esc_attr( $custom['title'] ),
                esc_attr__( 'Long', 'mobile-contact-bar' )
            );
            foreach ( $custom['options'] as $option_key => $option )
            {
                $out .= sprintf(
                    '<div class="mcb-custom-colors">
                        <span>%s</span>
                        <input type="text" class="color-picker" name="' . $prefix . '[custom][%s][%s]" data-alpha-enabled="true" value="%s">
                    </div>',
                    esc_attr( $option['desc'] ),
                    esc_attr( $custom_key ),
                    esc_attr( $option_key ),
                    esc_attr( $contact['custom'][$custom_key][$option_key] )
                );
            }
            $out .= '</div>';
            $out .= '</div>';
        }

        return $out;
    }
}
