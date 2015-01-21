<?php
/*
*	InstorePickup_WeightBasedShippingModification extends WeightBasedShippingModification
*/
class InstorePickup_WeightBasedShippingModification extends DataExtension {
	
	private static $db = array(
		'StoreTitle' => 'Varchar(250)',
		'StoreAddress' => 'HTMLText',
		'StorePhone' => 'Varchar(50)'
	);
	
	public function updateWeightShippingAdd($rate, $mod, $data){
		if($rate->ClassName == 'InstorePickupShippingRate'){
			$value = isset($data['StoreSelector']) ? $data['StoreSelector'] : null;
			
			if($value && $value != ''){
				$store = InstorePickup::get()->filter(array('ID' => $value))->first();
				
				$mod->StoreTitle = ($store && $store->exists()) ? $store->Title : null;
				$mod->StoreAddress = ($store && $store->exists()) ? $store->Address : null;
				$mod->StorePhone = ($store && $store->exists()) ? $store->Phone : null;
			}
		}
		
		return $mod;	
	}
	
	public function updateWeightShippingRates($rates, $regionCode){
		$shopConfig = ShopConfig::current_shop_config();
		$instoreRate = null;
		if($regionCode){
			$region = Region_Shipping::get()->filter('Code', $regionCode)->first();
			if($region && $region->exists()){
				$instorePickups = InstorePickup::get()->filter(array('RegionID' => $region->ID));
				if($instorePickups){
					$instoreRate = InstorePickupShippingRate::get()->filter(array('ShopConfigID' => $shopConfig->ID))->first();
				}
			}
		} else {
			$instoreRate = InstorePickupShippingRate::get()->filter(array('ShopConfigID' => $shopConfig->ID))->first();			
		}
		
		if($instoreRate){
			$instoreRate->Label = $instoreRate->Label();
			$rates->push($instoreRate);	
		}
		
		return $rates;
	}
	
	/*
	public function updateWeightShippingRatesForm($fields, $rate){
		$shopConfig = ShopConfig::current_shop_config();
		//$instorePickup = InstorePickup::get()->filter(array('CountryID' => $rate->CountryID));
		$instoreRate = InstorePickupShippingRate::get()->filter(array('ShopConfigID' => $shopConfig->ID))->first();
		$shippingField = $fields->fieldByName('Modifiers[' . $this->owner . ']');
		
		if($instoreRate->exists()){
			//$instoreField = HiddenField::create('instorePickup', '', $instoreRate->ID);
			//$fields->push($instoreField);
		}
		
		return $fields;
	}
	*/
}