<?php

$icons = array();
$icons['cart_trigger_icon'] = xt_woofc_option('cart_trigger_icon');
$icons['cart_trigger_close_icon'] = xt_woofc_option('cart_trigger_close_icon');

$options = get_option('xt_woofc');

foreach($icons as $key => $icon) {

    if(!empty($icon) && strpos($icon, 'xt_') === false) {
        $options[$key] = 'xt_'.$icon;
    }
}

update_option('xt_woofc', $options);