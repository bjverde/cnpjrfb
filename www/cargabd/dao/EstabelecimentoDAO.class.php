<?php
/*
 * CargaBd_CnpjRFB
 * https://github.com/bjverde/cnpjrfb
 *
 * Colunas na mesma ordem do arquivo CSV da RFB (cnpj-metadados.pdf) e do
 * modelo em modelo_banco/.
 */
class EstabelecimentoDAO extends Dao
{
    public function __construct($tpdo=null)
    {
        $colunas = array('cnpj_basico'
                ,'cnpj_ordem'
                ,'cnpj_dv'
                ,'identificador_matriz_filial'
                ,'nome_fantasia'
                ,'situacao_cadastral'
                ,'data_situacao_cadastral'
                ,'motivo_situacao_cadastral'
                ,'nome_cidade_exterior'
                ,'pais'
                ,'data_inicio_atividade'
                ,'cnae_fiscal_principal'
                ,'cnae_fiscal_secundaria'
                ,'tipo_logradouro'
                ,'logradouro'
                ,'numero'
                ,'complemento'
                ,'bairro'
                ,'cep'
                ,'uf'
                ,'municipio'
                ,'ddd_1'
                ,'telefone_1'
                ,'ddd_2'
                ,'telefone_2'
                ,'ddd_fax'
                ,'fax'
                ,'correio_eletronico'
                ,'situacao_especial'
                ,'data_situacao_especial'
                );
        $colunasData = array('data_situacao_cadastral','data_inicio_atividade','data_situacao_especial');
        parent::__construct($tpdo, 'estabelecimento', $colunas, $colunasData, 'cnpj_basico');
    }
    public function transformar(array $linhaArquivoCsv)
    {
        $values = parent::transformar($linhaArquivoCsv);
        $values[5] = ValidarHelper::codigoDoisDigitos($values[5]); //situacao_cadastral: '2' => '02'
        return $values;
    }
}
