<?php
 // remove that php tag when putting in your functions.php
 // Allow only numeric input on Tel fields - Backend
function validate_phone_numbers($errors, $update, $user) {
    if (isset($_POST['billing_phone']) && !empty($_POST['billing_phone'])) {
        if (!preg_match('/^[0-9]+$/', $_POST['billing_phone'])) {
            $errors->add('billing_phone_error', __('only numbers allowed in billing phone number ie: 8162590252'));
        }
    }
    if (isset($_POST['texty_phone']) && !empty($_POST['texty_phone'])) {
        if (!preg_match('/^[0-9]+$/', $_POST['texty_phone'])) {
            $errors->add('texty_phone_error', __('only numbers allowed in texty phone number ie: 8162590252'));
        }
    }
    if (isset($_POST['shipping_phone']) && !empty($_POST['shipping_phone'])) {
        if (!preg_match('/^[0-9]+$/', $_POST['shipping_phone'])) {
            $errors->add('shipping_phone_error', __('only numbers allowed in shipping phone number ie: 8162590252'));
        }
    }
}
add_action('user_profile_update_errors', 'validate_phone_numbers', 10, 3);
 // Allow only numeric input on Tel fields - Frontend
function validate_tel_input_fields() {
    if (is_account_page() || is_checkout() || is_admin()) {
        ?>
        <script type="text/javascript">
            jQuery(document).ready(function($) {
                // Function to remove existing error messages
                function removeErrorMessage(inputField) {
                    inputField.next('.tel-error-message').remove();
                }
                // Function to show error message
                function showErrorMessage(inputField, message) {
                    removeErrorMessage(inputField); // Remove any existing error messages first
                    // Append a new error message span
                    inputField.after('<span class="tel-error-message" style="color: red; display: block; margin-top: 5px;">' + message + '</span>');
                }
                // Validate all input fields of type 'tel' on input
                $('input[type="tel"]').on('input', function() {
                    var inputField = $(this);
                    var telValue = inputField.val();
                    var sanitizedValue = telValue.replace(/[^0-9]/g, '');
                    // If the value has changed after sanitization, update it and show error message
                    if (telValue !== sanitizedValue) {
                        inputField.val(sanitizedValue); // Correct the value
                        showErrorMessage(inputField, 'Only numeric values are allowed in phone number fields.');
                    } else {
                        removeErrorMessage(inputField); // If the input is now valid, remove the error message
                    }
                });
            });
        </script>
        <?php
    }
}
add_action('wp_footer', 'validate_tel_input_fields');