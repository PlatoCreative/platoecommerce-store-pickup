<?php

class InstorePickup_Region_Shipping extends DataExtension {
	private static $db = array(
	);
	
	private static $has_many = array(
		'InstorePickups' => 'InstorePickup'
	);
}