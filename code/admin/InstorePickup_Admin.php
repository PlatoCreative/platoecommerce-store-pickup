<?php

class InstorePickup_Admin extends ShopAdmin {
	private static $tree_class = 'ShopConfig';

	private static $allowed_actions = array(
		'InstorePickupSettings',
		'InstorePickupSettingsForm',
		'saveInstorePickupSettings'
	);

	private static $url_rule = 'ShopConfig/InstorePickup';
	protected static $url_priority = 110;
	private static $menu_title = 'Instore Pickup Locations';

	private static $url_handlers = array(
		'ShopConfig/InstorePickup/InstorePickupSettingsForm' => 'InstorePickupSettingsForm',
		'ShopConfig/InstorePickup' => 'InstorePickupSettings'
	);

	public function init() {
		parent::init();
		$this->modelClass = 'ShopConfig';
	}

	public function Breadcrumbs($unlinked = false) {
		$request = $this->getRequest();
		$items = parent::Breadcrumbs($unlinked);

		if($items->count() > 1){
			$items->remove($items->pop());
		}

		$items->push(new ArrayData(array(
			'Title' => 'Instore Pickup',
			'Link' => $this->Link(Controller::join_links($this->sanitiseClassName($this->modelClass), 'InstorePickup'))
		)));

		return $items;
	}

	public function SettingsForm($request = null) {
		return $this->InstorePickupSettingsForm();
	}

	public function InstorePickupSettings($request) {

		if ($request->isAjax()) {
			$controller = $this;
			$responseNegotiator = new PjaxResponseNegotiator(
				array(
					'CurrentForm' => function() use(&$controller) {
						return $controller->InstorePickupSettingsForm()->forTemplate();
					},
					'Content' => function() use(&$controller) {
						return $controller->renderWith('ShopAdminSettings_Content');
					},
					'Breadcrumbs' => function() use (&$controller) {
						return $controller->renderWith('CMSBreadcrumbs');
					},
					'default' => function() use(&$controller) {
						return $controller->renderWith($controller->getViewer('show'));
					}
				),
				$this->response
			);
			return $responseNegotiator->respond($this->getRequest());
		}

		return $this->renderWith('ShopAdminSettings');
	}

	public function InstorePickupSettingsForm() {
		$shopConfig = ShopConfig::get()->First();

		$fields = new FieldList(
			$rootTab = new TabSet('Root',
				$tabMain = new Tab('Shipping',
					GridField::create(
						'InstorePickups',
						'InstorePickups',
						$shopConfig->InstorePickups(),
						GridFieldConfig_HasManyRelationEditor::create()
					)
				)
			)
		);

		$actions = new FieldList();
		$actions->push(FormAction::create('saveInstorePickupSettings', _t('GridFieldDetailForm.Save', 'Save'))
			->setUseButtonTag(true)
			->addExtraClass('ss-ui-action-constructive')
			->setAttribute('data-icon', 'add'));

		$form = new Form(
			$this,
			'EditForm',
			$fields,
			$actions
		);

		$form->setTemplate('ShopAdminSettings_EditForm');
		$form->setAttribute('data-pjax-fragment', 'CurrentForm');
		$form->addExtraClass('cms-content cms-edit-form center ss-tabset');

		if($form->Fields()->hasTabset()){
			$form->Fields()->findOrMakeTab('Root')->setTemplate('CMSTabSet');
		}

		$form->setFormAction(Controller::join_links($this->Link($this->sanitiseClassName($this->modelClass)), 'InstorePickup/InstorePickupSettingsForm'));
		$form->loadDataFrom($shopConfig);

		return $form;
	}

	public function saveInstorePickupSettings($data, $form) {
		//Hack for LeftAndMain::getRecord()
		self::$tree_class = 'ShopConfig';

		$config = ShopConfig::get()->First();
		$form->saveInto($config);
		$config->write();
		$form->sessionMessage('Saved Instore Pickup Settings', 'good');

		$controller = $this;
		$responseNegotiator = new PjaxResponseNegotiator(
			array(
				'CurrentForm' => function() use(&$controller) {
					return $controller->InstorePickupSettingsForm()->forTemplate();
				},
				'Content' => function() use(&$controller) {
					//return $controller->renderWith($controller->getTemplatesWithSuffix('_Content'));
				},
				'Breadcrumbs' => function() use (&$controller) {
					return $controller->renderWith('CMSBreadcrumbs');
				},
				'default' => function() use(&$controller) {
					return $controller->renderWith($controller->getViewer('show'));
				}
			),
			$this->response
		);
		return $responseNegotiator->respond($this->getRequest());
	}

	public function getSnippet() {
		if(!$member = Member::currentUser()){
			return false;
		}
		if(!Permission::check('CMS_ACCESS_' . get_class($this), 'any', $member)){
			return false;
		}

		return $this->customise(array(
			'Title' => 'Instore Pickup Management',
			'Help' => 'Create instore pickup locations',
			'Link' => Controller::join_links($this->Link('ShopConfig'), 'InstorePickup'),
			'LinkTitle' => 'Edit instore pickup locations'
		))->renderWith('ShopAdmin_Snippet');
	}
}
