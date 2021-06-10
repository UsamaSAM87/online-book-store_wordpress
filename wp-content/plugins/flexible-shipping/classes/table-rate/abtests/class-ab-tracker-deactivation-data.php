<?php

use FSVendor\WPDesk\ABTesting\ABVariant;

/**
 * AB Test https://docs.google.com/document/d/1JA49dgOqJ1SawEdL506tWdW6zgD30cXDpFZhJ7r_SNo/edit?usp=sharing
 *
 * Class by which we can push some data to the deactivation filter
 */
class WPDesk_Flexible_Shipping_AB_Tracker_Deactivation_Data implements \FSVendor\WPDesk\PluginBuilder\Plugin\Hookable {

	/**
	 * Variant.
	 *
	 * @var ABVariant
	 */
	protected $variant;

	/**
	 * WPDesk_Flexible_Shipping_AB_Tracker_Deactivation_Data constructor.
	 *
	 * @param ABVariant $variant Variant.
	 */
	public function __construct( ABVariant $variant ) {
		$this->variant = $variant;
	}

	/**
	 * Fires hooks
	 *
	 * @return void
	 */
	public function hooks() {
		add_filter( 'wpdesk_tracker_deactivation_data', array( $this, 'append_variant_id_to_data' ) );
	}

	/**
	 * Set variant ID to data array
	 *
	 * @param array $data Data.
	 *
	 * @return array
	 */
	public function append_variant_id_to_data( array $data ) {
		if ( WPDesk_Flexible_Shipping_Tracker::is_plugin_flexible_shipping_in_data( $data ) ) {
			$data['variant_id'] = $this->variant->get_variant_id();
		}
		return $data;
	}

}
