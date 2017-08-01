<?php
/**
 *	InstorePickup's that can be set in {@link SiteConfig}.
 */
class InstorePickup extends DataObject {
	private static $db = array(
		'Title' => 'Varchar(250)',
		'Address' => 'HTMLText',
		'GoogleMap' => 'Text',
		'Price' => 'Decimal(19,4)',
		'Phone' => 'Varchar(50)',
		'Fax' => 'Varchar(50)',
		'Published' => 'Boolean'
	);

	private static $has_one = array(
		'ShopConfig' => 'ShopConfig',
		'Country' => 'Country_Shipping',
		'InstorePickupShippingRate' => 'InstorePickupShippingRate',
		'Region' => 'Region_Shipping'
	);

	private static $summary_fields = array(
		'Title' => 'Title',
		'Address' => 'Address',
		'SummaryOfPrice' => 'Amount',
		'Region.Title' => 'Region',
		'Published.Nice' => 'Enabled'
	);

	public function getCMSFields() {
		$shopConfig = ShopConfig::current_shop_config();

		$fields = FieldList::create(
			TabSet::create('Root',
				Tab::create('Main',
					TextField::create('Title', _t('FlatFeeShippingRate.TITLE', 'Title')),
					CheckboxField::create('Published', 'Is this store available for orders?'),
					TextareaField::create('Address', _t('FlatFeeShippingRate.DESCRIPTION', 'Address')),
					TextField::create('Phone', _t('FlatFeeShippingRate.PHONE', 'Phone')),
					TextField::create('Fax', _t('FlatFeeShippingRate.FAX', 'Fax number')),
					TextField::create('GoogleMap', _t('FlatFeeShippingRate.DESCRIPTION', 'Google Map Link')),
					LiteralField::create ('Instructions', '
						<div class="field">
							<ol id="mapinstructions" class="middleColumn">
								<li>Go to <a href="http://maps.google.co.nz" target="_blank">Google Maps</a></li>
								<li>Search for your address & ensure it is correct</li>
								<li>Click the &ldquo;Link&rdquo; button (to the top right in the white panel)</li>
								<li>Copy the <em>first</em> link (ctrl+c or right-click -> copy)</li>
								<li>Paste the link in the textbox above</li>
							</ol>
						</div>
					'),
					//DropdownField::create('CountryID', _t('FlatFeeShippingRate.COUNTRY', 'Country'), Country_Shipping::get()->filter(array('ShopConfigID' => $shopConfig->ID))->map()),
					DropdownField::create('RegionID', _t('FlatFeeShippingRate.REGION', 'Region'), Region_Shipping::get()->filter(array('ShopConfigID' => $shopConfig->ID))->map('ID', 'Title')->toArray())
					//PriceField::create('Price')
				)
			)
		);

		$this->extend('updateCMSFields', $fields);

		return $fields;
	}

	public function onBeforeWrite(){
		parent::onBeforeWrite();
		$shippingRate = InstorePickupShippingRate::get()->first();
		$shopConfig = ShopConfig::current_shop_config();
		if(!$shippingRate){
			// Create a new shipping rate for instore pickup
			$shippingRate = new InstorePickupShippingRate();
			$shippingRate->Price = 0;

			// Create new provider
			$provider = new InstorePickup_WeightBasedShippingProvider();
			$provider->Name = 'Instore Pickup';
			$provider->ShopConfigID = $shopConfig->ID;
			$provider->InstorePickup = 1;
			$provider->write();
			$shippingRate->ProviderID = $provider->ID;

			$shippingRate->ShopConfigID = $shopConfig->ID;
			$shippingRate->write();
		}

		$this->ShopConfigID = $shopConfig->ID;
		$this->InstorePickupShippingRateID = $shippingRate->ID;
	}

	// Return address with line breaks
	public function goodAddress(){
		return nl2br($this->Address);
	}

	// For the goole map
	public function LatLong(){
		$ll = preg_match('/\@([-.0-9]+),([-.0-9]+)/', $this->GoogleMap, $matches);
		if(!$ll){
			$ll = preg_match('/\@([-.0-9]+),([-.0-9]+)/', $this->GoogleMap, $matches);
		}
		return $ll ? ArrayData::create(array('Latitude' => $matches[1], 'Longitude' => $matches[2], 'Nice' => $matches[1] . ',' . $matches[2])) : null;
	}

	/**
	 * Label for using on {@link InstorePickupModifierField}s.
	 */
	public function Label() {
		return $this->Title . ' - ' . $this->Price()->Nice();
	}

	public function SummaryOfPrice() {
		return $this->Amount()->Nice();
	}

	public function Amount() {
		// TODO: Multi currency
		$shopConfig = ShopConfig::current_shop_config();
		$amount = new Price();
		$amount->setAmount($this->Price);
		$amount->setCurrency($shopConfig->BaseCurrency);
		$amount->setSymbol($shopConfig->BaseCurrencySymbol);
		return $amount;
	}

	public function Price() {
		$amount = $this->Amount();
		$this->extend('updatePrice', $amount);
		return $amount;
	}
}
