<?php

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * XT Woo Floating Cart Xirki Options Class.
 */
class XT_Woo_Floating_Cart_Customizer {

	public static $parent;
	public static $config_id = 'xt_woofc';
	public static $options = null;
	public static $path;
	
	/**
	 * Class constructor
	 */
	public function __construct($parent) {
		
		/**
		 * Exit early if Xirki does not exist or is not installed and activated.
		 */
		if ( ! class_exists( 'Xirki' ) ) {
			return;
		}

		self::$parent = $parent;
		self::$path = dirname(__FILE__);
		
		self::add_config();
		self::add_panels();
		self::add_sections();
		self::add_fields();

        add_action( 'customize_preview_init', array(__CLASS__, 'customizer_preview_script' ));
        add_action( 'customize_controls_enqueue_scripts', array(__CLASS__, 'customizer_styles' ));
		add_filter( 'wp_check_filetype_and_ext', array(__CLASS__, 'check_filetype_and_ext'), 10, 4 );
		add_filter( 'upload_mimes', array(__CLASS__, 'allow_myme_types'), 1, 1);

	}

	public static function customizer_link() {

		return admin_url('customize.php?autofocus[panel]='.self::$config_id);
	}

	/**
	 * Xirki Config
	 */
	public static function add_config() {

		Xirki::add_config( self::$config_id, array(
		    'capability'    => 'edit_theme_options',
		    'option_type'   => 'option',
		    'option_name'	=> self::$config_id	    
		));	
	}

	/**
	 * Add panels to Xirki.
	 */
	public static function add_panels() {

		Xirki::add_panel( self::panel_id(), array(
		    'priority'    => 130,
		    'title'       => self::$parent->plugin_menu_name(),
		    'icon' => 'dashicons-cart'
		));
	}

	/**
	 * Add sections to Xirki.
	 */
	public static function add_sections() {

		Xirki::add_section( 
			self::section_id('general'), 
			array(
			    'title'          => esc_html__( 'General', 'woo-floating-cart'),
			    'panel'		 	 => self::panel_id(),
			    'priority'       => 160,
			    'capability'     => 'edit_theme_options',
			    'icon' 			 => 'dashicons-admin-generic'
			)
		);

		Xirki::add_section( 
			self::section_id('visibility'), 
			array(
			    'title'          => esc_html__( 'Visibility', 'woo-floating-cart'),
			    'panel'		 	 => self::panel_id(),
			    'priority'       => 160,
			    'capability'     => 'edit_theme_options',
			    'icon' 			 => 'dashicons-visibility'
			)
		);
				
		Xirki::add_section( 
			self::section_id('typography'), 
			array(
			    'title'          => esc_html__( 'Typography', 'woo-floating-cart'),
			    'panel'		 	 => self::panel_id(),
			    'priority'       => 160,
			    'capability'     => 'edit_theme_options',
			    'icon' 			 => 'dashicons-editor-bold'
			)
		);
		
		Xirki::add_section( 
			self::section_id('trigger'), 
			array(
			    'title'          => esc_html__( 'Cart Trigger', 'woo-floating-cart'),
			    'panel'		 	 => self::panel_id(),
			    'priority'       => 160,
			    'capability'     => 'edit_theme_options',
			    'icon' 			 => 'dashicons-external'
			)
		);
		
		Xirki::add_section( 
			self::section_id('header'), 
			array(
			    'title'          => esc_html__( 'Cart Header', 'woo-floating-cart'),
			    'panel'		 	 => self::panel_id(),
			    'priority'       => 160,
			    'capability'     => 'edit_theme_options',
			    'icon'			 => 'dashicons-arrow-up-alt2'
			)
		);
		
		Xirki::add_section( 
			self::section_id('body'),
			array(
			    'title'          => esc_html__( 'Cart Body', 'woo-floating-cart'),
			    'panel'		 	 => self::panel_id(),
			    'priority'       => 160,
			    'capability'     => 'edit_theme_options',
			    'icon'			 => 'dashicons-feedback'
			)
		);
		
		Xirki::add_section( 
			self::section_id('footer'), 
			array(
			    'title'          => esc_html__( 'Cart Footer', 'woo-floating-cart'),
			    'panel'		 	 => self::panel_id(),
			    'priority'       => 160,
			    'capability'     => 'edit_theme_options',
			    'icon'			 => 'dashicons-arrow-down-alt2'
			)
		);

	}

	/**
	 * Add fields to Xirki.
	 */
	public static function add_fields() {
		
		// General Settings.
		
		require_once self::$path . '/fields/general.php';
		require_once self::$path . '/fields/visibility.php';
		require_once self::$path . '/fields/typography.php';
		require_once self::$path . '/fields/trigger.php';
		require_once self::$path . '/fields/header.php';
		require_once self::$path . '/fields/body.php';
		require_once self::$path . '/fields/footer.php';

	}

	public static function panel_id($id = null) {

		$panel_id = self::$config_id;
		if(!empty($id)) {
			$panel_id .= '-'.$id;
		}

		return $panel_id;
	}

	public static function section_id($id) {
		
		return self::$config_id.'_'.$id;
	}
	
	public static function field_id($id) {
		
		return $id;
	}

    public static function get_option_exists($id) {

        return isset(Xirki::$fields[ Xirki::$config[ self::$config_id ]['option_name'] . '[' . $id . ']' ]);
    }

	public static function get_option($id, $default = null) {

        if(!self::get_option_exists($id)) {

            return $default;
        }

        return Xirki::get_option(self::$config_id, $id);
	}

    public static function customizer_preview_script() {

        wp_enqueue_script(
            'xirki-customizer',
            self::$parent->plugin_url(). 'includes/customizer/assets/js/customizer-min.js',
            array( 'jquery','customize-preview' ),
            self::$parent->plugin_version(),
            true
        );
    }

    public static function customizer_styles() {

        wp_enqueue_style(
            'xirki-customizer',
            self::$parent->plugin_url(). 'includes/customizer/assets/css/customizer.css',
            array(),
            self::$parent->plugin_version()
        );

        wp_enqueue_style(
            self::$config_id.'-customizer',
            self::$parent->plugin_url(). 'includes/customizer/assets/css/customizer-custom.css',
            array(),
            self::$parent->plugin_version()
        );
	}

	// Allow SVG
	public static function check_filetype_and_ext($data, $file, $filename, $mimes) {
	
	  global $wp_version;
	  if ( $wp_version <= '4.7.1' ) {
	     return $data;
	  }
	
	  $filetype = wp_check_filetype( $filename, $mimes );
	
	  return [
	      'ext'             => $filetype['ext'],
	      'type'            => $filetype['type'],
	      'proper_filename' => $data['proper_filename']
	  ];
	
	}

	public static function allow_myme_types($mime_types){

		$mime_types['svg'] = 'image/svg+xml'; //Adding svg extension
		$mime_types['svgz'] = 'image/svg+xml';

		return $mime_types;

	}

	public static function get_page_options() {

        $pages = get_posts(array(
            'post_type' => 'page',
            'posts_per_page' => 100,
            'suppress_filters' => true
        ));

        $pages_options = array();
        foreach ($pages as $page) {
            $pages_options[$page->ID] = $page->post_title;
        }

        return $pages_options;
    }

} // End Class
	
