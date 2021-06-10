jQuery(document).ready(function () {
	var window_map;

	jQuery(document).on('click', '.flexible-shipping-collection-point-map-selector', function () {
		var service_id = jQuery(this).attr('data-service-id');
		var select_id = jQuery(this).attr('data-select-id');
		var params = {
			service_id: service_id,
			select_id: select_id,
		};

		var y = window.top.outerHeight / 2 + window.top.screenY - (600 / 2);
		var x = window.top.outerWidth / 2 + window.top.screenX - (800 / 2);

		window_map = window.open(fs_collection_points_map.collection_points_map_url + '&' + jQuery.param(params), "", "width=800,height=600,top=" + y + ",left=" + x);
		window.addEventListener('message', function (event) {
			if (event.origin !== window.location.origin) {
				return false;
			}

			if ('get_adresses_data' === event.data.action) {
				send_adresses_data_to_map(window_map, service_id, select_id);
			}

			if (event.data.point_id && 'select_point' === event.data.action) {
				update_point_from_map(event.data.point_id, select_id);
			}
		}, true);
		return false;
	});

	function update_point_from_map(point_id, select_id) {
		var point_select = jQuery('#' + select_id);
		point_select.val(point_id);
		point_select.trigger('change.select2');
		jQuery(document.body).trigger('update_checkout');
	}

	function send_adresses_data_to_map(window_map, service_id, select_field_id) {
		var data = {
			different_addres: jQuery('#ship-to-different-address-checkbox').prop('checked'),
			shipping_country: jQuery('#shipping_country').val(),
			shipping_address_1: jQuery('#shipping_address_1').val(),
			shipping_address_2: jQuery('#shipping_address_2').val(),
			shipping_postcode: jQuery('#shipping_postcode').val(),
			shipping_city: jQuery('#shipping_city').val(),
			billing_country: jQuery('#billing_country').val(),
			billing_address_1: jQuery('#billing_address_1').val(),
			billing_address_2: jQuery('#billing_address_2').val(),
			billing_postcode: jQuery('#billing_postcode').val(),
			billing_city: jQuery('#billing_city').val(),
			selected_point: jQuery('#' + select_field_id).val(),
			action: 'fs_collection_points_' + service_id,
			security: fs_collection_points_map.ajax_nonce
		};
		window_map.postMessage({addresses_data: data, action: 'send_address_data'}, window.location.origin);
	}
})
