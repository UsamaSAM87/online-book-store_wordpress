<?php

/**
 * Fired during plugin activation
 *
 * @link       http://xplodedthemes.com
 * @since      1.0.0
 *
 * @package    XT_Woo_Floating_Cart
 * @subpackage XT_Woo_Floating_Cart/includes
 */
/**
 * Fired during plugin activation.
 *
 * This class defines all code necessary to run during the plugin's activation.
 *
 * @since      1.0.0
 * @package    XT_Woo_Floating_Cart
 * @subpackage XT_Woo_Floating_Cart/includes
 * @author     XplodedThemes <helpdesk@xplodedthemes.com>
 */
class XT_Woo_Floating_Cart_Bootstrap
{
    public static  $_instance ;
    public  $plugin ;
    public  $sdk ;
    /**
     * Boot SDK
     *
     * @since    1.0.0
     * @param $plugin
     * @return mixed
     */
    public function __construct( $plugin )
    {
        $this->plugin = $plugin;
        
        if ( !isset( $this->sdk ) ) {
            /* ENVATO_EXCLUDE_BEGIN */
            
            if ( $this->plugin->market === 'freemius' ) {
                $this->sdk = $this->freemius();
                $this->sdk->add_filter( 'plugin_icon', array( $this, 'plugin_icon' ) );
                add_action( 'admin_footer', array( $this, 'sdk_assets' ) );
            }
            
            /* ENVATO_EXCLUDE_END */
            if ( $this->plugin->market !== 'freemius' ) {
                $this->sdk = $this->local__premium_only();
            }
            // Signal that SDK was initiated.
            do_action( 'xt_woofc_fs_loaded' );
            $this->base_hooks();
            $this->include_files();
        }
        
        return $this->sdk;
    }
    
    /* ENVATO_EXCLUDE_BEGIN */
    /**
     * Boot Freemius SDK
     *
     * @since    1.0.0
     * @return mixed
     */
    public function freemius()
    {
        // Activate multisite network integration.
        if ( !defined( 'WP_FS__PRODUCT_' . $this->plugin->freemius_id . '_MULTISITE' ) ) {
            define( 'WP_FS__PRODUCT_' . $this->plugin->freemius_id . '_MULTISITE', true );
        }
        // Include Freemius SDK.
        require_once plugin_dir_path( $this->plugin->file ) . 'includes/freemius/start.php';
        return fs_dynamic_init( array(
            'id'              => $this->plugin->freemius_id,
            'slug'            => $this->plugin->freemium_slug,
            'premium_slug'    => $this->plugin->premium_slug,
            'type'            => 'plugin',
            'public_key'      => 'pk_befd5797ccb0ea582377dfd79ce5b',
            'is_premium'      => false,
            'premium_suffix'  => 'Pro',
            'has_addons'      => false,
            'has_paid_plans'  => true,
            'has_affiliation' => 'all',
            'trial'           => array(
            'days'               => 14,
            'is_require_payment' => true,
        ),
            'menu'            => array(
            'slug'    => $this->plugin->slug,
            'contact' => true,
            'support' => false,
        ),
            'is_live'         => true,
        ) );
    }
    
    /* ENVATO_EXCLUDE_BEGIN */
    /**
     * Enqueue Freemius SDK Custom assets
     */
    public function sdk_assets()
    {
        wp_enqueue_style(
            'xt-freemius-sdk',
            'https://s3.amazonaws.com/xt-freemius/sdk.css',
            array(),
            $this->plugin->version,
            'all'
        );
        wp_enqueue_script(
            'xt-freemius-sdk',
            'https://s3.amazonaws.com/xt-freemius/sdk.min.js',
            array(),
            $this->plugin->version,
            true
        );
    }
    
    /* ENVATO_EXCLUDE_END */
    /**
     * Plugin main icon
     *
     * @return string Plugin icon
     */
    public function plugin_icon()
    {
        return dirname( $this->plugin->file ) . '/admin/assets/images/icon.png';
    }
    
    /**
     * Plugin base hooks to handle activation, deactivation and uninstall
     */
    public function base_hooks()
    {
        register_activation_hook( $this->plugin->file, array( $this, 'activate' ) );
        register_deactivation_hook( $this->plugin->file, array( $this, 'deactivate' ) );
        $this->sdk->add_action( 'after_uninstall', array( $this, 'uninstall' ) );
    }
    
    /**
     * The code that runs during plugin activation.
     * This action is documented in includes/class-activator.php
     */
    public function activate()
    {
        require_once plugin_dir_path( $this->plugin->file ) . 'includes/class-activator.php';
        XT_Woo_Floating_Cart_Activator::activate();
    }
    
    /**
     * The code that runs during plugin deactivation.
     * This action is documented in includes/class-deactivator.php
     */
    public function deactivate()
    {
        require_once plugin_dir_path( $this->plugin->file ) . 'includes/class-deactivator.php';
        XT_Woo_Floating_Cart_Deactivator::deactivate();
    }
    
    /**
     * The code that runs after plugin uninstall.
     * This action is documented in includes/class-uninstall.php
     */
    function uninstall()
    {
        require_once plugin_dir_path( $this->plugin->file ) . 'includes/class-uninstaller.php';
        XT_Woo_Floating_Cart_Uninstaller::uninstall();
    }
    
    /**
     * Include plugin files
     */
    public function include_files()
    {
        /**
         * Global functions used to access multiple class public methods.
         */
        require_once plugin_dir_path( $this->plugin->file ) . 'includes/global-functions.php';
        /**
         * WooCommerce Hooks, Filters and function overrides
         */
        require_once plugin_dir_path( $this->plugin->file ) . 'includes/woocommerce.php';
        /**
         * Plugin support
         */
        require_once plugin_dir_path( $this->plugin->file ) . 'includes/support/paypal-for-woocommerce.php';
        /**
         * The core plugin class that is used to define internationalization,
         * admin-specific hooks, and public-facing site hooks.
         */
        require_once plugin_dir_path( $this->plugin->file ) . 'includes/class-core.php';
    }
    
    /**
     * Main XT_Woo_Floating_Cart_Bootstrap Instance
     *
     * Ensures only one instance of XT_Woo_Floating_Cart_Bootstrap is loaded or can be loaded.
     *
     * @since 1.0.0
     * @static
     * @see XT_Woo_Floating_Cart_Bootstrap()
     * @return XT_Woo_Floating_Cart_Bootstrap instance
     */
    public static function boot( $plugin )
    {
        if ( is_null( self::$_instance ) ) {
            self::$_instance = new self( $plugin );
        }
        return self::$_instance;
    }

}