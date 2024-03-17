<?php
// email + logging
function kiasarecool_log_email_activity($to, $subject, $result) {
    $logger = wc_get_logger();
    $context = array('source' => 'kiasarecool-emails'); 

    $log_message = sprintf(
        "[%s] %s - To: %s, Subject: %s",
        date('Y-m-d H:i:s'),
        ($result ? 'SUCCESS' : 'FAILURE'),
        implode(', ', (array) $to), 
        $subject
    );

    if ($result) {
        $logger->info($log_message, $context);
    } else {
        $logger->error($log_message, $context);
    }
}

add_action('phpmailer_init', 'kiasarecool_phpmailer_configuration_with_logging');
function kiasarecool_phpmailer_configuration_with_logging($phpmailer) {
    $phpmailer->isSMTP();
    $phpmailer->Host = 'smtp.YOURHOST.com'; // Ensure you replace this with your actual SMTP host details.
    $phpmailer->SMTPAuth = true;
    $phpmailer->Username = 'Email@YOUR_SITE.com'; // And this with your SMTP username.
    $phpmailer->Password = 'YOUR PASSWORD!'; // And obviously, your actual password here.
    $phpmailer->SMTPSecure = 'ssl';
    $phpmailer->Port = 465;
    $phpmailer->setFrom('Email@YOUR_SITE.com', 'PRETTY BUSINESS NAME (FROM NAME)');
    $phpmailer->addReplyTo('SAMEOorDIFFRENT@YOUR_SITE.com', 'PRETTY BUSINESS NAME (REPLY TO NAME)');

    // post-send callback
    $phpmailer->action_function = function ($result, $to, $cc, $bcc, $subject, $body) {
        kiasarecool_log_email_activity($to, $subject, $result);
    };
}
/*
  • View logs -> WooCommerce > Status > Logs 
  • look for 'kiasarecool-emails'
ALPHA VERSION 0.0.1 -  USE AT OWN RISK I have not had a chance to test this yet, by all means, it should work fine, but use with caution, and be ready to roll back!
*/
