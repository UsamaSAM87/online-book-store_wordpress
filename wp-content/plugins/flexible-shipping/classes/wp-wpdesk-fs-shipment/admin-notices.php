<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
} // Exit if accessed directly

if ( ! class_exists( 'WPDesk_Flexible_Shipping_Admin_Notices' ) ) {

	class WPDesk_Flexible_Shipping_Admin_Notices {

		/**
		 *
		 */
		const SETTINGS_CHECKED_OPTION_NAME = 'flexible_shipping_smsc';

		/**
		 *
		 */
		const SETTINGS_CHECKED_OPTION_NAME_DISMISS = 'flexible_shipping_smsc_dismiss';

		/**
		 *
		 */
		const SETTINGS_CHECKED_OPTION_VALUE_SHOW_MESSAGE = '1';

		/**
		 *
		 */
		const SETTINGS_CHECKED_OPTION_VALUE_DO_NOT_SHOW_MESSAGE = '2';

		/**
		 *
		 */
		const BASED_ON_VALUE = 'value';

		/**
		 * @var Flexible_Shipping_Plugin
		 */
		private $plugin;

		/**
		 * WPDesk_Flexible_Shipping_Export constructor.
		 *
		 * @param Flexible_Shipping_Plugin $plugin
		 */
		public function __construct( Flexible_Shipping_Plugin $plugin ) {
			$this->plugin = $plugin;
			$this->hooks();
		}

		/**
		 *
		 */
		private function hooks() {
			//add_action( 'admin_notices', array( $this, 'admin_notices_plugin_versions' ) );
			add_action( 'admin_notices', array( $this, 'admin_notices_plugin_activepayments' ) );
			add_action( 'admin_notices', array( $this, 'admin_notices_plugin_enadawca' ) );
			add_action( 'admin_notices', array( $this, 'admin_notices_plugin_pwr' ) );
			add_action( 'admin_notices', array( $this, 'admin_notices_plugin_woo_fs' ) );

			add_action( 'admin_notices', array( $this, 'admin_notices_taxes' ) );

			add_action( 'wp_ajax_flexible_shipping_taxes_notice', array( $this, 'wp_ajax_flexible_shipping_taxes_notice' ) );
		}

		/**
		 *
		 */
		public function wp_ajax_flexible_shipping_taxes_notice() {
			update_option( self::SETTINGS_CHECKED_OPTION_NAME_DISMISS, 1 );
		}


		/**
		 * @param WC_Shipping_Method $shipping_method
		 *
		 * @return bool
		 */
		private function has_value_based_rule( $shipping_method ) {
			$methods = get_option( 'flexible_shipping_methods_' . $shipping_method->instance_id, array() );
			if ( is_array( $methods )  ) {
				foreach ( $methods as $method_settings ) {
					if ( isset( $method_settings['method_rules'] ) && is_array( $method_settings['method_rules'] ) ) {
						foreach ( $method_settings['method_rules'] as $rule ) {
							if ( isset( $rule['based_on'] ) && $rule['based_on'] == self::BASED_ON_VALUE ) {
								return true;
							}
						}
					}
				}
			}
			return false;
		}

		/**
		 *
		 */
		private function update_show_admin_notice_taxes_option() {
			$has_value_based_rule = false;
			$shipping_zones = WC_Shipping_Zones::get_zones();
			$shipping_zones[0] = WC_Shipping_Zones::get_zone_by( 'zone_id', 0 );
			foreach ( $shipping_zones as $zone_id => $shipping_zone_array ) {
				$shipping_zone = WC_Shipping_Zones::get_zone( $zone_id );
				/** @var WC_Shipping_Zone $shipping_zone */
				$shipping_methods = $shipping_zone->get_shipping_methods();
				foreach ( $shipping_methods as $shipping_method ) {
					/** @var WC_Shipping_Method $shipping_method */
					if ( $shipping_method->id == 'flexible_shipping' ) {
						$has_value_based_rule = $has_value_based_rule || $this->has_value_based_rule( $shipping_method );
					}
				}
			}
			if ( $has_value_based_rule ) {
				$shipping_methods_settings_checked = self::SETTINGS_CHECKED_OPTION_VALUE_SHOW_MESSAGE;
			}
			else {
				$shipping_methods_settings_checked = self::SETTINGS_CHECKED_OPTION_VALUE_DO_NOT_SHOW_MESSAGE;
			}
			update_option( self::SETTINGS_CHECKED_OPTION_NAME, $shipping_methods_settings_checked );
		}

		/**
		 * @return bool
		 */
		public function is_show_admin_notice_taxes() {
			$shipping_methods_settings_checked = get_option( self::SETTINGS_CHECKED_OPTION_NAME, '0' );
			if ( $shipping_methods_settings_checked == '0' ) {
				$this->update_show_admin_notice_taxes_option();
				$shipping_methods_settings_checked = get_option( self::SETTINGS_CHECKED_OPTION_NAME, '0' );
			}
			return $shipping_methods_settings_checked == self::SETTINGS_CHECKED_OPTION_VALUE_SHOW_MESSAGE;
		}

		/**
		 * @return bool
		 */
		public function is_in_zones() {
			if ( isset( $_GET['page'] ) && $_GET['page'] == 'wc-settings'
			     && isset( $_GET['tab'] ) && $_GET['tab'] == 'shipping'
			     && ( !isset( $_GET['section'] ) || $_GET['section'] == '' )
			) {
				return true;
			}
			return false;
		}

		/**
		 * @return bool
		 */
		public function is_admin_notice_taxes_dismissed() {
			if ( get_option( self::SETTINGS_CHECKED_OPTION_NAME_DISMISS, '0' ) == '1' ) {
				return true;
			}
			return false;
		}

		/**
		 *
		 */
		public function admin_notices_taxes() {
			if ( wc_tax_enabled() && !$this->is_admin_notice_taxes_dismissed() && $this->is_show_admin_notice_taxes() ) {
				$class = 'notice notice-error is-dismissible flexible-shipping-taxes-notice';
				$message = sprintf(
					__( 'Flexible Shipping has changed the calculation method for shipping rules. Currently, the cart value for rules based on price is determined by WooCommerce tax option "Display prices during cart and checkout". You should check the %ssettings%s.', 'flexible-shipping' ),
					'<a href="' . admin_url( 'admin.php?page=wc-settings&tab=tax' ) . '">',
					'</a>'
				);
				$this->print_notice( $class, $message );
			}
		}

		/*
		 *
		 */
		public function admin_notices_plugin_activepayments() {
			if ( is_plugin_active( 'woocommerce-active-payments/activepayments.php' ) ) {
				$plugin_activepayments = get_plugin_data( WP_PLUGIN_DIR . '/woocommerce-active-payments/activepayments.php' );
				$version_compare = version_compare( $plugin_activepayments['Version'], '2.7' );
				if ( $version_compare < 0 ) {
					$class = 'notice notice-error';
					$message = __( 'Flexible Shipping requires at least version 2.7 of Active Payments plugin.', 'flexible-shipping' );
					$this->print_notice( $class, $message );
				}
			}
		}

		/**
		 *
		 */
		public function admin_notices_plugin_enadawca() {
			if ( is_plugin_active( 'woocommerce-enadawca/woocommerce-enadawca.php' ) ) {
				$plugin_enadawca = get_plugin_data( WP_PLUGIN_DIR . '/woocommerce-enadawca/woocommerce-enadawca.php' );
				$version_compare = version_compare( $plugin_enadawca['Version'], '1.2' );
				if ( $version_compare < 0 ) {
					$class = 'notice notice-error';
					$message = __( 'Flexible Shipping requires at least version 1.2 of eNadawca plugin.', 'flexible-shipping' );
					$this->print_notice( $class, $message );
				}
			}
		}

		/**
		 *
		 */
		public function admin_notices_plugin_pwr() {
			if ( is_plugin_active( 'woocommerce-paczka-w-ruchu/woocommerce-paczka-w-ruchu.php' ) ) {
				$plugin_pwr = get_plugin_data( WP_PLUGIN_DIR . '/woocommerce-paczka-w-ruchu/woocommerce-paczka-w-ruchu.php' );
				$version_compare = version_compare( $plugin_pwr['Version'], '1.1' );
				if ( $version_compare < 0 ) {
					$class = 'notice notice-error';
					$message = __( 'Flexible Shipping requires at least version 1.1 of Paczka w Ruchu plugin.', 'flexible-shipping' );
					$this->print_notice( $class, $message );
				}
			}
		}

		/*
		 *
		 */
		public function admin_notices_plugin_woo_fs() {
			if ( is_plugin_active( 'woo-flexible-shipping/flexible-shipping.php' ) ) {
				$class = 'notice notice-error';
				$message = sprintf( __( 'You are using WooCommerce Flexible Shipping below 1.4. Please deactivate it on %splugins page%s. Read about big changes in Flexible Shipping on %sour blog →%s', 'flexible-shipping' ), '<a href="' . admin_url('plugins.php') . '">', '</a>', '<a href="https://www.wpdesk.pl/blog/nowy-flexible-shipping/">', '</a>' );
				$this->print_notice( $class, $message );
			}
		}

		/**
		 * @param string $class
		 * @param string $message
		 */
		private function print_notice( $class, $message ) {
			printf( '<div class="%1$s"><p>%2$s</p></div>', $class, $message );
		}

	}
}
