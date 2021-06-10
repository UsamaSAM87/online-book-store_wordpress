<?php

/**
 * The admin-specific functionality of the plugin.
 *
 * @link       http://xplodedthemes.com
 * @since      1.0.0
 *
 * @package    XT_Woo_Floating_Cart
 * @subpackage XT_Woo_Floating_Cart/admin
 */

/**
 * The admin-specific functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the admin-specific stylesheet and JavaScript.
 *
 * @package    XT_Woo_Floating_Cart
 * @subpackage XT_Woo_Floating_Cart/admin
 * @author     XplodedThemes <helpdesk@xplodedthemes.com>
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

class XT_Woo_Floating_Cart_Admin {

	/**
	 * Core class reference.
	 *
	 * @since    1.0.0
	 * @access   private
     * @var      XT_Woo_Floating_Cart    core    Core Class
	 */
	private $core;

    /**
     * Core class reference.
     *
     * @since    1.0.0
     * @access   public
     * @var      XT_Woo_Floating_Cart_Welcome    welcome    Welcome Class
     */
    public $welcome;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    1.0.0
	 * @param    XT_Woo_Floating_Cart    $core    Plugin core class.
	 */
	public function __construct( &$core ) {

		$this->core = $core;

        $this->welcome = $this->init_welcome_page();
	}

	function admin_body_class( $classes ) {

		$screen = get_current_screen();

		if(!empty($screen) && strpos($screen->base, $this->core->plugin_slug()) !== false) {
	    	$classes .= ' '.$this->core->plugin_slug('admin');
	    }

	    return $classes;
	}

    public function auto_update ( $update, $item ) {
        // Array of plugin slugs to always auto-update
        $plugins = array ($this->core->plugin()->freemium_slug);

        if ( in_array( $item->slug, $plugins ) ) {
            return true; // Always update plugins in this array
        } else {
            return $update; // Else, use the normal API response to decide whether to update or not
        }
    }
    
	function init_welcome_page() {

        require_once 'welcome/class-welcome.php';

        $sections = array();

        $sections[] = array(
            'id' => 'changelog',
            'title' => esc_html__( 'Change Log', 'woo-floating-cart' ),
            'menu_title' => esc_html__( 'About', 'woo-floating-cart' ),
            'show_menu' => true,
            'content' => array(
                'type' => 'changelog',
                'show_refresh' => true
            )
        );

        $sections[] = array(
            'id' => 'customizer',
            'title' => esc_html__( 'Customize', 'woo-floating-cart' ),
            'show_menu' => true,
            'action_link' => true,
            'redirect' => XT_Woo_Floating_Cart_Customizer::customizer_link()
        );

        if($this->core->plugin_market() !== 'freemius') {

            $sections[] = array(
                'id' => 'support',
                'title' => esc_html__( 'Support', 'woo-floating-cart' ),
                'show_menu' => true,
                'external' => 'https://xplodedthemes.com/support'
            );

        }else{

            $sections[] = array(
                'id' => 'support',
                'title' => esc_html__( 'Support', 'woo-floating-cart' ),
                'show_menu' => false,
                'redirect' => $this->core->plugin_admin_url('contact')
            );
        }

        $sections[] = array(
            'id' => 'shop',
            'title' => esc_html__( 'Shop', 'woo-floating-cart' ),
            'show_menu' => false,
            'content' => array(
                'type' => 'url',
                'url' => 'http://xplodedthemes.com/api/products.php?format=html&exclude='.$this->core->plugin_slug(),
                'title' => esc_html__( 'Products you might like', 'woo-floating-cart' ),
                'show_refresh' => true,
            )
        );

        if(!$this->core->fs()->is_paying() && $this->core->plugin_market() === 'freemius') {

            $sections[] = array(
                'id' => 'upgrade',
                'title' => esc_html__( 'Upgrade', 'woo-floating-cart' ),
                'show_menu' => false,
                'featured' => true,
                'redirect' => $this->core->fs()->get_upgrade_url()
            );
        }

        $sections = apply_filters('xt_woofc_welcome_sections', $sections, $this->core);

        return new XT_Woo_Floating_Cart_Welcome($this->core, $sections);
    }
    

	/**
	 * Check if woocommerce is activated, error if not
	 *
	 * @since    1.0.0
	 */
	public function woocommerce_missing_notice() {

		if ( ! class_exists( 'WooCommerce' ) ) {

			$class = 'notice notice-error';
			$message = sprintf(
				__( '<strong>%1$s</strong> plugin requires %2$s to be installed and active.', 'woo-floating-cart' ),
				$this->core->plugin_name(),
				'<a target="_blank" href="https://en-ca.wordpress.org/plugins/woocommerce/">WooCommerce</a>'
			);
			printf( '<div class="%1$s"><p>%2$s</p></div>', $class, $message );

			deactivate_plugins( $this->core->plugin_file() );
		}
	}

	public function woocommerce_settings() {

		$wc_ajax_cart_enabled = get_option( 'woocommerce_enable_ajax_add_to_cart' ) === 'yes';
		if(!$wc_ajax_cart_enabled) {
			update_option( 'woocommerce_enable_ajax_add_to_cart', 'yes' );

			$class = 'notice notice-error';
			$message = sprintf(
				__( '<strong>%1$s</strong> plugin requires that WooCommerce "enable ajax add to cart" option to be enabled', 'woo-floating-cart' ),
				$this->core->plugin_name()
			);
			printf( '<div class="%1$s"><p>%2$s</p></div>', $class, $message );
		}

		$wc_cart_redirect_after_add = get_option( 'woocommerce_cart_redirect_after_add' ) === 'yes';
		if($wc_cart_redirect_after_add) {
			update_option( 'woocommerce_cart_redirect_after_add', 'no' );

			$class = 'notice notice-error';
			$message = sprintf(
				__( '<strong>%1$s</strong> plugin requires that WooCommerce "cart redirect after to cart" option to be enabled', 'woo-floating-cart' ),
				$this->core->plugin_name()
			);
			printf( '<div class="%1$s"><p>%2$s</p></div>', $class, $message );
		}
	}

	/**
	 * Register the stylesheets for the admin area.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_styles() {

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in XT_Woo_Floating_Cart_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The XT_Woo_Floating_Cart_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */

        //wp_enqueue_style( $this->core->plugin_slug(), $this->core->plugin_url( 'admin' ) . 'assets/css/admin.css', array(), $this->core->plugin_version(), 'all' );

	}

	/**
	 * Register the JavaScript for the admin area.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_scripts() {

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in XT_Woo_Floating_Cart_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The XT_Woo_Floating_Cart_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */

		//wp_enqueue_script( $this->core->plugin_slug(), $this->core->plugin_url( 'admin' ) . 'assets/js/admin'.$this->core->script_suffix.'.js', array( 'jquery' ), $this->core->plugin_version(), false );

	}

}
