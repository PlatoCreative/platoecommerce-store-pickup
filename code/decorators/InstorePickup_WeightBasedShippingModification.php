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
				$store = InstorePickup::get()->filter(array('ID' => $value, 'Published' => 1))->first();

				$mod->StoreTitle = ($store && $store->exists()) ? $store->Title : null;
				$mod->StoreAddress = ($store && $store->exists()) ? $store->Address : null;
				$mod->StorePhone = ($store && $store->exists()) ? $store->Phone : null;
			}
		}

		return $mod;
	}

	public function updateWeightShippingRates($rates, $regionCode){
		$shopConfig = ShopConfig::current_shop_config();
		$instoreRate = false;

		if($regionCode){
			$region = Region_Shipping::get()->filter('Code', $regionCode)->first();
			if($region && $region->exists()){
				$instorePickups = InstorePickup::get()->filter(array('RegionID' => $region->ID, 'Published' => 1));
				if($instorePickups->count() > 0){
					$instoreRate = InstorePickupShippingRate::get()->filter(array('ShopConfigID' => $shopConfig->ID))->first();
				}
			}
		}

		if($instoreRate){
			$instoreRate->Label = $instoreRate->Label();
			$rates->push($instoreRate);
		}

		return $rates;
	}
}
