<?php
// Limit to 60 unique items per cart (still allow unlimted total piece qty) 
add_filter( 'woocommerce_add_to_cart_validation', 'only_n_products_allowed_in_cart', 10, 3 );
function only_n_products_allowed_in_cart( $passed, $product_id, $quantity ) {
    $items_limit = 60;
    $total_count = count( WC()->cart->get_cart() ) + 1;

    if( $total_count > $items_limit ){
        // Set to false
        $passed = false;
        // Display a message
         wc_add_notice( sprintf( __( "Please place this order and continue on an additional order, thank you! ( %s unique item limit )", "woocommerce" ), $items_limit ), "error" );
    }
    return $passed;
}