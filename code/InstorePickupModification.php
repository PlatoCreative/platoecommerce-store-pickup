<?php

class InstorePickupModification extends Modification {
	private static $has_one = array(
		'InstorePickup' => 'InstorePickup'
	);

	private static $defaults = array(
		'SubTotalModifier' => false,
		'SortOrder' => 50
	);

	private static $default_sort = 'SortOrder ASC';

	public function add($order, $value = null) {

		$this->OrderID = $order->ID;
		$country = Country_Shipping::get()
				->filter("Code",$order->ShippingCountryCode)
				->first();

        //die($order->ShippingCountryCode);




		$rates = $this->getInstorePickups($country);
		if ($rates && $rates->exists()) {
			//Pick the rate
			$rate = $rates->find('ID', $value);

			if (!$rate || !$rate->exists()) {
				$rate = $rates->first();
			}

			//Generate the Modification now that we have picked the correct rate
			$mod = new InstorePickupModification();
			$mod->Price = $rate->Amount()->getAmount();
			$mod->Description = $rate->Description;
			$mod->OrderID = $order->ID;
			$mod->Value = $rate->ID;
			$mod->InstorePickupID = $rate->ID;
			$mod->write();
		}
	}

	public function getInstorePickups(Country_Shipping $country) {
		//Get valid rates for this country

		//$countryID = ($country && $country->exists()) ? $country->ID : null;
		$rates = InstorePickup::get();//->filter("CountryID", $countryID);
		$this->extend("updateInstorePickups", $rates, $country);
		return $rates;
	}

	public function getFormFields() {
		$fields = new FieldList();
		$rate = $this->InstorePickup();
		$rates = $this->getInstorePickups($rate->Country());


		if ($rates && $rates->exists()) {
			if ($rates->count() > 1) {
				$field = InstorePickupModifierField_Multiple::create(
					$this,
					'Store Location',
					$rates->map('ID', 'Label')->toArray()
				)->setValue($rate->ID);
			} else {
				$newRate = $rates->first();
				$field = InstorePickupModifierField::create(
					$this,
					$newRate->Title,
					$newRate->ID
				)->setAmount($newRate->Price());
			}

			$fields->push($field);

		}

		if (!$fields->exists()){
			Requirements::javascript('storepickup/javascript/InstorePickupModifierField.js');
		}

		return $fields;
	}
}