/**
 * Conditional Logic for Access Point
 *
 * @param field_name
 * @param shipment_id
 * @param conditions
 * @constructor
 */
function ApConditionalLogic(field_name, shipment_id, conditions) {
	this.field_name = field_name;
	this.shipment_id = shipment_id;
	this.conditions = conditions;

	this.field_id = '#' + this.field_name + '_' + this.shipment_id;
	
	this.field_id = this.createFieldId( this.field_name, this.shipment_id );

	let conditional_logic = this;
	this.conditions.forEach(function(value){
		jQuery( conditional_logic.createFieldId( value.field, shipment_id ) ).on('change', function(event) {
			conditional_logic.doLogic(event)
		});
	});
	jQuery(document).ready(function(event) { conditional_logic.doLogic(event) });
}

/**
 * Create field id from field name and shipment id;
 *
 * @param field_name
 * @param shipment_id
 * @return {string}
 */
ApConditionalLogic.prototype.createFieldId = function( field_name, shipment_id ) {
	return '#' + field_name + '_' + shipment_id;
};

/**
 * Do logic.
 *
 * @param event
 */
ApConditionalLogic.prototype.doLogic = function(event) {
	let show_field = true;
	let field_id = this.field_id;
	let shipment_id = this.shipment_id;
	let ap_conditional_logic = this;
	this.conditions.forEach(function(condition) {
		let cond_value = false;
		if ( condition.cond === 'in' && condition.values.indexOf( jQuery( ap_conditional_logic.createFieldId( condition.field, shipment_id ) ).val() ) !== -1 ) {
			cond_value = true;
		}
		show_field = show_field && cond_value;
	});
	var field_div = jQuery(field_id).closest('div');
	if ( show_field ) {
		field_div.show();
	} else {
		field_div.hide();
	}
};
