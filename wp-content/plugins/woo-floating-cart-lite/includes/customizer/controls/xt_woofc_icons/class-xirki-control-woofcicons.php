<?php
// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! class_exists( 'Xirki_Control_Woofcicons' ) && class_exists('Xirki_Control_Base')) {


	/**
	 * Dashicons control (modified radio).
	 */
	class Xirki_Control_Woofcicons extends Xirki_Control_Base {

		/**
		 * The control type.
		 *
		 * @access public
		 * @var string
		 */
		public $type = 'woofcicons';


		/**
		 * Enqueue control related scripts/styles.
		 *
		 * @access public
		 */
		public function enqueue() {
			
			$plugin = xt_woo_floating_cart();
			wp_enqueue_style( 'woofcicons', $plugin->plugin_url('public/assets/css', 'woofcicons.css'), array(), '1.0.0');
			wp_enqueue_style( 'xirki-woofcicons', plugin_dir_url(__FILE__).'css/woofcicons.css', array(), '1.0.0');
			wp_enqueue_script( 'xirki-woofcicons', plugin_dir_url(__FILE__).'js/woofcicons.js', array(), '1.0.0');
		}
		
		
		/**
		 * Refresh the parameters passed to the JavaScript via JSON.
		 *
		 * @access public
		 */
		public function to_json() {
			
			$icons = false;

			if(!empty($this->choices) && !empty($this->choices['types']) && is_array($this->choices['types'])) {
				
				$types = $this->choices['types'];
				$icons = array();
				foreach($types as $type) {
					$icons = array_merge($icons, self::get_icons($type));
				}
				$this->choices = $icons;
			} 
			
			parent::to_json();
			
			if($icons === false) {
				$this->json['icons'] = self::get_icons();
			}
		}

		/**
		 * An Underscore (JS) template for this control's content (but not its container).
		 *
		 * Class variables for this control class are available in the `data` JS object;
		 * export custom variables by overriding {@see Xirki_Customize_Control::to_json()}.
		 *
		 * @see WP_Customize_Control::print_template()
		 *
		 * @access protected
		 */
		protected function content_template() {
			?>
			<# if ( data.tooltip ) { #>
				<a href="#" class="tooltip hint--left" data-hint="{{ data.tooltip }}"><span class='woofcicons woofcicons-info'></span></a>
			<# } #>
			<# if ( data.label ) { #>
				<span class="customize-control-title">{{ data.label }}</span>
			<# } #>
			<# if ( data.description ) { #>
				<span class="description customize-control-description">{{{ data.description }}}</span>
			<# } #>
			<div class="icons-wrapper">
				<# if ( 'undefined' !== typeof data.choices && 1 < _.size( data.choices ) ) { #>
					<# for ( key in data.choices ) { #>
						<input {{{ data.inputAttrs }}} class="woofcicons-select" type="radio" value="{{ key }}" name="_customize-woofcicons-radio-{{ data.id }}" id="{{ data.id }}_{{ key }}" {{{ data.link }}}<# if ( data.value === key ) { #> checked="checked"<# } #>>
							<label for="{{ data.id }}_{{ key }}">
								<span class="{{ data.choices[ key ] }}"></span>
							</label>
						</input>
					<# } #>
				<# } else { #>
				
					<h4>Cart Icons</h4>
					<# for ( key in data.icons['cart'] ) { #>
						<input {{{ data.inputAttrs }}} class="woofcicons-select" type="radio" value="{{ data.icons['cart'][ key ] }}" name="_customize-woofcicons-radio-{{ data.id }}" id="{{ data.id }}_{{ data.icons['cart'][ key ] }}" {{{ data.link }}}<# if ( data.value === data.icons['cart'][ key ] ) { #> checked="checked"<# } #>>
							<label for="{{ data.id }}_{{ data.icons['cart'][ key ] }}">
								<span class="{{ data.icons['cart'][ key ] }}"></span>
							</label>
						</input>
					<# } #>
					
					<h4>Close Icons</h4>
					<# for ( key in data.icons['close'] ) { #>
						<input {{{ data.inputAttrs }}} class="woofcicons-select" type="radio" value="{{ data.icons['close'][ key ] }}" name="_customize-woofcicons-radio-{{ data.id }}" id="{{ data.id }}_{{ data.icons['close'][ key ] }}" {{{ data.link }}}<# if ( data.value === data.icons['close'][ key ] ) { #> checked="checked"<# } #>>
							<label for="{{ data.id }}_{{ data.icons['close'][ key ] }}">
								<span class="{{ data.icons['close'][ key ] }}"></span>
							</label>
						</input>
					<# } #>
					
				<# } #>
			</div>
			<?php
		}
		
			
		public function render_content() {
			self::print_template();
		}
		
		public static function get_icons($type = null) {
			
			$icons = array(
				
				'cart' => array(
					'xt_woofcicon-bag' => 'xt_woofcicon-bag',
					'xt_woofcicon-bag-1' => 'xt_woofcicon-bag-1',
					'xt_woofcicon-bag-2' => 'xt_woofcicon-bag-2',
					'xt_woofcicon-bag-3' => 'xt_woofcicon-bag-3',
					'xt_woofcicon-bag-4' => 'xt_woofcicon-bag-4',
					'xt_woofcicon-bag-5' => 'xt_woofcicon-bag-5',
					'xt_woofcicon-bag-6' => 'xt_woofcicon-bag-6',
					'xt_woofcicon-basket' => 'xt_woofcicon-basket',
					'xt_woofcicon-basket-1' => 'xt_woofcicon-basket-1',
					'xt_woofcicon-basket-2' => 'xt_woofcicon-basket-2',
					'xt_woofcicon-basket-3' => 'xt_woofcicon-basket-3',
					'xt_woofcicon-basket-supermarket' => 'xt_woofcicon-basket-supermarket',
					'xt_woofcicon-business' => 'xt_woofcicon-business',
					'xt_woofcicon-business-1' => 'xt_woofcicon-business-1',
					'xt_woofcicon-business-2' => 'xt_woofcicon-business-2',
					'xt_woofcicon-cart' => 'xt_woofcicon-cart',
					'xt_woofcicon-cart-1' => 'xt_woofcicon-cart-1',
					'xt_woofcicon-cart-2' => 'xt_woofcicon-cart-2',
					'xt_woofcicon-cart-3' => 'xt_woofcicon-cart-3',
					'xt_woofcicon-cart-4' => 'xt_woofcicon-cart-4',
					'xt_woofcicon-cart-5' => 'xt_woofcicon-cart-5',
					'xt_woofcicon-cart-6' => 'xt_woofcicon-cart-6',
					'xt_woofcicon-cart-7' => 'xt_woofcicon-cart-7',
					'xt_woofcicon-commerce' => 'xt_woofcicon-commerce',
					'xt_woofcicon-commerce-1' => 'xt_woofcicon-commerce-1',
					'xt_woofcicon-commerce-10' => 'xt_woofcicon-commerce-10',
					'xt_woofcicon-commerce-11' => 'xt_woofcicon-commerce-11',
					'xt_woofcicon-commerce-12' => 'xt_woofcicon-commerce-12',
					'xt_woofcicon-commerce-13' => 'xt_woofcicon-commerce-13',
					'xt_woofcicon-commerce-14' => 'xt_woofcicon-commerce-14',
					'xt_woofcicon-commerce-2' => 'xt_woofcicon-commerce-2',
					'xt_woofcicon-commerce-3' => 'xt_woofcicon-commerce-3',
					'xt_woofcicon-commerce-4' => 'xt_woofcicon-commerce-4',
					'xt_woofcicon-commerce-5' => 'xt_woofcicon-commerce-5',
					'xt_woofcicon-commerce-6' => 'xt_woofcicon-commerce-6',
					'xt_woofcicon-commerce-7' => 'xt_woofcicon-commerce-7',
					'xt_woofcicon-commerce-8' => 'xt_woofcicon-commerce-8',
					'xt_woofcicon-commerce-9' => 'xt_woofcicon-commerce-9',
					'xt_woofcicon-empty-shopping-cart' => 'xt_woofcicon-empty-shopping-cart',
					'xt_woofcicon-food' => 'xt_woofcicon-food',
					'xt_woofcicon-full-items-inside-a-shopping-bag' => 'xt_woofcicon-full-items-inside-a-shopping-bag',
					'xt_woofcicon-groceries' => 'xt_woofcicon-groceries',
					'xt_woofcicon-groceries-store' => 'xt_woofcicon-groceries-store',
					'xt_woofcicon-interface' => 'xt_woofcicon-interface',
					'xt_woofcicon-market' => 'xt_woofcicon-market',
					'xt_woofcicon-market-1' => 'xt_woofcicon-market-1',
					'xt_woofcicon-market-2' => 'xt_woofcicon-market-2',
					'xt_woofcicon-market-3' => 'xt_woofcicon-market-3',
					'xt_woofcicon-market-4' => 'xt_woofcicon-market-4',
					'xt_woofcicon-online-shopping-cart' => 'xt_woofcicon-online-shopping-cart',
					'xt_woofcicon-restaurant' => 'xt_woofcicon-restaurant',
					'xt_woofcicon-shop' => 'xt_woofcicon-shop',
					'xt_woofcicon-shop-1' => 'xt_woofcicon-shop-1',
					'xt_woofcicon-shop-2' => 'xt_woofcicon-shop-2',
					'xt_woofcicon-shop-3' => 'xt_woofcicon-shop-3',
					'xt_woofcicon-shop-4' => 'xt_woofcicon-shop-4',
					'xt_woofcicon-shop-5' => 'xt_woofcicon-shop-5',
					'xt_woofcicon-shopping' => 'xt_woofcicon-shopping',
					'xt_woofcicon-shopping-1' => 'xt_woofcicon-shopping-1',
					'xt_woofcicon-shopping-bag' => 'xt_woofcicon-shopping-bag',
					'xt_woofcicon-shopping-bag-1' => 'xt_woofcicon-shopping-bag-1',
					'xt_woofcicon-shopping-bag-2' => 'xt_woofcicon-shopping-bag-2',
					'xt_woofcicon-shopping-bag-3' => 'xt_woofcicon-shopping-bag-3',
					'xt_woofcicon-shopping-bag-4' => 'xt_woofcicon-shopping-bag-4',
					'xt_woofcicon-shopping-bag-5' => 'xt_woofcicon-shopping-bag-5',
					'xt_woofcicon-shopping-bag-6' => 'xt_woofcicon-shopping-bag-6',
					'xt_woofcicon-shopping-basket' => 'xt_woofcicon-shopping-basket',
					'xt_woofcicon-shopping-basket-1' => 'xt_woofcicon-shopping-basket-1',
					'xt_woofcicon-shopping-basket-2' => 'xt_woofcicon-shopping-basket-2',
					'xt_woofcicon-shopping-basket-3' => 'xt_woofcicon-shopping-basket-3',
					'xt_woofcicon-shopping-basket-4' => 'xt_woofcicon-shopping-basket-4',
					'xt_woofcicon-shopping-basket-5' => 'xt_woofcicon-shopping-basket-5',
					'xt_woofcicon-shopping-basket-6' => 'xt_woofcicon-shopping-basket-6',
					'xt_woofcicon-shopping-basket-7' => 'xt_woofcicon-shopping-basket-7',
					'xt_woofcicon-shopping-basket-8' => 'xt_woofcicon-shopping-basket-8',
					'xt_woofcicon-shopping-basket-button' => 'xt_woofcicon-shopping-basket-button',
					'xt_woofcicon-shopping-cart' => 'xt_woofcicon-shopping-cart',
					'xt_woofcicon-shopping-cart-1' => 'xt_woofcicon-shopping-cart-1',
					'xt_woofcicon-shopping-cart-10' => 'xt_woofcicon-shopping-cart-10',
					'xt_woofcicon-shopping-cart-2' => 'xt_woofcicon-shopping-cart-2',
					'xt_woofcicon-shopping-cart-3' => 'xt_woofcicon-shopping-cart-3',
					'xt_woofcicon-shopping-cart-4' => 'xt_woofcicon-shopping-cart-4',
					'xt_woofcicon-shopping-cart-5' => 'xt_woofcicon-shopping-cart-5',
					'xt_woofcicon-shopping-cart-6' => 'xt_woofcicon-shopping-cart-6',
					'xt_woofcicon-shopping-cart-7' => 'xt_woofcicon-shopping-cart-7',
					'xt_woofcicon-shopping-cart-8' => 'xt_woofcicon-shopping-cart-8',
					'xt_woofcicon-shopping-cart-9' => 'xt_woofcicon-shopping-cart-9',
					'xt_woofcicon-shopping-cart-of-checkered-design' => 'xt_woofcicon-shopping-cart-of-checkered-design',
					'xt_woofcicon-shopping-purse-icon' => 'xt_woofcicon-shopping-purse-icon',
					'xt_woofcicon-store' => 'xt_woofcicon-store',
					'xt_woofcicon-supermarket-basket' => 'xt_woofcicon-supermarket-basket',
					'xt_woofcicon-tool' => 'xt_woofcicon-tool',
					'xt_woofcicon-tool-1' => 'xt_woofcicon-tool-1',
					'xt_woofcicon-tool-2' => 'xt_woofcicon-tool-2',
					'xt_woofcicon-tool-3' => 'xt_woofcicon-tool-3',
				),
				'close' => array(
					'xt_woofcicon-delete' => 'xt_woofcicon-delete',
					'xt_woofcicon-delete-1' => 'xt_woofcicon-delete-1',
					'xt_woofcicon-delete-2' => 'xt_woofcicon-delete-2',
					'xt_woofcicon-delete-3' => 'xt_woofcicon-delete-3',					
					'xt_woofcicon-cross' => 'xt_woofcicon-cross',
					'xt_woofcicon-cross-1' => 'xt_woofcicon-cross-1',
					'xt_woofcicon-close' => 'xt_woofcicon-close',
					'xt_woofcicon-close-1' => 'xt_woofcicon-close-1',
					'xt_woofcicon-close-2' => 'xt_woofcicon-close-2',
					'xt_woofcicon-close-3' => 'xt_woofcicon-close-3',
					'xt_woofcicon-close-4' => 'xt_woofcicon-close-4',
					'xt_woofcicon-close-5' => 'xt_woofcicon-close-5',
					'xt_woofcicon-close-7' => 'xt_woofcicon-close-7',
					'xt_woofcicon-circle' => 'xt_woofcicon-circle',
					'xt_woofcicon-close-6' => 'xt_woofcicon-close-6',
					'xt_woofcicon-close-8' => 'xt_woofcicon-close-8',
					'xt_woofcicon-close-9' => 'xt_woofcicon-close-9',
					'xt_woofcicon-arrow' => 'xt_woofcicon-arrow',
					'xt_woofcicon-arrows' => 'xt_woofcicon-arrows',
					'xt_woofcicon-arrows-1' => 'xt_woofcicon-arrows-1',
					'xt_woofcicon-arrows-10' => 'xt_woofcicon-arrows-10',
					'xt_woofcicon-arrows-11' => 'xt_woofcicon-arrows-11',
					'xt_woofcicon-arrows-2' => 'xt_woofcicon-arrows-2',
					'xt_woofcicon-arrows-3' => 'xt_woofcicon-arrows-3',
					'xt_woofcicon-arrows-4' => 'xt_woofcicon-arrows-4',
					'xt_woofcicon-arrows-5' => 'xt_woofcicon-arrows-5',
					'xt_woofcicon-arrows-6' => 'xt_woofcicon-arrows-6',
					'xt_woofcicon-arrows-7' => 'xt_woofcicon-arrows-7',
					'xt_woofcicon-arrows-8' => 'xt_woofcicon-arrows-8',
					'xt_woofcicon-arrows-9' => 'xt_woofcicon-arrows-9'			
				),
				'plus' => array(
					'xt_woofcicon-add-1' => 'xt_woofcicon-add-1',
					'xt_woofcicon-add' => 'xt_woofcicon-add',
					'xt_woofcicon-plus' => 'xt_woofcicon-plus',
					'xt_woofcicon-plus-1' => 'xt_woofcicon-plus-1',
					'flaticon-xt_woofcicon-flat-plus' => 'flaticon-xt_woofcicon-flat-plus',				
				),
				'minus' => array(
					'xt_woofcicon-substract' => 'xt_woofcicon-substract',
					'xt_woofcicon-substract-1' => 'xt_woofcicon-substract-1',
					'xt_woofcicon-minus' => 'xt_woofcicon-minus',
					'xt_woofcicon-minus-1' => 'xt_woofcicon-minus-1',	
					'flaticon-xt_woofcicon-flat-minus' => 'flaticon-xt_woofcicon-flat-minus',				
				)
			);	
			
			if(!empty($type) && !empty($icons[$type])) {
				return $icons[$type];	
			}	
			
			return $icons;
		}
		
	}
}
