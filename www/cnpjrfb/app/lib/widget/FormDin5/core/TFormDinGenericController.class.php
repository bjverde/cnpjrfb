<?php
class TFormDinGenericController
{
    private $dao;

    public function __construct(object $dao){
        $this->setDao($dao);
    }
    public function getDao(){
        return $this->dao;
    }
    public function setDao($dao){
        $this->dao = $dao;
    }
    //--------------------------------------------------------------------------------
    public function selectById( $id )
    {
        $result = $this->getDao()->selectById( $id );
        return $result;
    }
    //--------------------------------------------------------------------------------
    public function selectCount( $where=null )
    {
        $result = $this->getDao()->selectCount( $where );
        return $result;
    }
    //--------------------------------------------------------------------------------
    public function selectAllPagination( $orderBy=null, $where=null, $page=null,  $rowsPerPage= null)
    {
        $result = $this->getDao()->selectAllPagination( $orderBy, $where, $page,  $rowsPerPage );
        return $result;
    }
    //--------------------------------------------------------------------------------
    public function selectAll( $orderBy=null, $where=null )
    {
        $result = $this->getDao()->selectAll( $orderBy, $where );
        return $result;
    }
    //--------------------------------------------------------------------------------
    /**
     * Faz um Select usando o TCriteria
     * @param TCriteria $criteria    - 01: Obj TCriteria
     * @param string $repositoryName - 02: nome de classe
     * @return array Adianti
     */
    public function selectByTCriteria( TCriteria $criteria)
    {
        $result = $this->getDao()->selectByTCriteria($criteria);
        return $result;
    }
    //--------------------------------------------------------------------------------
    /**
     * Faz um Select Count usando o TCriteria
     * @param TCriteria $criteria    - 01: Obj TCriteria
     * @param string $repositoryName - 02: nome de classe
     * @return array Adianti
     */
    public function selectByTCriteriaCount( TCriteria $criteria)
    {
        $result = $this->getDao()->selectByTCriteriaCount($criteria);
        return $result;
    }
    
    /**
     * Busca arrays baseado em uma criteria
     *
     * @param TCriteria $criteria
     * @param bool $showDumpLogTela
     * @return mixed|null
     */    
    public function getArrayByCriteria(TCriteria $criteria,bool $showDumpLogTela=false){
        $result = $this->getDao()->getArrayByCriteria($criteria,$showDumpLogTela);
        return $result;
    }

    /**
     * Busca objetos baseados em uma criteria
     *
     * @param TCriteria $criteria
     * @param bool $showDumpLogTela
     * @return mixed|null
     */
    public function getListObjByCriteria(TCriteria $criteria,bool $showDumpLogTela=false){
        $result = $this->getDao()->getListObjByCriteria($criteria,$showDumpLogTela);
        return $result;
    }

    /**
     * Conta registros baseado em uma criteria
     *
     * @param TCriteria $criteria
     * @param bool $showDumpLogTela
     * @return mixed|null
     */    
    public function getCountByCriteria(TCriteria $criteria,bool $showDumpLogTela=false){
        $result = $this->getDao()->getCountByCriteria($criteria,$showDumpLogTela);
        return $result;
    }    
}//fim classe