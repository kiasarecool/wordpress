<?php
//Change word shipping to Delivery fees
add_filter( 'woocommerce_shipping_package_name', 'custom_shipping_package_name' );
function custom_shipping_package_name( $name ) {
    return 'Delivery Fees';
}
add_filter('gettext','change_shipping_text');
add_filter('ngettext','change_shipping_text');
function change_shipping_text($text) {
    $text = str_ireplace('Shipping','Delivery Fees',$text);
    return $text;
}
