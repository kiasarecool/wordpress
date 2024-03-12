<?php

// Block add to cart for guest users / non logged in users
add_filter( 'woocommerce_add_to_cart_validation', 'logged_in_customers_validation', 10, 3 );
function logged_in_customers_validation( $passed, $product_id, $quantity) {
    if( ! is_user_logged_in() ) {
        $passed = false;
/*        // Displaying an error to guest //does not show error when ajax is enabled
*        $message = __("You must be logged in to add to cart…", "woocommerce");
*        $button_link = get_permalink( get_option('woocommerce_myaccount_page_id') );
*        $button_text = __("Login or register", "woocommerce");
*        $message .= ' <a href="'.$button_link.'" class="login-register button" style="float:right;">'.$button_text.'</a>';
*		 wc_add_notice( $message, 'error' );
*/        
    }
    return $passed;
}