<?php

// Allow customers to cancel their own processing orders 
add_filter('woocommerce_valid_order_statuses_for_cancel', 'let_them_cancel', 10, 2);
function let_them_cancel($statuses, $order){
  return array('pending', 'processing', 'on-hold');
}
