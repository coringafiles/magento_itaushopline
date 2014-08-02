<?php

/**
 * Bank Transfer payment method model
 */
class Letti_Boleto_Model_Boleto extends Mage_Payment_Model_Method_Abstract {

	const PAYMENT_METHOD_BOLETO_CODE = 'boleto';

	/**
	 * Código para forma de pagamento
	 *
	 * @var string
	 */
	protected $_code = self::PAYMENT_METHOD_BOLETO_CODE;
	protected $_formBlockType = "boleto/checkout_onepage";

	/* Método de pagamento é um gateway? */
	protected $_isGateway = false;

	/* Método precisa ser autorizado? */
	protected $_canAuthorize = false;

	public function getOrderPlaceRedirectUrl() {
		// Obtém informações sobre o método de pagamento atual
		$info = $this->getInfoInstance();
			
		// Obtém id da compra corrente
		$orders = Mage::getModel('sales/order')->getCollection()
		->setOrder('increment_id','DESC')
		->setPageSize(1)
		->setCurPage(1);
		$orderId = $orders->getFirstItem()->getEntityId();

		// Obtém informações sobre ordem e pagamento
		$order = $info->getQuote();
		$payment = $order->getPayment();

		// Obtém nome do banco escolhido para pagamento
		$banco = $payment->getBanco();
			
		/*
		 * Obtém dados que serão utilizados na geração da url criptografada
		 * que será enviada para o banco para a geraçãod o boleto
		 */
		// Número de dias que será incrementado à data autual
		// para geração da data de vencimento
		$somaVecto = Mage::getStoreConfig('payment/boleto/dias_vencimento');
		// Obtém url de retorno. Esta url será enviada ao banco para que
		// quando o cliente selecione o tipo de pagamento, o banco possa
		// retornar a informação do tipo de pagamento para esta url
		$urlRetorno = Mage::getStoreConfig('payment/boleto/url_retorno');
		// Obtém objeto customer (customer logado)
		$customer = Mage::getSingleton('customer/session')->getCustomer();
		// Obtém id do endereço relacionado à este customer
		$customerAddressId = $customer->getDefaultBilling();
		if($customerAddressId)	// Id foi obtido com sucesso?
		$customerAddress = Mage::getModel('customer/address')->load($customerAddressId); // Carregar endereço

		$dtVectoTimestamp = strtotime("+${somaVecto} days");
		$dtVectoFormated = date('d/m/Y', $dtVectoTimestamp);
		$dtVecto = date('dmY', $dtVectoTimestamp);
			
		// Monta array que será utilizado no método de criptografia
		$data = array(
			'numPedido' => str_pad($orderId, 8, '0', STR_PAD_LEFT),				// Formata número do pedido (8 dígitos)
			'valor' => number_format($order->getGrandTotal(), 2, ',', ''),		// Formata valor do pedido (2 cadas decimais separado por vírgula)
			'obs' => '',	
			'cedente' => $customer->getName(),
			'codDocto' => '01',													// 01 - CPF 02 - CNPJ
			'docto' => $customer->getCpf(),											// Número de cpf
			'endereco' => $customerAddress['street'] . ', ' . $customerAddress['numero'] . ' - ' . $customerAddress['complemento'],
			'bairro' => $customerAddress['bairro'],
			'cep' => $customerAddress['postcode'],
			'cidade' => $customerAddress['city'],
			'estado' => $customerAddress['region'],
			'dtVecto' => $dtVecto												// Incrementa x dias na data de vencimento (formato: ddmmyyyy)
		);

		// Verifica banco escolhido para pagamento
		switch ($banco)
		{
			case 'bradesco':
				$boletoUrl = '/magento/boleto.pdf';
				break;
			case 'banco_do_brasil':
				$boletoUrl = '/magento/boleto.pdf';
				break;
			case 'itau':
				// Carrega o metodo de pagamento do itaú shopline
				$boletoItau = new Letti_Boleto_Model_Method_BoletoItau();
				// Obtém a url para emissão do boleto
				$boletoUrl = $boletoItau->getUrl($data);
				break;
		}

		// Monta dados adicionais de pagamento
		$additionalInformation = array(
			'banco' => $banco,
			'formaPgto' => 'boleto',
			'dtVecto' => $dtVecto,
			'url' => $boletoUrl
		);

		// Persiste dados na sessão
		Mage::getSingleton('core/session')->setData('additionalInformation', $additionalInformation);
			
		// Redireciona para a página de confirmação da compra
		return Mage::getUrl('boleto/onepage/success');
}

}

