<?php
 //Email setup       /////////////////////////////////////////////////////
 // use phpmailer for all website emails

add_action('phpmailer_init', 'my_phpmailer_configuration');
function my_phpmailer_configuration($phpmailer) {
	$phpmailer->isSMTP();
	$phpmailer->Host = 'smtp.YOURHOST.com';  // Specify main and backup SMTP servers
	$phpmailer->SMTPAuth = true;     // Enable SMTP authentication
	$phpmailer->Username = 'Email@YOUR_SITE.com';  // SMTP username
	$phpmailer->Password = 'YOUR PASSWORD!';  // SMTP password
	$phpmailer->SMTPSecure = 'ssl';  // Enable TLS encryption, `ssl` also accepted
	$phpmailer->Port = 465;  // TCP port to connect to
	$phpmailer->setFrom('Email@YOUR_SITE.com', 'PRETTY BUSINESS NAME (FROM NAME)');
	$phpmailer->addReplyTo('SAME OR DIFFRENTl@YOUR_SITE.com', 'PRETTY BUSINESS NAME (REPLY TO NAME)');
    // Adjust PHPMailer settings like SERVER, ADDRESS, PASSWORD, FROM AND REPLY TO 
}