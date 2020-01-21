<?php
/**
 * Plugin Name: Dokan Custom More Products
 * Description: Controlled more product count
 * Version: 1.0.0
 * Author: Edi Amin
 * Author URI: https://github.com/ediamin
 * Text Domain: dokan-custom
 * Domain Path: /i18n/languages/
 */

function dokan_custom_set_more_from_seller_tab( $tabs ) {
    if ( check_more_seller_product_tab() ) {
        $tabs['more_seller_product'] = array(
            'title'     => __( 'More Products', 'dokan-lite' ),
            'priority'  => 99,
            'callback'  => 'dokan_custom_get_more_products_from_seller',
        );
    }

    return $tabs;
}

add_filter( 'woocommerce_product_tabs', 'dokan_custom_set_more_from_seller_tab', 11 );

function dokan_custom_get_more_products_from_seller() {
    global $product, $post;

    $maximum_products_to_show = 6;
    $products_per_row = 3;

    $seller_id = $post->post_author;

    $args = array(
        'post_type'      => 'product',
        'posts_per_page' => $maximum_products_to_show,
        'orderby'        => 'rand',
        'post__not_in'   => array( $post->ID ),
        'author'         => $seller_id,
    );

    $products = new WP_Query( $args );

    // Require at least `$products_per_row` products to display
    if ( $products->have_posts() && $products->post_count >= $products_per_row ) {
        // Magic starts here. Restrict to show products have count divisible by $products_per_row
        $remainder = $products->post_count % $products_per_row;

        if ( $remainder ) {
            $products->post_count -= $remainder;
        }
        // Magic ends

        woocommerce_product_loop_start();

        while ( $products->have_posts() ) {
            $products->the_post();
            wc_get_template_part( 'content', 'product' );
        }

        woocommerce_product_loop_end();
    } else {
        esc_html_e( 'No product has been found!', 'dokan-lite' );
    }

    wp_reset_postdata();
}