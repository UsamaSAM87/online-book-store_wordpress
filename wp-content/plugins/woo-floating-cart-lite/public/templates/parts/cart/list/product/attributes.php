<?php

/**
 * Provide a public-facing view for the plugin
 *
 * This file is used to markup the cart list product item attributes.
 *
 * This template can be overridden by copying it to yourtheme/woo-floating-cart/parts/cart/list/product/attributes.php.
 *
 * Available global vars: $product, $cart_item, $cart_item_key
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

$show_sku = xt_woofc_option_bool('cart_product_show_sku', false);
$show_attributes = xt_woofc_option_bool('cart_product_show_attributes', false);

if($show_sku) {
	$sku = $product->get_sku();
}

if($show_attributes) {
	$attributes = xt_woofc_item_attributes( $cart_item );
}

?>

<?php if ( ($show_attributes && !empty($attributes)) || ( $show_sku && !empty($sku)) ): ?>
		
	<div class="xt_woofc-product-attributes">

        <?php if($show_sku && !empty($sku)): ?>
            <dl class="xt_woofc-sku"><dt><?php echo esc_html__( "SKU: ", "woo-floating-cart" ); ?></dt><dd><?php echo $sku; ?></dd></dl>
        <?php endif; ?>

	    <?php if($show_attributes && !empty($attributes)): ?>
		    <?php echo $attributes; ?>
	    <?php endif; ?>

	</div>
		    	
<?php endif; ?>