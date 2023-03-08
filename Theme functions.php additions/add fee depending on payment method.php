<?php
// Credit Card Fee
add_action( 'woocommerce_cart_calculate_fees', 'cc_fee' );
function cc_fee ( $cart ) {
    if ( is_admin() && ! defined( 'DOING_AJAX' ) )
        return;

    $chosen_payment_id = WC()->session->get('chosen_payment_method');

    if ( empty( $chosen_payment_id ) )
        return;

    $subtotal = $cart->subtotal;
	$shipping_total = WC()->cart->get_shipping_total();
	
    // SETTINGS: Here set in the array the (payment Id) / (fee cost) pairs
    $targeted_payment_ids = array(
        'apple_pay' => ( 4 * $subtotal / 100 + 0.30 ) + ( 4 * $shipping_total /100 ) , 
		'google_pay' => ( 4 * $subtotal / 100 + 0.30 ) + ( 4 * $shipping_total /100 ),
        'woocommerce_payments' => ( 4 * $subtotal / 100 + 0.30 ) + ( 4 * $shipping_total /100 ) , // Card fee
    );
    // Loop through defined payment Ids array
    foreach ( $targeted_payment_ids as $payment_id => $fee_cost ) {
        if ( $chosen_payment_id === $payment_id ) {
            $cart->add_fee( __('Card fee', 'woocommerce'), $fee_cost, false );
        }
    }
}

//refresh totals when payment method changes 
add_action('woocommerce_review_order_before_payment', function() {
    ?><script type="text/javascript">
        (function($){
            $('form.checkout').on('change', 'input[name^="payment_method"]', function() {
                $('body').trigger('update_checkout').trigger('wc_fragment_refresh');
            });
        })(jQuery);
    </script><?php
});