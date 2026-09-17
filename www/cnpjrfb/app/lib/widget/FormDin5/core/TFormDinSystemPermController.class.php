<?php
class TFormDinSystemPermController
{
    private string|null $database = null;

    public function __construct(string|null $database = null){
        $this->setDatabase($database);
    }
    public function getDatabase()
    {
        return $this->database;
    }

    public function setDatabase(string $database)
    {
        $this->database = $database;
    }
    //--------------------------------------------------------------------------------    
    /**
     * Cria um combo de usuários para usar em formulários compativel com diferentes versões do Adianti
     * @param string $idCombo - ID do combo
     * @return TDBCombo|null
     */
    public function getTDBComboUser(string $idCombo, TCriteria|null $criteria=null){
        $combo = null;
        if (class_exists('SystemUser')) {
            $combo = new TDBCombo($idCombo, $this->getDatabase(), 'SystemUser', 'id', '{name}','name asc' , $criteria);
            $combo->enableSearch();
            $combo->setSize('100%');
        }
        if (class_exists('SystemUsers')) {
            $combo = new TDBCombo($idCombo, $this->getDatabase(), 'SystemUsers', 'id', '{name}','name asc' , $criteria);
            $combo->enableSearch();
            $combo->setSize('100%');
        }
        return $combo; // retorna o objeto salvo
    }
    //--------------------------------------------------------------------------------
    /**
     * Busca usuário por ID, compatível com diferentes versões do Adianti
     * @param int $system_user_id - ID do usuário
     * @return object | null
     */        
    public function getUserById(int $system_user_id){
        try{
            $conn = TTransaction::open($this->getDatabase()); // open transaction
            $usuario = null;
            if (class_exists('SystemUser')) {
                $usuario = SystemUser::find($system_user_id);
            }
            if (class_exists('SystemUsers')) {
                $criteria = new TCriteria;
                $criteria->add(new TFilter('id', '=', $system_user_id));

                $repository = new TRepository('SystemUsers');
                $objects = $repository->load($criteria);
                $usuario = $objects ? $objects[0] : null;
            }
            TTransaction::close();
            return $usuario; // retorna o objeto salvo
        }catch (Exception $e) {
            throw $e;
        }
    }    
    public function getNomeUserById(int $system_user_id):string{
        try{
            if( is_numeric($system_user_id) && $system_user_id>0 ){
                $usuario = $this->getUserById($system_user_id);
                if(is_object($usuario) && isset($usuario->name)){
                    return $usuario->name; // retorna o objeto salvo
                }else{
                    return '';
                }
            }else{
                return '';
            }
        }catch (Exception $e) {
            throw $e;
        }
    }
    //--------------------------------------------------------------------------------    
    public function getUnitById(int $system_unit_id){
        try{
            $conn = TTransaction::open($this->getDatabase()); // open transaction
            $unidade = SystemUnit::find($system_unit_id);
            TTransaction::close();
            return $unidade; // retorna o objeto salvo
        }catch (Exception $e) {
            TTransaction::rollback();
            throw $e;
        }
    }
    //--------------------------------------------------------------------------------    
    public function getNomeUnitById(int $system_unit_id):string{
        try{
            $unidade = $this->getUnitById($system_unit_id);
            return $unidade->name; // retorna o objeto salvo
        }catch (Exception $e) {
            TTransaction::rollback();
            throw $e;
        }
    }
    //--------------------------------------------------------------------------------    
    /**
     * Cria um combo de usuários para usar em formulários compativel com diferentes versões do Adianti
     * @param string $idCombo - ID do combo
     * @return TDBCombo | null
     */
    public function getTDBComboUnit(string $idCombo, ?TCriteria $criteria=null){
        $combo = new TDBCombo($idCombo, $this->getDatabase(), 'SystemUnit', 'id', '{name}','name asc' , $criteria);
        $combo->enableSearch();
        $combo->setSize('100%');
        return $combo; // retorna o objeto salvo
    }    
}//fim classe