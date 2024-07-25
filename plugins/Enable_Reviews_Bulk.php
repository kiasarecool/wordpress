<?php
/**
 *
 * Plugin Name:       WooCommerce Enable Reviews - Bulk Edit
 * Description:       Allow enable reviews by bulk edit into WooCommerce
 * Version:           1.0.0
 * Author:            Mário Valney
 * Author URI:        http://mariovalney.com
 * Text Domain:       woo-enable-reviews-bulk-edit
 *
 */
add_action( 'woocommerce_product_bulk_edit_end', 'wcerbe_woocommerce_product_bulk_edit_end' );
function wcerbe_woocommerce_product_bulk_edit_end() {
    $output = '<label><span class="title">' . esc_html__( "Enable reviews", "woocommerce" ) . '?</span>';
    $output .= '<span class="input-text-wrap"><select class="reviews_allowed" name="_reviews_allowed">';
    $options = array(
        ''    => __( '— No change —', 'woocommerce' ),
        'yes' => __( 'Yes', 'woocommerce' ),
        'no'  => __( 'No', 'woocommerce' ),
    );
    foreach ( $options as $key => $value ) {
        $output .= '<option value="' . esc_attr( $key ) . '">' . esc_html( $value ) . '</option>';
    }
    $output .= '</select></span></label>';
    echo $output;
}
add_action( 'woocommerce_product_bulk_edit_save', 'wcerbe_woocommerce_product_bulk_edit_save', 10, 1 );
function wcerbe_woocommerce_product_bulk_edit_save( $product ) {
    // Enable reviews
    if ( ! empty( $_REQUEST['_reviews_allowed'] ) ) {
        if ( 'yes' === $_REQUEST['_reviews_allowed'] ) {
            $product->set_reviews_allowed( 'yes' );
        } else {
            $product->set_reviews_allowed( '' );
        }
    }
    $product->save();
}
