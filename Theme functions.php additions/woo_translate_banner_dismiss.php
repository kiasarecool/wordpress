<?php

// Translate dismiss on notice [x] Click to dismiss [x] 
function wc_custom_store_notice_updated( $text ) {
	return str_replace( 'Dismiss', '[x] Click to dismiss [x]', $text );
}
add_filter( 'woocommerce_demo_store', 'wc_custom_store_notice_updated' );
