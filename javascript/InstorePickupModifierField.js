// Instore Pickup Checkout JS

jQuery.noConflict();
jQuery(document).ready(function($){
	
	var storeFormData;
	
	// Set here to avoid the form clearing it out
	function setStoreFormData(){
		storeFormData = $('#OrderForm_OrderForm').serialize();	
	}	
	setStoreFormData();
	
	function showStoresAvailable(){
		if($('select.weightbasedshippingmodifierfield_multiple').val() == instorePickup){
			// Get and show the instore locations
			$.ajax({
				url: window.location.pathname + '/StoreLocations',
				type: 'POST',
				data: storeFormData,
				success: function(data){
					$('#shippingInfoArea').html(data);
				}
			});
		}
	}

	$('#shippingInfoArea').entwine({
		onmatch : function() {
			showStoresAvailable();
		}
	});
	
	$('#StoreSelector').entwine({
		onchange : function(){
			setStoreFormData()
		}
	});
});