<?php
/*
*	InstorePickupCheckoutPage_Controller extends CheckoutPage_Controller
*/
class InstorePickupCheckoutPage_Controller extends DataExtension {	
	public function onAfterInit(){
		$shopConfig = ShopConfig::current_shop_config();
		$instoreRate = InstorePickupShippingRate::get()->filter(array('ShopConfigID' => $shopConfig->ID))->first();

		if($instoreRate->exists()){
			Requirements::CSS('storepickup/css/layout.css');
			Requirements::customScript("var instorePickup = " . $instoreRate->ID . ";");
			Requirements::javascript('storepickup/javascript/InstorePickupModifierField.js');
			Requirements::javascript('https://maps.googleapis.com/maps/api/js?v=3.exp&sensor=false');
		}
	}
	
	private static $allowed_actions = array (
		'StoreLocations',
		'StorePickupFields'
	);
	
	public function StoreLocations($request){
		$shopConfig = ShopConfig::current_shop_config();
		if(Director::is_ajax()){
			$regionCode = Session::get('ShippingAddressID') ? DataObject::get_by_id('Address_Shipping', Session::get('ShippingAddressID'))->RegionCode : null;
			$region = $regionCode ? Region_Shipping::get()->filter('Code', $regionCode)->first() : null;
			
			if($region && $region->exists()){
				$stores = InstorePickup::get()->filter(array('ShopConfigID' => $shopConfig->ID, 'RegionID' => $region->ID));
			} else {
				$stores = InstorePickup::get()->filter(array('ShopConfigID' => $shopConfig->ID));				
			}
			
			$currentStore = $request->requestVar('StoreSelector') ? $request->requestVar('StoreSelector') : '';
			
			$fields = CompositeField::create(
				DropdownField::create('StoreSelector', '', $stores->map())->setEmptyString('Select a store')->setValue($currentStore),
				LiteralField::create('StoreSelectedInfo', $this->StorePickupFields($currentStore))
			)->setName('StoreSelectorFields');
			
			return $fields->FieldHolder();
		}
	}
	
	public function StorePickupFields($storeID = 0){
		//if(Director::is_ajax()){
		if($storeID != 0){
			$storePickup = InstorePickup::get()->filter(array('ID' => $storeID))->first();
			
			if($storePickup){
				$mapcodes = $storePickup->LatLong();
				$fields = '';
				
				if($mapcodes){
					$fields .= '
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
											{visibility: "on"}
										]
									}
								];
								var mapOptions = {
									zoom: 14,
									scrollwheel: true,
									center: new google.maps.LatLng(' . $mapcodes->Latitude . ',' . $mapcodes->Longitude . '),
									mapTypeControlOptions: {
										mapTypeIds: [google.maps.MapTypeId.ROADMAP, MY_MAPTYPE_ID]
									},
									mapTypeId: MY_MAPTYPE_ID
								};
						
								map = new google.maps.Map(document.getElementById("storemap"), mapOptions);
								var styledMapOptions = { name: "Custom" },
									customMapType = new google.maps.StyledMapType(featureOpts, styledMapOptions);
								map.mapTypes.set(MY_MAPTYPE_ID, customMapType);
						
								var marker = new google.maps.Marker({
									position: new google.maps.LatLng(' . $mapcodes->Latitude . ',' . $mapcodes->Longitude . '), 
									map: map,
									title: "' . $storePickup->Title . '"
								});   
							}
							initialize();
						</script>
					';
				}
				
				$fields .= '<span class="store-pickup-info">';
					$fields .= '<span class="store-pickup-info-left">';
						$fields .= '<p><span class="heading">' . $storePickup->Title . '</span><br />' . $storePickup->goodAddress() . '</p>';
					$fields .= '</span>';
					if($mapcodes){
						$fields .= '<span class="store-pickup-info-right">';
							$fields .= '<span id="storemap"></span>';
						$fields .= '</span>';
					}
				$fields .= '</span>';
				
				return $fields;
			}
		}
		return null;
	}
}