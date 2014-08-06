<?php

class Letti_ItauShopline_Model_ItauShopline extends Mage_Payment_Model_Method_Abstract {

    const PAYMENT_METHOD_BOLETO_CODE = 'itaushopline';

    /**
     * Código para forma de pagamento
     *
     * @var string
     */
    protected $_code = 'itaushopline';
	protected $_formBlockType = "itaushopline/checkout_onepage";

    /* Método de pagamento é um gateway? */
    protected $_isGateway = false;

    /* Método precisa ser autorizado? */
    protected $_canAuthorize = false;

    public function getOrderPlaceRedirectUrl() {
        // Obtém informações sobre o método de pagamento atual
        $info = $this->getInfoInstance();

        // Obtém id da compra corrente
        $orders = Mage::getModel('sales/order')->getCollection()
                ->setOrder('increment_id', 'DESC')
                ->setPageSize(1)
                ->setCurPage(1);
        $orderId = $orders->getFirstItem()->getEntityId();

        // Obtém informações sobre ordem e pagamento
        $order = $info->getQuote();
        $payment = $order->getPayment();

        /*
         * Obtém dados que serão utilizados na geração da url criptografada
         * que será enviada para o banco para a geraçãod o boleto
         */
        // Número de dias que será incrementado à data autual
        // para geração da data de vencimento
        $somaVecto = Mage::getStoreConfig('payment/itaushopline/dias_vencimento');
        // Obtém url de retorno. Esta url será enviada ao banco para que
        // quando o cliente selecione o tipo de pagamento, o banco possa
        // retornar a informação do tipo de pagamento para esta url
        $urlRetorno = Mage::getStoreConfig('payment/itaushopline/url_retorno');
        // Obtém objeto customer (customer logado)
        $customer = Mage::getSingleton('customer/session')->getCustomer();
        // Obtém id do endereço relacionado à este customer
        $customerAddressId = $customer->getDefaultBilling();
        if (!$customerAddressId) // Id foi obtido com sucesso?
            return;

        $customerAddress = Mage::getModel('customer/address')->load($customerAddressId); // Carregar endereço
        $dtVectoTimestamp = strtotime("+{$somaVecto} days");
        $dtVecto = date('dmY', $dtVectoTimestamp);

        // Monta array que será utilizado no método de criptografia
        $data = array(
            'numPedido' => str_pad($orderId, 8, '0', STR_PAD_LEFT), // Formata número do pedido (8 dígitos)
            'valor' => number_format($order->getGrandTotal(), 2, ',', ''), // Formata valor do pedido (2 cadas decimais separado por vírgula)
            'obs' => '',
            'cedente' => $customer->getName(),
            'codDocto' => '01', // 01 - CPF 02 - CNPJ
            'docto' => $customer->getCpf(), // Número de cpf
            'endereco' => $customerAddress['street_1'] . ', ' . $customerAddress['steet_2'] . ' - ' . $customerAddress['street_3'],
            'bairro' => $customerAddress['street_4'],
            'cep' => $customerAddress['postcode'],
            'cidade' => $customerAddress['city'],
            'estado' => $customerAddress['region'],
            'dtVecto' => $dtVecto            // Incrementa x dias na data de vencimento (formato: ddmmyyyy)
        );


        // Carrega o metodo de pagamento do itaú shopline
        $pgtoItau = new Letti_ItauShopline_Model_Method_ItauShopline();

        // Obtém a url para emissão do boleto
        $pgtoUrl = $pgtoItau->getUrl($data);
        
        // Armazena a url na sessão
        Mage::getSingleton('core/session')->setData('itaushopline_url', $pgtoUrl);

        // Monta dados adicionais de pagamento
        $additionalInformation = array(
            'formaPgto' => 'Itaú Shopline',
            'dtVecto' => $dtVecto,
            'url' => $pgtoUrl
        );

        // Insere informações adicionais sobre o pagamento
        $payment->setAdditionalInformation('banco', $additionalInformation['banco']);
        $payment->setAdditionalInformation('formaPgto', $additionalInformation['formaPgto']);
        $payment->setAdditionalInformation('dtVecto', $additionalInformation['dtVecto']);
        $payment->save();
        
        // Envia e-mail de confirmação da compra
//        $order->sendNewOrderEmail();
//        $order->setEmailSent(true);
//        $order->save();

        // Redireciona para a página de confirmação da compra
        return Mage::getUrl('itaushopline/onepage/success');
    }

}

?>
