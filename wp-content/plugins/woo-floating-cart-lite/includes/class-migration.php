<?php

if ( ! defined( 'ABSPATH' ) ) exit;

class XT_Woo_Floating_Cart_Migration {

 	/**
	 * The single instance of XT_Woo_Floating_Cart_Migration.
	 * @var 	object
	 * @access  private
	 * @since 	1.0.0
	 */
	private static $_instance = null;

	/**
	 * The main plugin object.
	 * @var 	object
	 * @access  public
	 * @since 	1.0.0
	 */
	public $parent = null;
	public $version_key = null;
	public $migrations = array();

	public function __construct ( $parent ) {

		$this->parent = &$parent;
		$this->parent->migration = &$this;
		$this->version_key = $this->get_version_key();

		$this->migrations = $this->get_migrations();

		if(!empty($this->migrations)) {
			add_action('admin_init', array($this, 'upgrade'), 10);
		}
	}	
			
	function get_version_key() {

        return $this->parent->plugin_slug('version');
	}	
	
	function get_migrations() {
		
		$files = scandir($this->parent->plugin_path() . 'includes/migrations/');
		$migrations = array();
		
		foreach($files as $file) {
			if($file === '.' || $file == '..') {
				continue;
			}
			$migrations[] = preg_replace('/migration\-(.+?)\.php/', '$1', $file);
		}
		
		return $migrations;
	}
	
	function upgrade() {

		$old_version = get_option( $this->version_key );
		$new_version = $this->parent->plugin_version(); 

		if ( $new_version !== $old_version )
		{
	
			foreach($this->migrations as $migration) {

				if ( $old_version < $migration )
				{
					$this->migrate($migration);
				}
			}

			// End Migrations	
					
			update_option($this->version_key, $new_version);
			
			$this->after_upgrade();
			
		}
	}
	
	function migrate($version) {
	
		$path = $this->parent->plugin_path() . 'includes/migrations/migration-'.$version.'.php';	
		
		if(file_exists($path)) {
			
			include_once $path;
		}
	
	}

	
	function after_upgrade() {
		
		wp_redirect($this->parent->backend()->welcome->get_url());
		exit;
	}


	/**
	 * XT_Woo_Floating_Cart_Migration Instance
	 *
	 * Ensures only one instance of XT_Woo_Floating_Cart_Migration is loaded or can be loaded.
	 *
	 * @since 1.0.0
	 * @static
	 * @see Slick_Menu()
	 * @return XT_Woo_Floating_Cart_Migration instance
	 */
	public static function instance ( $parent ) {
		if ( is_null( self::$_instance ) ) {
			self::$_instance = new self( $parent );
		}
		return self::$_instance;
	} // End instance()

}
