<?php 

require_once Mage::getBaseDir('app') . '/code/core/Mage/Checkout/controllers/OnepageController.php';

class Letti_ItauShopline_OnepageController extends Mage_Checkout_OnepageController {
	
	public function successAction() {
		parent::successAction();
	}	
	
}

?>
