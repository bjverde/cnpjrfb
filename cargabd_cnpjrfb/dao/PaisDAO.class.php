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
class PaisDAO  extends Dao
{
    private static $sqlBasicSelect = "select
                                      codigo
                                     ,descricao
                                     from pais ";

    public function __construct($tpdo=null)
    {
        parent::__construct($tpdo);
        $this->setTabelaName('pais');
    }
    //--------------------------------------------------------------------------------
    public function insert( array $linhaArquivoCsv )
    {
        $values = array(  $linhaArquivoCsv[0]
                        , $linhaArquivoCsv[1]
                        );
        $sql = 'insert into pais(
                                 codigo
                                ,descricao
                                ) values (?,?)';
        $result = $this->getTPDOConnection()->executeSql($sql, $values);
        return true;
    }
}
?>