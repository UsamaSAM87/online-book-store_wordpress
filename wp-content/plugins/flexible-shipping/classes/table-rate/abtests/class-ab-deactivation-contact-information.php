<?php

use FSVendor\WPDesk\ABTesting\ABTest\EqualGroupsRandomABTest;
use FSVendor\WPDesk\Persistence\PersistentContainer;
use FSVendor\WPDesk\Persistence\Wordpress\WordpressOptionsContainer;
use FSVendor\WPDesk\ABTesting\ABVariant;


/**
 * AB Menu Test https://docs.google.com/document/d/1O7SDr-suN1ooLrHdtkM7L6ARbsnveSj_fsRWMpFr5Ac/
 *
 * We've created 2 groups/variants.
 */
class WPDesk_Flexible_Shipping_AB_Deactivation_Contact_Information extends EqualGroupsRandomABTest {
	const TEST_NAME   = 'deactivation-contact-information';
	const GROUP_COUNT = 3;

	const VARIANT_WITH_CONTACT_INFORMATION_ON_TOP    = 1;
	const VARIANT_WITH_CONTACT_INFORMATION_ON_BOTTOM = 2;
	const VARIANT_WITH_CONTACT_BUTTON                = 3;

	const NEW_USER_AFTER_THIS_DATE = '2019-11-06 01:00:00';

	const VARIANT_ID_FOR_OLD_INSTALLATION = 0;

	const CONTACT_INFORMATION_ON_TOP    = 'contact_information_on_top';
	const CONTACT_INFORMATION_ON_BOTTOM = 'contact_information_on_bottom';
	const CONTACT_INFORMATION_BUTTON    = 'contact_information_button';

	/**
	 * Persistent container
	 *
	 * @var PersistentContainer
	 */
	public $container;

	/**
	 * WPDesk_Flexible_Shipping_AB_Deactivation_Contact_Information constructor.
	 */
	public function __construct() {
		$container       = new WordpressOptionsContainer();
		$this->container = $container;

		parent::__construct( self::GROUP_COUNT, self::TEST_NAME, $container );

		$this->override_id_for_old_user( $container );
	}

	/**
	 * Clears info about variant and draws again
	 */
	public function reset() {
		parent::reset();
		$this->override_id_for_old_user( $this->container );
	}

	/**
	 * If old user then should have static group
	 *
	 * @param PersistentContainer $container Persistent container.
	 */
	private function override_id_for_old_user( PersistentContainer $container ) {
		if ( self::VARIANT_ID_FOR_OLD_INSTALLATION !== $this->current_variant_id && $this->is_old_installation() ) {
			$this->current_variant_id = self::VARIANT_ID_FOR_OLD_INSTALLATION;
			$container->set( $this->get_container_key(), self::VARIANT_ID_FOR_OLD_INSTALLATION );
		}
	}

	/**
	 * If this a old user? If so then FS should work like always.
	 *
	 * @return bool
	 */
	private function is_old_installation() {
		return strtotime( self::NEW_USER_AFTER_THIS_DATE ) > $this->activation_date_according_to_wpdesk_helper();
	}

	/**
	 * Activation date according to wpdesk helper.
	 *
	 * @return int timestamp
	 */
	private function activation_date_according_to_wpdesk_helper() {
		$option_name     = 'plugin_activation_flexible-shipping/flexible-shipping.php';
		$activation_date = get_option( $option_name, current_time( 'mysql' ) );

		if ( ! $activation_date ) {
			return time();
		}

		return strtotime( $activation_date );
	}

	/**
	 * Get variant.
	 *
	 * @return ABVariant
	 */
	public function get_variant() {
		switch ( $this->current_variant_id ) {
			case self::VARIANT_WITH_CONTACT_INFORMATION_ON_TOP:
				return new WPDesk_Flexible_Shipping_AB_Variant_With_Contact_Information_On_Top();
			case self::VARIANT_WITH_CONTACT_INFORMATION_ON_BOTTOM:
				return new WPDesk_Flexible_Shipping_AB_Variant_With_Contact_Information_On_Bottom();
			case self::VARIANT_WITH_CONTACT_BUTTON:
				return new WPDesk_Flexible_Shipping_AB_Variant_With_Contact_Information_Button();
			default:
				return new WPDesk_Flexible_Shipping_AB_Variant_Old();
		}
	}
}
