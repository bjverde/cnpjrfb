<?php
/*
 * CargaBd_CnpjRFB
 * https://github.com/bjverde/cnpjrfb
 *
 * Colunas na mesma ordem do arquivo CSV da RFB (cnpj-metadados.pdf) e do
 * modelo em modelo_banco/.
 */
class SociosDAO extends Dao
{
    public function __construct($tpdo=null)
    {
        $colunas = array('cnpj_basico'
                ,'identificador_socio'
                ,'nome_socio_razao_social'
                ,'cpf_cnpj_socio'
                ,'qualificacao_socio'
                ,'data_entrada_sociedade'
                ,'pais'
                ,'representante_legal'
                ,'nome_do_representante'
                ,'qualificacao_representante_legal'
                ,'faixa_etaria'
                );
        $colunasData = array('data_entrada_sociedade');
        parent::__construct($tpdo, 'socios', $colunas, $colunasData, 'cnpj_basico');
    }}
