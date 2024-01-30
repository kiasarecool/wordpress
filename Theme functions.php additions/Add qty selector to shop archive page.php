<?php
// use with free plugin 'ajax add to cart' for best results!
// majorly reduces abandonment by removing all page reloads/ redirects
// during the cart filling stage & allows adding of more than 1 at a time form 
// shop archive / category pages

// add qty selector to shop archive page 
add_filter( 'woocommerce_loop_add_to_cart_link', 'quantity_inputs_for_loop_ajax_add_to_cart', 10, 2 );
function quantity_inputs_for_loop_ajax_add_to_cart( $html, $product ) {
    if ( $product && $product->is_type( 'simple' ) && $product->is_purchasable() && $product->is_in_stock() && ! $product->is_sold_individually() ) {
        // Get the necessary classes
        $class = implode( ' ', array_filter( array(
            'button',
            'product_type_' . $product->get_type(),
            $product->is_purchasable() && $product->is_in_stock() ? 'add_to_cart_button' : '',
            $product->supports( 'ajax_add_to_cart' ) ? 'ajax_add_to_cart' : '',
        ) ) );
        // Adding embeding <form> tag and the quantity field
        $html = sprintf( '%s%s<a rel="nofollow" href="%s" data-quantity="%s" data-product_id="%s" data-product_sku="%s" class="%s">%s</a>%s',
        '<form class="cart">',
        woocommerce_quantity_input( array(), $product, false ),
        esc_url( $product->add_to_cart_url() ),
        esc_attr( isset( $quantity ) ? $quantity : 1 ),
        esc_attr( $product->get_id() ),
        esc_attr( $product->get_sku() ),
        esc_attr( isset( $class ) ? $class : 'button' ),
        esc_html( $product->add_to_cart_text() ),
        '</form>'
        );
    }
    return $html;
}
 //  make that qty selector actually work too
add_action( 'wp_footer' , 'archives_quantity_fields_script' );
function archives_quantity_fields_script(){
    //if( is_shop() || is_product_category() || is_product_tag() ): ?>
    <script type='text/javascript'>
        jQuery( document ).ready( function( $ ) {
            $('.qty').each(function(){ // Loop through all the qty inputs
                var qty = $(this).val();    // grab the value from the input and set to a variable
                $(this).parent( '.quantity' ).next( '.add_to_cart_button' ).attr( 'data-quantity', qty ); // find the add to cart button and set the correct qty value.
            });
        $( document ).on( 'change', '.quantity .qty', function() {
            $( this ).parent( '.quantity' ).next( '.add_to_cart_button' ).attr( 'data-quantity', $( this ).val() );
            //alert("Changed");
        });
    });  
        jQuery(function($) {
            // Update quantity on 'a.button' in 'data-quantity' attribute (for ajax) 
            $(".add_to_cart_button.product_type_simple").on('click', function() {
                var $button = $(this);
                $button.data('quantity', $button.parent().find('input.qty').val());
            });
            // remove old "view cart" text, only need latest one thanks!
            $(document.body).on("adding_to_cart", function() {
                $("a.added_to_cart").remove();
            });
        });
    </script>
    <?php 
    //endif;
}