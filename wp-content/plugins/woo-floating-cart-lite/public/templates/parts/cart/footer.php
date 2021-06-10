<?php

/**
 * Provide a public-facing view for the plugin
 *
 * This file is used to markup the cart footer.
 *
 * This template can be overridden by copying it to yourtheme/woo-floating-cart/parts/cart/footer.php.
 *
 * HOWEVER, on occasion we will need to update template files and you (the theme developer).
 * will need to copy the new files to your theme to maintain compatibility. We try to do this.
 * as little as possible, but it does happen. When this occurs the version of the template file will.
 * be bumped and the readme will list any important changes.
 *
 * @link       http://xplodedthemes.com
 * @since      1.3.4
 *
 * @package    XT_Woo_Floating_Cart
 * @subpackage XT_Woo_Floating_Cart/public/templates/parts
 */
 
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
} 
?>

<div class="xt_woofc-footer">
	
	<a href="<?php echo xt_woofc_checkout_link(); ?>" data-text="<?php echo xt_woofc_checkout_label(); ?>" data-processing-text="<?php echo xt_woofc_checkout_processing_label(); ?>" class="xt_woofc-checkout xt_woofc-btn">
		<em>
			<span class="xt_woofc-footer-label"><?php echo xt_woofc_checkout_label(); ?></span> <span class="xt_woofc-dash">-</span>
			<span class="amount"><?php echo xt_woofc_checkout_total(); ?></span>
		</em>
	</a>
	
	<?php xt_woo_floating_cart_template('parts/trigger'); ?>
	
</div>
