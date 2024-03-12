<?php
// Block guest users / non logged in users from seeing prices 
add_filter( 'woocommerce_get_price_html', 'you_no_see_price', 10, 2 );
function you_no_see_price( $price, $product ) {
   if ( ! is_user_logged_in() ) { 
      $price = '<div><a href="' . get_permalink( wc_get_page_id( 'myaccount' ) ) . '">' . __( 'You are not logged in', 'bbloomer' ) . '</a></div>';
      remove_action( 'woocommerce_after_shop_loop_item', 'woocommerce_template_loop_add_to_cart', 10 );
      remove_action( 'woocommerce_single_product_summary', 'woocommerce_template_single_add_to_cart', 30 );
   }
   return $price;
}