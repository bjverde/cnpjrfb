<?php
/*
 * CargaBd_CnpjRFB
 * https://github.com/bjverde/cnpjrfb
 *
 * Colunas na mesma ordem do arquivo CSV da RFB (cnpj-metadados.pdf) e do
 * modelo em modelo_banco/.
 */
class NatjuDAO extends Dao
{
    public function __construct($tpdo=null)
    {
        parent::__construct($tpdo, 'natju', array('codigo','descricao'));
    }
}
