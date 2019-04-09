<?php
/**
 * Plugin Name: Dokan Custom Vendor Registration Field
 * Description: Custom Field for Dokan Vendor Registration Form
 * Version: 1.0.0
 * Author: Edi Amin
 * Author URI: https://github.com/ediamin
 * Text Domain: dokan-custom
 * Domain Path: /i18n/languages/
 */

/**
 * Set field as a required one
 *
 * @since 1.0.0
 *
 * @param array $required_fields
 *
 * @return array
 */
function dokan_custom_seller_registration_required_fields( $required_fields ) {
    $required_fields['agent_id'] = __( 'Please enter your agent ID', 'dokan-custom' );

    return $required_fields;
};

add_filter( 'dokan_seller_registration_required_fields', 'dokan_custom_seller_registration_required_fields' );


/**
 * Add field in the registration form
 *
 * This will add a new field after TOC checkbox.
 * For more flexible option, use next template filter.
 *
 * @since 1.0.0
 *
 * @return void
 */
function dokan_custom_seller_registration_field_after() {
    $post_data = wp_unslash( $_POST );

    ?>
        <p class="form-row form-group form-row-wide">
            <label for="shop-phone"><?php esc_html_e( 'Agent ID', 'dokan-custom' ); ?><span class="required">*</span></label>
            <input type="text" class="input-text form-control" name="agent_id" id="agent-id" value="<?php if ( ! empty( $postdata['agent_id'] ) ) echo esc_attr($postdata['agent_id']); ?>" required="required" />
        </p>
    <?php
}

// add_action( 'dokan_seller_registration_field_after', 'dokan_custom_seller_registration_field_after' );

/**
 * Override the global/seller-registration-form template and add new field
 *
 * @since 1.0.0
 *
 * @param string $template
 * @param string $slug
 * @param string $name
 *
 * @return string
 */
function dokan_custom_get_template_part( $template, $slug, $name ) {
    if ( 'global/seller-registration-form' === $slug ) {
        $template = dirname( __FILE__ ) . '/dokan-seller-registration-form.php';
    }

    return $template;
}

add_filter( 'dokan_get_template_part', 'dokan_custom_get_template_part', 10, 3 );

/**
 * Save custom field data
 *
 * @since 1.0.0
 *
 * @param int   $vendor_id
 * @param array $dokan_settings
 *
 * @return void
 */
function dokan_custom_new_seller_created( $vendor_id, $dokan_settings ) {
    $post_data = wp_unslash( $_POST );

    $agent_id = sanitize_text_field( $post_data['agent_id'] );

    /**
     * This will save agent_id value with the `dokan_custom_agent_id` user meta key
     */
    update_user_meta( $vendor_id, 'dokan_custom_agent_id', $agent_id );
}

add_action( 'dokan_new_seller_created', 'dokan_custom_new_seller_created', 10, 2 );
