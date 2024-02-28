<?php
// Hook into the action that fires after an order is placed
add_action( 'woocommerce_thankyou', 'update_payment_method_for_prebook_order' );

function update_payment_method_for_prebook_order( $order_id ) {
    // Bail out if WooCommerce ain't running
    if ( !function_exists( 'wc_get_order' ) ) {
        return;
    }

    $order = wc_get_order( $order_id );
    $contains_prebook_item = false;

    // Check each item in the order
    foreach ( $order->get_items() as $item_id => $item ) {
        $product_id = $item->get_product_id();
        $product_categories = get_the_terms( $product_id, 'product_cat' );

        if ( $product_categories ) {
            foreach ( $product_categories as $category ) {
                if ( preg_match( '/Pre-Book$/', $category->name ) ) {
                    $contains_prebook_item = true;
                    break 2; // Break out of both foreach loops
                }
            }
        }
    }

    // If the order contains a prebook item, update the payment method
    if ( $contains_prebook_item ) {
        $order->set_payment_method_title( 'prebook' );
        $order->save();
    }
}
