<?php

/**
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */
class Letti_ItauShopline_Block_Adminhtml_Sales_Order_Payment extends Mage_Adminhtml_Block_Sales_Order_Payment {

    protected function _toHtml() {
        $payment = $this->getParentBlock()->getOrder()->getPayment();
        $toHtml = "<b>Forma de pagamento:</b> Itaú Shopline <br />";
        $toHtml .= "Data de vencimento: " . $payment->getAdditionalInformation('dtVecto');;
        return $toHtml;
    }

}

?>