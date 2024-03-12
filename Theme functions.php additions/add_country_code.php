function sanitize_input_number($input) {

    if (!isset($_GET['number'])) {
        return '';
    }
    $number = $input;
    // Check if 'number' exists in GET and sanitize it
    $number = preg_replace('/[^0-9]/', '', $number);
    $number = preg_replace('/^1/', '', $number);
	$number = str_replace('-', '', $number);
    $sanitized_number = sanitize_text_field($input['number']);

    if (substr($sanitized_number, 0, 2) !== '+1') {
        $sanitized_number = '+1' . $sanitized_number;
    }
    return $sanitized_number;
}