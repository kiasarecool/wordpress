<?php
// Generate a dynamic log in/out button
add_filter('wp_nav_menu_items', 'float_login_logout', 10, 2);
function float_login_logout($items, $args) {
        ob_start();
        wp_loginout('/my-account/');
        $loginoutlink = ob_get_contents();
        ob_end_clean();
        $items .= $loginoutlink .'</li>';
    return $items;
}