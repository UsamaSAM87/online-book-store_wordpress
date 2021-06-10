<?php

use FSVendor\WPDesk\ABTesting\ABVariant;

class WPDesk_Flexible_Shipping_AB_Variant_Old implements ABVariant {

	/**
	 * Define a new value for the generated variant.
	 *
	 * @var int
	 */
	const VARIANT_ID = 0;

	/**
	 * Is On?
	 *
	 * @param string $functionality Functionality.
	 *
	 * @return bool
	 */
	public function is_on( $functionality ) {
		return WPDesk_Flexible_Shipping_AB_Deactivation_Contact_Information::CONTACT_INFORMATION_ON_TOP === ! $functionality;
	}

	/**
	 * Get variant id.
	 *
	 * @return int|string
	 */
	public function get_variant_id() {
		return self::VARIANT_ID;
	}
}
