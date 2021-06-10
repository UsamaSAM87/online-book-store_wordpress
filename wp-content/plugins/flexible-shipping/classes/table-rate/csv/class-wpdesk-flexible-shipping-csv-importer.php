<?php
/**
 * CSV importer
 *
 * @package \WPDesk_Flexible_Shipping
 */

/**
 * Import shipping methods and rules from CSV.
 */
class WPDesk_Flexible_Shipping_Csv_Importer {

	const CSV_DELIMITER = ';';

	/**
	 * Flexible Shipping shipping method.
	 *
	 * @var WPDesk_Flexible_Shipping
	 */
	private $flexible_shipping_method;

	/**
	 * Hashmap for shipping classes with name->term_id data.
	 *
	 * @var \stdClass[]
	 */
	private $wc_shipping_classes_hashmap;

	/**
	 * Delimiter used in CSV file.
	 *
	 * @return string
	 */
	public static function get_csv_delimiter() {
		return apply_filters( 'flexible_shipping_csv_delimiter', self::CSV_DELIMITER );
	}

	/**
	 * WPDesk_Flexible_Shipping_Csv_Importer constructor.
	 *
	 * @param WPDesk_Flexible_Shipping $flexible_shipping_method Flexible shipping method.
	 */
	public function __construct( $flexible_shipping_method ) {
		$this->flexible_shipping_method    = $flexible_shipping_method;
		$this->wc_shipping_classes_hashmap = $this->prepare_shipping_class_hashmap();
	}

	/**
	 * Prepares hashmap for fast checking the term_id of given shipment class.
	 *
	 * @return array
	 */
	private function prepare_shipping_class_hashmap() {
		$cache = [];
		foreach ( WC()->shipping()->get_shipping_classes() as $class ) {
			$cache[ html_entity_decode( $class->name ) ] = (int) $class->term_id;
		}

		return $cache;
	}

	/**
	 * Load CSV from file.
	 *
	 * @param string $tmp_name File name.
	 *
	 * @return array
	 */
	private function load_csv_from_file( $tmp_name ) {
		return array_map(
			function ( $v ) {
					return str_getcsv( $v, self::get_csv_delimiter() );
			}, file( $tmp_name )
		);
	}

	/**
	 * Add columns to row.
	 *
	 * @param array $row Row.
	 * @param array $columns Columns.
	 *
	 * @return array
	 */
	private function add_columns_to_row( array $row, array $columns ) {
		foreach ( $columns as $col_key => $col ) {
			$row[ $col ] = $row[ $col_key ];
		}

		return $row;
	}

	/**
	 * Convert rows to named values.
	 *
	 * @param array $csv_array CSV.
	 *
	 * @return array
	 */
	private function convert_rows_to_named_values( array $csv_array ) {
		$first   = true;
		$columns = array();
		foreach ( $csv_array as $row_key => $csv_row ) {
			if ( $first ) {
				$columns = $csv_row;
			} else {
				$csv_array[ $row_key ] = $this->add_columns_to_row( $csv_array[ $row_key ], $columns );
			}
			$first = false;
		}

		return $csv_array;
	}


	/**
	 * Create new shipping method.
	 *
	 * @param array $csv_row CSV row.
	 * @param array $shipping_methods Shipping methods.
	 * @param int   $import_row_count Rows count.
	 *
	 * @return array
	 * @throws WPDesk_Flexible_Shipping_Csv_Importer_Exception Exception.
	 */
	private function new_shipping_method( array $csv_row, array $shipping_methods, $import_row_count ) {
		$new_shipping_method = array( 'method_enabled' => 'no' );
		if ( ! isset( $csv_row['Method Title'] ) || '' === trim( $csv_row['Method Title'] ) ) {
			throw new WPDesk_Flexible_Shipping_Csv_Importer_Exception(
				__(
					'Sorry, there has been an error. The CSV is invalid or incorrect file type.',
					'flexible-shipping'
				)
			);
		}
		$method_title = $csv_row['Method Title'];
		$count        = 0;
		while ( $this->flexible_shipping_method->shipping_method_title_used( $method_title, $shipping_methods ) ) {
			if ( 0 === $count ) {
				$method_title = $csv_row['Method Title'] . ' (' . __( 'import', 'flexible-shipping' ) . ')';
			} else {
				$method_title = $csv_row['Method Title'] . ' (' . __(
					'import',
					'flexible-shipping'
				) . ' ' . $count . ')';
			}
			$count ++;
		}
		$new_shipping_method['id']                 = $this->flexible_shipping_method->shipping_method_next_id( $shipping_methods );
		$new_shipping_method['id_for_shipping']    = $this->flexible_shipping_method->id . '_' . $this->flexible_shipping_method->instance_id . '_' . $new_shipping_method['id'];
		$new_shipping_method['method_title']       = $method_title;
		$new_shipping_method['method_description'] = $csv_row['Method Description'];
		if ( '' !== trim( $csv_row['Free Shipping'] ) && ! is_numeric(
			str_replace(
				',', '.',
				$csv_row['Free Shipping']
			)
		) ) {
			throw new WPDesk_Flexible_Shipping_Csv_Importer_Exception(
				sprintf(
					// Translators: free shipping value and row number.
					__( 'Free Shipping value %1$s is not valid number. Row number %2$d.', 'flexible-shipping' ),
					$csv_row['Free Shipping'], $import_row_count
				)
			);
		}
		$new_shipping_method[ WPDesk_Flexible_Shipping::FIELD_METHOD_FREE_SHIPPING ] = str_replace(
			',', '.',
			$csv_row['Free Shipping']
		);
		if ( trim( $csv_row['Maximum Cost'] ) !== '' && ! is_numeric(
			str_replace(
				',', '.',
				$csv_row['Maximum Cost']
			)
		) ) {
			throw new WPDesk_Flexible_Shipping_Csv_Importer_Exception(
				sprintf(
					// Translators: maximum cost value and row number.
					__( 'Maximum Cost value %1$s is not valid number. Row number %2$d.', 'flexible-shipping' ),
					$csv_row['Maximum Cost'], $import_row_count
				)
			);
		}
		$new_shipping_method['method_max_cost']           = str_replace( ',', '.', $csv_row['Maximum Cost'] );
		$new_shipping_method['method_calculation_method'] = $csv_row['Calculation Method'];
		if ( ! in_array(
			$new_shipping_method['method_calculation_method'],
			array( 'sum', 'lowest', 'highest' ),
			true
		) ) {
			throw new WPDesk_Flexible_Shipping_Csv_Importer_Exception(
				sprintf(
					// Translators: row number.
					__( 'Invalid value for Calculation Method in row number %d.', 'flexible-shipping' ), $import_row_count
				)
			);
		}
		$new_shipping_method['method_visibility'] = $csv_row['Visibility'];
		if ( 'yes' !== $new_shipping_method['method_visibility'] ) {
			$new_shipping_method['method_visibility'] = 'no';
		}
		$new_shipping_method['method_default'] = $csv_row['Default'];
		if ( 'yes' !== $new_shipping_method['method_default'] ) {
			$new_shipping_method['method_default'] = 'no';
		}
		$new_shipping_method['method_rules'] = array();

		return $new_shipping_method;
	}

	/**
	 * Get numeric value from row.
	 *
	 * @param array  $csv_row CSV row.
	 * @param string $column Column.
	 * @param int    $import_row_count Row count.
	 *
	 * @return string
	 * @throws WPDesk_Flexible_Shipping_Csv_Importer_Exception Exception.
	 */
	private function get_numeric_value_from_row( array $csv_row, $column, $import_row_count ) {
		if ( '' !== trim( $csv_row[ $column ] ) && ! is_numeric( str_replace( ',', '.', $csv_row[ $column ] ) ) ) {
			throw new WPDesk_Flexible_Shipping_Csv_Importer_Exception(
				sprintf(
					// Translators: column name, value and row number.
					__( '%1$s value %2$s is not valid number. Row number %3$d.', 'flexible-shipping' ),
					$column,
					$csv_row['Min'],
					$import_row_count
				)
			);
		}

		return str_replace( ',', '.', $csv_row[ $column ] );
	}

	/**
	 * Find and returns shipping class term id
	 *
	 * @param string $name Shipping class name to search.
	 *
	 * @return int|null Term id
	 */
	private function find_shipping_class_by_name( $name ) {
		$name = html_entity_decode( $name );
		if ( isset( $this->wc_shipping_classes_hashmap[ $name ] ) ) {
			return $this->wc_shipping_classes_hashmap[ $name ];
		}

		return null;
	}

	/**
	 * Creates a shipping class
	 *
	 * @param string $name Shipping class name.
	 * @param string $description Shipping class description.
	 *
	 * @return int Term id
	 * @throws WPDesk_Flexible_Shipping_Csv_Importer_Exception When can't create the class.
	 */
	private function create_shipping_class( $name, $description ) {
		$term_id = wp_insert_term( $name, 'product_shipping_class', array( 'description' => $description ) );
		if ( is_wp_error( $term_id ) ) {
			throw new WPDesk_Flexible_Shipping_Csv_Importer_Exception(
				sprintf(
					// Translators: rule shipping class and wp_error message.
					__( 'Error while creating shipping class: %1$s, %2$s', 'flexible-shipping' ), $name,
					$term_id->get_error_message()
				)
			);
		}
		$term_id                                    = (int) $term_id['term_id'];
		$this->wc_shipping_classes_hashmap[ html_entity_decode( $name ) ] = $term_id;

		return $term_id;
	}

	/**
	 * Maybe populate and create shipping classes.
	 *
	 * @param array $rule Rule.
	 *
	 * @return array
	 * @throws WPDesk_Flexible_Shipping_Csv_Importer_Exception Exception.
	 */
	private function maybe_populate_and_create_shipping_classes( array $rule ) {
		if ( '' !== trim( $rule['shipping_class'] ) ) {
			$rule_shipping_classes  = explode( ',', trim( $rule['shipping_class'] ) );
			$rule['shipping_class'] = array();
			foreach ( $rule_shipping_classes as $rule_shipping_class ) {
				if ( ! in_array( $rule_shipping_class, array( 'all', 'any', 'none' ), true ) ) {
					$term_id = $this->find_shipping_class_by_name( $rule_shipping_class );
					if ( null === $term_id ) {
						$term_id = $this->create_shipping_class( $rule_shipping_class, $rule_shipping_class );
					}
					$rule['shipping_class'][] = $term_id;
				} else {
					$rule['shipping_class'][] = $rule_shipping_class;
				}
			}
		}

		return $rule;
	}

	/**
	 * New shipping method rule.
	 *
	 * @param array $csv_row CSV row.
	 * @param int   $import_row_count Row count.
	 *
	 * @return array
	 * @throws WPDesk_Flexible_Shipping_Csv_Importer_Exception Exception.
	 */
	private function new_rule( array $csv_row, $import_row_count ) {
		$rule             = array();
		$rule['based_on'] = $csv_row['Based on'];
		if ( ! in_array(
			$rule['based_on'],
			array( 'none', 'value', 'weight', 'item', 'cart_line_item' ),
			true
		) ) {
			throw new WPDesk_Flexible_Shipping_Csv_Importer_Exception(
				sprintf(
					// Translators: row number.
					__( 'Invalid value for Based On in row number %d.', 'flexible-shipping' ), $import_row_count
				)
			);
		}

		$rule['min']             = $this->get_numeric_value_from_row( $csv_row, 'Min', $import_row_count );
		$rule['max']             = $this->get_numeric_value_from_row( $csv_row, 'Max', $import_row_count );
		$rule['cost_per_order']  = $this->get_numeric_value_from_row( $csv_row, 'Cost per order', $import_row_count );
		$rule['cost_additional'] = $this->get_numeric_value_from_row( $csv_row, 'Additional cost', $import_row_count );
		$rule['per_value']       = $this->get_numeric_value_from_row( $csv_row, 'Value', $import_row_count );

		$rule['shipping_class'] = trim( $csv_row['Shipping Class'] );

		$rule = $this->maybe_populate_and_create_shipping_classes( $rule );

		$rule['stop'] = $csv_row['Stop'];
		if ( 'yes' === $rule['stop'] ) {
			$rule['stop'] = 1;
		} else {
			$rule['stop'] = 0;
		}
		$rule['cancel'] = $csv_row['Cancel'];
		if ( 'yes' === $rule['cancel'] ) {
			$rule['cancel'] = 1;
		} else {
			$rule['cancel'] = 0;
		}

		return $rule;
	}

	/**
	 * Import file.
	 *
	 * @param string $tmp_name Tmp file name.
	 * @param array  $shipping_methods Shipping methods.
	 *
	 * @return array
	 * @throws WPDesk_Flexible_Shipping_Csv_Importer_Exception Exception.
	 */
	public function import( $tmp_name, array $shipping_methods ) {
		$csv_array = $this->load_csv_from_file( $tmp_name );
		$csv_array = $this->convert_rows_to_named_values( $csv_array );

		$first                    = true;
		$current_method_title     = '';
		$method_title             = '';
		$imported_shipping_method = array();
		$import_row_count         = 0;
		foreach ( $csv_array as $row_key => $csv_row ) {
			$import_row_count ++;
			$new_method = false;
			if ( ! $first ) {
				if ( ! isset( $csv_row['Method Title'] ) || $current_method_title !== $csv_row['Method Title'] || ! isset( $csv_row['Based on'] ) || '' === $csv_row['Based on'] ) {
					$new_method = true;

					$imported_shipping_method = $this->new_shipping_method(
						$csv_row, $shipping_methods,
						$import_row_count
					);

					$current_method_title = $imported_shipping_method['method_title'];
					$method_title         = $current_method_title;

				} else {
					$imported_shipping_method['method_rules'][] = $this->new_rule( $csv_row, $import_row_count );
				}
			}
			if ( ! $first ) {
				$shipping_methods[ $imported_shipping_method['id'] ] = $imported_shipping_method;
				if ( $new_method ) {
					WC_Admin_Settings::add_message(
						sprintf(
							// Translators: imported method title and method title.
							__( 'Shipping method %1$s imported as %2$s.', 'flexible-shipping' ), $current_method_title,
							$method_title
						)
					);
				}
			}
			$first = false;
		}

		return $shipping_methods;
	}
}
