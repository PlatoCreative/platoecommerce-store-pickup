<?php

class InstorePickupCheckoutPage extends DataExtension {	
	public function onAfterInit(){
		Requirements::javascript('storepickup/js/InstorePickupModifierField.js');
	}
	
	private static $allowed_actions = array (
		'StorePickupFields'
	);
	
	public function StorePickupFields($request){
		if(Director::is_ajax()){
			$storePickup = InstorePickup::get_by_id($request->ID);
			
			if($storePickup){
				$mapcodes = $storePickup->LatLong();
				
				$fields = '';
				
				if($mapcodes){
					$fields .= '
						<script src="https://maps.googleapis.com/maps/api/js?v=3.exp&sensor=false"></script>
						<script>
							var map,
								MY_MAPTYPE_ID = "custom_style";
							function initialize() {
								var featureOpts = [
								{
									stylers: [
										{ saturation : 0 },
										{ gamma: 0 }
									]
								},
								{
									elementType: "labels",
									stylers: [
										{ visibility: "on"}
									]
								}
								];
								var mapOptions = {
									zoom: 15,
									scrollwheel: false,
									center: new google.maps.LatLng(' . $mapcodes->Latitude . ',' . $mapcodes->Longitude . '),
									mapTypeControlOptions: {
										mapTypeIds: [google.maps.MapTypeId.ROADMAP, MY_MAPTYPE_ID]
									},
									mapTypeId: MY_MAPTYPE_ID
								};
						
								map = new google.maps.Map(document.getElementById("map"), mapOptions);
								var styledMapOptions = { name: "Custom" };
								var customMapType = new google.maps.StyledMapType(featureOpts, styledMapOptions);
								map.mapTypes.set(MY_MAPTYPE_ID, customMapType);
						
								var marker = new google.maps.Marker({
									position: new google.maps.LatLng(' . $mapcodes->Latitude . ',' . $mapcodes->Longitude . '), 
									map: map,
									title: "' . $storePickup->Title . '"
								});   
							}
							google.maps.event.addDomListener(window, "load", initialize);
						</script>
					';
				}
				
				$fields .= '<div class="store-pickup-info">';
					$fields .= '<div class="store-pickup-info-left">';
						$fields .= '<p><span class="heading">' . $storePickup->Title . '</span><br />' . $storePickup->goodAddress() . '</p>';
					$fields .= '</div>';
					if($$mapcodes){
						$fields .= '<div class="store-pickup-info-right">';
							$fields .= '<div id="map"></div>';
						$fields .= '</div>';
					}
				$fields .= '</div>';
				
				return $fields;
			}
		}
		return false;
	}
}