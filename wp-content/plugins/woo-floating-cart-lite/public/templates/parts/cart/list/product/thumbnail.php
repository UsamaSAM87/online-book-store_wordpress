<?php

/**
 * Provide a public-facing view for the plugin
 *
 * This file is used to markup the cart list product item thumbnail.
 *
 * This template can be overridden by copying it to yourtheme/woo-floating-cart/parts/cart/list/product/thumbnail.php.
 *
 * Available global vars: $product, $cart_item, $cart_item_key
 *
 * HOWEVER, on occasion we will need to update template files and you (the theme developer).
 * will need to copy the new files to your theme to maintain compatibility. We try to do this.
 * as little as possible, but it does happen. When this occurs the version of the template file will.
 * be bumped and the readme will list any important changes.
 *
 * @link       http://xplodedthemes.com
 * @since      1.0.8.8
 *
 * @package    XT_Woo_Floating_Cart
 * @subpackage XT_Woo_Floating_Cart/public/templates/parts
 */
 
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
} 
?>

<?php if ( ! $product->is_visible() || !(xt_woofc_option_bool('cart_product_link_to_single', true))) : ?>

	<?php echo xt_woofc_item_image($product, $cart_item, $cart_item_key); ?>

<?php else: ?>

    <a href="<?php echo esc_url(get_permalink( $product->get_id() )); ?>">
        <?php echo xt_woofc_item_image($product, $cart_item, $cart_item_key); ?>
    </a>

<?php endif; ?>