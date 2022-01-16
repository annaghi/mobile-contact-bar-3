<?php

namespace MobileContactBar\Buttons;

use MobileContactBar\Icons;
use MobileContactBar\ButtonTypes;


final class View
{
    /**
     * Adds Button Builder meta box to the options page.
     * 
     * @return void
     */
    public function add()
    {
        add_settings_section(
            'mcb-meta-box-builder',
            __( 'Button Builder', 'mobile-contact-bar' ),
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
     * Renders the Button Builder.
     * 
     * @return void
     */
    public function render_builder()
    {
        $settings = abmcb()->option_bar['settings'];
        $buttons  = abmcb()->option_bar['buttons'];

        ?>
        <div id="mcb-section-builder">
            <div id="mcb-builder">
            <?php foreach ( $buttons as $button_key => $button ) : ?>
                <div class="mcb-button<?php echo ( $button['checked'] ) ? ' mcb-checked' : ''?>" data-button-key="<?php echo esc_attr( $button_key ); ?>">
                <?php
                    echo $this->output_summary( ['button_key' => $button_key, 'button' => $button] );
                    echo $this->output_details( ['button_key' => $button_key, 'button' => $button] );
                ?>
                </div>
            <?php endforeach; ?>
            </div>
            <div id="mcb-footer-builder">
                <button type="button" class="button button-primary" id="mcb-add-button" title="<?php esc_attr_e( 'Add New Button', 'mobile-contact-bar' ); ?>">
                    <span class="dashicons dashicons-plus-alt2" aria-hidden="true"></span>
                    <span><?php esc_html_e( 'New Button', 'mobile-contact-bar' ); ?></span>
                </button>
            </div>
        </div>
        <?php
    }


    /**
     * Outputs button summary.
     *
     * @param  array  $args
     * 	       string $button_key
     * 	       array  $button
     * @return string              HTML
     */
    public function output_summary( $args )
    {
        extract( $args );

        $out = '';

        $prefix = abmcb()->id . '[buttons][' . esc_attr( $button_key ) . ']';

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
            ( $button['checked'] ) ? checked( $button['checked'], 1, false ) : ''
        );

        $out .= '</div>';

        $out .= '<div class="mcb-summary-cta">';

        // 'icon'
        if ( 'ti' === $button['brand'] )
        {
            $out .= sprintf(
                '<div class="mcb-summary-brand">%s</div><div class="mcb-summary-icon">%s</div>',
                esc_attr( strtoupper( $button['brand'] )),
                file_get_contents( plugin_dir_path( abmcb()->file ) . 'assets/svg/ti/icons/'. esc_attr( $button['icon'] ) . '.svg' )
            );
        }
        elseif ( 'fa' === $button['brand'] )
        {
            $out .= sprintf(
                '<div class="mcb-summary-brand">%s</div><div class="mcb-summary-icon mcb-fa">%s</div>',
                esc_attr( strtoupper( $button['brand'] )),
                file_get_contents( plugin_dir_path( abmcb()->file ) . 'assets/svg/fa/svgs/' . esc_attr( $button['group'] ) . '/' . esc_attr( $button['icon'] ) . '.svg' )
            );
        }
        else
        {
            $out .= '<div class="mcb-summary-brand mcb-blank-icon">--</div><div class="mcb-summary-icon mcb-blank-icon">--</div>';
        }

        // 'label'
        $out .= sprintf(
            '<div class="mcb-summary-label">%s</div>',
            esc_attr( $button['label'] )
        );

        $out .= '</div>';

        // 'URI'
        if ( in_array( $button['type'], ['historyback', 'historyforward', 'scrolltotop'] ))
        {
            $out .= '<div class="mcb-summary-uri mcb-monospace">#</div>';
        }
        else
        {
            $uri = esc_attr( $button['uri'] );
            $out .= sprintf(
                '<div class="mcb-summary-uri%s">%s</div>',
                empty( $uri ) ? '' : ' mcb-monospace',
                empty( $uri ) ? esc_attr__( '(no URI)', 'mobile-contact-bar' ) : $uri
            );
        }

        $out .= '<div class="mcb-right-actions">';

        $out .= sprintf(
            '<button type="button" class="mcb-action-icon mcb-action-delete-button" title="%1$s">
                <span class="dashicons dashicons-no-alt" aria-hidden="true"></span>
                <span class="screen-reader-text">%1$s</span>
            </button>',
            esc_attr__( 'Delete this button', 'mobile-contact-bar' )
        );

        $out .= sprintf(
            '<button type="button" class="mcb-action-icon mcb-action-order-higher" title="%1$s">
                <span class="dashicons dashicons-arrow-up-alt2" aria-hidden="true"></span>
                <span class="screen-reader-text">%1$s</span>
            </button>',
            esc_attr__( 'Order button higher', 'mobile-contact-bar' )
        );

        $out .= sprintf(
            '<button type="button" class="mcb-action-icon mcb-action-order-lower" title="%1$s">
                <span class="dashicons dashicons-arrow-down-alt2" aria-hidden="true"></span>
                <span class="screen-reader-text">%1$s</span>
            </button>',
            esc_attr__( 'Order button lower', 'mobile-contact-bar' )
        );

        $out .= sprintf(
            '<button type="button" class="mcb-action-icon mcb-action-toggle-details" title="%1$s" aria-expanded="false">
                <span class="dashicons dashicons-admin-tools" aria-hidden="true"></span>
                <span class="screen-reader-text">%1$s</span>
            </button>',
            esc_attr__( 'Edit button', 'mobile-contact-bar' )
        );

        $out .= '</div>';
        $out .= '</div>';

        return $out;
    }


    /**
     * Outputs editable button details.
     *
     * @param  array  $args
     * 	       string $button_key
     * 	       array  $button
     * @return string              HTML
     */
    public function output_details( $args )
    {
        extract( $args );

        $out = '';

        $prefix = abmcb()->id . '[buttons][' . esc_attr( $button_key ) . ']';

        $button_field = abmcb()->button_types[$button['type']]->field();

        $out .= '<div class="mcb-details">';

        // 'brand' hidden
        $out .= sprintf( '<input type="hidden" name="' . $prefix . '[brand]" value="%s">', esc_attr( $button['brand'] ));

        // 'group' hidden
        $out .= sprintf( '<input type="hidden" name="' . $prefix . '[group]" value="%s">', esc_attr( $button['group'] ));

        // 'icon' hidden
        $out .= sprintf( '<input type="hidden" name="' . $prefix . '[icon]" value="%s">', esc_attr( $button['icon'] ));

        // 'type' input
        $select = '<select name="' . $prefix . '[type]" id="' . $prefix . '[type]">';
        foreach ( abmcb()->button_types as $button_type )
        {
            $field = $button_type->field();
            $select .= sprintf(
                '<option value="%s" %s>%s</option>',
                esc_attr( $field['type'] ),
                selected( $field['type'], $button['type'], false ),
                esc_html( $field['title'] )
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
            esc_html__( 'Type', 'mobile-contact-bar' ),
            esc_html( $button_field['desc_type'] ),
            $select,
            esc_html( $button['type'] )
        );

        // 'icon' visible
        if ( 'ti' === $button['brand'] )
        {
            $icon = sprintf(
                '<span class="mcb-details-brand">%s</span><span class="mcb-details-icon">%s</span>',
                esc_attr( strtoupper( $button['brand'] )),
                file_get_contents( plugin_dir_path( abmcb()->file ) . 'assets/svg/ti/icons/'. esc_attr( $button['icon'] ) . '.svg' )
            );
        }
        elseif ( 'fa' === $button['brand'] )
        {
            $icon = sprintf(
                '<span class="mcb-details-brand">%s</span><span class="mcb-details-icon mcb-fa">%s</span>',
                esc_attr( strtoupper( $button['brand'] )),
                file_get_contents( plugin_dir_path( abmcb()->file ) . 'assets/svg/fa/svgs/' . esc_attr( $button['group'] ) . '/' . esc_attr( $button['icon'] ) . '.svg' )
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
                    <button type="button" class="button button-secondary mcb-action-pick-icon" title="%3$s">%3$s</button>
                    <button type="button" class="button button-secondary mcb-action-clear-icon" title="%4$s">%4$s</button>
                </div>
            </div>',
            esc_html__( 'Icon', 'mobile-contact-bar' ),
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
            esc_html__( 'Label', 'mobile-contact-bar' ),
            esc_html__( 'Use \n for new lines', 'mobile-contact-bar' ),
            esc_attr( $button['label'] )
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
            esc_html__( 'Screen Reader Text', 'mobile-contact-bar' ),
            esc_attr( $button['text'] )
        );

        $out .= $this->output_details_uri( ['button_key' => $button_key, 'button' => $button, 'button_field' => $button_field] );
        $out .= $this->output_query( ['button_key' => $button_key, 'button' => $button, 'button_field' => $button_field] );
        $out .= $this->output_customization( ['button_key' => $button_key, 'button' => $button, 'button_field' => $button_field] );

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

        $prefix = abmcb()->id . '[buttons][' . esc_attr( $button_key ) . ']';

        if ( in_array( $button['type'], ['historyback', 'historyforward', 'scrolltotop'] ))
        {
            // 'URI' hidden
            $out .= sprintf(
                '<input type="hidden" name="' . $prefix . '[uri]" value="%s">',
                esc_attr( $button['uri'] )
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
                esc_html__( 'URI', 'mobile-contact-bar' ),
                esc_html( $button_field['desc_uri'] )
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
                esc_html__( 'URI', 'mobile-contact-bar' ),
                esc_html( $button_field['desc_uri'] ),
                esc_attr( $button_field['placeholder'] ),
                esc_attr( $button['uri'] )
            );
        }

        return $out;
    }


    /**
     * Outputs query head row.
     *
     * @param  array  $args
     * 	       string $button_key
     * 	       array  $button
     * @return string              HTML
     */
    public function output_query( $args )
    {
        extract( $args );

        $out = '';

        if ( 'link' === $button['type'] )
        {
            $out .= sprintf(
                '<div class="mcb-row mcb-link-query">
                    <div class="mcb-label">
                        <label>%s</label>
                    </div>
                    <div class="mcb-input">
                        <button type="button" class="button button-secondary mcb-action-add-parameter" title="%2$s">%2$s</button>
                    </div>
                </div>',
                esc_html__( 'Query String', 'mobile-contact-bar' ),
                esc_attr__( 'Add Parameter', 'mobile-contact-bar' )
            );
            
            foreach ( $button['query'] as $parameter_key => $parameter )
            {
                $out .= $this->output_link_parameter(
                    [
                        'button_key'     => $button_key,
                        'parameter_type' => ['field' => 'text'],
                        'parameter_key'  => $parameter_key,
                        'parameter'      => $parameter
                    ]
                );
            }
        }
        elseif ( isset( $button['query'] ))
        {
            $out .= sprintf(
                '<div class="mcb-row mcb-builtin-query">
                    <div class="mcb-label">
                        <label>%s</label>
                    </div>
                    <div class="mcb-input"></div>
                </div>',
                esc_html__( 'Query String', 'mobile-contact-bar' )
            );

            foreach ( $button['query'] as $parameter_key => $parameter )
            {
                $parameter_index = array_search( $parameter['key'], array_column( $button_field['query'], 'key' ));
                $parameter_type = $button_field['query'][$parameter_index];
                $out .= $this->output_builtin_parameter(
                    [
                        'button_key'     => $button_key,
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
     *         string $button_key
     * 	       string $parameter_key
     * 	       array  $parameter_key
     * @return string                 HTML
     */
    public function output_link_parameter( $args )
    {
        extract( $args );

        $out = '';

        $prefix = abmcb()->id . '[buttons][' . esc_attr( $button_key ) . '][query][' . esc_attr( $parameter_key ) . ']';

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
            esc_html__( 'Query String Key', 'mobile-contact-bar' ),
            esc_html__( 'Query String Key', 'mobile-contact-bar' ),
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
                    esc_html__( 'Query String Value', 'mobile-contact-bar' ),
                    esc_html__( 'Query String Value', 'mobile-contact-bar' ),
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
                    esc_html__( 'Query String Value', 'mobile-contact-bar' ),
                    esc_html__( 'Query String Value', 'mobile-contact-bar' ),
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
     *         string $button_key
     * 	       string $parameter_key
     * 	       array  $parameter_key
     * @return string                 HTML
     */
    public function output_builtin_parameter( $args )
    {
        extract( $args );

        $out = '';

        $prefix = abmcb()->id . '[buttons][' . esc_attr( $button_key ) . '][query][' . esc_attr( $parameter_key ) . ']';

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
                    esc_html__( 'Query String Value', 'mobile-contact-bar' ),
                    esc_html__( 'Query String Value', 'mobile-contact-bar' ),
                    esc_html( $parameter['key'] ),
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
                    esc_html__( 'Query String Value', 'mobile-contact-bar' ),
                    esc_html__( 'Query String Value', 'mobile-contact-bar' ),
                    esc_html( $parameter['key'] ),
                    esc_attr( $parameter_type['placeholder'] ),
                    esc_textarea( $parameter['value'] )
                );
                break;
        }

        $out .= '</div>';

        return $out;
    }


    /**
     * Outputs the CSS ID selector input and custom colors of the button.
     *
     * @param  array  $args
     * 	       string $button_key
     * 	       array  $button
     * @return string              HTML
     */
    public function output_customization( $args )
    {
        extract( $args );

        $out = '';

        $prefix = abmcb()->id . '[buttons][' . esc_attr( $button_key ) . ']';
        $custom_fields = ButtonTypes\Button::custom_fields();

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
            esc_html__( 'CSS ID selector', 'mobile-contact-bar' ),
            preg_match( '/^mcb-sample-id-[0-9]+/', $button['id'] )
                ? esc_html__( 'This is a generated ID, do not rely on it. Change it to your needs.', 'mobile-contact-bar' )
                : esc_html__( 'Used when custom colors are specified.', 'mobile-contact-bar' ),
            esc_attr( $button['id'] )
        );

        // 'custom' inputs
        foreach( $custom_fields as $custom_key => $custom )
        {
            $out .= sprintf(
                '<div class="mcb-row mcb-custom mcb-custom-%s">
                    <div class="mcb-label">
                        <label>%s</label>
                    </div>
                    <div class="mcb-input">',
                esc_attr( $custom_key ),
                esc_html( $custom['title'] )
            );
            foreach ( $custom['options'] as $option_key => $option )
            {
                $out .= sprintf(
                    '<div class="mcb-custom-colors">
                        <span>%s</span>
                        <input type="text" class="color-picker" name="' . $prefix . '[custom][%s][%s]" data-alpha-enabled="true" value="%s">
                    </div>',
                    esc_html( $option['desc'] ),
                    esc_attr( $custom_key ),
                    esc_attr( $option_key ),
                    esc_attr( $button['custom'][$custom_key][$option_key] )
                );
            }
            $out .= '</div>';
            $out .= '</div>';
        }

        return $out;
    }
}
