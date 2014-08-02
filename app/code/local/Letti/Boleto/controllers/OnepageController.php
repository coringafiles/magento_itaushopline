<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */
class Letti_Boleto_OnepageController extends Mage_Checkout_Controller_Action {
    	
	/**
	 * Método executado antes de qualquer ação do controller 
	 */
	public function preDispatch()
	{
		parent::preDispatch();										// Invoca método da classe pai
		$action = $this->getRequest()->getActionName();				// Obtém ação atual que o usuário está tentando acessar
		$loginUrl = Mage::helper('customer')->getLoginUrl();		

		// O usuário está logado?
		if (!Mage::getSingleton('customer/session')->authenticate($this, $loginUrl)) {
			$this->setFlag('', self::FLAG_NO_DISPATCH, true);
		}
	}
	
    public function successAction() {   	       
    	$additionalInformation = Mage::getSingleton('core/session')->getData('additionalInformation');
    	
        // Obtém dados da compra como código, formas de pagamento etc
        $orderId = Mage::getSingleton('checkout/session')->getLastRealOrderId();
        $order = Mage::getModel('sales/order')->loadByIncrementId($orderId);
        
        // Insere informações adicionais sobre o pagamento
		$payment = $order->getPayment();
        $payment->setAdditionalInformation('banco', $additionalInformation['banco']);
        $payment->setAdditionalInformation('formaPgto', $additionalInformation['formaPgto']);
        $payment->setAdditionalInformation('dtVecto', $additionalInformation['dtVecto']);
        $payment->save();
        
        // Envia e-mail de confirmação da compra
        $order->sendNewOrderEmail();
        $order->setEmailSent(true);
        $order->save();    

        // Carrega layout
        $this->loadLayout();
        
        // Passa variáveis necessárias do controller para a view
        $layout = $this->getLayout();
        if($block = $layout->getBlock('letti_checkout.success'))
        {
        	$block->setOrderId($orderId);
        	$block->setCanViewOrder(true);
        	$block->setCanPrintOrder(true);
        	$block->setBoletoUrl($additionalInformation['url']);
        }
 
        // Renderiza o layout
		$this->renderLayout();
    }
}

?>
