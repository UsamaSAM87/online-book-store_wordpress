<?php

/**
 * The file that defines the core plugin class
 *
 * A class definition that includes attributes and functions used across both the
 * public-facing side of the site and the admin area.
 *
 * @link       http://xplodedthemes.com
 * @since      1.0.0
 *
 * @package    XT_Woo_Floating_Cart
 * @subpackage XT_Woo_Floating_Cart/includes
 */
/**
 * The core plugin class.
 *
 * This is used to define internationalization, admin-specific hooks, and
 * public-facing site hooks.
 *
 * Also maintains the unique identifier of this plugin as well as the current
 * version of the plugin.
 *
 * @since      1.0.0
 * @package    XT_Woo_Floating_Cart
 * @subpackage XT_Woo_Floating_Cart/includes
 * @author     XplodedThemes <helpdesk@xplodedthemes.com>
 */
// If this file is called directly, abort.
if ( !defined( 'WPINC' ) ) {
    die;
}
class XT_Woo_Floating_Cart
{
    /**
     * The single instance of XT_Woo_Floating_Cart.
     * @var 	object
     * @access  private
     * @since 	1.0.0
     */
    private static  $_instance = null ;
    /**
     * Var that holds the plugin name.
     *
     * @since    1.0.0
     * @access   protected
     * @var      object    $plugin    Plugin Info
     */
    protected  $plugin ;
    /**
     * Var that holds the public class object.
     *
     * @since    1.0.0
     * @access   protected
     * @var      string    $plugin_public    Plugin Public
     */
    protected  $plugin_public ;
    /**
     * Var that holds the admin class object.
     *
     * @since    1.0.0
     * @access   protected
     * @var      string    $plugin_admin   Plugin Admin
     */
    protected  $plugin_admin ;
    /**
     * The loader that's responsible for maintaining and registering all hooks that power
     * the plugin.
     *
     * @since    1.0.0
     * @access   protected
     * @var      XT_Woo_Floating_Cart_Loader    $loader    Maintains and registers all hooks for the plugin.
     */
    protected  $loader ;
    /**
     * Define the core functionality of the plugin.
     *
     * Set the plugin name and the plugin version that can be used throughout the plugin.
     * Load the dependencies, define the locale, and set the hooks for the admin area and
     * the public-facing side of the site.
     *
     * @since    1.0.0
     */
    public function __construct( $plugin )
    {
        // Load plugin environment variables
        $this->plugin = $plugin;
        $this->script_suffix = ( defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ? '' : '-min' );
        $this->loader = $this->load_dependencies();
        $this->set_locale();
        $this->define_admin_hooks();
        $this->define_public_hooks();
        $this->loader->run();
    }
    
    /**
     * Load the required dependencies for this plugin.
     *
     * Include the following files that make up the plugin:
     *
     * - XT_Woo_Floating_Cart_Loader. Orchestrates the hooks of the plugin.
     * - XT_Woo_Floating_Cart_i18n. Defines internationalization functionality.
     * - XT_Woo_Floating_Cart_Admin. Defines all hooks for the admin area.
     * - XT_Woo_Floating_Cart_Public. Defines all hooks for the public side of the site.
     *
     * Create an instance of the loader which will be used to register the hooks
     * with WordPress.
     *
     * @since    1.0.0
     * @access   private
     */
    private function load_dependencies()
    {
        /**
         * The class responsible for orchestrating the actions and filters of the
         * core plugin.
         */
        require_once $this->plugin_path() . 'includes/class-loader.php';
        /**
         * The class responsible for defining internationalization functionality
         * of the plugin.
         */
        require_once $this->plugin_path() . 'includes/class-i18n.php';
        /**
         * The class responsible for checking for migrations
         */
        require_once $this->plugin_path() . 'includes/class-migration.php';
        /**
         * XT Plugins Tab
         */
        require_once $this->plugin_path() . 'includes/class-plugins-tab.php';
        /**
         * The class responsible for defining all actions that occur in the admin area.
         */
        require_once $this->plugin_path() . 'admin/class-admin.php';
        /**
         * The class responsible for defining all actions that occur in the public-facing
         * side of the site.
         */
        require_once $this->plugin_path() . 'public/class-public.php';
        return new XT_Woo_Floating_Cart_Loader();
    }
    
    /**
     * Define the locale for this plugin for internationalization.
     *
     * Uses the XT_Woo_Floating_Cart_i18n class in order to set the domain and to register the hook
     * with WordPress.
     *
     * @since    1.0.0
     * @access   private
     */
    private function set_locale()
    {
        $plugin_i18n = new XT_Woo_Floating_Cart_i18n();
        $this->loader->add_action( 'plugins_loaded', $plugin_i18n, 'load_plugin_textdomain' );
    }
    
    /**
     * Register all of the hooks related to the admin area functionality
     * of the plugin.
     *
     * @since    1.0.0
     * @access   private
     */
    private function define_admin_hooks()
    {
        $this->xirki_init();
        if ( is_admin() ) {
            // Check for migrations
            new XT_Woo_Floating_Cart_Migration( $this );
        }
        $this->plugin_admin = new XT_Woo_Floating_Cart_Admin( $this );
        $this->loader->add_filter(
            'auto_update_plugin',
            $this->plugin_admin,
            'auto_update',
            10,
            2
        );
        $this->loader->add_action(
            'admin_body_class',
            $this->plugin_admin,
            'admin_body_class',
            1
        );
        $this->loader->add_action( 'admin_enqueue_scripts', $this->plugin_admin, 'enqueue_styles' );
        $this->loader->add_action( 'admin_enqueue_scripts', $this->plugin_admin, 'enqueue_scripts' );
        $this->loader->add_action(
            'admin_notices',
            $this->plugin_admin,
            'woocommerce_missing_notice',
            1
        );
        $this->loader->add_action(
            'admin_notices',
            $this->plugin_admin,
            'woocommerce_settings',
            1
        );
    }
    
    /**
     * Register all of the hooks related to the public-facing functionality
     * of the plugin.
     *
     * @since    1.0.0
     * @access   private
     */
    private function define_public_hooks()
    {
        $this->plugin_public = new XT_Woo_Floating_Cart_Public( $this );
        $this->loader->add_filter( 'body_class', $this->plugin_public, 'body_class' );
        $this->loader->add_action( 'wp_enqueue_scripts', $this->plugin_public, 'enqueue_vendors' );
        $this->loader->add_action( 'wp_enqueue_scripts', $this->plugin_public, 'enqueue_styles' );
        $this->loader->add_action( 'wp_enqueue_scripts', $this->plugin_public, 'enqueue_scripts' );
        $this->loader->add_action( 'wp_enqueue_scripts', $this->plugin_public, 'enqueue_theme_fixes' );
        $this->loader->add_action(
            'init',
            $this->plugin_public,
            'init_ajax',
            30
        );
        $this->loader->add_action(
            'template_redirect',
            $this->plugin_public,
            'define_woocommerce_constants',
            10
        );
        $this->loader->add_action( 'wp_footer', $this->plugin_public, 'render' );
    }
    
    public function xirki_init()
    {
        add_filter( 'xirki_telemetry', '__return_false', 1 );
        require_once $this->plugin_path() . 'includes/xirki/xirki.php';
        require_once $this->plugin_path() . 'includes/customizer/class-customizer.php';
        $this->loader->add_action( 'after_setup_theme', $this, 'customizer_init' );
        $this->loader->add_action( 'customize_register', $this, 'customizer_controls' );
        $this->loader->add_action( 'customize_preview_init', $this, 'customizer_preview_script' );
    }
    
    public function customizer_init()
    {
        new XT_Woo_Floating_Cart_Customizer( $this );
    }
    
    public function customizer_controls( $wp_customize )
    {
        require_once $this->plugin_path() . 'includes/customizer/class-customizer-controls.php';
        new XT_Woo_Floating_Cart_Customizer_Controls( $wp_customize );
    }
    
    public function customizer_preview_script()
    {
        wp_enqueue_script(
            $this->plugin_slug( 'customizer' ),
            $this->plugin_url( 'includes/customizer' ) . 'assets/js/customizer' . $this->script_suffix . '.js',
            array( 'jquery', 'customize-preview' ),
            $this->plugin_version(),
            true
        );
        $js_vars_fields = array();
        $fields = Xirki::$fields;
        foreach ( $fields as $field ) {
            if ( isset( $field['transport'] ) && 'postMessage' === $field['transport'] && isset( $field['js_vars'] ) && !empty($field['js_vars']) && is_array( $field['js_vars'] ) && isset( $field['settings'] ) ) {
                $js_vars_fields[$field['settings']] = $field['js_vars'];
            }
        }
        wp_localize_script( $this->plugin_slug( 'customizer' ), 'jsvars', $js_vars_fields );
    }
    
    /**
     * The plugin info
     *
     * @since     1.0.0
     * @return    object    The plugin info.
     */
    public function plugin()
    {
        return $this->plugin;
    }
    
    /**
     * The name of the plugin
     *
     * @since     1.0.0
     * @return    string    The name of the plugin.
     */
    public function plugin_name()
    {
        return $this->plugin()->name;
    }
    
    /**
     * The menu name of the plugin
     *
     * @since     1.0.0
     * @return    string    The menu name of the plugin.
     */
    public function plugin_menu_name()
    {
        return $this->plugin()->menu_name;
    }
    
    /**
     * The ID of the plugin used to uniquely identify it within the context of
     * WordPress and to define internationalization functionality.
     *
     * @since     1.0.0
     * @return    string    The name of the plugin.
     */
    public function plugin_slug( $section = '' )
    {
        return $this->plugin()->slug . (( !empty($section) ? '-' . $section : '' ));
    }
    
    /**
     * The dashicon of the plugin
     *
     * @since     1.0.0
     * @return    string    The name of the plugin.
     */
    public function plugin_icon()
    {
        return $this->plugin()->icon;
    }
    
    /**
     * Retrieve the version number of the plugin.
     *
     * @since     1.0.0
     * @return    string    The version number of the plugin.
     */
    public function plugin_version()
    {
        return $this->plugin()->version;
    }
    
    /**
     * Retrieve the plugin marketplace
     *
     * @since     1.0.0
     * @return    string    The plugin marketplace.
     */
    public function plugin_market()
    {
        return $this->plugin()->market;
    }
    
    /**
     * The plugin file
     *
     * @since     1.0.0
     * @return    string    The plugin file.
     */
    public function plugin_file()
    {
        return $this->plugin()->file;
    }
    
    /**
     * The plugin directory
     *
     * @since     1.0.0
     * @return    string    The plugin directory.
     */
    public function plugin_dir()
    {
        return dirname( $this->plugin_file() );
    }
    
    /**
     * The plugin path
     *
     * @since     1.0.0
     * @return    string    The plugin path.
     */
    public function plugin_path( $dir = null, $file = null )
    {
        $path = plugin_dir_path( $this->plugin_file() );
        if ( !empty($dir) ) {
            $path .= $dir . "/";
        }
        if ( !empty($file) ) {
            $path .= $file;
        }
        return $path;
    }
    
    /**
     * The plugin URL
     *
     * @since     1.0.0
     * @return    string    The plugin url.
     */
    public function plugin_url( $dir = null, $file = null )
    {
        $url = plugin_dir_url( $this->plugin_file() );
        if ( !empty($dir) ) {
            $url .= $dir . "/";
        }
        if ( !empty($file) ) {
            $url .= $file;
        }
        return $url;
    }
    
    /**
     * The plugin admin URL
     *
     * @since     1.0.0
     * @return    string    The plugin admin url.
     */
    public function plugin_admin_url( $section = '', $params = array() )
    {
        $url = admin_url( 'admin.php?page=' . $this->plugin_slug( $section ) );
        if ( !empty($params) ) {
            $url = add_query_arg( $params, $url );
        }
        return $url;
    }
    
    /**
     * The plugin theme templates path
     *
     * @since     1.0.0
     * @return    string    The plugin theme templates path.
     */
    public function template_path()
    {
        return apply_filters( 'xt_woo_floating_cart_template_path', 'woo-floating-cart/' );
    }
    
    /**
     * The reference to the class that orchestrates the hooks with the plugin.
     *
     * @since     1.0.0
     * @return    XT_Woo_Floating_Cart_Loader    Orchestrates the hooks of the plugin.
     */
    public function plugin_loader()
    {
        return $this->loader;
    }
    
    /**
     * The reference to the class that manages the frontend side of the plugin.
     *
     * @since     1.0.0
     * @return    XT_Woo_Floating_Cart_Public    Frontend side of the plugin.
     */
    public function frontend()
    {
        return $this->plugin_public;
    }
    
    /**
     * The reference to the class that manages the backend side of the plugin.
     *
     * @since     1.0.0
     * @return    XT_Woo_Floating_Cart_Admin    Backend side of the plugin.
     */
    public function backend()
    {
        return $this->plugin_admin;
    }
    
    /**
     * Get Freemius Function
     *
     * @since     1.0.0
     * @return    Object
     */
    public function fs()
    {
        return xt_woofc_fs();
    }
    
    /**
     * Main XT_Woo_Floating_Cart Instance
     *
     * Ensures only one instance of XT_Woo_Floating_Cart is loaded or can be loaded.
     *
     * @since 1.0.0
     * @static
     * @see XT_Woo_Floating_Cart()
     * @return XT_Woo_Floating_Cart instance
     */
    public static function instance( $plugin )
    {
        if ( is_null( self::$_instance ) ) {
            self::$_instance = new self( $plugin );
        }
        return self::$_instance;
    }
    
    // End instance()
    /**
     * Cloning is forbidden.
     *
     * @since 1.0.0
     */
    public function __clone()
    {
        _doing_it_wrong( __FUNCTION__, esc_html__( 'Cheatin&#8217; huh?', 'woo-floating-cart' ), $this->plugin_version() );
    }
    
    // End __clone()
    /**
     * Unserializing instances of this class is forbidden.
     *
     * @since 1.0.0
     */
    public function __wakeup()
    {
        _doing_it_wrong( __FUNCTION__, esc_html__( 'Cheatin&#8217; huh?', 'woo-floating-cart' ), $this->plugin_version() );
    }

}