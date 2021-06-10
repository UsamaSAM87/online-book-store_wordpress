<?php

/**
 * Provide a public-facing view for the plugin
 *
 * This file is used to markup the cart header.
 *
 * This template can be overridden by copying it to yourtheme/woo-floating-cart/parts/cart/header.php.
 *
 * HOWEVER, on occasion we will need to update template files and you (the theme developer).
 * will need to copy the new files to your theme to maintain compatibility. We try to do this.
 * as little as possible, but it does happen. When this occurs the version of the template file will.
 * be bumped and the readme will list any important changes.
 *
 * @link       http://xplodedthemes.com
 * @since      1.3.0
 *
 * @package    XT_Woo_Floating_Cart
 * @subpackage XT_Woo_Floating_Cart/public/templates/parts
 */
 
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
} 
?>

<div class="xt_woofc-header">
	
	<span class="xt_woofc-title">
        <?php echo apply_filters('xt_woofc_lang_header_title', esc_html__('Cart', 'woo-floating-cart')); ?>
    </span>
    <span class="xt_woofc-notif">
        <span class="xt_woofc-undo">
            <?php echo apply_filters('xt_woofc_lang_header_item_removed', esc_html__('Item Removed.', 'woo-floating-cart')); ?>
            <a href="#0"><?php echo apply_filters('xt_woofc_lang_header_undo_item_removed', esc_html__('Undo', 'woo-floating-cart')); ?></a>
        </span>
        <span class="xt_woofc-cart-error"></span>
    </span>
    <?php if(xt_woofc_option_bool('enable_coupon_form', false)) : ?>
    <span class="xt_woofc-coupon xt_woofc-visible">
        <a class="xt_woofc-show-coupon" href="#0"><?php echo apply_filters('xt_woofc_lang_header_have_coupon', esc_html__('Have a coupon ?', 'woo-floating-cart')); ?></a>
    </span>
    <?php endif; ?>

</div>
