<?php

// Redirect customers to shop after login
add_filter('woocommerce_login_redirect', 'customer_redirect', 10, 3);
function customer_redirect( $redirect, $user ) {
    if( current_user_can( 'customer' ) );
        $redirect = 'https://www.your-site.com/shop/';
    return $redirect; 
}