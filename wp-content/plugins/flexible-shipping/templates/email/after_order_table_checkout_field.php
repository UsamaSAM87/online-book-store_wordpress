<?php
/**
 * Checkout field: ie. access point.
 *
 * This template can be overridden by copying it to yourtheme/flexible-shipping/email/after_order_table_checkout_field.php
 *
 * @author  WP Desk
 * @version 1.0.0
 * @package Flexible Shipping.
 *
 * @var string $field_label
 * @var string $field_value
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
} // Exit if accessed directly
?>
<h2><?php echo $field_label; /* phpcs:ignore */ ?></h2>
<p>
	<?php echo $field_value; /* phpcs:ignore */ ?>
</p>
