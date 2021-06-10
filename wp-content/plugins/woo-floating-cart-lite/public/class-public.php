<?php

/**
 * The public-facing functionality of the plugin.
 *
 * @link       http://xplodedthemes.com
 * @since      1.0.0
 *
 * @package    XT_Woo_Floating_Cart
 * @subpackage XT_Woo_Floating_Cart/public
 */
/**
 * The public-facing functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the admin-specific stylesheet and JavaScript.
 *
 * @package    XT_Woo_Floating_Cart
 * @subpackage XT_Woo_Floating_Cart/public
 * @author     XplodedThemes <helpdesk@xplodedthemes.com>
 */
// If this file is called directly, abort.
if ( !defined( 'WPINC' ) ) {
    die;
}
class XT_Woo_Floating_Cart_Public
{
    /**
     * Core class reference.
     *
     * @since    1.0.0
     * @access   private
     * @var      XT_Woo_Floating_Cart    core    Core Class
     */
    private  $core ;
    /**
     * Initialize the class and set its properties.
     *
     * @since    1.0.0
     * @param    obj    $core    Plugin core class
     */
    public function __construct( &$core )
    {
        $this->core = $core;
    }
    
    public function enabled()
    {
        if ( $this->is_checkout_page() || $this->is_cart_page() ) {
            return false;
        }
        $exclude_pages = xt_woofc_option( 'hidden_on_pages', array() );
        if ( !empty($exclude_pages) ) {
            foreach ( $exclude_pages as $page ) {
                if ( !empty($page) && is_page( $page ) ) {
                    return false;
                }
            }
        }
        return true;
    }
    
    public function is_checkout_page()
    {
        $checkout_page_id = get_option( 'woocommerce_checkout_page_id' );
        return is_page( $checkout_page_id );
    }
    
    public function is_cart_page()
    {
        $cart_page_id = get_option( 'woocommerce_cart_page_id' );
        return is_page( $cart_page_id );
    }
    
    public function define_woocommerce_constants()
    {
        do_action( 'xt_woofc_before_woocommerce_constants' );
        if ( $this->enabled() ) {
        }
    }
    
    public function init_ajax()
    {
        require_once $this->core->plugin_path() . 'public/class-ajax.php';
    }
    
    function body_class( $classes )
    {
        
        if ( $this->enabled() ) {
            $trigger_hide_view_cart = xt_woofc_option( 'trigger_hide_view_cart', false );
            $classes[] = 'woocommerce';
            if ( !empty($trigger_hide_view_cart) ) {
                $classes[] = 'xt_woofc-hide-view-cart';
            }
        }
        
        return $classes;
    }
    
    /**
     * Register vendors assets for the public-facing side of the site.
     *
     * @since    1.0.0
     */
    public function enqueue_vendors()
    {
        wp_enqueue_style(
            'xt-woo-custom',
            $this->core->plugin_url( 'public/assets/css', 'woo-custom.css' ),
            array(),
            $this->core->plugin_version(),
            'all'
        );
        if ( !$this->enabled() ) {
            return false;
        }
        wp_enqueue_script( 'jquery-effects-core' );
        wp_enqueue_script(
            'xt-jquery-ajaxqueue',
            $this->core->plugin_url( 'public' ) . 'assets/vendors/jquery.ajaxqueue' . $this->core->script_suffix . '.js',
            array( 'jquery' ),
            $this->core->plugin_version(),
            false
        );
        wp_enqueue_script(
            'xt-jquery-touch',
            $this->core->plugin_url( 'public' ) . 'assets/vendors/jquery.touch' . $this->core->script_suffix . '.js',
            array( 'jquery' ),
            $this->core->plugin_version(),
            false
        );
        wp_enqueue_script(
            'xt-jquery-serializejson',
            $this->core->plugin_url( 'public' ) . 'assets/vendors/jquery.serializejson' . $this->core->script_suffix . '.js',
            array( 'jquery' ),
            $this->core->plugin_version(),
            false
        );
        wp_enqueue_script(
            'xt-cookie',
            $this->core->plugin_url( 'public' ) . 'assets/vendors/js.cookie' . $this->core->script_suffix . '.js',
            array( 'jquery' ),
            $this->core->plugin_version(),
            false
        );
        if ( xt_woofc_option_bool( 'active_cart_body_lock_scroll', false ) ) {
            wp_enqueue_script(
                'xt-body-scroll-lock',
                $this->core->plugin_url( 'public' ) . 'assets/vendors/bodyScrollLock' . $this->core->script_suffix . '.js',
                array(),
                $this->core->plugin_version(),
                false
            );
        }
        wp_enqueue_style(
            'xt-woofcicons',
            $this->core->plugin_url( 'public/assets/css', 'woofcicons.css' ),
            array(),
            $this->core->plugin_version(),
            'all'
        );
        if ( !$this->is_cart_page() ) {
            wp_dequeue_script( 'wc-cart' );
        }
        wp_enqueue_script( 'wc-add-to-cart' );
        wp_enqueue_script( 'wc-cart-fragments' );
    }
    
    /**
     * Register the stylesheets for the public-facing side of the site.
     *
     * @since    1.0.0
     */
    public function enqueue_styles()
    {
        if ( !$this->enabled() ) {
            return false;
        }
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
        wp_register_style(
            $this->core->plugin_slug(),
            $this->core->plugin_url( 'public/assets/css', 'frontend.css' ),
            array(),
            filemtime( $this->core->plugin_path( 'public/assets/css', 'frontend.css' ) ),
            'all'
        );
        wp_enqueue_style( $this->core->plugin_slug() );
        
        if ( $this->core->fs()->can_use_premium_code__premium_only() && is_rtl() ) {
            wp_register_style(
                $this->core->plugin_slug( 'rtl' ),
                $this->core->plugin_url( 'public/assets/css', 'rtl.css' ),
                array( $this->core->plugin_slug() ),
                filemtime( $this->core->plugin_path( 'public/assets/css', 'rtl.css' ) ),
                'all'
            );
            wp_enqueue_style( $this->core->plugin_slug( 'rtl' ) );
        }
    
    }
    
    /**
     * Register the JavaScript for the public-facing side of the site.
     *
     * @since    1.0.0
     */
    public function enqueue_scripts()
    {
        if ( !$this->enabled() ) {
            return false;
        }
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
        // MAIN SCRIPT
        wp_register_script(
            $this->core->plugin_slug(),
            $this->core->plugin_url( 'public/assets/js', 'frontend' . $this->core->script_suffix . '.js' ),
            array( 'jquery', 'wc-add-to-cart', 'wc-cart-fragments' ),
            filemtime( $this->core->plugin_path( 'public/assets/js', 'frontend' . $this->core->script_suffix . '.js' ) ),
            false
        );
        $wc_ajax_url = add_query_arg( 'wc-ajax', '%%endpoint%%', home_url( '/' ) );
        $vars = array(
            'ajaxurl'              => admin_url( 'admin-ajax.php' ),
            'wc_ajax_url'          => urldecode( $wc_ajax_url ),
            'can_checkout'         => xt_woofc_can_checkout(),
            'body_lock_scroll'     => xt_woofc_option_bool( 'active_cart_body_lock_scroll', false ),
            'can_use_premium_code' => $this->core->fs()->can_use_premium_code__premium_only(),
            'lang'                 => array(
            'wait'              => esc_html__( 'Please wait', 'woo-floating-cart' ),
            'loading'           => esc_html__( 'Loading', 'woo-floating-cart' ),
            'min_qty_required'  => esc_html__( 'Min quantity required', 'woo-floating-cart' ),
            'max_stock_reached' => esc_html__( 'Stock limit reached', 'woo-floating-cart' ),
        ),
        );
        wp_localize_script( $this->core->plugin_slug(), 'XT_WOOFC', $vars );
        wp_enqueue_script( $this->core->plugin_slug() );
    }
    
    /**
     * Load frontend Theme Fixes.
     * @access  public
     * @since   1.0.0
     * @return void
     */
    public function enqueue_theme_fixes()
    {
        if ( !$this->enabled() ) {
            return false;
        }
        $theme_name = get_template();
        $theme_fixes = array();
        if ( !empty($theme_fixes[$theme_name]) ) {
            foreach ( $theme_fixes[$theme_name] as $type ) {
                
                if ( $type == 'css' ) {
                    wp_register_style(
                        $this->core->plugin_slug( $theme_name ),
                        $this->core->plugin_url( 'public' ) . 'assets/theme-fix/css/' . $theme_name . '.css',
                        array( $this->core->plugin_slug() ),
                        $this->core->plugin_version()
                    );
                    wp_enqueue_style( $this->core->plugin_slug( $theme_name ) );
                } else {
                    wp_register_script(
                        $this->core->plugin_slug( $theme_name ),
                        $this->core->plugin_url( 'public' ) . 'assets/theme-fix/js/' . $theme_name . '.js',
                        array( $this->core->plugin_slug() ),
                        $this->core->plugin_version(),
                        true
                    );
                    wp_enqueue_script( $this->core->plugin_slug( $theme_name ) );
                }
            
            }
        }
    }
    
    public function render()
    {
        if ( !$this->enabled() ) {
            return false;
        }
        WC()->cart->calculate_totals();
        echo  '<div id="xt_woofc">' ;
        xt_woo_floating_cart_template( 'minicart' );
        echo  '</div>' ;
    }

}