<?php

// first (or second) make a discount code on woo that is set to percentage at 0%, 
// add exp date, 

//     Custom scaling percentage discounts based on cart subtotal     ///////////////////////////////////
add_action( 'woocommerce_cart_calculate_fees', 'discount_based_on_cart_total_and_codes', 10, 1 );
function discount_based_on_cart_total_and_codes( $cart_object ) {
    if ( is_admin() && ! defined( 'DOING_AJAX' ) )
        return;
    // Array of matching coupon codes change these to your created coupon codes 
    $coupon_codes = ['code1', '123coupon-code', 'spendmore-savemore'];
    // Check if any of our specific coupon codes are applied
    $coupon_applied = false;
    foreach ( $coupon_codes as $code ) {
        if ( $cart_object->has_discount( $code ) ) {
            $coupon_applied = true;
            break;
        }
    }
    // If none of the specified coupons are applied, return
    if ( ! $coupon_applied )
        return;
    $cart_total = $cart_object->cart_contents_total; // Cart total
    // Determine discount percentage based on cart total
    if ( $cart_total > 1500 )
        $percent = 20; // 20%
    elseif ( $cart_total >= 1000 && $cart_total < 1499 )
        $percent = 10; // 10%
    elseif ( $cart_total >= 500 && $cart_total < 999 )
        $percent = 10; // 10%
    elseif ( $cart_total >= 1 && $cart_total < 499 )
        $percent = 5; // 5%
    else
        $percent = 0;
    // Apply discount if applicable
    if ( $percent != 0 ) {
        $discount = $cart_total * $percent / 100; 
        $cart_object->add_fee( "Coupon ($percent%)", -$discount, true );
    }
}