// Adding multiselector to checkout
// https://stackoverflow.com/questions/13684533/woocommerce-custom-fields-multiselect

add_filter( 'woocommerce_form_field_multiselect', 'custom_multiselect_handler', 10, 4 );

function custom_multiselect_handler( $field, $key, $args, $value ) {

    $options = '';

    if ( ! empty( $args['options'] ) ) {
        foreach ( $args['options'] as $option_key => $option_text ) {
            $options .= '<option value="' . $option_key . '" '. selected( $value, $option_key, false ) . '>' . $option_text .'</option>';
        }

        if ($args['required']) {
            $args['class'][] = 'validate-required';
            $required = '&nbsp;<abbr class="required" title="' . esc_attr__('required', 'woocommerce') . '">*</abbr>';
        }
        else {
            $required = '&nbsp;<span class="optional">(' . esc_html__('optional', 'woocommerce') . ')</span>';
        }

        $field = '<p class="form-row ' . implode( ' ', $args['class'] ) .'" id="' . $key . '_field">
            <label for="' . $key . '" class="' . implode( ' ', $args['label_class'] ) .'">' . $args['label']. $required . '</label>
            <select name="' . $key . '" id="' . $key . '" class="select" multiple="multiple">
                ' . $options . '
            </select>
        </p>' . $args['after'];
    }

    return $field;
}

add_action('woocommerce_after_order_notes', 'my_custom_checkout_field');

function my_custom_checkout_field( $checkout ) {

    echo '<div id="my_custom_checkout_field"><h3>'.__('Please tell us which courses you will visit:').'</h3>';
    echo 'Use CRTL button to select more than one course';
	woocommerce_form_field( 'my_field_name', array(
        'type'          => 'multiselect',
        'class'         => array('course_select form-row-wide'),
        'label'         => __('Please make your choice if you are register for a monthly course'),
        
        'options'       => array(
            'bachataA1' => __('Bachata Beginners A1 (Tuesday, 20:30-22:30)', 'woocommerce' ),
            'bachataA2' => __('Bachata Beginners A2 (Wednesday, 20:30-22:30)', 'woocommerce' ),
			'bachataA3' => __('Bachata Beginners A3 (Monday, 18:30-20:30)', 'woocommerce' ),
			'SalsaA1' => __('Salsa Beginners A1 (Monday, 20:30-22:30)', 'woocommerce' ),
			'SalsaA2' => __('Salsa Beginners A2 (Tuesday, 18:30-20:30)', 'woocommerce' ),
			'BachataB1' => __('Bachata Improvers B1 (Wednesday, 18:30-20:30)', 'woocommerce' ),
			'BachataC1' => __('Bachata Intermediate C1 (Thursday, 19:30-21:30)', 'woocommerce' ),
			'BachataD1' => __('Bachata Advanced D1 (Tuesday, 18:30-20:30)', 'woocommerce' ),
			'LadiesShine' => __('Ladies Shines (Thursday, 18:15-19:30)', 'woocommerce' ),
			'ShowDance' => __('Show Dance Open (Sunday, 17:00-19:00)', 'woocommerce' ),
			'BodyMove' => __('Body movement (Thursday, 18:15-19:30) (not included in the membership )', 'woocommerce' )
        )
        ), $checkout->get_value( 'my_field_name'));
	
    echo '</div>';
	

}

/**
 * Update the order meta with field value
 */
add_action( 'woocommerce_checkout_update_order_meta', 'my_custom_checkout_field_update_order_meta' );

function my_custom_checkout_field_update_order_meta( $order_id ) {
    if ( ! empty( $_POST['my_field_name'] ) ) {
        update_post_meta( $order_id, 'my_field_name', sanitize_text_field( $_POST['my_field_name'] ) );
    }
}
/**
 * Display field value on the order edit page
 */
add_action( 'woocommerce_admin_order_data_after_shipping_address', 'my_custom_checkout_field_display_admin_order_meta', 10, 1 );

function my_custom_checkout_field_display_admin_order_meta($order){
	
    echo '<p><strong>'.__('Я пойду на курсы по:').':</strong> ' . get_post_meta( $order->id, 'my_field_name', true ) . '</p>';
	$coursename = get_post_meta( $order->id, 'my_field_name', true );
	var_dump($coursename);
	
}

add_filter('woocommerce_email_order_meta_keys', 'my_custom_order_meta_keys');

function my_custom_order_meta_keys( $keys ) {
     $keys[] = 'my_field_name'; // This will look for a custom field called 'Tracking Code' and add it to emails
     return $keys;
}
