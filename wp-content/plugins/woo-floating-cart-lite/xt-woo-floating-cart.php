<?php
/**
 * XT WooCommerce Floating Cart
 *
 * @package     XT_Woo_Floating_Cart
 * @author      XplodedThemes
 * @copyright   2018 XplodedThemes
 * @license     GPL-2.0+
 *
 * @wordpress-plugin
 * Plugin Name: XT WooCommerce Floating Cart
 * Plugin URI:  https://xplodedthemes.com/products/woo-floating-cart/
 * Description: An Interactive Floating Cart for WooCommerce that slides in when the user decides to buy an item. Fully customizable right from WordPress Customizer with Live Preview. Products, quantities and prices are updated instantly via Ajax.
 * Version:     1.4.5
 * WC requires at least: 3.0.0
 * WC tested up to: 3.8.0
 * Author:      XplodedThemes
 * Author URI:  https://xplodedthemes.com
 * Text Domain: woo-floating-cart
 * Domain Path: /languages
 * License:     GPL-2.0+
 * License URI: http://www.gnu.org/licenses/gpl-2.0.txt
 *
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

global $xt_woofc_plugin;

$market = '##XT_MARKET##';
$market = (strpos($market, 'XT_MARKET') !== false) ? 'freemius' : $market;
$market = (defined('XT_MARKET')) ? XT_MARKET : $market;

$xt_woofc_plugin = (object) array(
    'version'       => '1.4.5',
    'name'          => esc_html__('XT WooCommerce Floating Cart', 'woo-floating-cart'),
    'menu_name'     => esc_html__('Woo Floating Cart', 'woo-floating-cart'),
    'icon'          => 'dashicons-cart',
    'slug'          => 'xt-woo-floating-cart',
    'premium_slug'  => 'xt-woo-floating-cart',
    'freemium_slug' => 'woo-floating-cart-lite',
    'freemius_id'   => '2907',
    'market'        => $market,
	'markets'       => array(
		'envato' => array(
			'id' => 22115713,
			'buy_url' => 'https://codecanyon.net/item/woocommerce-floating-cart/22115713'
		)
	),
    'license_section_slug' => 'xt-woo-floating-cart',
    'file'          => __FILE__
);

if ( function_exists( 'xt_woofc_fs' ) ) {

    xt_woofc_fs()->set_basename( false, __FILE__ );

} else {

    // Load sdk bootstrap file.
    require_once plugin_dir_path(__FILE__) . 'includes/class-bootstrap.php';

    /**
     * Freemius helper function for easy SDK access.
     *
     * @since    1.0.0
     */

    function xt_woofc_fs()
    {
        global $xt_woofc_plugin;

        return XT_Woo_Floating_Cart_Bootstrap::boot($xt_woofc_plugin)->sdk;
    }

    /**
     * Begins execution of the plugin.
     *
     * Since everything within the plugin is registered via hooks,
     * then kicking off the plugin from this point in the file does
     * not affect the page life cycle.
     *
     * @since    1.0.0
     */
    function xt_woo_floating_cart() {

        global $xt_woofc_plugin;

        return XT_Woo_Floating_Cart::instance($xt_woofc_plugin);
    }

    // Init Freemius.
    xt_woofc_fs();

    // Run Plugin.
    xt_woo_floating_cart();

}
