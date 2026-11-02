<?php
/*
 * CargaBd_CnpjRFB
 * https://github.com/bjverde/cnpjrfb
 *
 * Colunas na mesma ordem do arquivo CSV da RFB (cnpj-metadados.pdf) e do
 * modelo em modelo_banco/.
 */
class SimplesDAO extends Dao
{
    public function __construct($tpdo=null)
    {
        $colunas = array('cnpj_basico'
                ,'opcao_pelo_simples'
                ,'data_opcao_simples'
                ,'data_exclusao_simples'
                ,'opcao_mei'
                ,'data_opcao_mei'
                ,'data_exclusao_mei'
                );
        $colunasData = array('data_opcao_simples','data_exclusao_simples','data_opcao_mei','data_exclusao_mei');
        parent::__construct($tpdo, 'simples', $colunas, $colunasData, 'cnpj_basico');
    }}
