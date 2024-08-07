<?php
/**
 * Plugin Name: WooCommerce View Receipt by kiasarecool
 * Description: Adds a "View Receipt" button to WooCommerce orders. No search function 
 * Version: 1.0
 * Author: kiasarecool
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

// Add a custom action to display the "View Receipt" button
add_action( 'woocommerce_admin_order_actions_end', 'kiasarecool_add_view_receipt_button' );

function kiasarecool_add_view_receipt_button( $order ) {
    $order_id = $order->get_id();
    $receipt_url = wp_nonce_url( admin_url( 'admin-ajax.php?action=kiasarecool_view_receipt&order_id=' . $order_id ), 'kiasarecool_view_receipt' );
    echo '<a href="' . $receipt_url . '" class="button" target="_blank">View Receipt</a>';
}

// Register the custom AJAX handler
add_action( 'wp_ajax_kiasarecool_view_receipt', 'kiasarecool_view_receipt_handler' );

function kiasarecool_view_receipt_handler() {
    if ( ! isset( $_GET['order_id'] ) || ! wp_verify_nonce( $_GET['_wpnonce'], 'kiasarecool_view_receipt' ) ) {
        wp_die( __( 'Invalid request.', 'wc-view-receipt' ) );
    }

    $order_id = intval( $_GET['order_id'] );
    $order = wc_get_order( $order_id );

    if ( ! $order ) {
        wp_die( __( 'Order not found.', 'wc-view-receipt' ) );
    }

    $consumer_key = get_option( 'kiasarecool_consumer_key' );
    $consumer_secret = get_option( 'kiasarecool_consumer_secret' );
    $receipt_url = sprintf( 'https://www.kcplantfactory.com/wp-json/wc/v3/orders/%d/receipt', $order_id );

    $response = wp_remote_get( $receipt_url, array(
        'headers' => array(
            'Authorization' => 'Basic ' . base64_encode( $consumer_key . ':' . $consumer_secret )
        )
    ));

    if ( is_wp_error( $response ) ) {
        wp_die( __( 'Error fetching receipt.', 'wc-view-receipt' ) );
    }

    $body = wp_remote_retrieve_body( $response );

    if ( wp_remote_retrieve_response_code( $response ) != 200 ) {
        wp_die( __( 'Invalid response from API.', 'wc-view-receipt' ) );
    }

    wp_redirect( json_decode( $body )->receipt_url );
    exit;
}

// Add settings menu
add_action( 'admin_menu', 'kiasarecool_add_settings_menu' );

function kiasarecool_add_settings_menu() {
    add_options_page( 
        'WooCommerce View Receipt Settings', 
        'WC View Receipt', 
        'manage_options', 
        'kiasarecool-view-receipt-settings', 
        'kiasarecool_settings_page' 
    );
}

// Register settings
add_action( 'admin_init', 'kiasarecool_register_settings' );

function kiasarecool_register_settings() {
    register_setting( 'kiasarecool-settings-group', 'kiasarecool_consumer_key' );
    register_setting( 'kiasarecool-settings-group', 'kiasarecool_consumer_secret' );
}

function kiasarecool_settings_page() {
?>
<div class="wrap">
    <h1>WooCommerce View Receipt Settings</h1>
    <form method="post" action="options.php">
        <?php settings_fields( 'kiasarecool-settings-group' ); ?>
        <?php do_settings_sections( 'kiasarecool-settings-group' ); ?>
        <table class="form-table">
            <tr valign="top">
                <th scope="row">Consumer Key</th>
                <td><input type="text" name="kiasarecool_consumer_key" value="<?php echo esc_attr( get_option('kiasarecool_consumer_key') ); ?>" /></td>
            </tr>
             
            <tr valign="top">
                <th scope="row">Consumer Secret</th>
                <td><input type="text" name="kiasarecool_consumer_secret" value="<?php echo esc_attr( get_option('kiasarecool_consumer_secret') ); ?>" /></td>
            </tr>
        </table>
        
        <?php submit_button(); ?>

    </form>
</div>
<?php
}
?>
