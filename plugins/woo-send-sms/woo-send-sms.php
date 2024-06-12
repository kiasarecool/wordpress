<?php
/*
Plugin Name: WooCommerce Send SMS to Customer
Description: Adds a meta box to the WooCommerce order edit screen to send SMS to customers.
Version: 1.0
Author: kiasarecool
*/

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

// Check if Texty is active
add_action( 'admin_init', 'woo_check_texty_active' );

function woo_check_texty_active() {
    if ( ! is_plugin_active( 'texty/texty.php' ) ) {
        add_action( 'admin_notices', 'woo_texty_inactive_notice' );
        deactivate_plugins( plugin_basename( __FILE__ ) );
    }
}

function woo_texty_inactive_notice() {
    echo '<div class="error"><p><strong>WooCommerce Send SMS to Customer:</strong> The Texty plugin is required and is not active. Please activate Texty to use this plugin.</p></div>';
}

// Add a meta box to the edit order screen
add_action( 'add_meta_boxes', 'woo_send_sms_meta_box' );

function woo_send_sms_meta_box() {
    add_meta_box(
        'send_sms_meta_box',       // Unique ID
        'Send SMS to Customer',    // Box title
        'woo_display_sms_meta_box', // Content callback
        'shop_order',              // Post type
        'side',                    // Context (side, normal, advanced)
        'default'                  // Priority
    );
}

// Display the meta box content
function woo_display_sms_meta_box( $post ) {
    wp_nonce_field( 'send_sms_meta_box', 'send_sms_meta_box_nonce' );

    echo '<label for="sms_message"></label>';
    echo '<textarea id="sms_message" name="sms_message" rows="4" cols="25"></textarea>';
    echo '<button type="button" id="send_sms_button" class="button button-primary">Send SMS</button>';
    echo '<div id="send_sms_result"></div>';
}

// Enqueue admin scripts for order edit screen
add_action( 'admin_enqueue_scripts', 'woo_enqueue_sms_admin_scripts' );

function woo_enqueue_sms_admin_scripts( $hook ) {
    global $post_type;

    if ( 'shop_order' === $post_type ) {
        wp_enqueue_script( 'send_sms_script', plugin_dir_url( __FILE__ ) . 'js/send-sms.js', array( 'jquery' ), '1.0', true );
        wp_localize_script( 'send_sms_script', 'send_sms_ajax', array( 'ajax_url' => admin_url( 'admin-ajax.php' ) ) );
    }
}

// Handle the AJAX request to send SMS
add_action( 'wp_ajax_send_sms_to_customer', 'woo_send_sms_to_customer' );

function woo_send_sms_to_customer() {
    check_ajax_referer( 'send_sms_meta_box', 'security' );

    if ( isset( $_POST['order_id'] ) && isset( $_POST['sms_message'] ) ) {
        $order_id = intval( $_POST['order_id'] );
        $sms_message = sanitize_textarea_field( $_POST['sms_message'] );

        $order = wc_get_order( $order_id );
        $billing_phone = $order->get_billing_phone();

        if ( $billing_phone ) {
            $response = woo_myfunction_to_send_sms( $billing_phone, $sms_message );
            if ( $response['success'] ) {
                wp_send_json_success( 'SMS sent successfully.' );
            } else {
                wp_send_json_error( 'Failed to send SMS: ' . $response['message'] );
            }
        } else {
            wp_send_json_error( 'No billing phone number found.' );
        }
    } else {
        wp_send_json_error( 'Order ID and message are required.' );
    }
}

// Function to send SMS using Texty
function woo_myfunction_to_send_sms( $to, $message ) {
    $status = texty()->gateways()->send( $to, $message );

    $response = [
        'success' => is_wp_error( $status ) ? false : true,
        'message' => is_wp_error( $status ) ? $status->get_error_message() : '',
    ];

    return $response;
}
