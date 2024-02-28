<?php

//Remove Font Awesome from WordPress theme to increase pagespeed score
add_action( 'wp_print_styles', 'kias_dequeue_font_awesome_style' );
function kias_dequeue_font_awesome_style() {
      wp_dequeue_style( 'fontawesome' );
      wp_deregister_style( 'fontawesome' );
}


//Remove dashicoin icons to increase pagespeed score
function wpdocs_dequeue_dashicon() {
    
        if (current_user_can( 'update_core' )) {
            return;
        }
        wp_deregister_style('dashicons');
}
add_action( 'wp_enqueue_scripts', 'wpdocs_dequeue_dashicon' );
