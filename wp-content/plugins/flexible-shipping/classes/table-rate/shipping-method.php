<?php

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

if ( ! class_exists( 'WPDesk_Flexible_Shipping' ) ) {
	class WPDesk_Flexible_Shipping extends WC_Shipping_Method {

		const FIELD_METHOD_FREE_SHIPPING = 'method_free_shipping';

		const META_DEFAULT = '_default';

		const WEIGHT_ROUNDING_PRECISION = 6;

		/**
		 * Message added.
		 *
		 * @var bool
		 */
		private $message_added = false;


		/**
		 * Constructor for your shipment class
		 *
		 * @access public
		 * @return void
		 */
		public function __construct( $instance_id = 0 ) {
			$this->instance_id 			     	= absint( $instance_id );
			$this->id                 			= 'flexible_shipping';
			$this->shipping_methods_option 		= 'flexible_shipping_methods_' . $this->instance_id;
			$this->shipping_method_order_option = 'flexible_shipping_method_order_' . $this->instance_id;
			$this->section_name 				= 'flexible_shipping';
			$this->method_title       			= __( 'Flexible Shipping', 'flexible-shipping' );
			$this->method_description 			= __( 'Flexible Shipping', 'flexible-shipping' );

			$this->supports              = array(
					'shipping-zones',
					'instance-settings',
			);

			$this->instance_form_fields = array(
					'enabled' => array(
							'title' 		=> __( 'Enable/Disable', 'flexible-shipping' ),
							'type' 			=> 'checkbox',
							'label' 		=> __( 'Enable this shipment method', 'flexible-shipping' ),
							'default' 		=> 'yes',
					),
					'title' => array(
							'title' 		=> __( 'Shipping Title', 'flexible-shipping' ),
							'type' 			=> 'text',
							'description' 	=> __( 'This controls the title which the user sees during checkout.', 'flexible-shipping' ),
							'default'		=> __( 'Flexible Shipping', 'flexible-shipping' ),
							'desc_tip'		=> true
					)
			);

			if ( version_compare( WC()->version, '2.6' ) < 0  && $this->get_option( 'enabled', 'yes' ) == 'no' ) {
			    $this->enabled		    = $this->get_option( 'enabled' );
			}

			$this->title            = $this->get_option( 'title' );

			$this->init();

			
			//$this->method_title    	= $this->get_option( 'title' );

            //add_action( 'woocommerce_sections_' . $this->id, array( $this, 'process_admin_options' ) );
			add_action( 'woocommerce_update_options_shipping_' . $this->id, array( $this, 'process_admin_options' ) );

		}

		/**
		 * Init your settings
		 *
		 * @access public
		 * @return void
		 */
		function init() {
			$this->instance_form_fields = include( 'settings/flexible-shipping.php' );
			// Load the settings API
			$this->init_form_fields(); // This is part of the settings API. Override the method to add your own settings
			$this->init_settings(); // This is part of the settings API. Loads settings you previously init.

			// Define user set variables
			$this->title        		= $this->get_option( 'title' );
			$this->tax_status   		= $this->get_option( 'tax_status' );

			$this->availability         = $this->get_option( 'availability' );

			$this->type                 = $this->get_option( 'type', 'class' );
		}


		/**
		 * @param unknown $key
		 * @return string
		 *
		 * Dodane w WooCommerce 2.4
		 * Dodane w celu zachowania kompatybilności z WooCommerce 2.3
		 * Przetestowane na WooCommerce 2.3.9
		 */
		public function get_field_key( $key ) {
			return $this->plugin_id . $this->id . '_' . $key;
		}

		public function generate_title_shipping_methods_html( $key, $data ) {
			$field    = $this->get_field_key( $key );
			$defaults = array(
				'title'             => '',
				'class'             => ''
			);

			$data = wp_parse_args( $data, $defaults );

			ob_start();

			?>
				</table>
				<h3 class="wc-settings-sub-title <?php echo esc_attr( $data['class'] ); ?>" id="<?php echo esc_attr( $field ); ?>"><?php echo wp_kses_post( $data['title'] ); ?>
				<a href="<?php echo remove_query_arg( 'added', add_query_arg( 'action', 'new' ) ); ?>" class="add-new-h2"><?php echo __('Add New', 'flexible-shipping' ); ?></a></h3>

				<?php if ( ! empty( $data['description'] ) ) : ?>
					<p><?php echo wp_kses_post( $data['description'] ); ?></p>
				<?php endif; ?>
				<table class="form-table">
			<?php

			return ob_get_clean();
		}

		public function generate_shipping_methods_html( $key, $data ) {
			$shipping_methods = $this->get_shipping_methods();
			$shipping_method_order = $this->get_shipping_method_order();
			ob_start();
			include ( 'views/html-shipping-method-settings.php' );
			return ob_get_clean();
		}

		public function get_shipping_methods( $enabled = false ) {
			$shipping_methods = get_option( $this->shipping_methods_option, array() );
			$shipping_method_order = get_option( $this->shipping_method_order_option, array() );
			$ret = array();
			if ( is_array( $shipping_method_order ) ) {
				foreach ( $shipping_method_order as $method_id ) {
					if ( isset( $shipping_methods[$method_id] ) ) $ret[$method_id] = $shipping_methods[$method_id];
				}
			}
			foreach ( $shipping_methods as $shipping_method ) {
				if ( !isset( $ret[$shipping_method['id']] ) ) $ret[$shipping_method['id']] = $shipping_method;
			}
			if ( $enabled )	{
				foreach ( $ret as $key => $shipping_method ) {
					if ( isset( $shipping_method['method_enabled'] ) && 'yes' != $shipping_method['method_enabled'] ) unset($ret[$key]);
				}
			}
			return $ret;
		}

		public function get_shipping_method_order() {
			$shipping_methods = get_option( $this->shipping_methods_option, array() );
			$shipping_method_order = get_option( $this->shipping_method_order_option, array() );
			$ret = array();
			if ( is_array( $shipping_method_order ) ) {
				foreach ( $shipping_method_order as $method_id ) {
					if ( isset( $shipping_methods[$method_id] ) ) $ret[$method_id] = $method_id;
				}
			}
			foreach ( $shipping_methods as $shipping_method ) {
				if ( !isset( $ret[$shipping_method['id']] ) ) $ret[$shipping_method['id']] = $shipping_method['id'];
			}
			return $ret;
		}

		public function generate_shipping_rules_html( $key, $data ) {
			if ( ! function_exists( 'woocommerce_form_field' ) ) {
				$wc_template_functions = trailingslashit( dirname( __FILE__) ) . '../../woocommerce/includes/wc-template-functions.php';
				if ( file_exists( $wc_template_functions ) ) {
					include_once( $wc_template_functions );
				}
			}
			ob_start();
			include ( 'views/html-shipping-method-rules.php' );
			return ob_get_clean();
		}


		/**
	 	 * Initialise Settings Form Fields
	 	 */
		public function init_form_fields() {
			$this->form_fields = include( 'settings/flexible-shipping.php' );
		}

		public function update_rates( $shipping_methods ) {
			$rates = array();
			foreach ( $shipping_methods as $shipping_method ) {
				$id = $this->id . '_' . $this->instance_id . '_' . sanitize_title($shipping_method['method_title'] );
				$id = apply_filters( 'flexible_shipping_method_rate_id', $id, $shipping_method );
				if ( ! isset( $rates[$id] ) && $shipping_method['method_enabled'] == 'yes' )
					$rates[$id] = array(
							'identifier' => $id,
							'title' => $shipping_method['method_title']
					);
			}
			update_option( 'flexible_shipping_rates', $rates );
		}

		public function shipping_method_title_used( $title, $shipping_methods ) {
		    foreach ( $shipping_methods as $shipping_method ) {
                if ( $title == $shipping_method['method_title'] ) {
                    return true;
                }
		    }
		    return false;
		}

		public function shipping_method_next_id( $shipping_methods ) {
		    $next_id = 0;
		    foreach ( $shipping_methods as $shipping_method ) {
                if ( intval($shipping_method['id'] ) > $next_id ) {
                    $next_id = intval($shipping_method['id'] );
                }
		    }
		    $next_id++;
		    return $next_id;
		}

		/**
		* Add method creation date.
		*/
		private function add_method_creation_date() {
			if ( ! get_option( 'flexible_shipping_method_creation_date' ) ) {
				add_option( 'flexible_shipping_method_creation_date', current_time('mysql') );
			}
		}

		public function process_admin_options()	{
			$action = false;
			if ( isset( $_POST['method_action'] ) ) {
				$action = $_POST['method_action'];
			}
			if ( $action == 'new' || $action == 'edit' ) {
				$this->add_method_creation_date();
				$shipping_methods = get_option( $this->shipping_methods_option, array() );
				$shipping_method = array();
				if ( $action == 'new' )	{
					$shipping_methods = get_option( $this->shipping_methods_option, array() );
					$shipping_method_order = get_option( $this->shipping_method_order_option, array() );
					//
					$method_id = get_option( 'flexible_shipping_method_id', 0 );
					//$method_id = 0;
					foreach ( $shipping_methods as $shipping_method ) {
						if ( intval( $shipping_method['id'] ) > $method_id ) $method_id = intval( $shipping_method['id'] );
					}
					$method_id++;
					update_option( 'flexible_shipping_method_id', $method_id );
					//
					$method_id_for_shipping = $this->id . '_' . $this->instance_id . '_' . $method_id;
				}
				else {
					$method_id = $_POST['method_id'];
					$method_id_for_shipping = $_POST['method_id_for_shipping'];
					if ( isset( $shipping_methods[ $method_id ] ) ) {
						$shipping_method = $shipping_methods[ $method_id ];
					}
				}
				$shipping_method['woocommerce_method_instance_id'] = $this->instance_id;
				$shipping_method['id'] = $method_id;
				$shipping_method['id_for_shipping'] = $method_id_for_shipping;
				$shipping_method['method_title'] = wp_unslash( $_POST['woocommerce_' . $this->id . '_method_title'] );
				$shipping_method['method_description'] = wp_unslash( $_POST['woocommerce_' . $this->id . '_method_description'] );
				$shipping_method[self::FIELD_METHOD_FREE_SHIPPING] = '';

				if ( isset( $_POST['woocommerce_' . $this->id . '_method_free_shipping'] ) && $_POST['woocommerce_' . $this->id . '_method_free_shipping'] != '' ) {
				    $shipping_method[self::FIELD_METHOD_FREE_SHIPPING] = wc_format_decimal( $_POST['woocommerce_' . $this->id . '_method_free_shipping'] );
				}
				if ( version_compare( WC()->version, '2.6' ) >= 0 ) {
					$shipping_method['method_free_shipping_label'] = wp_unslash( $_POST['woocommerce_' . $this->id . '_method_free_shipping_label'] );
				}
				$shipping_method['method_calculation_method'] = $_POST['woocommerce_' . $this->id . '_method_calculation_method'];
				$shipping_method['method_visibility'] = 'no';
				if ( isset( $_POST['woocommerce_' . $this->id . '_method_visibility'] ) && $_POST['woocommerce_' . $this->id . '_method_visibility'] == 1 )
					$shipping_method['method_visibility'] = 'yes';
				//
				$shipping_method['method_default'] = 'no';
				if ( isset( $_POST['woocommerce_' . $this->id . '_method_default'] ) && $_POST['woocommerce_' . $this->id . '_method_default'] == 1 )
					$shipping_method['method_default'] = 'yes';
				//
				$shipping_method['method_enabled'] = 'no';
				if ( isset( $_POST['woocommerce_' . $this->id . '_method_enabled'] ) && $_POST['woocommerce_' . $this->id . '_method_enabled'] == 1 )
					$shipping_method['method_enabled'] = 'yes';
				//
				$shipping_method['method_integration'] = $_POST['woocommerce_' . $this->id . '_method_integration'];
				//
				$shipping_method = apply_filters( 'flexible_shipping_process_admin_options', $shipping_method );
				//
				$count = 0;
				$shipping_method['method_rules'] = array();
				if ( isset( $_POST['method_rules'] ) ) {
					foreach ( $_POST['method_rules'] as $rule ) {
						$count++;
						$method_rule = array();
						$method_rule['based_on'] = $rule['based_on'];
						$method_rule['min'] = wc_format_decimal( $rule['min'] );
						$method_rule['max'] = wc_format_decimal( $rule['max'] );
						$method_rule['cost_per_order'] = wc_format_decimal( $rule['cost_per_order'] );
						$method_rule = apply_filters( 'flexible_shipping_method_rule_save', $method_rule, $rule );
						$shipping_method['method_rules'][$count] = $method_rule;
					}
				}
				//
				$shipping_methods[$method_id] = $shipping_method;
				//
				update_option( $this->shipping_methods_option, $shipping_methods );
				//
				$this->update_rates($shipping_methods);
				//
				if ( $action == 'new' )	{
					$shipping_method_order[$method_id] = $method_id;
					update_option( $this->shipping_method_order_option, $shipping_method_order );
				}
				if ( $action == 'new' )	{
					$redirect = add_query_arg( array('added' => $method_id, 'action' => false, 'method_id' => false ));
					$redirect .= '#method_' . $method_id;
					$redirect = add_query_arg( array('added' => $method_id, 'action' => 'edit', 'method_id' => $method_id ));
					wpdesk_redirect( $redirect );
					exit;
				}
				if ( $action == 'edit' ) {
					$redirect = add_query_arg( array('updated' => $method_id, 'action' => false, 'method_id' => false ));
					$redirect .= '#method_' . $method_id;
				}
			}
			else {
                if ( isset( $_POST['import_action'] ) && $_POST['import_action'] == '1' ) {
                    $tmp_name = $_FILES['import_file']['tmp_name'];
                    $shipping_methods = get_option( $this->shipping_methods_option, array() );
                    try {
                        $importer = new WPDesk_Flexible_Shipping_Csv_Importer( $this );
						$shipping_methods = $importer->import( $tmp_name, $shipping_methods );
                        update_option( $this->shipping_methods_option, $shipping_methods );
                    } catch ( Exception $e ) {
						WC_Admin_Settings::add_message( $e->getMessage() );
                    }
                    WC_Admin_Settings::show_messages();
                }
                else {
				    parent::process_admin_options();
				    if ( isset( $_POST['method_order'] ) ) {
				        update_option( $this->shipping_method_order_option, $_POST['method_order'] );
				    }
				}
			}
		}

		public function get_shipping_method_form( $shipping_method ) {
			$this->form_fields = include( 'settings/shipping-method-form.php' );
		}


		public function admin_options()	{
			?>
			<table class="form-table">
			<?php
				$action = false;
				if ( isset( $_GET['action'] ) )
				{
					$action = $_GET['action'];
				}
				if ( $action == 'new' || $action == 'edit' ) {
					$shipping_methods = get_option( $this->shipping_methods_option, array() );
					$shipping_method = array(
							'method_title' 				=> '',
							'method_description'		=> '',
							'method_enabled' 			=> 'no',
							'method_shipping_zone' 		=> '',
							'method_calculation_method'	=> 'sum',
							self::FIELD_METHOD_FREE_SHIPPING		=> '',
							'method_free_shipping_label'=> '',
							'method_visibility'			=> 'no',
							'method_default'			=> 'no',
							'method_integration'		=> '',
					);
					$method_id = '';
					if ( $action == 'edit' ) {
						$method_id = $_GET['method_id'];
						$shipping_method = $shipping_methods[$method_id];
						$method_id_for_shipping = $this->id . '_' . $this->instance_id . '_' . sanitize_title( $shipping_method['method_title'] );
						$method_id_for_shipping = apply_filters( 'flexible_shipping_method_rate_id', $method_id_for_shipping, $shipping_method );
					}
					else {
						$method_id_for_shipping = '';
					}
					?>
					<input type="hidden" name="method_action" value="<?php echo $action; ?>" />
					<input type="hidden" name="method_id" value="<?php echo $method_id; ?>" />
					<input type="hidden" name="method_id_for_shipping" value="<?php echo $method_id_for_shipping; ?>" />
					<?php if ( $action == 'new' ) : ?>
						<h2><?php _e('New Shipping Method', 'flexible-shipping' ); ?></h2>
					<?php endif; ?>
					<?php if ( $action == 'edit' ) : ?>
						<h2><?php _e('Edit Shipping Method', 'flexible-shipping' ); ?></h2>
					<?php endif; ?>
					<?php
					if ( isset( $_GET['added'] ) ) {
						$method_id = $_GET['added'];
						$shipping_methods = get_option( $this->shipping_methods_option, array() );
						if ( isset( $shipping_methods[$method_id] ) )
						{
							if ( ! $this->message_added ) {
								$shipping_method = $shipping_methods[$method_id];
								WC_Admin_Settings::add_message( sprintf(__( 'Shipping method %s added.', 'flexible-shipping' ), $shipping_method['method_title'] ) );
								$this->message_added = true;
							}
						}
						WC_Admin_Settings::show_messages();
					}
					$shipping_method['woocommerce_method_instance_id'] = $this->instance_id;
					$this->generate_settings_html( $this->get_shipping_method_form($shipping_method) );
				}
				else if ( $action == 'delete' ) {
                    $methods_id = '';
                    if ( isset( $_GET['methods_id'] ) ) {
                        $methods_id = explode( ',' , $_GET['methods_id'] );
                    }
					$shipping_methods = get_option( $this->shipping_methods_option, array() );
					$shipping_method_order = get_option( $this->shipping_method_order_option, array() );
					foreach ( $methods_id as $method_id ) {
                        if ( isset( $shipping_methods[$method_id] ) ) {
                            $shipping_method = $shipping_methods[$method_id];
                            unset(	$shipping_methods[$method_id] );
                            if ( isset( $shipping_method_order[$method_id] ) ) {
                                unset(	$shipping_method_order[$method_id] );
                            }
                            update_option( $this->shipping_methods_option, $shipping_methods );
                            update_option( $this->shipping_method_order_option, $shipping_method_order );
                            WC_Admin_Settings::add_message( sprintf(__('Shipping method %s deleted.', 'flexible-shipping' ), $shipping_method['method_title'] ) );
                        }
                        else {
                            WC_Admin_Settings::add_error( __( 'Shipping method not found.', 'flexible-shipping' ) );
                        }
                    }
					WC_Admin_Settings::show_messages();
					$this->generate_settings_html();
				}
				else {
					if ( isset( $_GET['added'] ) ) {
						$method_id = $_GET['added'];
						$shipping_methods = get_option( $this->shipping_methods_option, array() );
						if ( isset( $shipping_methods[$method_id] ) )
						{
							if ( ! $this->message_added ) {
								$shipping_method = $shipping_methods[$method_id];
								WC_Admin_Settings::add_message( sprintf(__( 'Shipping method %s added.', 'flexible-shipping' ), $shipping_method['method_title'] ) );
								$this->message_added = true;
							}
						}
						WC_Admin_Settings::show_messages();
					}
					if ( isset( $_GET['updated'] ) ) {
						$method_id = $_GET['updated'];
						$shipping_methods = get_option( $this->shipping_methods_option, array() );
						if ( isset( $shipping_methods[$method_id] ) )
						{
							$shipping_method = $shipping_methods[$method_id];
							WC_Admin_Settings::add_message( sprintf(__( 'Shipping method %s updated.', 'flexible-shipping' ), $shipping_method['method_title'] ) );
						}
						WC_Admin_Settings::show_messages();
					}

                    // General Settings
				    $this->generate_settings_html();
				}
			?>
			</table>
			<script type="text/javascript">
                if ( typeof window.history.pushState == 'function' ) {
                    var url = document.location.href;
                    url = fs_removeParam('action', url);
                    url = fs_removeParam('methods_id', url);
                    url = fs_removeParam('added', url);
                    url = fs_trimChar(url,'?');
                    window.history.pushState({}, "", url);
                }
            </script>
			<?php do_action( 'flexible_shipping_method_script' ); ?>
			<?php
		}

		private function package_subtotal( $items ) {
			$subtotal = 0;
			foreach( $items as $item )
				$subtotal += $item['line_subtotal'];
				return $subtotal;
		}

		public function package_weight( $items ) {
			$weight = 0;
			foreach( $items as $item ) {
				$weight += $item['data']->weight * $item['quantity'];
			}
			return $weight;
		}

		/**
         * Get cart weight.
		 * It rounds return value to declared precision.
		 *
		 * @return float
         */
		public function cart_weight() {
            if ( version_compare( WC_VERSION, '2.7', '<' ) ) {
		        add_filter( 'woocommerce_product_weight', array( $this, 'woocommerce_product_weight' ) );
		    }
		    $cart_weight = WC()->cart->get_cart_contents_weight();
            if ( version_compare( WC_VERSION, '2.7', '<' ) ) {
		        remove_filter( 'woocommerce_product_weight', array( $this, 'woocommerce_product_weight' ) );
		    }
		    return round( $cart_weight, apply_filters( 'flexible_shipping_weight_rounding_precision', self::WEIGHT_ROUNDING_PRECISION ) );
		}

		/* Fix for Woocommerce 2.6 weight calculation */
		/* PHP Warning:  A non-numeric value encountered in /wp-content/plugins/woocommerce/includes/class-wc-cart.php on line 359 */
        public function woocommerce_product_weight( $weight ) {
			if ( $weight === '' ) {
				return 0;
			}
			return $weight;
        }

		public function package_item_count( $items ) {
			$item_count = 0;

			foreach( $items as $item ) {
				$item_count += $item['quantity'];
			}
			return $item_count;
		}

		public function cart_item_count() {
			$item_count = 0;

			$cart = WC()->cart;
			foreach( $cart->cart_contents as $item ) {
				$item_count += $item['quantity'];
			}

			return $item_count;
		}

		function calculate_method_cost( $shipping_method, $rule_costs ) {
			$cost = 0;
			if ( $shipping_method['method_calculation_method'] == 'sum' ) {
				$cost = 0;
				foreach ( $rule_costs as $rule_cost ) {
					$cost += $rule_cost['cost'];
				}
			}
			return $cost;
		}

        public function cart_display_prices_including_tax() {
            if ( version_compare( WC_VERSION, '3.3', '<' ) ) {
                $display_prices_including_tax = 'incl' === WC()->cart->tax_display_cart;
            }
            else {
                $display_prices_including_tax = WC()->cart->display_prices_including_tax();
            }
            return $display_prices_including_tax;
        }

		/**
         * @return int
         */
		public function contents_cost_with_tax() {
            $display_prices_including_tax = $this->cart_display_prices_including_tax();
            if ( $display_prices_including_tax ) {
		        $total = WC()->cart->get_displayed_subtotal();
            }
            else {
                if ( version_compare( WC_VERSION, '3.2', '<' ) ) {
                    $total = WC()->cart->subtotal;
                }
                else {
                    $total = WC()->cart->get_displayed_subtotal() + WC()->cart->get_subtotal_tax();
                }
            }
		    if ( version_compare( WC_VERSION, '3.2', '<' ) ) {
                $total_discount = WC()->cart->discount_cart + WC()->cart->discount_cart_tax;
		    }
		    else {
                $total_discount = WC()->cart->get_cart_discount_total() + WC()->cart->get_cart_discount_tax_total();
		    }
		    $total = round( $total - $total_discount, wc_get_price_decimals() );
			return $total;
		}

		/**
         * @return int
         */
		public function contents_cost_without_tax() {
            $display_prices_including_tax = $this->cart_display_prices_including_tax();
		    $total = WC()->cart->get_displayed_subtotal();
		    if ( $display_prices_including_tax ) {
		        if ( version_compare( WC_VERSION, '3.2', '<' ) ) {
		            $total = WC()->cart->subtotal_ex_tax;
		        }
		        else {
		            $total = $total - WC()->cart->get_subtotal_tax();
		        }
		    }
		    if ( version_compare( WC_VERSION, '3.2', '<' ) ) {
		        $discount_without_tax = WC()->cart->discount_cart;
		    }
		    else {
		        $discount_without_tax = WC()->cart->get_cart_discount_total();
		    }
		    $total = round( $total - $discount_without_tax, wc_get_price_decimals() );
			return $total;
		}

		public function prices_include_tax() {
		    if ( version_compare( WC_VERSION, '3.3', '<' ) ) {
		        $prices_include_tax = 'incl' === WC()->cart->tax_display_cart;
		    }
		    else {
		        $prices_include_tax = WC()->cart->display_prices_including_tax();
		    }
		    return apply_filters( 'flexible_shipping_prices_include_tax', $prices_include_tax );
		}

		/**
         * Is free shipping?
         *
		 * @param array $shipping_method_settings Flexible shipping method settings.
		 * @param float $cart_contents_cost Cart contents cost.
         *
         * @return bool
         */
		public function is_free_shipping( array $shipping_method_settings, $cart_contents_cost ) {
			if ( isset( $shipping_method_settings[ self::FIELD_METHOD_FREE_SHIPPING ] ) && '' !== $shipping_method_settings[ self::FIELD_METHOD_FREE_SHIPPING ] ) {
			    $shipping_method_settings[self::FIELD_METHOD_FREE_SHIPPING] = trim( $shipping_method_settings[self::FIELD_METHOD_FREE_SHIPPING] );
			    if ( '0' !== $shipping_method_settings[self::FIELD_METHOD_FREE_SHIPPING] && is_numeric( $shipping_method_settings[self::FIELD_METHOD_FREE_SHIPPING] ) ) {
				    if ( apply_filters( 'flexible_shipping_value_in_currency', floatval( $shipping_method_settings[self::FIELD_METHOD_FREE_SHIPPING] ) ) <= floatval( $cart_contents_cost ) ) {
					    return true;
				    }
			    }
			}
			return false;
		}

		/**
         * Calculate contents cost.
         *
		 * @return float
        */
		public function contents_cost() {
			if ( $this->prices_include_tax() ) {
			    return $this->contents_cost_with_tax();
			} else {
				return $this->contents_cost_without_tax();
			}
		}

		/**
         * @param array $package
         */
		public function calculate_shipping( $package = array() ) {

			$cart_contents_cost = 0;

			$processed = apply_filters( 'flexible_shipping_calculate_shipping', false, $this, $package, 0 );

			if ( $processed === false ) {

				$shipping_methods = $this->get_shipping_methods( true );

				foreach ( $shipping_methods as $shipping_method ) {

					$rule_costs = array();

					$add_method = false;

					if ( isset( $shipping_method['method_visibility'] ) && $shipping_method['method_visibility'] == 'yes' && !is_user_logged_in() ) {
						/* only for logged in */
						continue;
					}

					foreach ( $shipping_method['method_rules'] as $rule_key => $method_rule ) {
						$rule_triggered = false;

						if ( $method_rule['based_on'] == 'none' ) {
							$rule_triggered = true;
						}

						$contents_cost      = $this->contents_cost();
						$cart_contents_cost = $contents_cost;

						if ( $method_rule['based_on'] == 'value' ) {
							if ( trim( $method_rule['min'] ) == '' ) {
								$min = 0;
							}
							else {
								$min = floatval( apply_filters( 'flexible_shipping_value_in_currency', floatval( $method_rule['min'] ) ) );
							}
							if ( trim( $method_rule['max'] ) == '' ) {
								$max = INF;
							}
							else {
								$max = floatval( apply_filters( 'flexible_shipping_value_in_currency', floatval( $method_rule['max'] ) ) );
							}
							if ( $contents_cost >= $min && $contents_cost <= $max ) {
								$rule_triggered = true;
							}
						}

						if ( $method_rule['based_on'] == 'weight' ) {
							if ( trim( $method_rule['min'] ) == '' ) {
								$min = 0;
							}
							else {
								$min = floatval( $method_rule['min'] );
							}
							if ( trim( $method_rule['max'] ) == '' ) {
								$max = INF;
							}
							else {
								$max = floatval( $method_rule['max'] );
							}
							$contents_weight = floatval( $this->cart_weight() );
							if ( $contents_weight >= $min && $contents_weight <= $max ) {
								$rule_triggered = true;
							}
						}
						if ( $rule_triggered ) {
							$rule_triggered = apply_filters( 'flexible_shipping_rule_triggered', $rule_triggered, $method_rule, $package );
						}
						if ( $rule_triggered ) {
							$rule_cost = array( 'cost' => floatval( $method_rule['cost_per_order'] ) );
							$rule_costs[$rule_key] = $rule_cost;
							$add_method = true;
						}

					}
					$cost = $this->calculate_method_cost( $shipping_method, $rule_costs );
					$add_method = apply_filters( 'flexible_shipping_add_method' , $add_method, $shipping_method, $package, $this );
					if ( $add_method === true ) {

						if ( $this->is_free_shipping( $shipping_method, $cart_contents_cost ) ) {
							$cost = 0;
						}

						$method_title = wpdesk__( $shipping_method['method_title'], 'flexible-shipping' );
						if ( version_compare( WC()->version, '2.6' ) >= 0 ) {
							if ( $cost == 0 ) {
								if ( ! isset( $shipping_method['method_free_shipping_label'] ) ) {
									$shipping_method['method_free_shipping_label'] = __( 'Free', 'flexible-shipping' );
								}
								if ( $shipping_method['method_free_shipping_label'] != '' ) {
									$method_title .= ' (' . wpdesk__( $shipping_method['method_free_shipping_label'], 'flexible-shipping' ) . ')';
								}
							}
						}

						$id = $this->id . '_' . $this->instance_id . '_' . sanitize_title( $shipping_method['method_title'] );
						$id = apply_filters( 'flexible_shipping_method_rate_id', $id, $shipping_method );
						$this->add_rate( array(
								'id'    		=> $id,
								'label' 		=> $method_title,
								'cost' 	 		=> $cost,
								'method'		=> $shipping_method,
								'rule_costs' 	=> $rule_costs,
								'meta_data'     => array(
								        self::META_DEFAULT => $shipping_method['method_default'],
								        '_fs_method'       => $shipping_method
								)
						) );
						if ( isset( $shipping_method['method_description'] ) ) {
							WC()->session->set('flexible_shipping_description_' . $id, wpdesk__( $shipping_method['method_description'], 'flexible-shipping' ) );
						}
						else {
							WC()->session->set( 'flexible_shipping_description_' . $id, '' );
						}
					}
				}
			}
		}

		public function is_available( $package ) {
            return parent::is_available( $package );
        }

		public function get_all_rates() {
			if ( class_exists( 'WC_Shipping_Zones' ) ) {
				$zones = WC_Shipping_Zones::get_zones();
				$zone0 = WC_Shipping_Zones::get_zone(0);
				$zones[0] = $zone0->get_data();
				$zones[0]['formatted_zone_location'] = $zone0->get_formatted_location();
				$zones[0]['shipping_methods']        = $zone0->get_shipping_methods();
				$rates = array();
				foreach ( $zones as $zone ) {
					foreach ( $zone['shipping_methods'] as $instance_id => $woo_shipping_method ) {
						if ( $woo_shipping_method->id == $this->id ) {
							$shipping_methods = $woo_shipping_method->get_shipping_methods();
							foreach ( $shipping_methods as $shipping_method ) {
								$id = $this->id . '_' . $woo_shipping_method->instance_id . '_' . sanitize_title($shipping_method['method_title'] );
								$id = apply_filters( 'flexible_shipping_method_rate_id', $id, $shipping_method );
								$shipping_method['instance_id'] = $woo_shipping_method->instance_id;
								$rates[$id] = $shipping_method;
							}
						}
					}
				}
			}
			else {
				$shipping_methods = $this->get_shipping_methods();
				$rates = array();
				foreach ( $shipping_methods as $shipping_method ) {
					$id = $this->id . '_' . $this->instance_id . '_' . sanitize_title($shipping_method['method_title'] );
					$id = apply_filters( 'flexible_shipping_method_rate_id', $id, $shipping_method );
					$rates[$id] = $shipping_method;
				}
			}
			return $rates;
		}

		public function get_method_from_rate( $rate_id ) {
			$rates = $this->get_all_rates();
			return $rates[$rate_id];
		}

		public function generate_header_html( $key, $data ) {
			$field_key = $this->get_field_key( $key );
			$defaults  = array(
				'title' => '',
				'class' => '',
			);
			$data = wp_parse_args( $data, $defaults );
			return sprintf( '<tr><td colspan="2"><h4 class="%1$s" id="%2$s">%3$s</h4></td></tr>', esc_attr( $data['class'] ), esc_attr( $field_key ), esc_html( $data['title'] ) );
		}

		/**
         * Generate SaaS connection error field HTML.
         *
		 * @param string $key
		 * @param array $data
         *
         * @return string
        */
		public function generate_saas_connection_error_html( $key, $data ) {
			$field_key = $this->get_field_key( $key );
			$defaults  = array(
				'title' => '',
				'class' => '',
			);
			$data = wp_parse_args( $data, $defaults );
			return sprintf( '<tr><td></td><td><span class="%1$s" id="%2$s">%3$s</span></td></tr>', esc_attr( $data['class'] ), esc_attr( $field_key ), $data['description'] );
		}
		
		/**
         * Generate custom_services field HTML.
         *
		 * @param string $key
		 * @param array $data
         *
         * @return string
        */
		public function generate_custom_services_html( $key, $data ) {
			$field_key = $this->get_field_key( $key );
			$defaults  = array(
				'title'             => '',
				'disabled'          => false,
				'class'             => '',
				'css'               => '',
				'placeholder'       => '',
				'type'              => 'text',
				'desc_tip'          => false,
				'description'       => '',
				'custom_attributes' => array(),
				'services'          => array()
			);
			$data = wp_parse_args( $data, $defaults );
			ob_start();
			$services = $data['services'];
			include ( 'views/html-custom-services.php' );
			return ob_get_clean();
		}

	}

}
