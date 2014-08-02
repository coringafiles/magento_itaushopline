<?php

/**
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */
class Letti_Boleto_Block_Adminhtml_Sales_Order_Payment extends Mage_Adminhtml_Block_Sales_Order_Payment {
    protected function _toHtml() {   
        $payment = $this->getParentBlock()->getOrder()->getPayment();
        $method = $payment->getMethodInstance()->getCode();
        $formaPgto = $payment->getAdditionalInformation('formaPgto');
        $banco = $payment->getAdditionalInformation('banco');
        $dtVecto = $formaPgto == 'boleto' ? $payment->getAdditionalInformation('dtVecto') : null;
        
        $toHtml = "<b>Forma de pagamento:</b> ";
        
        switch ($formaPgto)
        {
        	case 'boleto':
        		$toHtml .= 'Boleto bancário <br />';
        		break;
        }
        
        $toHtml .= "<b>Banco: </b> ";
        
        switch ($banco)
        {
        	case 'itau':
        		$toHtml .= 'Itaú <br />';
        		break;
        	case 'bradesco':
        		$toHtml .= 'Bradesco <br />';
        		break;
        	case 'banco_do_brasil':
        		$toHtml .= 'Banco do Brasil <br />';
        		break;      		
        }
        
        $toHtml .= $formaPgto == 'boleto' ? $dtVecto : "";
        $toHtml .= "<br /><br />";
        return $toHtml;
    }
}

?>
