<?php
 // Add action to display quantity in stock on the archive/ catalog
add_action( 'woocommerce_after_shop_loop_item_title', 'display_quantity_in_stock', 10 );
function display_quantity_in_stock() {
    global $product;
    if ( $product->is_in_stock() ) {
        echo '<div class="stock" style="color: green;">' . $product->get_stock_quantity() . ' IN STOCK</div>';
    }
}