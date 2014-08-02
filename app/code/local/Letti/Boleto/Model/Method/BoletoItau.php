<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */
require_once('Itaucripto.php');
class Letti_Boleto_Model_Method_BoletoItau extends Mage_Payment_Model_Method_Abstract {
	
	protected $itauCripto = null;
	protected $codEmpresa;
	protected $chaveCriptografada;
		
	public function __construct()
	{
		$this->itauCripto = new Itaucripto();
		$this->codEmpresa = Mage::getStoreConfig('payment/boleto/codigo_empresa');
        $this->chaveCriptografada = Mage::getStoreConfig('payment/boleto/chave_criptografada');
	}
	
	public function getUrl(array $data)
	{
		$cripto = $this->itauCripto->geraDados(
			$this->codEmpresa,
			$data['numPedido'],
			$data['valor'],
			$data['obs'],
			$this->chaveCriptografada,
			$data['cedente'],
			$data['codDocto'],
			$data['docto'],
			$data['endereco'],
			'Cid Nova Heliopolis',
			$data['cep'],
			$data['cidade'],
			$data['estado'],
			$data['dtVecto'],
			''
			);
		return 'https://shopline.itau.com.br/shopline/shopline.aspx?DC=' . $cripto;
	}
}

?>
