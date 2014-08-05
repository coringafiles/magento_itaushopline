<?php

require_once ("Itaucripto.php");
/**
 * Bank Transfer payment method model
 */
class Letti_ItauShopline_Model_ItauShopline extends Mage_Payment_Model_Method_Abstract {

	protected $_code 					= 'Itaú Shopline';
	protected $_isGateway 				= false;
	protected $_canAuthorize 			= false;
	protected $_canCapture	 			= false;
	protected $_canCapturePartial 		= false;
	protected $_canRefund				= false;
    protected $_canVoid                 = true;
    protected $_canUseInternal          = true;
    protected $_canUseCheckout          = true;
    protected $_canUseForMultishipping  = true;
    protected $_canSaveCc 				= false;	

	public function getOrderPlaceRedirectUrl() {
		/*
		$info = $this->getInfoInstance();
		$order = $info->getQuote();
		


		$somaVecto = Mage::getStoreConfig('payment/itaushopline/dias_vencimento');
		$urlRetorno = Mage::getStoreConfig('payment/itaushopline/url_retorno');

		$dtVectoTimestamp = strtotime("+${somaVecto} days");
		$dtVectoFormated = date('d/m/Y', $dtVectoTimestamp);
		$dtVecto = date('dmY', $dtVectoTimestamp);
				
		$numPedido = str_pad($orderId, 8, '0', STR_PAD_LEFT);
		$valor = number_format($order -> getGrandTotal(), 2, ',', '');
		$obs = '';
		$cedente = $customer->getName();
		$codDocto = '01';// 01 - CPF 02 - CNPJ
		$docto = $customer->getTaxvat();
		$endereco = $customerAddress['street'];
	

		$itaucripto = new Itaucripto();
		$codEmpresa = Mage::getStoreConfig('payment/boleto/codigo_empresa');
        $chaveCriptografada = Mage::getStoreConfig('payment/boleto/chave_criptografada');
		
		//$cripto = $itaucripto->geraDados($codEmpresa, $s1, $s2, $s3, $s4, $s5, $s6, $s7, $s8, $s9, $s10, $s11, $s12, $s13, $s14, $s15, $s16, $s17);
		*/
		// Redireciona para a página de confirmação da compra
		return parent::getOrderPlaceRedirectUrl();
	}

}
