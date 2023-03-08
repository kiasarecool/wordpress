<?php
// Add a new custom column to admin order list for payment method
add_filter( 'manage_edit-shop_order_columns', 'add_payment_shop_order_column', 11);
function add_payment_shop_order_column($columns) {
    $reordered_columns = array();

    foreach( $columns as $key => $column){
        $reordered_columns[$key] = $column;
        if( $key ==  'order_number' ){
            $reordered_columns['payment_method'] = __( 'Payment','Woocommerce');
        }
    }
    return $reordered_columns;
}
// The data of the new custom column in admin order list
add_action( 'manage_shop_order_posts_custom_column' , 'orders_list_column_payment_title', 10, 2 );
function orders_list_column_payment_title( $column, $post_id ){
    if( 'payment_method' === $column ){
        $payment_title = get_post_meta( $post_id, '_payment_method_title', true );
        if( ! empty($payment_title) )
            echo $payment_title;
    }
}