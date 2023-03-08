<?php
// Send failed payment email to customer also
function wc_cancelled_order_add_customer_email( $recipient, $order ){
    return $recipient . ',' . $order->user_email;
}
add_filter( 'woocommerce_email_recipient_cancelled_order', 'wc_cancelled_order_add_customer_email', 10, 2 );
add_filter( 'woocommerce_email_recipient_failed_order', 'wc_cancelled_order_add_customer_email', 10, 2 );
