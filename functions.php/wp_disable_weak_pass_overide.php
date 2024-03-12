<?php

// make admins behave 
// Disable weak password admin overide option
add_action('login_init', 'no_weak_password_header');
add_action('admin_head', 'no_weak_password_header');
function no_weak_password_header() {
	echo"<style>.pw-weak{display:none!important}</style>";
	echo'<script>document.getElementById("pw-checkbox").disabled = true;</script>';
}
