<?php

class TFormDinSelectField
{
    protected $adiantiObj;
    
    /**
     * Campo do tipo SelectField ou Combo Simples
     * Reconstruido FormDin 4 Sobre o Adianti 7
     *
     * @param string $id            - 1: ID do campo
     * @param string $strLabel      - 2: Label do campo
     * @param boolean $boolRequired - 3: Obrigatorio
     * @param array $mixOptions     - 4: array dos valores. no formato "key=>value"
     * @return TCombo
     */
    public function __construct(string $id,string $strLabel,$boolRequired = false, array $mixOptions)
    {
        $this->adiantiObj = new TCombo($id);
        $this->adiantiObj->addItems($mixOptions);
        if($boolRequired){
            $strLabel = empty($strLabel)?$id:$strLabel;
            $this->adiantiObj->addValidation($strLabel, new TRequiredValidator);
        }
        return $this->getAdiantiObj();
    }

    public function getAdiantiObj(){
        return $this->adiantiObj;
    }
}