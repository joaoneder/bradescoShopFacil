<?php

/*
 * Consumo Api Bradesco ShopFacil
 * @author João Neder
 * @date 10/07/2019
 * */
 
namespace Checkout\RegistrarTituloBradesco;

class RegistrarTituloBradesco {
	
	/** Dados da Requisição */
	public $merchantId = null;
    public $token = null;
    public $meioPagamento = null;
    public $url = null;
    public $requestConfirmacaoPagamento = null;
	
	/** Dados do Pedido */
    public $valor = 0;
    public $numeroPedido = null;
    public $descricao = null;
    
    /** Dados do Comprador */
    public $compradorNome = null;
    public $compradorCpf = null;
    public $compradorEnderecoNumero = null;
    public $compradorEnderecoLogradouro = null;
    public $compradorEnderecoComplemento = null;
    public $compradorEnderecoBairro = null;
    public $compradorEnderecoCidade = null;
    public $compradorEnderecoUf = null;
    public $compradorEnderecoCep = null;

	/** Dados do Boleto */
    public $boletoBeneficiario = null;
    public $boletoCarteira = null;
    public $boletoNossoNumero = null;
    public $boletoDataEmissao = null;
    public $boletoDataVencimento = null;
    public $boletoValor = null;
    public $boletoUrlLogotipo = null;
    public $boletoMsgCabecalho = null;
    public $boletoRenderizacao = null;
    public $boletoInstrucaoL1 = null;
    public $boletoInstrucaoL2 = null;
    public $boletoInstrucaoL3 = null;
	
	private $dadosRequisicao = array();
    private $dadosPedido = array();
    private $dadosComprador = array();
    private $dadosBoleto = array();


    /**
     * Bradesco constructor.
     * @param $merchantId
     * @param $token
     */
     
    public function __construct($merchantId, $token) {
        
        $this->merchantId = trim($merchantId);
        $this->token = trim($token);
        $this->meioPagamento = '300';
        $this->url = 'https://homolog.meiosdepagamentobradesco.com.br/apiboleto/transacao';
		$this->requestConfirmacaoPagamento = time();
    }

   
	/**
     * @return array
     */
     
    public function setDadosRequisicao()
    {
        $this->dadosRequisicao = array(
            "merchant_id" => $this->merchantId,
            "meio_pagamento" => $this->meioPagamento,
            "pedido" => $this->setDadosPedido(), 
			"comprador" => $this->setDadosComprador(), 
			"boleto" => $this->setDadosBoleto(),           
            "token_request_confirmacao_pagamento" => $this->requestConfirmacaoPagamento
        );

        return json_encode($this->dadosRequisicao);
    }
      
   
    /**
     * @return array
     */
     
    public function setDadosPedido()
    {
        $this->dadosPedido = array(
			"numero" => $this->numeroPedido,
			"valor" => $this->valor,
            "desricao" => $thi->descricao
        );

        return $this->dadosPedido;

    }
   
   
    /**
     * @return array
     */
     
    public function setDadosComprador()
    {
        $this->dadosComprador = array(
			"documento" => $this->compradorCpf,
			"nome" => $this->compradorNome,
            "endereco" => array(
					"cep" => $this->compradorEnderecoCep,
					"logradouro" => $this->compradorEnderecoLogradouro,
					"numero" => $this->compradorEnderecoNumero,
					"complemento" => $this->compradorEnderecoComplemento,
					"bairro" => $this->compradorEnderecoBairro,
					"cidade" => $this->compradorEnderecoCidade,
					"uf" => $this->compradorEnderecoUf
				),
			"ip" => $_SERVER["REMOTE_ADDR"],
            "user_agent" => $_SERVER["HTTP_USER_AGENT"]
        );

        return $this->dadosComprador;

    }

   
    /**
     * @return array
     */
     
    public function setDadosBoleto()
    {
        $this->dadosBoleto = array(
            "beneficiario" => $this->boletoBeneficiario,
            "carteira" => $this->boletoCarteira,
            "nosso_numero" => substr((string)$this->boletoNossoNumero, -11),
            "data_emissao" => $this->boletoDataEmissao,
            "data_vencimento" => $this->boletoDataVencimento,
            "valor_titulo" => $this->boletoValor,
            "url_logotipo" => $this->boletoUrlLogotipo,
            "mensagem_cabecalho" => $this->boletoMsgCabecalho,
            "tipo_renderizacao" => $this->boletoRenderizacao,
            "instrucoes" => $this->boletoInstrucaoL1.$this->boletoInstrucaoL2.$this->boletoInstrucaoL3
        );

        return $this->dadosBoleto;

    }
     
 
    /**
     * @param $dadosRequiscao
     * @return mixed
     */
     
    public function enviarRequisicao()
    {

        /** Token de autorização de acordo com o manual do Bradesco */
		$headers = array();
		$headers[] = "Accept: application/json";
		$headers[] = "Accept-Charset: UTF-8";
		$headers[] = "Content-Type: application/json;charset=UTF-8";
		$AuthorizationHeader = $this->merchantId.":".$this->token;
		$AuthorizationHeaderBase64 = base64_encode($AuthorizationHeader);
		$headers[] = "Authorization: Basic ".$AuthorizationHeaderBase64;

		$ch = curl_init($this->url);
		curl_setopt($ch, CURLOPT_VERBOSE, true); /** false para não exibir detalhes de requisição */
		curl_setopt($ch, CURLOPT_URL, $this->url);
		curl_setopt($ch, CURLOPT_POST, 1); /** envio via post */
		curl_setopt($ch, CURLOPT_POSTFIELDS, $this->setDadosRequisicao);
		curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);  /** configura curl_exec() para retornar o valor em string e não printar na saída padrão */
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false); /** não verificar certificado ssl */
		$resultado = curl_exec($ch);

		$arrRetorno = json_decode($resultado, true);

		return $arrRetorno;

    }

}
