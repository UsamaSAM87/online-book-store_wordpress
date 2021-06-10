<?php

namespace WPDesk\FS\Rate;

class RateNoticeCreator {

    const SETTINGS_RATE_NOTICE_VARIANT_ID = 'flexible_shipping_rate_notice_variant_id';

	/**
     * Create rate variant
     *
	 * @param string $notice_id Variant ID.
	 *
	 * @return RateNoticeInterface;
	 */
	public function create( $variant ) {
		switch( $variant ) {
			case FirstRateNotice::SETTINGS_VARIANT_ID: return new FirstRateNotice(); break;
			case SecondRateNotice::SETTINGS_VARIANT_ID: return new SecondRateNotice(); break;
			case ThirdRateNotice::SETTINGS_VARIANT_ID: return new ThirdRateNotice(); break;
			default: return new FirstRateNotice();
		}
	}
}
