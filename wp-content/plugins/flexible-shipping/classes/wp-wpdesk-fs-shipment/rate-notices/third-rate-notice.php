<?php

namespace WPDesk\FS\Rate;

class ThirdRateNotice extends RateNotice
{

	const CLOSE_TEMPORARY_NOTICE  = 'close-temporary-notice-number';

    const SETTINGS_VARIANT_ID = 'notice_3';

	/**
	 * Action links
	 *
	 * @return array
	 */
	protected function action_links() {
		$actions[] = sprintf(
			__( '%1$sOk, you deserved it%2$s', 'flexible-shipping' ),
			'<a target="_blank" href="' . esc_url( 'https://wpde.sk/fs-rate-3' ) . '">',
			'</a>'
		);
		$actions[] = sprintf(
			__( '%1$sNope, maybe later%2$s', 'flexible-shipping' ),
			'<a data-type="number" class="fs close-temporary-notice notice-dismiss-link" data-source="' . self::CLOSE_TEMPORARY_NOTICE . '" href="#">',
			'</a>'
		);
		$actions[] = sprintf(
			__( '%1$sI already did%2$s', 'flexible-shipping' ),
			'<a class="close-rate-notice notice-dismiss-link" data-source="already-did" href="#">',
			'</a>'
		);

		return $actions;
	}

	/**
	 * Should show message
	 *
	 * @return bool
	 */
	public function should_show_message() {
		$total_orders = intval( get_option( Flexible_Shipping_Order_Counter::FS_ORDER_COUNTER, '0' ) );
		if ( $total_orders >= 100 ) {
			return true;
		}

		return false;
	}

	/**
	 * Get rate message
	 *
	 * @return string
	 */
	protected function get_message() {
		$message   = __( 'Awesome, you just crossed the 100 orders on Flexible Shipping method. Could you please do me a BIG favor and give it a 5-star rating on WordPress? ~ Peter', 'flexible-shipping' );
		$message .= '<br/>';
		$message .= implode( ' | ', $this->action_links() );
		return $message;
	}

}
