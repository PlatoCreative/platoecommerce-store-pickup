// Instore Pickup Checkout JS

jQuery.noConflict();
jQuery(document).ready(function($){
	
	$('.order-form').on('change', 'select.country-code, .modifier-set-field select', function(e){
		$('.order-form').entwine('sws').updateCart();
	});
	
});