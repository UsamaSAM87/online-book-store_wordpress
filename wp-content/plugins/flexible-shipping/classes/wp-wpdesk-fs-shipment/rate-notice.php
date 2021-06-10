<?php

namespace WPDesk\FS\Rate;

/**
 * Display rate notice.
 */
class WPDesk_Flexible_Shipping_Rate_Notice implements \FSVendor\WPDesk\PluginBuilder\Plugin\Hookable {

	const FIRST_NOTICE_MIN_ORDERS = 100;
	const CLOSE_TEMPORARY_NOTICE_NUMBER  = 'close-temporary-notice-number';
	const CLOSE_TEMPORARY_NOTICE_DATE    = 'close-temporary-notice-date';
	const CLOSE_ALREADY_DID              = 'already-did';

	const SETTINGS_OPTION_DISMISSED_COUNT = 'flexible_shipping_rate_dismissed_count';

	const SETTINGS_RATE_NOTICE_VARIANT_ID = 'flexible_shipping_rate_notice_variant_id';

	const SETTINGS_OPTION_RATE_NOTICE_DATE_DISMISS = 'flexible_shipping_rate_notice_date_dismiss';
	const SETTINGS_OPTION_RATE_NOTICE_NUMBER_DISMISS = 'flexible_shipping_rate_notice_number_dismiss';

	/**
	 * Hooks.
	 */
	public function hooks() {
		add_action( 'admin_notices', array( $this, 'add_admin_notice_action' ) );
		add_action( 'wpdesk_notice_dismissed_notice', array( $this, 'reset_rate_variant_action' ), 10, 2 );
		add_action( 'wp_ajax_flexible_shipping_rate_notice', array( $this, 'wp_ajax_flexible_shipping_rate_notice' ) );
		add_action( 'wp_ajax_flexible_shipping_close_rate_notice', array( $this, 'wp_ajax_flexible_shipping_close_rate_notice' ) );
	}

	/**
	 * Reset rate variant
	 *
	 * @param string $notice_name Notice name.
	 * @param string $source      Sorcue.
	 */
	public function reset_rate_variant_action( $notice_name, $source ) {
		$variant_id = get_option( self::SETTINGS_RATE_NOTICE_VARIANT_ID );
		if ( 'flexible_shipping_rate_plugin' !== $notice_name ) {
			return false;
		}

		$dismissed_count = (int) get_option( self::SETTINGS_OPTION_DISMISSED_COUNT, 0 );

		if ( ( empty( $source ) || self::CLOSE_TEMPORARY_NOTICE_DATE === $source ) && ( $variant_id === FirstRateNotice::SETTINGS_VARIANT_ID || $variant_id === SecondRateNotice::SETTINGS_VARIANT_ID ) ) {
			update_option( self::SETTINGS_OPTION_RATE_NOTICE_DATE_DISMISS, date( "Y-m-d H:i:s", strtotime( 'NOW + 2 weeks' ) ) );
			delete_option( \FSVendor\WPDesk\Notice\PermanentDismissibleNotice::OPTION_NAME_PREFIX . $notice_name );
			update_option( self::SETTINGS_OPTION_DISMISSED_COUNT, 1 );
		} elseif ( ( empty( $source ) || self::CLOSE_TEMPORARY_NOTICE_NUMBER === $source ) && $variant_id === ThirdRateNotice::SETTINGS_VARIANT_ID ) {
			update_option( Flexible_Shipping_Order_Counter::FS_ORDER_COUNTER, 0 );
			delete_option( \FSVendor\WPDesk\Notice\PermanentDismissibleNotice::OPTION_NAME_PREFIX . $notice_name );
			update_option( self::SETTINGS_OPTION_DISMISSED_COUNT, 1 );
		} elseif ( self::CLOSE_ALREADY_DID === $source ) {
			update_option( \FSVendor\WPDesk\Notice\PermanentDismissibleNotice::OPTION_NAME_PREFIX . $notice_name, 1 );
		}

		if ( $dismissed_count > 0 ) {
			update_option( \FSVendor\WPDesk\Notice\PermanentDismissibleNotice::OPTION_NAME_PREFIX . $notice_name, 1 );
		}

	}



	/**
	 * Should display notice.
	 *
	 * @return bool
	 */
	private function should_display_notice() {
		$current_screen     = get_current_screen();
		$display_on_screens = [ 'shop_order', 'edit-shop_order', 'woocommerce_page_wc-settings' ];
		if ( ! empty( $current_screen ) && in_array( $current_screen->id, $display_on_screens, true ) ) {
			return true;
		}
		return false;
	}

    /**
     * Generate rate notice variant ID
     *
     * @return string
     */
    private function generate_rate_notice_variant_id()
    {
        $variant = get_option(self::SETTINGS_RATE_NOTICE_VARIANT_ID, '0');
        if ( $variant === '0' ) {
            $variant = 'notice_' . mt_rand(1, 3);
            add_option( self::SETTINGS_RATE_NOTICE_VARIANT_ID, $variant );
            $this->set_notice_defaults( $variant );
        }
        return $variant;
    }

	/**
	 * Set defaults for notice
	 *
     * @param string $variant Variant ID.
	 */
	private function set_notice_defaults( $variant ) {
		if( 'notice_3' !== $variant ) {
			add_option( self::SETTINGS_OPTION_RATE_NOTICE_DATE_DISMISS, date( "Y-m-d H:i:s", strtotime('NOW + 2 weeks') ) );
		} else {
			add_option( Flexible_Shipping_Order_Counter::FS_ORDER_COUNTER, 0 );
		}
	}

	/**
	 * Add admin notice.
	 */
	public function add_admin_notice_action()
	{
		$variant = $this->generate_rate_notice_variant_id();
		if ( $this->should_display_notice() ) {
			$creator = new \WPDesk\FS\Rate\RateNoticeCreator();
			$instance = $creator->create( $variant );
			if( $instance->should_show_message() ) {
                $instance->show_message();
            }

		}
	}


}
