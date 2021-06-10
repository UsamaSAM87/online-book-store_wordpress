<?php

function xt_woofc_wc_get_template_cart_shipping($template_name, $args = array()) {

	if(strpos($template_name, 'cart/cart-shipping.php') !== false && empty($args['show_shipping_calculator']) ) {

		if(xt_woo_floating_cart()->frontend()->enabled() && (xt_woofc_option_bool( 'enable_totals', false ) || xt_woofc_option_bool( 'cart_checkout_form', false ))) {

			$template_name = xt_woo_floating_cart_template( 'parts/cart/shipping', $args, false, true );
		}
	}

	return $template_name;
}
add_filter( 'wc_get_template', 'xt_woofc_wc_get_template_cart_shipping', 10, 2);

