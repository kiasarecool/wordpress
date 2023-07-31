<?php
//Clear that cart
add_action( 'woocommerce_payment_complete', 'order_received_empty_cart_action', 10, 1 );
function order_received_empty_cart_action( $order_id ){
    WC()->cart->empty_cart();