<?php
class XT_Woo_Floating_Cart_Customizer_Controls {
	
	function __construct( $wp_customize ) {
		
		$this->path = dirname(__FILE__);
		$this->controls_classes = glob($this->path.'/controls/*/*.php');
		
		foreach($this->controls_classes as $control) {
			require_once($control);
		}
		
		// Register our custom control with Xirki
		add_filter( 'xirki/control_types', array($this, 'register_xirki' ), 10, 1);
	}	
	
	function register_xirki( $controls ) {

		foreach($this->controls_classes as $control) {
			$control_id = str_replace(array("class-xirki-control-", "-control"), array("", ""), basename($control, ".php"));
			$control_name = str_replace(" ", "_", ucwords(str_replace("-", " ", $control_id)));
			$controls[$control_id] = 'Xirki_Control_'.$control_name;
		}
	
		return $controls;
	}
}