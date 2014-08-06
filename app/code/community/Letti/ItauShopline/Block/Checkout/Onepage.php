<?php

class Letti_ItauShopline_Block_Checkout_Onepage extends Mage_Payment_Block_Form {

    protected function _construct() {
        parent::_construct();
        $this->setTemplate('letti/itaushopline/checkout/onepage.phtml');
    }

}

?>
