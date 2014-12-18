<?php

class InstorePickup_WeightBasedShippingProvider extends WeightBasedShippingProvider {
	private static $db = array(
		'InstorePickup' => 'Boolean'
	);
	
	public function canEdit($member = null){
        if($this->InstorePickup){
			return false;	
		} else {
        	return Permission::check('EDIT_WEIGHTBASEDSHIPPINGPROVIDER');
		}
    }

    public function canDelete($member = null){
		if($this->InstorePickup){
			return false;	
		} else {
        	return Permission::check('EDIT_WEIGHTBASEDSHIPPINGPROVIDER');
		}
    }
}