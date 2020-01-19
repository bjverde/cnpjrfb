<?php
/**
 * Empresas Active Record
 * @author  <your-name-here>
 */
class Empresa extends TRecord
{
    const TABLENAME = 'empresas';
    const PRIMARYKEY= 'cnpj';
    const IDPOLICY =  'max'; // {max, serial}
    
    
    /**
     * Constructor method
     */
    public function __construct($id = NULL, $callObjectLoad = TRUE)
    {
        parent::__construct($id, $callObjectLoad);
        parent::addAttribute('matriz_filial');
        parent::addAttribute('razao_social');
        parent::addAttribute('nome_fantasia');
        parent::addAttribute('situacao');
        parent::addAttribute('data_situacao');
        parent::addAttribute('motivo_situacao');
        parent::addAttribute('nm_cidade_exterior');
        parent::addAttribute('cod_pais');
        parent::addAttribute('nome_pais');
        parent::addAttribute('cod_nat_juridica');
        parent::addAttribute('data_inicio_ativ');
        parent::addAttribute('cnae_fiscal');
        parent::addAttribute('tipo_logradouro');
        parent::addAttribute('logradouro');
        parent::addAttribute('numero');
        parent::addAttribute('complemento');
        parent::addAttribute('bairro');
        parent::addAttribute('cep');
        parent::addAttribute('uf');
        parent::addAttribute('cod_municipio');
        parent::addAttribute('municipio');
        parent::addAttribute('ddd_1');
        parent::addAttribute('telefone_1');
        parent::addAttribute('ddd_2');
        parent::addAttribute('telefone_2');
        parent::addAttribute('ddd_fax');
        parent::addAttribute('num_fax');
        parent::addAttribute('email');
        parent::addAttribute('qualif_resp');
        parent::addAttribute('capital_social');
        parent::addAttribute('porte');
        parent::addAttribute('opc_simples');
        parent::addAttribute('data_opc_simples');
        parent::addAttribute('data_exc_simples');
        parent::addAttribute('opc_mei');
        parent::addAttribute('sit_especial');
        parent::addAttribute('data_sit_especial');
    }


}
