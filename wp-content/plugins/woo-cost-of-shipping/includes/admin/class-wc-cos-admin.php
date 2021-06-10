<?php

/**
 * The admin-specific functionality of the plugin.
 * 
 * This plugin can be expanded into net profit reporting by getting the WooCommerce Net Profit plugin
 * developed by TheRiteSites and found at https://www.theritesites.com/plugins/woocommerce-net-profit
 *
 * @link       https://www.theritesites.com
 * @since      1.0.0
 *
 * @package    WC_COS
 * @subpackage WC_COS/includes/admin
 */


 if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
 
class WC_COS_Admin {

	/**
	 * Parent plugin class.
	 *
	 * @var    WooCommerce_Net_Profit
	 * @since  1.0.0
	 */
	protected $plugin = null;

	/**
	 * Instance of WC_COS_Admin_Orders
	 * 
	 * @var WC_COS_Admin_Orders
	 * @since 1.0.0
	 */
	protected $WC_COS_Admin_Orders;

	/**
	 * Constructor.
	 *
	 * @since    1.0.0
	 * @param      string    $plugin_name       The name of this plugin.
	 * @param      string    $version    The version of this plugin.
	 */
	public function __construct( $plugin ) {

		$this->plugin = $plugin;
		
		$this->admin_classes();
		
		$this->init_hooks();

	}
	
	/**
	 * Initializes the classes used in the admin area.
	 * 
	 * @since 1.0.0
	 */
	public function admin_classes() {
		include_once 'class-wc-cos-admin-orders.php';
		$this->WC_COS_Admin_Orders = new WC_COS_Admin_Orders( $this->plugin );
	}
	
	/**
	 * Initializes the admin area hooks
	 * 
	 * @since 1.0.0
	 */
	public function init_hooks() {
		
	}
}