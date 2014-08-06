<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */
require_once('ItauShopline/Itaucripto.php');

class Letti_ItauShopline_Model_Method_ItauShopline extends Mage_Payment_Model_Method_Abstract {

    protected $itauCripto = null;
    protected $codEmpresa;
    protected $chaveCriptografada;

    public function __construct() {
        $this->itauCripto = new Itaucripto();
        $this->codEmpresa = Mage::getStoreConfig('payment/itaushopline/codigo_empresa');
        $this->chaveCriptografada = Mage::getStoreConfig('payment/itaushopline/chave_criptografada');
    }

    public function getUrl(array $data) {
        $cripto = $this->itauCripto->geraDados(
                $this->codEmpresa, $data['numPedido'], $data['valor'], $data['obs'], $this->chaveCriptografada, $data['cedente'], $data['codDocto'], $data['docto'], $data['endereco'], $data['bairro'], $data['cep'], $data['cidade'], $data['estado'], $data['dtVecto'], ''
        );
        return 'https://shopline.itau.com.br/shopline/shopline.aspx?DC=' . $cripto;
    }

}

?>