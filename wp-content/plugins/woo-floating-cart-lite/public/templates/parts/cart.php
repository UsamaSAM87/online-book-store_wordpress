<?php

/**
 * Provide a public-facing view for the plugin
 *
 * This file is used to markup the cart part of the minicart.
 *
 * This template can be overridden by copying it to yourtheme/woo-floating-cart/parts/cart.php.
 *
 * HOWEVER, on occasion we will need to update template files and you (the theme developer).
 * will need to copy the new files to your theme to maintain compatibility. We try to do this.
 * as little as possible, but it does happen. When this occurs the version of the template file will.
 * be bumped and the readme will list any important changes.
 *
 * @link       http://xplodedthemes.com
 * @since      1.4.5
 *
 * @package    XT_Woo_Floating_Cart
 * @subpackage XT_Woo_Floating_Cart/public/templates/parts
 */
 
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
} 
?>

<div class="xt_woofc-inner">
	<div class="xt_woofc-wrapper">
		
		<?php xt_woo_floating_cart_template('parts/cart/header'); ?>

		<div class="xt_woofc-body">

			<?php do_action( 'xt_woofc_before_cart_body_header' ); ?>
            <div class="xt_woofc-body-header">
                <div class="xt_woofc-notices-wrapper"></div>
                <?php do_action( 'xt_woofc_cart_body_header' ); ?>
            </div>
			<?php do_action( 'xt_woofc_after_cart_body_header' ); ?>

			<?php xt_woo_floating_cart_template('parts/cart/list'); ?>

			<?php do_action( 'xt_woofc_before_cart_body_footer' ); ?>
            <div class="xt_woofc-body-footer"><?php do_action( 'xt_woofc_cart_body_footer' ); ?></div>
			<?php do_action( 'xt_woofc_after_cart_body_footer' ); ?>

		</div> <!-- .xt_woofc-body -->

		<?php xt_woo_floating_cart_template('parts/cart/footer'); ?>
		
		<?php xt_woofc_spinner_html(); ?>
		
	</div> <!-- .xt_woofc-wrapper -->
</div> <!-- .xt_woofc-inner -->