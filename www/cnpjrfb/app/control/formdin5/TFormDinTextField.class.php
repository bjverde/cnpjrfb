<?php

class TFormDinTextField
{
    protected $adiantiObj;
    

    /**
     * Campo de entrada de dados texto livre
     * Reconstruido FormDin 4 Sobre o Adianti 7
     *
     * @param string $id            - 1: ID do campo
     * @param string $strLabel      - 2: Label do campo, usado para validações
     * @param integer $intMaxLength - 3: Tamanho máximo de caracteres
     * @param boolean $boolRequired - 4: Obrigatorio. DEFAULT = False.
     * @param string $strValue      - 5: Texto preenchido ou valor default
     * @param string $strExampleText- 6: Texto de exemplo ou placeholder 
     * @return TEntry
     */
    public function __construct(string $id
                               ,string $strLabel
                               ,int $intMaxLength = null
                               ,$boolRequired = false
                               ,string $strValue=null
                               ,string $strExampleText =null)
    {
        $this->adiantiObj = new TEntry($id);
        if($intMaxLength>=1){
            $this->adiantiObj->addValidation($strLabel, new TMaxLengthValidator, array($intMaxLength));
        }
        if($boolRequired){
            $strLabel = empty($strLabel)?$id:$strLabel;
            $this->adiantiObj->addValidation($strLabel, new TRequiredValidator);
        }
        if(!empty($strExampleText)){
            $this->adiantiObj->placeholder = $strExampleText;
        } 
        return $this->getAdiantiObj();
    }

    public function getAdiantiObj(){
        return $this->adiantiObj;
    }
}