<?php
/**
 *	Weightbased shipping rate
 */
class InstorePickupShippingRate extends WeightBasedShippingRate {
	private static $db = array(
	);

	private static $has_one = array(
	);

	private static $has_many = array(
		'InstorePickups' => 'InstorePickup'
	);

	public function canDelete($member = null){
		return false;
	}

	public function canEdit($member = null){
		return false;
	}
}