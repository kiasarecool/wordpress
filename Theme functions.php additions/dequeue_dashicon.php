<?php
function wpdocs_dequeue_dashicon() {
    if (is_user_logged_in()) {
        $user = wp_get_current_user();
        
        // Replace 'subadmin' with your actual staff role name, if it's different
        $allowed_roles = array('administrator', 'subadmin'); 
        $user_roles = array_intersect($allowed_roles, $user->roles);
        if (!empty($user_roles)) {
            return;
        }
    }
    wp_dequeue_style('dashicons');
}
add_action( 'wp_enqueue_scripts', 'wpdocs_dequeue_dashicon' );
