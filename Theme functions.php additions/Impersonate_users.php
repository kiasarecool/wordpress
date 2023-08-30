<?php

// add the following code to your theme's functions.php file

// Add a Impersonating button on the admin bar

function add_impersonate_link_to_admin_bar( $wp_admin_bar ) {
    if ( ! current_user_can('administrator') ) {
        return;
    }

    $args = array(
        'id'     => 'impersonate_user_link',
        'title'  => 'Impersonate a User',
        'href'   => admin_url(),  // Redirects to the dashboard
        'meta'   => array( 'class' => 'impersonate-user-link-class' )
    );

    $wp_admin_bar->add_node( $args );
}

add_action( 'admin_bar_menu', 'add_impersonate_link_to_admin_bar', 100 );


// Add a Stop Impersonating button on the front-end.

function add_frontend_stop_impersonating_button() {
    // Check if the user is currently impersonating another user.
    $current_user_id = get_current_user_id();
    $original_user_id = get_transient('original_user_' . $current_user_id);

    if ($original_user_id) {
        // Display the Stop Impersonating button.
        echo '<div style="position: fixed; bottom: 20px; left: 50%; transform: translateX(-50%); z-index: 9999; background-color: #33cc33; padding: 10px; border: 3px solid #ff0000;">';
        echo '<a href="' . add_query_arg('stop_impersonating', 'true') . '">Stop Impersonating</a>';
        echo '</div>';
    }
}
add_action('wp_footer', 'add_frontend_stop_impersonating_button');


//implement user search

add_action( 'wp_ajax_search_users', 'search_users' );
add_action( 'wp_ajax_impersonate_user', 'impersonate_user' );

function search_users() {
    $search = $_GET['q'];
    $users = new WP_User_Query(array(
        'search' => "*{$search}*",
        'number' => 15,  // Limit number of results
    ));
    $users_found = $users->get_results();
    $user_array = array();
    foreach ( $users_found as $user ) {
        $user_array[] = array( 'id' => $user->ID, 'text' => $user->user_login );
    }
    echo json_encode($user_array);
    die();
}

// Add a Dashboard Widget for Admins and Shop Managers

function add_impersonate_user_widget() {
    if ( current_user_can('administrator') || current_user_can('shop_manager') ) {
        wp_add_dashboard_widget( 'impersonate_user_widget', 'Impersonate User', 'render_impersonate_user_widget' );
    }
}
add_action( 'wp_dashboard_setup', 'add_impersonate_user_widget' );

// Render the widget content

function render_impersonate_user_widget() {
    echo '<select id="impersonate_user_select" style="width: 200px;"></select>';
    echo '<button id="impersonate_button">Impersonate</button>';
    echo '<script>
    jQuery(document).ready(function($) {
        $("#impersonate_user_select").select2({
            ajax: {
                url: ajaxurl,
                dataType: "json",
                delay: 250,
                data: function(params) {
                    return {
                        q: params.term,
                        action: "search_users"
                    };
                },
                processResults: function(data) {
                    return {
                        results: data
                    };
                }
            }
        });
        $("#impersonate_button").click(function() {
            $.post(ajaxurl, { action: "impersonate_user", user_id: $("#impersonate_user_select").val() }, function(response) {
                if ( response === "success" ) {
                    location.reload();
                }
            });
        });
    });
    </script>';
}

// Hook for the AJAX request

if (!session_id()) {
    session_start();
}

add_action( 'wp_ajax_impersonate_user', 'impersonate_user' );

// The impersonation

function impersonate_user() {
    if ( current_user_can('administrator') || current_user_can('shop_manager') ) {
        $user_id = intval($_POST['user_id']);
        if ( $user_id ) {
            set_transient('original_user_' . $user_id, get_current_user_id(), 60*60);  // set for 1 hour
            wp_set_current_user( $user_id );
            wp_set_auth_cookie( $user_id );
            echo 'success';
        }
    }
    die();
}

function stop_impersonating() {
    if ( isset($_GET['stop_impersonating']) && $_GET['stop_impersonating'] == 'true' ) {
        $current_user_id = get_current_user_id();
        $original_user_id = get_transient('original_user_' . $current_user_id);

        if ( $original_user_id ) {
            delete_transient('original_user_' . $current_user_id);
            wp_set_current_user( $original_user_id );
            wp_set_auth_cookie( $original_user_id );
            wp_redirect(admin_url());
            exit;
        }
    }
}

/*
function impersonate_user() {
    if ( current_user_can('administrator') || current_user_can('shop_manager') ) {
        $user_id = intval($_POST['user_id']);
        // Check if the user exists
        if ($user = get_user_by('id', $user_id)) {
            // Save the original user ID in a session variable
            $_SESSION['original_user_id'] = get_current_user_id();
            
            // Set the new user
            wp_set_current_user($user_id);
            wp_set_auth_cookie($user_id);

            echo 'success';
            exit();
        }
    }

    echo 'failure';
    exit();
}
*/