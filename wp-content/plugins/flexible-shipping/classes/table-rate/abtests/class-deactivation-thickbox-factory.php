<?php

use FSVendor\WPDesk\ABTesting\ABVariant;
use FSVendor\WPDesk\Tracker\Deactivation\PluginData;
use FSVendor\WPDesk\Tracker\Deactivation\Thickbox;

/**
 * Can display deactivation thickbox content.
 */
class WPDesk_Flexible_Shipping_Deactivation_Thickbox_Factory {

	/**
	 * Variant.
	 *
	 * @var ABVariant
	 */
	private $variant;

	/**
	 * @var PluginData
	 */
	private $plugin_data;

	/**
	 * WPDesk_Flexible_Shipping_Deactivation_Thickbox_Factory constructor.
	 *
	 * @param ABVariant  $variant Variant.
	 * @param PluginData $plugin_data .
	 */
	public function __construct( ABVariant $variant, PluginData $plugin_data ) {
		$this->variant     = $variant;
		$this->plugin_data = $plugin_data;
	}

	/**
	 * Create thickbox.
	 *
	 * @return Thickbox
	 */
	public function create_thickbox() {
		$view_file = null;
		if ( $this->variant->is_on( WPDesk_Flexible_Shipping_AB_Deactivation_Contact_Information::CONTACT_INFORMATION_ON_TOP ) ) {
			$view_file = __DIR__ . '/views/html-thickbox-contact-information-on-top.php';
		} elseif ( $this->variant->is_on( WPDesk_Flexible_Shipping_AB_Deactivation_Contact_Information::CONTACT_INFORMATION_ON_BOTTOM ) ) {
			$view_file = __DIR__ . '/views/html-thickbox-contact-information-on-bottom.php';
		} elseif ( $this->variant->is_on( WPDesk_Flexible_Shipping_AB_Deactivation_Contact_Information::CONTACT_INFORMATION_BUTTON ) ) {
			$view_file = __DIR__ . '/views/html-thickbox-contact-information-button.php';
		}
		return new Thickbox( $this->plugin_data, $view_file );
	}
}
