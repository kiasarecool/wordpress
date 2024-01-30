<?php
 ///////////////////////////////////////////////////////////////////////
 //          Pre Booking               || ||
 //     make a category that has       \\ //
 //   'Pre-Book' in the name            \ /
 // and this will allow users to only order pre order items 
 // by blocking other category items from being added to cart
function cart_contains_preorder_item() {
    // Check if WooCommerce is active and the cart is available
    if (function_exists('WC') && isset(WC()->cart)) {
        foreach (WC()->cart->get_cart_contents() as $cart_item) {
            $product_categories = get_the_terms($cart_item['product_id'], 'product_cat');
            if ($product_categories) {
                foreach ($product_categories as $category) {
                    if (preg_match('/Pre-Book$/', $category->name)) {
                        return true; // Return true if any category ends with 'Pre-Book'
                    }
                }
            }
        }
    }
    return false;
}
 // Validate adding only pre-order items to cart
add_filter( 'woocommerce_add_to_cart_validation', 'only_preorder_items_allowed', 10, 3 );
function only_preorder_items_allowed( $passed, $product_id, $quantity ) {
    $product_categories = get_the_terms( $product_id, 'product_cat' );
    $is_preorder_product = false;

    if ( $product_categories ) {
        foreach ( $product_categories as $category ) {
            if ( preg_match( '/Pre-Book$/', $category->name ) ) {
                $is_preorder_product = true;
                break;
            }
        }
    }
    if ( cart_contains_preorder_item() && !$is_preorder_product ) {
        wc_add_notice( 'You can only add other pre-order items.', 'error' );
        $passed = false;
    }
    return $passed;
}
 //JavaScript for AJAX response on the front-end
function enqueue_woocommerce_scripts() {
    if ( is_woocommerce() ) {
        wp_enqueue_script( 'prebook-error-messages', get_template_directory_uri() . '/js/prebook-error-message.js', array( 'jquery' ), null, true );
    }
}
add_action( 'wp_enqueue_scripts', 'enqueue_woocommerce_scripts' );