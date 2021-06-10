<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
} // Exit if accessed directly

if ( ! class_exists( 'WPDesk_Flexible_Shipping_Export' ) ) {

	class WPDesk_Flexible_Shipping_Export {

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
			add_action( 'wp_ajax_flexible_shipping_export', array( $this, 'wp_ajax_flexible_shipping_export' ) );
		}

		/**
		 *
		 */
		public function wp_ajax_flexible_shipping_export() {
			check_ajax_referer( 'flexible_shipping', 'flexible_shipping_nonce' );
			$ret                      = array( 'status' => 'ok' );
			$flexible_shipping_action = '';
			if ( isset( $_REQUEST['flexible_shipping_action'] ) ) {
				$flexible_shipping_action = $_REQUEST['flexible_shipping_action'];
			}
			if ( $flexible_shipping_action == 'export' ) {
				$instance_id = '';
				if ( isset( $_REQUEST['instance_id'] ) ) {
					$instance_id = $_REQUEST['instance_id'];
				}
				$ret['instance_id'] = $instance_id;
				$methods            = '';
				if ( isset( $_REQUEST['methods'] ) ) {
					$methods = $_REQUEST['methods'];
				}
				$methods_array          = explode( ',', $methods );
				$shipping_method        = WC_Shipping_Zones::get_shipping_method( $instance_id );
				$wc_shipping_classes    = WC()->shipping->get_shipping_classes();
				$ret['shipping_method'] = $shipping_method;

				$all_shipping_methods = flexible_shipping_get_all_shipping_methods();

				/** @var WPDesk_Flexible_Shipping $flexible_shipping */
				$flexible_shipping       = $all_shipping_methods['flexible_shipping'];
				$flexible_shipping_rates = $flexible_shipping->get_all_rates();

				$filename = 'fs_' . str_replace( 'http://', '', str_replace( 'https://', '', site_url() ) ) . '-' . $instance_id;

				$ret['all_rates'] = $flexible_shipping_rates;
				$ret['methods']   = $methods;
				$csv_array        = array();
				$csv_header       = array(
					'Method Title',
					'Method Description',
					'Free Shipping',
					'Maximum Cost',
					'Calculation Method',
					'Visibility',
					'Default',
					'Based on',
					'Min',
					'Max',
					'Cost per order',
					'Additional cost',
					'Value',
					'Shipping Class',
					'Stop',
					'Cancel',
				);
				$csv_array[]      = $csv_header;
				foreach ( $flexible_shipping_rates as $flexible_shipping_rate ) {
					if ( strval( $flexible_shipping_rate['instance_id'] ) !== $instance_id
						|| ! in_array( strval( $flexible_shipping_rate['id'] ), $methods_array, true )
					) {
						continue;
					}
					$filename .= '_' . $flexible_shipping_rate['id'];
					if ( ! isset( $flexible_shipping_rate['method_description'] ) ) {
						$flexible_shipping_rate['method_description'] = '';
					}
					if ( ! isset( $flexible_shipping_rate['method_free_shipping'] ) ) {
						$flexible_shipping_rate['method_free_shipping'] = '';
					}
					if ( ! isset( $flexible_shipping_rate['method_max_cost'] ) ) {
						$flexible_shipping_rate['method_max_cost'] = '';
					}
					if ( ! isset( $flexible_shipping_rate['method_calculation_method'] ) ) {
						$flexible_shipping_rate['method_calculation_method'] = '';
					}
					if ( ! isset( $flexible_shipping_rate['method_visibility'] ) ) {
						$flexible_shipping_rate['method_visibility'] = '';
					}
					if ( $flexible_shipping_rate['method_visibility'] != 'yes' ) {
						$flexible_shipping_rate['method_visibility'] = '';
					}
					if ( ! isset( $flexible_shipping_rate['method_default'] ) ) {
						$flexible_shipping_rate['method_default'] = '';
					}
					if ( $flexible_shipping_rate['method_default'] != 'yes' ) {
						$flexible_shipping_rate['method_default'] = '';
					}
					$csv_array[] = array(
						$flexible_shipping_rate['method_title'],
						$flexible_shipping_rate['method_description'],
						$flexible_shipping_rate['method_free_shipping'],
						$flexible_shipping_rate['method_max_cost'],
						$flexible_shipping_rate['method_calculation_method'],
						$flexible_shipping_rate['method_visibility'],
						$flexible_shipping_rate['method_default'],
						'',
						'',
						'',
						'',
						'',
						'',
						'',
						'',
						'',
					);
					foreach ( $flexible_shipping_rate['method_rules'] as $method_rule ) {
						if ( ! isset( $method_rule['based_on'] ) ) {
							$method_rule['based_on'] = '';
						}
						if ( ! isset( $method_rule['min'] ) ) {
							$method_rule['min'] = '';
						}
						if ( ! isset( $method_rule['max'] ) ) {
							$method_rule['max'] = '';
						}
						if ( ! isset( $method_rule['cost_per_order'] ) ) {
							$method_rule['cost_per_order'] = '';
						}
						if ( ! isset( $method_rule['cost_additional'] ) ) {
							$method_rule['cost_additional'] = '';
						}
						if ( ! isset( $method_rule['per_value'] ) ) {
							$method_rule['per_value'] = '';
						}
						if ( ! isset( $method_rule['shipping_class'] ) ) {
							$method_rule['shipping_class'] = '';
						} else {
							$method_shipping_class = $method_rule['shipping_class'];
							if ( ! is_array( $method_shipping_class ) ) {
								$method_shipping_class = array( $method_shipping_class );
							}
							$method_rule['shipping_class'] = '';
							foreach ( $method_shipping_class as $shipping_class ) {
								if ( in_array( $shipping_class, array( 'none', 'any', 'all' ) ) ) {
									$method_rule['shipping_class'] .= $shipping_class;
									$method_rule['shipping_class'] .= ',';
								}
							}
							foreach ( $wc_shipping_classes as $shipping_class ) {
								if ( in_array( $shipping_class->term_id, $method_shipping_class ) ) {
									$method_rule['shipping_class'] .= $shipping_class->name;
									$method_rule['shipping_class'] .= ',';
								}
							}
							$method_rule['shipping_class'] = trim( $method_rule['shipping_class'], ',' );
						}
						if ( ! isset( $method_rule['stop'] ) ) {
							$method_rule['stop'] = '';
						}
						if ( $method_rule['stop'] == '1' ) {
							$method_rule['stop'] = 'yes';
						} else {
							$method_rule['stop'] = '';
						}
						if ( ! isset( $method_rule['cancel'] ) ) {
							$method_rule['cancel'] = '';
						}
						if ( $method_rule['cancel'] == '1' ) {
							$method_rule['cancel'] = 'yes';
						} else {
							$method_rule['cancel'] = '';
						}
						$csv_array[] = array(
							$flexible_shipping_rate['method_title'],
							'',
							'',
							'',
							'',
							'',
							'',
							$method_rule['based_on'],
							$method_rule['min'],
							$method_rule['max'],
							$method_rule['cost_per_order'],
							$method_rule['cost_additional'],
							$method_rule['per_value'],
							$method_rule['shipping_class'],
							$method_rule['stop'],
							$method_rule['cancel'],
						);
					}
				}
				$ret['csv_array'] = $csv_array;
				header( 'Content-Type: text/csv; charset=utf-8' );
				header( 'Content-Disposition: attachment; filename=' . $filename . '.csv' );
				$out = fopen( 'php://output', 'w' );
				foreach ( $csv_array as $fields ) {
					fputcsv( $out, $fields, WPDesk_Flexible_Shipping_Csv_Importer::get_csv_delimiter() );
				}
				fclose( $out );
				wp_die();
			}
			echo json_encode( $ret, JSON_PRETTY_PRINT );
			wp_die();
		}
	}
}
