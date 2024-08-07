<?php
/**
 * Plugin Name: WooCommerce View Receipt by kiasarecool
 * Description: Adds a "View Receipt" button to WooCommerce orders and a search function to find invoices by order number.
 * Version: 4.0
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
    echo '<a href="' . $receipt_url . '" class="button tips view" target="_blank" data-tip="' . __( 'View Receipt', 'wc-view-receipt' ) . '">';
    echo '<span class="dashicons dashicons-media-text"></span>';
    echo '</a>';
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

    // Log the URL and headers
    error_log( 'Requesting receipt URL: ' . $receipt_url );
    error_log( 'Authorization header: Basic ' . base64_encode( $consumer_key . ':' . $consumer_secret ) );

    $response = wp_remote_get( $receipt_url, array(
        'headers' => array(
            'Authorization' => 'Basic ' . base64_encode( $consumer_key . ':' . $consumer_secret )
        )
    ));

    if ( is_wp_error( $response ) ) {
        error_log( 'Error fetching receipt: ' . $response->get_error_message() );
        wp_die( __( 'Error fetching receipt.', 'wc-view-receipt' ) );
    }

    $body = wp_remote_retrieve_body( $response );
    $response_code = wp_remote_retrieve_response_code( $response );

    // Log the response code and body
    error_log( 'API Response Code: ' . $response_code );
    error_log( 'API Response Body: ' . $body );

    if ( $response_code != 200 ) {
        wp_die( __( 'Invalid response from API. Response code: ' . $response_code, 'wc-view-receipt' ) );
    }

    $receipt_data = json_decode( $body );

    if ( ! isset( $receipt_data->receipt_url ) ) {
        wp_die( __( 'Receipt URL not found in API response.', 'wc-view-receipt' ) );
    }

    wp_redirect( $receipt_data->receipt_url );
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
                <td><input type="password" name="kiasarecool_consumer_secret" value="<?php echo esc_attr( get_option('kiasarecool_consumer_secret') ); ?>" /></td>
            </tr>
        </table>
        
        <?php submit_button(); ?>
    </form>
    <h2>Find Order Receipt</h2>
    <form method="get" action="<?php echo admin_url( 'admin-ajax.php' ); ?>">
        <input type="hidden" name="action" value="kiasarecool_find_order" />
        <?php wp_nonce_field( 'kiasarecool_find_order', '_wpnonce' ); ?>
        <table class="form-table">
            <tr valign="top">
                <th scope="row">Order Number</th>
                <td><input type="text" name="order_number" value="" /></td>
            </tr>
        </table>
        
        <?php submit_button( 'Find Order' ); ?>
    </form>
</div>
<?php
}

// Register the custom AJAX handler for finding order by order number
add_action( 'wp_ajax_kiasarecool_find_order', 'kiasarecool_find_order_handler' );

function kiasarecool_find_order_handler() {
    if ( ! isset( $_GET['order_number'] ) || ! wp_verify_nonce( $_GET['_wpnonce'], 'kiasarecool_find_order' ) ) {
        wp_die( __( 'Invalid request.', 'wc-view-receipt' ) );
    }

    $order_number = sanitize_text_field( $_GET['order_number'] );
    $order_id = wc_get_order_id_by_order_key( $order_number );

    if ( ! $order_id ) {
        wp_die( __( 'Order not found.', 'wc-view-receipt' ) );
    }

    $receipt_url = wp_nonce_url( admin_url( 'admin-ajax.php?action=kiasarecool_view_receipt&order_id=' . $order_id ), 'kiasarecool_view_receipt' );
    wp_redirect( $receipt_url );
    exit;
}
?>
