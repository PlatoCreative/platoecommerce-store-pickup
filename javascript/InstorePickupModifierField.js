// Instore Pickup Checkout JS

jQuery.noConflict();
jQuery(document).ready(function($){
	
	// CHANGED TO ALLOW FOR STORE PICK UP DROPDOWN
	// TODO: Must be a better way of extending this
	$.entwine('sws', function($){
		$('.order-form').entwine({
			updateCart: function(){
				if($('select.weightbasedshippingmodifierfield_multiple').val() == instorePickup){		
					var self = this;
					var values = this.serialize();
					
					$.ajax({
						url: window.location.pathname + '/OrderForm/update',
						type: 'POST',
						data: values,
						beforeSend: function() {
							$('#cart-loading-js').show();
							$('#checkout-order-table').addClass('loading-currently');
						},
						success: function(data){
							$('#checkout-order-table').replaceWith(data);
							
							// Get and show the instore location
							$.ajax({
								url: window.location.pathname + '/StoreLocations',
								type: 'POST',
								data: values,
								success: function(data){
									$('#shippingInfoArea').html(data);
								}
							});
						},
						complete: function() {
							$('#cart-loading-js').hide();
							$('#checkout-order-table').removeClass('loading-currently');
						}
					});
				} else {
					var self = this;
					var values = this.serialize();
					
					$.ajax({
						url: window.location.pathname + '/OrderForm/update',
						type: 'POST',
						data: values,
						beforeSend: function() {
							$('#cart-loading-js').show();
							$('#checkout-order-table').addClass('loading-currently');
						},
						success: function(data){
							$('#checkout-order-table').replaceWith(data);
						},
						complete: function() {
							$('#cart-loading-js').hide();
							$('#checkout-order-table').removeClass('loading-currently');
						}
					});	
				}
			}
		});
	});
});