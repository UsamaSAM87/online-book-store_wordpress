<?php

$fields = array();
$fields['cart_header_undo_color'] = xt_woofc_option('cart_header_undo_color');
$fields['cart_header_undo_link_color'] = xt_woofc_option('cart_header_undo_link_color');
$fields['cart_header_undo_link_hover_color'] = xt_woofc_option('cart_header_undo_link_hover_color');
$fields['typo_header_undo_msg'] = xt_woofc_option('typo_header_undo_msg');

$options = get_option('xt_woofc');

foreach($fields as $key => $value) {

	if(!empty($value)) {
		$new_key = str_replace( 'undo_', '', $key );
		$options[$new_key] = $value;
	}

	if(isset($options[$key])) {
		unset( $options[ $key ] );
	}
}

update_option('xt_woofc', $options);