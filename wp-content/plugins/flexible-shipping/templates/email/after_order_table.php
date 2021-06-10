<?php
/**
 * Shipment tracking links
 *
 * This template can be overridden by copying it to yourtheme/flexible-shipping/email/after_order_table.php
 *
 * @author  WP Desk
 * @version 1.0.0
 */ 
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly
?>
<h2><?php _e( 'Shipment', 'flexible-shipping' ); ?></h2>
<?php foreach ( $shipments as $shipment ) : ?>
	<p>
		<?php _e( 'Track shipment: ', 'flexible-shipping' ); ?><a target="_blank" href="<?php echo $shipment['tracking_url']; ?>"><?php echo $shipment['tracking_number']; ?></a>
	</p>
<?php endforeach; ?>
