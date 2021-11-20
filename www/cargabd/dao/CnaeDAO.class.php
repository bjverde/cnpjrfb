<?php
/**
 * System generated by SysGen (System Generator with Formdin Framework) 
 * Download SysGen: https://github.com/bjverde/sysgen
 * Download Formdin Framework: https://github.com/bjverde/formDin
 * 
 * SysGen  Version: 1.11.0
 * FormDin Version: 4.11.0
 * 
 * System concursomembroadm created in: 2021-03-10 20:42:27
 */
class CnaeDAO  extends Dao
{
    private static $sqlBasicSelect = "select
                                      codigo
                                     ,descricao
                                     from cnae ";

    public function __construct($tpdo=null)
    {
        parent::__construct($tpdo);
        $this->setTabelaName('cnae');
    }
    //--------------------------------------------------------------------------------
    public function insert( array $linhaArquivoCsv )
    {
        $values = array(  $linhaArquivoCsv[0]
                        , $linhaArquivoCsv[1]
                        );
        $sql = 'insert into cnae(
                                 codigo
                                ,descricao
                                ) values (?,?)';
        $result = $this->executeSql($sql, $values);
        return true;
    }
}
?>