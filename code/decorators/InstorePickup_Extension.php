<?php

/**
 * So that {@link InstorePickup}s can be created in {@link SiteConfig}.
 */
class InstorePickup_Extension extends DataExtension {
	private static $has_many = array(
		'InstorePickups' => 'InstorePickup'
	);
}
