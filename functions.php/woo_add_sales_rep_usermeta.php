// Add REP field to user profiles
function add_sales_rep_field( $user ) {
    ?>
    <h3><?php _e("Sales Rep Information", "sales-rep-manager"); ?></h3>
    
    <table class="form-table">
        <tr>
            <th><label for="sales_rep"><?php _e("Select Sales Rep"); ?></label></th>
            <td>
                <?php 
                // You should fetch this array from a reliable data source or define it elsewhere.
                $sales_reps = array('Denny', 'Lucas', 'Sean', 'Jason', 'Greenhouse'); 
                ?>
                <select name="sales_rep" id="sales_rep">
                    <option value=""><?php _e("None"); ?></option>
                    <?php foreach($sales_reps as $rep) : ?>
                        <option value="<?php echo esc_attr($rep); ?>" <?php selected(get_user_meta($user->ID, 'user_sales_rep', true), $rep); ?>>
                            <?php echo esc_html($rep); ?>
                        </option>

                    <?php endforeach; ?>
                </select>
            </td>
        </tr>
    </table>
    <?php
}
add_action( 'show_user_profile', 'add_sales_rep_field' );
add_action( 'edit_user_profile', 'add_sales_rep_field' );
function update_sales_rep_for_customer_orders( $user_id, $sales_rep ) {
    $customer_orders = wc_get_orders( array( 'customer_id' => $user_id ) );
    foreach ( $customer_orders as $order ) {
        $order->update_meta_data( 'user_sales_rep', $sales_rep );
        $order->save();
    }
}
 // Save the field sales rep info in the user's meta data
function save_sales_rep_field( $user_id ) {
    if ( !current_user_can( 'edit_user', $user_id ) ) {
        return false;
    }
    
    update_user_meta( $user_id, 'user_sales_rep', $_POST['sales_rep'] );
    //update_sales_rep_for_customer_orders( $user_id, $_POST['sales_rep'] );
}
add_action( 'personal_options_update', 'save_sales_rep_field' );
add_action( 'edit_user_profile_update', 'save_sales_rep_field' ); 
function assign_sales_rep_to_order_meta( $order_id ) {
    $order = wc_get_order( $order_id );
    $customer_user_id = $order->get_customer_id();

    if ( $customer_user_id ) {
        $sales_rep = get_user_meta( $customer_user_id, 'user_sales_rep', true );
        if ( !empty( $sales_rep ) ) {
            update_post_meta( $order_id, 'user_sales_rep', $sales_rep );
        }
    }
}
add_action( 'woocommerce_checkout_update_order_meta', 'assign_sales_rep_to_order_meta' );
 // print rep in ship to address
function custom_order_formatted_shipping_address( $address, $order ) {
    $customer_user_id = $order->get_customer_id();
    if ( $customer_user_id ) {
        $sales_rep = get_user_meta( $customer_user_id, 'user_sales_rep', true );
        if ( ! empty( $sales_rep ) ) {
            //$address['user_sales_rep'] = $sales_rep ? 'Sales Rep: ' . $sales_rep : 'Sales Rep: None';
            $address['user_sales_rep'] = $sales_rep; // Do not prefix with 'Sales Rep:'
        }
    }
    return $address;
}
add_filter( 'woocommerce_order_formatted_shipping_address', 'custom_order_formatted_shipping_address', 10, 2 );
function custom_order_formatted_billing_address( $address, $order ) {
    $customer_user_id = $order->get_customer_id();
    if ( $customer_user_id ) {
        $sales_rep = get_user_meta( $customer_user_id, 'user_sales_rep', true );
        if ( !empty( $sales_rep ) ) {
            //$address['user_sales_rep'] = $sales_rep ? 'Sales Rep: ' . $sales_rep : 'Sales Rep: None';
            $address['user_sales_rep'] = $sales_rep;
        }
    }
    return $address;
}
add_filter( 'woocommerce_order_formatted_billing_address', 'custom_order_formatted_billing_address', 10, 2 );
 // Correctly append the sales rep placeholder to the address format
function add_sales_rep_to_address_format( $formats ) {
    foreach ( $formats as $key => $format ) {
        $formats[$key] .= "\n{user_sales_rep}";
    }
    return $formats;
}
add_filter( 'woocommerce_localisation_address_formats', 'add_sales_rep_to_address_format' );
// place REP in address as new line 
function add_sales_rep_to_address_replacements( $replacements, $args ) {
    // Replace the placeholder with 'Sales Rep: <Name>' or 'Sales Rep: None'
    $replacements['{user_sales_rep}'] = !empty($args['user_sales_rep']) ? 'Sales Rep: ' . $args['user_sales_rep'] : 'Sales Rep: None';
    return $replacements;
}
add_filter( 'woocommerce_formatted_address_replacements', 'add_sales_rep_to_address_replacements', 10, 2 );
// Add a new column header for 'Sales Rep'
add_filter( 'manage_edit-shop_order_columns', 'add_sales_rep_column_header', 20 );
function add_sales_rep_column_header( $columns ) {
    $new_columns = array();

    foreach ( $columns as $column_name => $column_info ) {
        $new_columns[ $column_name ] = $column_info;

        // Add the sales rep column right after the order status column
        if ( 'order_status' === $column_name ) {
            $new_columns['show_sales_rep'] = __( 'Rep', 'sales-rep-manager' );
        }
    }

    return $new_columns;
}
// Populate the new column with the sales rep data
function add_sales_rep_column_content( $column ) {
    global $post;
    if ( 'show_sales_rep' === $column ) {
        $order = wc_get_order( $post->ID );
        $customer_user_id = $order->get_customer_id();
        if ( $customer_user_id ) {
            $sales_rep = get_user_meta( $customer_user_id, 'user_sales_rep', true );
            if ($sales_rep) {
                // Make sales rep name clickable and add a query arg to filter orders by this rep
                $filter_link = esc_url( add_query_arg( 'sales_rep_filter', urlencode( $sales_rep ), admin_url( 'edit.php?post_type=shop_order' ) ) );
                printf( '<a href="%s">%s</a>', $filter_link, esc_html( $sales_rep ) );
            } else {
                echo 'None';
            }
        }
    }
}
add_action( 'manage_shop_order_posts_custom_column', 'add_sales_rep_column_content' );

// show only this rep's orders when name clicked in rep column
add_action( 'pre_get_posts', 'filter_orders_by_sales_rep' );
function filter_orders_by_sales_rep( $query ) {
    if ( ! is_admin() || ! $query->is_main_query() || 'shop_order' !== $query->get( 'post_type' ) ) {
        return;
    }

    if ( ! empty( $_GET['sales_rep_filter'] ) ) {
        $sales_rep = urldecode( $_GET['sales_rep_filter'] );
        $query->set( 'meta_query', array(
            array(
                'key'     => 'user_sales_rep', // Changed from '_sales_rep' to 'sales_rep'
                'value'   => $sales_rep,
                'compare' => '=',
            ),
        ));
    }
    
}
// Remove rep filter button
add_action( 'restrict_manage_posts', 'add_clear_sales_rep_filter_button' );
function add_clear_sales_rep_filter_button() {
    global $typenow;

    if ( 'shop_order' === $typenow && ! empty( $_GET['sales_rep_filter'] ) ) {
        $url = remove_query_arg( 'sales_rep_filter' );
        echo '<a href="' . esc_url( $url ) . '" class="button">' . __( 'Clear sales rep filter', 'sales-rep-manager' ) . '</a>';
    }
}
