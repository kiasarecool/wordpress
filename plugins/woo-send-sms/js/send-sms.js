jQuery(document).ready(function($) {
    $('#send_sms_button').on('click', function() {
        var orderId = $('#post_ID').val();
        var message = $('#sms_message').val();
        var nonce = $('#send_sms_meta_box_nonce').val();

        $.ajax({
            url: send_sms_ajax.ajax_url,
            type: 'POST',
            data: {
                action: 'send_sms_to_customer',
                order_id: orderId,
                sms_message: message,
                security: nonce
            },
            success: function(response) {
                if (response.success) {
                    $('#send_sms_result').html('<div class="updated"><p>' + response.data + '</p></div>');
                    $('#sms_message').val(''); // Clear the textarea
                } else {
                    $('#send_sms_result').html('<div class="error"><p>' + response.data + '</p></div>');
                }
            },
            error: function() {
                $('#send_sms_result').html('<div class="error"><p>An error occurred.</p></div>');
            }
        });
    });
});
