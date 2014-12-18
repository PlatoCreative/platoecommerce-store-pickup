<?php
/*
*	InstorePickup_WeightBasedShippingModification extends WeightBasedShippingModification
*/
class InstorePickup_WeightBasedShippingModification extends DataExtension {
		
	public function updateWeightShippingRates($rates, $regionCode){
		$shopConfig = ShopConfig::current_shop_config();
		
		if($regionCode){
			$instorePickups = InstorePickup::get()->filter(array('RegionID' => $regionCode));
			if($instorePickups){
				$instoreRate = InstorePickupShippingRate::get()->filter(array('ShopConfigID' => $shopConfig->ID))->first();
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
	
	public function updateWeightShippingRatesForm($fields, $rate){
		$instorePickup = InstorePickup::get()->filter(array('CountryID' => $rate->CountryID));

		if(count($instorePickup)){
			$instoreField = HiddenField::create('hasInstorePickup', '', true);
			$fields->push($instoreField);
		}
		
		return $fields;
	}
}