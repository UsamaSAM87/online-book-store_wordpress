<?php

// update multicolor field to 2 color fields	
$fields = [
	'cart_checkout_button_bg_color' => array('cart_checkout_button_bg_color', 'cart_checkout_button_bg_hover_color'),
	'cart_checkout_button_text_color' => array('cart_checkout_button_text_color', 'cart_checkout_button_text_hover_color'),
	'cart_header_undo_link_color' => array('cart_header_undo_link_color', 'cart_header_undo_link_hover_color'),
	'cart_product_title_color' => array('cart_product_title_color', 'cart_product_title_hover_color'),
	'cart_product_qty_plus_minus_color' => array('cart_product_qty_plus_minus_color', 'cart_product_qty_plus_minus_hover_color'),
];	

foreach($fields as $field) {
	
	$old_key = $field[0];
	
	$link_color_key = $old_key;
	$hover_color_key = $field[1];
	
	$color = xt_woofc_option($old_key);
	
	if(!empty($color)) {
		
		if(isset($color['link'])) {
			xt_woofc_update_option($link_color_key, $color['link']);
		}
		
		if(isset($color['hover'])) {
			xt_woofc_update_option($hover_color_key, $color['hover']);
		}
	}
}
