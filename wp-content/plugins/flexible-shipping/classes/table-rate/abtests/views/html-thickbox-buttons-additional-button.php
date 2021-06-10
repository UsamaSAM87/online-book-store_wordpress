<?php
/**
 * @var $support_link string
 *
 */
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
?><div class="footer">
	<a href="#" class="button button-secondary button-close tracker-button-close"><?php _e( 'Cancel', 'flexible-shipping' ); ?></a>
	<a target="_blank" href="<?php echo $support_link; ?>" class="button button-secondary button-support"><?php _e( 'Any problems? Write to us.', 'flexible-shipping' ); ?></a>
	<a href="#" class="button button-primary button-deactivate allow-deactivate"><?php _e( 'Skip &amp; Deactivate', 'flexible-shipping' ); ?></a>
</div>
