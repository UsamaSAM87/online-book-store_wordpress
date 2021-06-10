<?php

function xt_woo_floating_cart_template(
    $slug,
    $vars = array(),
    $return = false,
    $locateOnly = false
)
{
    $plugin = xt_woo_floating_cart();
    $plugin_path = $plugin->plugin_path( 'public' );
    $template_path = $plugin->template_path();
    $debug_mode = defined( 'XT_WOOFC_TEMPLATE_DEBUG_MODE' ) && XT_WOOFC_TEMPLATE_DEBUG_MODE;
    $template = '';
    // Look in yourtheme/woo-floating-cart/slug.php
    if ( empty($template) && !$debug_mode ) {
        $template = locate_template( array( $template_path . "{$slug}.php" ) );
    }
    // Get default slug.php
    if ( empty($template) && file_exists( $plugin_path . "templates/{$slug}.php" ) ) {
        $template = $plugin_path . "templates/{$slug}.php";
    }
    // Allow 3rd party plugins to filter template file from their plugin.
    $template = apply_filters( 'xt_woo_floating_cart_template', $template, $slug );
    if ( $locateOnly ) {
        return $template;
    }
    
    if ( $template ) {
        extract( $vars );
        
        if ( !$return ) {
            require $template;
        } else {
            ob_start();
            require $template;
            return ob_get_clean();
        }
    
    }

}

function xt_woofc_class()
{
    $classes = array( 'xt_woofc' );
    $position = xt_woofc_option( 'position', 'bottom-right' );
    $tablet_position = xt_woofc_option( 'position_tablet', 'bottom-right' );
    $mobile_position = xt_woofc_option( 'position_mobile', 'bottom-right' );
    $counter_position = xt_woofc_option( 'counter_position', 'top-left' );
    $counter_tablet_position = xt_woofc_option( 'counter_position_tablet', 'top-left' );
    $counter_mobile_position = xt_woofc_option( 'counter_position_mobile', 'top-left' );
    $keep_visible_on_empty = xt_woofc_option( 'visible_on_empty', false );
    $visibility = xt_woofc_option( 'visibility', 'show-on-all' );
    $hide_thumbs = xt_woofc_option( 'cart_product_hide_thumb', false );
    $enable_coupon_form = xt_woofc_option( 'enable_coupon_form', false );
    $enable_totals = xt_woofc_option( 'enable_totals', false );
    if ( !empty($position) ) {
        $classes[] = 'xt_woofc-pos-' . $position;
    }
    if ( !empty($tablet_position) ) {
        $classes[] = 'xt_woofc-tablet-pos-' . $tablet_position;
    }
    if ( !empty($mobile_position) ) {
        $classes[] = 'xt_woofc-mobile-pos-' . $mobile_position;
    }
    if ( !empty($counter_position) ) {
        $classes[] = 'xt_woofc-counter-pos-' . $counter_position;
    }
    if ( !empty($counter_tablet_position) ) {
        $classes[] = 'xt_woofc-counter-tablet-pos-' . $counter_tablet_position;
    }
    if ( !empty($counter_mobile_position) ) {
        $classes[] = 'xt_woofc-counter-mobile-pos-' . $counter_mobile_position;
    }
    if ( !empty($keep_visible_on_empty) ) {
        $classes[] = 'xt_woofc-force-visible';
    }
    if ( !empty($visibility) ) {
        $classes[] = 'xt_woofc-' . $visibility;
    }
    if ( !empty($hide_thumbs) ) {
        $classes[] = 'xt_woofc-' . $hide_thumbs;
    }
    if ( !empty($enable_coupon_form) ) {
        $classes[] = 'xt_woofc-enable-coupon';
    }
    if ( !empty($enable_totals) ) {
        $classes[] = 'xt_woofc-enable-totals';
    }
    if ( WC()->cart->is_empty() ) {
        $classes[] = 'xt_woofc-empty';
    }
    $classes = apply_filters( 'xt_woofc_container_class', $classes );
    echo  implode( ' ', $classes ) ;
}

function xt_woofc_attributes()
{
    $attributes = array(
        'data-ajax-init'        => xt_woofc_option_bool( 'ajax_init', false ),
        'data-express-checkout' => xt_woofc_option_bool( 'cart_checkout_form', false ),
        'data-position'         => xt_woofc_option( 'position', 'bottom-right' ),
        'data-tablet-position'  => xt_woofc_option( 'position_tablet', 'bottom-right' ),
        'data-mobile-position'  => xt_woofc_option( 'position_mobile', 'bottom-right' ),
        'data-triggerevent'     => xt_woofc_option( 'trigger_event_type', 'vclick' ),
        'data-hoverdelay'       => xt_woofc_option( 'trigger_hover_delay', 0 ),
        'data-flytocart'        => xt_woofc_option_bool( 'flytocart_animation', false ),
        'data-flyduration'      => xt_woofc_option( 'flytocart_animation_duration', '650' ),
        'data-shaketrigger'     => xt_woofc_option( 'shake_trigger', 'vertical' ),
        'data-opencart-onadd'   => xt_woofc_option_bool( 'open_cart_on_product_add', false ),
        'data-loadingtimeout'   => xt_woofc_option( 'loading_timeout', 100 ),
    );
    $attributes = apply_filters( 'xt_woofc_container_attributes', $attributes );
    $data_string = '';
    foreach ( $attributes as $key => $value ) {
        $data_string .= ' ' . $key . '="' . esc_attr( $value ) . '"';
    }
    echo  $data_string ;
}

function xt_woofc_trigger_cart_icon_class()
{
    $classes = array( 'xt_woofc-trigger-cart-icon' );
    $icon_type = xt_woofc_option( 'trigger_icon_type', 'image' );
    
    if ( $icon_type == 'font' ) {
        $icon = xt_woofc_option( 'cart_trigger_icon' );
        if ( !empty($icon) ) {
            $classes[] = $icon;
        }
    }
    
    $classes = apply_filters( 'xt_woofc_trigger_cart_icon_class', $classes );
    echo  implode( ' ', $classes ) ;
}

function xt_woofc_trigger_close_icon_class()
{
    $classes = array( 'xt_woofc-trigger-close-icon' );
    $icon_type = xt_woofc_option( 'trigger_icon_type', 'image' );
    
    if ( $icon_type == 'font' ) {
        $icon = xt_woofc_option( 'cart_trigger_close_icon' );
        if ( !empty($icon) ) {
            $classes[] = $icon;
        }
    }
    
    $classes = apply_filters( 'xt_woofc_trigger_close_icon_class', $classes );
    echo  implode( ' ', $classes ) ;
}

function xt_woofc_get_spinner()
{
    
    if ( isset( $_POST['customized'] ) && is_object( $_POST['customized'] ) ) {
        $customized = $_POST['customized'];
        if ( !empty($customized->xt_woofc["loading_spinner"]) ) {
            return $customized->xt_woofc["loading_spinner"];
        }
    }
    
    return xt_woofc_option( 'loading_spinner', '7-three-bounce' );
}

function xt_woofc_spinner_html( $return = false, $wrapSpinner = true )
{
    $spinner_class = 'xt_woofc-spinner';
    $spinner_type = xt_woofc_get_spinner();
    if ( empty($spinner_type) ) {
        if ( $return ) {
            return "";
        }
    }
    $spinner = '';
    switch ( $spinner_type ) {
        case '1-rotating-plane':
            $spinner = '<div class="' . esc_attr( $spinner_class ) . ' xt_woofc-spinner-rotating-plane"></div>';
            break;
        case '2-double-bounce':
            $spinner = '
			<div class="' . esc_attr( $spinner_class ) . ' xt_woofc-spinner-double-bounce">
		        <div class="xt_woofc-spinner-child xt_woofc-spinner-double-bounce1"></div>
		        <div class="xt_woofc-spinner-child xt_woofc-spinner-double-bounce2"></div>
		    </div>';
            break;
        case '3-wave':
            $spinner = '
			<div class="' . esc_attr( $spinner_class ) . ' xt_woofc-spinner-wave">
		        <div class="xt_woofc-spinner-rect xt_woofc-spinner-rect1"></div>
		        <div class="xt_woofc-spinner-rect xt_woofc-spinner-rect2"></div>
		        <div class="xt_woofc-spinner-rect xt_woofc-spinner-rect3"></div>
		        <div class="xt_woofc-spinner-rect xt_woofc-spinner-rect4"></div>
		        <div class="xt_woofc-spinner-rect xt_woofc-spinner-rect5"></div>
		    </div>';
            break;
        case '4-wandering-cubes':
            $spinner = '
			<div class="' . esc_attr( $spinner_class ) . ' xt_woofc-spinner-wandering-cubes">
		        <div class="xt_woofc-spinner-cube xt_woofc-spinner-cube1"></div>
		        <div class="xt_woofc-spinner-cube xt_woofc-spinner-cube2"></div>
		    </div>';
            break;
        case '5-pulse':
            $spinner = '<div class="' . esc_attr( $spinner_class ) . ' xt_woofc-spinner-spinner-pulse"></div>';
            break;
        case '6-chasing-dots':
            $spinner = '
			<div class="' . esc_attr( $spinner_class ) . ' xt_woofc-spinner-chasing-dots">
		        <div class="xt_woofc-spinner-child xt_woofc-spinner-dot1"></div>
		        <div class="xt_woofc-spinner-child xt_woofc-spinner-dot2"></div>
		    </div>';
            break;
        case '7-three-bounce':
            $spinner = '
			<div class="' . esc_attr( $spinner_class ) . ' xt_woofc-spinner-three-bounce">
		        <div class="xt_woofc-spinner-child xt_woofc-spinner-bounce1"></div>
		        <div class="xt_woofc-spinner-child xt_woofc-spinner-bounce2"></div>
		        <div class="xt_woofc-spinner-child xt_woofc-spinner-bounce3"></div>
		    </div>';
            break;
        case '8-circle':
            $spinner = '
			<div class="' . esc_attr( $spinner_class ) . ' xt_woofc-spinner-circle">
		        <div class="xt_woofc-spinner-circle1 xt_woofc-spinner-child"></div>
		        <div class="xt_woofc-spinner-circle2 xt_woofc-spinner-child"></div>
		        <div class="xt_woofc-spinner-circle3 xt_woofc-spinner-child"></div>
		        <div class="xt_woofc-spinner-circle4 xt_woofc-spinner-child"></div>
		        <div class="xt_woofc-spinner-circle5 xt_woofc-spinner-child"></div>
		        <div class="xt_woofc-spinner-circle6 xt_woofc-spinner-child"></div>
		        <div class="xt_woofc-spinner-circle7 xt_woofc-spinner-child"></div>
		        <div class="xt_woofc-spinner-circle8 xt_woofc-spinner-child"></div>
		        <div class="xt_woofc-spinner-circle9 xt_woofc-spinner-child"></div>
		        <div class="xt_woofc-spinner-circle10 xt_woofc-spinner-child"></div>
		        <div class="xt_woofc-spinner-circle11 xt_woofc-spinner-child"></div>
		        <div class="xt_woofc-spinner-circle12 xt_woofc-spinner-child"></div>
		    </div>';
            break;
        case '9-cube-grid':
            $spinner = '
			<div class="' . esc_attr( $spinner_class ) . ' xt_woofc-spinner-cube-grid">
		        <div class="xt_woofc-spinner-cube xt_woofc-spinner-cube1"></div>
		        <div class="xt_woofc-spinner-cube xt_woofc-spinner-cube2"></div>
		        <div class="xt_woofc-spinner-cube xt_woofc-spinner-cube3"></div>
		        <div class="xt_woofc-spinner-cube xt_woofc-spinner-cube4"></div>
		        <div class="xt_woofc-spinner-cube xt_woofc-spinner-cube5"></div>
		        <div class="xt_woofc-spinner-cube xt_woofc-spinner-cube6"></div>
		        <div class="xt_woofc-spinner-cube xt_woofc-spinner-cube7"></div>
		        <div class="xt_woofc-spinner-cube xt_woofc-spinner-cube8"></div>
		        <div class="xt_woofc-spinner-cube xt_woofc-spinner-cube9"></div>
		    </div>';
            break;
        case '10-fading-circle':
            $spinner = '
			<div class="' . esc_attr( $spinner_class ) . ' xt_woofc-spinner-fading-circle">
		        <div class="xt_woofc-spinner-circle1 xt_woofc-spinner-circle"></div>
		        <div class="xt_woofc-spinner-circle2 xt_woofc-spinner-circle"></div>
		        <div class="xt_woofc-spinner-circle3 xt_woofc-spinner-circle"></div>
		        <div class="xt_woofc-spinner-circle4 xt_woofc-spinner-circle"></div>
		        <div class="xt_woofc-spinner-circle5 xt_woofc-spinner-circle"></div>
		        <div class="xt_woofc-spinner-circle6 xt_woofc-spinner-circle"></div>
		        <div class="xt_woofc-spinner-circle7 xt_woofc-spinner-circle"></div>
		        <div class="xt_woofc-spinner-circle8 xt_woofc-spinner-circle"></div>
		        <div class="xt_woofc-spinner-circle9 xt_woofc-spinner-circle"></div>
		        <div class="xt_woofc-spinner-circle10 xt_woofc-spinner-circle"></div>
		        <div class="xt_woofc-spinner-circle11 xt_woofc-spinner-circle"></div>
		        <div class="xt_woofc-spinner-circle12 xt_woofc-spinner-circle"></div>
		    </div>';
            break;
        case '11-folding-cube':
            $spinner = '
			<div class="' . esc_attr( $spinner_class ) . ' xt_woofc-spinner-folding-cube">
		        <div class="xt_woofc-spinner-cube1 xt_woofc-spinner-cube"></div>
		        <div class="xt_woofc-spinner-cube2 xt_woofc-spinner-cube"></div>
		        <div class="xt_woofc-spinner-cube4 xt_woofc-spinner-cube"></div>
		        <div class="xt_woofc-spinner-cube3 xt_woofc-spinner-cube"></div>
		    </div>';
            break;
        case 'loading-text':
            $spinner = '<div class="' . esc_attr( $spinner_class ) . ' xt_woofc-spinner-loading-text">' . esc_html__( 'Loading...', 'woo-floating-cart' ) . '</div>';
            break;
    }
    $spinner = '<div class="xt_woofc-spinner-inner">' . $spinner . '</div>';
    if ( $wrapSpinner ) {
        $spinner = '<div class="xt_woofc-spinner-wrap">' . $spinner . '</div>';
    }
    if ( $return ) {
        return $spinner;
    }
    echo  $spinner ;
}

function xt_woofc_can_checkout()
{
    return !(!WC()->checkout->is_registration_enabled() && WC()->checkout->is_registration_required() && !is_user_logged_in());
}

function xt_woofc_checkout_link()
{
    
    if ( xt_woofc_option_bool( 'cart_checkout_form', false ) ) {
        $link = wc_get_checkout_url();
    } else {
        $checkout_link_type = xt_woofc_option( 'cart_checkout_link', 'checkout' );
        
        if ( $checkout_link_type == 'checkout' ) {
            $link = wc_get_checkout_url();
        } else {
            $link = wc_get_cart_url();
        }
    
    }
    
    $link = apply_filters_deprecated(
        'xt_woo_floating_cart_checkout_link',
        array( $link ),
        '1.3.2',
        'xt_woofc_checkout_link'
    );
    return apply_filters( 'xt_woofc_checkout_link', $link );
}

function xt_woofc_checkout_label()
{
    
    if ( xt_woofc_option_bool( 'cart_checkout_form', false ) ) {
        
        if ( xt_woofc_can_checkout() ) {
            $label = apply_filters( 'woocommerce_order_button_text', esc_html__( 'Place order', 'woo-floating-cart' ) );
        } else {
            $label = esc_html__( 'Checkout', 'woo-floating-cart' );
        }
    
    } else {
        $checkout_link_type = xt_woofc_option( 'cart_checkout_link', 'checkout' );
        
        if ( $checkout_link_type == 'checkout' ) {
            $label = esc_html__( 'Checkout', 'woo-floating-cart' );
        } else {
            $label = esc_html__( 'Cart', 'woo-floating-cart' );
        }
    
    }
    
    $label = apply_filters_deprecated(
        'xt_woofc_lang_footer_checkout',
        array( $label ),
        '1.3.2',
        'xt_woofc_checkout_label'
    );
    return apply_filters( 'xt_woofc_checkout_label', $label );
}

function xt_woofc_checkout_processing_label()
{
    $label = esc_html__( 'Please Wait...', 'woo-floating-cart' );
    if ( xt_woofc_option_bool( 'cart_checkout_form', false ) && xt_woofc_can_checkout() ) {
        $label = apply_filters( 'woocommerce_order_button_text', esc_html__( 'Placing Order...', 'woo-floating-cart' ) );
    }
    return apply_filters( 'xt_woofc_checkout_processing_label', $label );
}

function xt_woofc_checkout_subtotal()
{
    return strip_tags( apply_filters( 'xt_woo_floating_cart_checkout_subtotal', WC()->cart->get_cart_subtotal() ) );
}

function xt_woofc_checkout_total()
{
    return strip_tags( apply_filters( 'xt_woo_floating_cart_checkout_total', WC()->cart->get_total() ) );
}

function xt_woofc_item_product( &$cart_item, $cart_item_key )
{
    return apply_filters(
        'woocommerce_cart_item_product',
        $cart_item['data'],
        $cart_item,
        $cart_item_key
    );
}

function xt_woofc_item_permalink( &$product, &$cart_item, $cart_item_key )
{
    return esc_url( apply_filters(
        'woocommerce_cart_item_permalink',
        ( $product->is_visible() ? $product->get_permalink( $cart_item ) : '' ),
        $cart_item,
        $cart_item_key
    ) );
}

function xt_woofc_item_title( &$product, &$cart_item, $cart_item_key )
{
    return apply_filters(
        'woocommerce_cart_item_name',
        $product->get_title(),
        $cart_item,
        $cart_item_key
    );
}

function xt_woofc_item_image( &$product, &$cart_item, $cart_item_key )
{
    return apply_filters(
        'woocommerce_cart_item_thumbnail',
        $product->get_image(),
        $cart_item,
        $cart_item_key
    );
}

function xt_woofc_item_price( &$product, &$cart_item, $cart_item_key )
{
    $qty = xt_woofc_item_qty( $cart_item, $cart_item_key );
    return strip_tags( apply_filters(
        'woocommerce_cart_item_subtotal',
        WC()->cart->get_product_subtotal( $product, $qty ),
        $cart_item,
        $cart_item_key
    ) );
}

function xt_woofc_item_qty( &$cart_item, $cart_item_key )
{
    return strip_tags( apply_filters(
        'woocommerce_widget_cart_item_quantity',
        $cart_item['quantity'],
        $cart_item,
        $cart_item_key
    ) );
}

function xt_woofc_get_cart_item( $car_item_key )
{
    $cart_content = WC()->cart->get_cart();
    if ( !empty($cart_content[$car_item_key]) ) {
        return $cart_content[$car_item_key];
    }
    return null;
}

function xt_woofc_item_attributes( &$cart_item )
{
    $display_type = xt_woofc_option( 'cart_product_attributes_display', 'list' );
    $hide_attribute_label = (bool) xt_woofc_option( 'cart_product_attributes_hide_label', 0 );
    $class = array( 'xt_woofc-variation' );
    $class[] = 'xt_woofc-variation-' . $display_type;
    $class = implode( ' ', $class );
    $html = '';
    $item_data = array();
    if ( $cart_item['data']->is_type( 'variation' ) && is_array( $cart_item['variation'] ) ) {
        foreach ( $cart_item['variation'] as $name => $value ) {
            if ( !is_string( $value ) ) {
                continue;
            }
            $taxonomy = wc_attribute_taxonomy_name( str_replace( 'attribute_pa_', '', urldecode( $name ) ) );
            // If this is a term slug, get the term's nice name
            
            if ( taxonomy_exists( $taxonomy ) ) {
                $term = get_term_by( 'slug', $value, $taxonomy );
                if ( !is_wp_error( $term ) && $term && $term->name ) {
                    $value = $term->name;
                }
                $label = wc_attribute_label( $taxonomy );
                // If this is a custom option slug, get the options name.
            } else {
                $value = apply_filters( 'woocommerce_variation_option_name', $value );
                $label = wc_attribute_label( str_replace( 'attribute_', '', $name ), $cart_item['data'] );
            }
            
            $item_data[] = array(
                'key'   => $label,
                'value' => $value,
            );
        }
    }
    // Filter item data to allow 3rd parties to add more to the array
    $item_data = apply_filters( 'woocommerce_get_item_data', $item_data, $cart_item );
    // Format item data ready to display
    foreach ( $item_data as $key => $data ) {
        // Set hidden to true to not display meta on cart.
        
        if ( !empty($data['hidden']) ) {
            unset( $item_data[$key] );
            continue;
        }
        
        $key = ( !empty($data['key']) ? $data['key'] : $data['name'] );
        $display = ( !empty($data['display']) ? $data['display'] : $data['value'] );
        $display = strip_tags( $display );
        $html .= '<dl class="' . esc_attr( $class ) . '">';
        
        if ( $hide_attribute_label ) {
            $html .= '	<dt>' . ucfirst( wp_kses_post( $display ) ) . '</dt>';
        } else {
            $html .= '	<dt>' . esc_html( $key ) . ':</dt><dd>' . ucfirst( wp_kses_post( $display ) ) . '</dd>';
        }
        
        $html .= '</dl>';
    }
    return apply_filters( 'xt_woo_floating_cart_attributes', $html );
}

function xt_woofc_get_echo_function( $function_name, $params )
{
    if ( !function_exists( $function_name ) ) {
        return '';
    }
    extract( $params );
    ob_start();
    $function_name();
    return ob_get_clean();
}

function xt_woofc_get_variation_data_from_variation_id( $item_id )
{
    $_product = new WC_Product_Variation( $item_id );
    $variation_data = $_product->get_variation_attributes();
    return $variation_data;
}

function xt_woofc_option( $id, $default = null )
{
    $config_id = XT_Woo_Floating_Cart_Customizer::$config_id;
    $value = XT_Woo_Floating_Cart_Customizer::get_option( $id, $default );
    
    if ( !empty($_POST['customized']) ) {
        $options = json_decode( stripslashes( sanitize_text_field( $_POST['customized'] ) ), true );
        
        if ( isset( $options[$config_id . '[' . $id . ']'] ) ) {
            $value = $options[$config_id . '[' . $id . ']'];
            if ( strpos( $options[$config_id . '[' . $id . ']'], '%22' ) !== false ) {
                $value = json_decode( urldecode( $value ), true );
            }
        }
    
    }
    
    return apply_filters(
        'xt_woofc_option',
        $value,
        $id,
        $default,
        'xt_woofc'
    );
}

function xt_woofc_option_bool( $id, $default = null )
{
    return (bool) xt_woofc_option( $id, $default );
}

function xt_woofc_update_option( $id, $value )
{
    $config_id = XT_Woo_Floating_Cart_Customizer::$config_id;
    $options = get_option( $config_id );
    $options[$id] = $value;
    update_option( $config_id, $options );
}

function xt_woofc_delete_option( $id )
{
    $config_id = XT_Woo_Floating_Cart_Customizer::$config_id;
    $options = get_option( $config_id );
    if ( isset( $options[$id] ) ) {
        unset( $options[$id] );
    }
    update_option( $config_id, $options );
}

function xt_woofc_is_action( $action )
{
    if ( !empty($_GET['xt_woofcaction']) && $_GET['xt_woofcaction'] == $action ) {
        return true;
    }
    return false;
}
