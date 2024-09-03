<?php
/*
Plugin Name: Kiasarecool WooCommerce Star Rating
Description: Enable and display star ratings on all WooCommerce products.
Version: 1.0
Author: Kiasarecool
*/

// Exit if accessed directly.
if (!defined('ABSPATH')) {
    exit;
}

// Hook to add star rating support.
add_action('init', 'kiasarecool_add_star_rating_support');

function kiasarecool_add_star_rating_support() {
    // Enable star rating on all products.
    add_post_type_support('product', 'comments');
}

// Hook to display star rating.
add_action('woocommerce_single_product_summary', 'kiasarecool_display_star_rating', 15);

function kiasarecool_display_star_rating() {
    global $product;

    if (comments_open($product->get_id())) {
        echo '<div class="woocommerce-product-rating">';
        echo wc_get_rating_html($product->get_average_rating());
        echo '</div>';
    }
}

// Enable ratings in the product reviews tab.
add_filter('woocommerce_product_review_list_args', 'kiasarecool_enable_ratings_in_reviews');

function kiasarecool_enable_ratings_in_reviews($args) {
    $args['callback'] = 'woocommerce_comments';
    return $args;
}
