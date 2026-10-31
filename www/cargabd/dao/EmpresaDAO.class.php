<?php
/*
 * CargaBd_CnpjRFB
 * https://github.com/bjverde/cnpjrfb
 *
 * Colunas na mesma ordem do arquivo CSV da RFB (cnpj-metadados.pdf) e do
 * modelo em modelo_banco/.
 */
class EmpresaDAO extends Dao
{
    public function __construct($tpdo=null)
    {
        $colunas = array('cnpj_basico'
                ,'razao_social'
                ,'natureza_juridica'
                ,'qualificacao_responsavel'
                ,'capital_social'
                ,'porte_empresa'
                ,'ente_federativo_responsavel'
                );
        $colunasData = array();
        parent::__construct($tpdo, 'empresa', $colunas, $colunasData, 'cnpj_basico');
    }
    public function transformar(array $linhaArquivoCsv)
    {
        $values = parent::transformar($linhaArquivoCsv);
        if ( !is_null($values[4]) ) {
            $values[4] = str_replace(',', '.', $values[4]); //capital_social vem com vírgula decimal
        }
        $values[5] = ValidarHelper::codigoDoisDigitos($values[5]); //porte_empresa: '1' => '01'
        return $values;
    }
}
