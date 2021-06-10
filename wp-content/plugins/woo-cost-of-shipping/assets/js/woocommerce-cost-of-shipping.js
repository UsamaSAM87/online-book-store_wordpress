/**
 * WooCommerce Cost of Shipping
 * https://www.theritesites.com/plugins/woocomerce-cost-of-shipping
 *
 * This plugin can be expanded into net profit reporting by getting the WooCommerce Net Profit plugin
 * developed by TheRiteSites and found at https://www.theritesites.com/plugins/woocommerce-net-profit
 * 
 * Licensed under the GPLv2+ license.
 */

window.WCCOS = window.WCCOS || {};

( function( window, document, $, plugin ) {
	var $c = {};

	plugin.init = function() {
		plugin.cache();
		plugin.bindEvents();
	};

	plugin.cache = function() {
		$c.window = $( window );
		$c.body = $( document.body );
	};

	plugin.bindEvents = function() {
	};

	$( plugin.init );
	
	
}( window, document, jQuery, window.WCCOS ) );

jQuery(document).ready(function( $ ) {
    'use strict';
    
    $('#edit-shipping-cost').on('click', function(e) {
		e.preventDefault();
        $('.edit-shipping-tooltip').toggle();
        $('#shipping-cost-input').focus();
    });
    
    $('#edit-shipping-cancel').on('click', function(e) {
		e.preventDefault();
        $('.edit-shipping-tooltip').toggle();
    });
	
	$('#edit-shipping-save').on('click', function(e) {
		e.preventDefault();

		var data = {
			action: 'set_shipping_cost',
			security: WCCOSApi.nonce,
			post_id: $('#edit-shipping-cost').data('orderid'),
			shipping_cost: $('#shipping-cost-input').val()
		};
		
		$.post( ajaxurl, data, function(response)  {
			if( WCCOSApi.debug )
				console.log("Shipping cost sent!");
		})
		.done(function() {
			
		})
		.fail(function() {
			console.log("Failed to save the shipping cost from manual input.");
		})
		.success(function() {
			if( WCCOSApi.debug )
				console.log("success");
			var new_cos = parseFloat($('#shipping-cost-input').val()).toFixed(2);
			$('#shipping-cost .amount').html(
				'<span class="woocommerce-Price-currencySymbol">' + WCCOSApi.currency + '</span>' + new_cos
			);
		})
		.always(function() {
			
		});
		
		$('.edit-shipping-tooltip').toggle();
	});

	$('.wc-cos-wcs-import').on('click', function(e){
		e.preventDefault();
		console.log();
		var data = {
			action: 'set_label_cost_wcs',
			security: WCCOSApi.nonce,
			post_id: $(this).data('orderid'),
		};

		$('.wc-cos-wcs-import').prop('disabled', true);
		$(this).html('Loading...');

		$.post( ajaxurl, data, function(response)  {
			if( WCCOSApi.debug )
				console.log("Shipping cost being imported!");
		})
		.done(function() {
			$('#wc-cos-protip').remove();
			$('.wc-cos-wcs-import').attr('disabled', false);
			$('.wc-cos-wcs-import').after('<div id="wc-cos-protip"><p><b>Pro tip:</b> If you purchase your label(s) and then set the order status to complete, the cost of shipping will automatically be imported!</p></div>');
		})
		.fail(function() {
			console.log("failed to import shipping cost from WooCommerce services created label.");
			$('.wc-cos-wcs-import').html('Retry import');
		})
		.success(function(response) {
			if( WCCOSApi.debug )
				console.log("Success");
			$('.wc-cos-wcs-import').html('Re-import');
			$('.shipping-total .woocommerce-Price-amount').html(
				'<span class="woocommerce-Price-currencySymbol">' + response['data']['currency'] + '</span>' + (parseFloat(response['data']['cost'])).toFixed(2)
			);
		});
	});
    
});
