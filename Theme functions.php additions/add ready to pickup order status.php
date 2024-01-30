<?php
// add ready to pickup order status
function register_ready_for_pickup_order_status() {
    register_post_status( 'wc-pickup-ready', array(
        'label'                     => 'Ready for Pickup',
        'public'                    => true,
        'show_in_admin_status_list' => true,
        'show_in_admin_all_list'    => true,
        'exclude_from_search'       => false,
        'label_count'               => _n_noop( 'Ready for Pickup <span class="count">(%s)</span>', 'Pickup Ready <span class="count">(%s)</span>' )
    ) );
}
add_action( 'init', 'register_ready_for_pickup_order_status' );
function add_ready_for_pickup_to_order_statuses( $order_statuses ) {
   $new_order_statuses = array();
   foreach ( $order_statuses as $key => $status ) {
       $new_order_statuses[ $key ] = $status;
       if ( 'wc-processing' === $key ) {
           $new_order_statuses['wc-pickup-ready'] = 'Ready for Pickup';
       }
   }
   return $new_order_statuses;
}
add_filter( 'wc_order_statuses', 'add_ready_for_pickup_to_order_statuses' );
