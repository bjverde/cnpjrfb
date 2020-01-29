<?php

class TFormDinSwitch
{
    protected $adiantiObj;
    
    /**
     * Cria um RadioGroup com efeito visual de Switch
     * Reconstruido FormDin 4 Sobre o Adianti 7
     * 
     * @param string $id            - 1: ID do campo
     * @param string $strLabel      - 2: Label do campo
     * @param boolean $boolRequired - 3: Obrigatorio
     * @param array $itens
     * @return mixed TRadioGroup
     */
    public function __construct(string $id,string $strLabel,$boolRequired = false,array $itens= null)
    {
        $this->adiantiObj = new TRadioGroup($id);
        $this->adiantiObj->setLayout('horizontal');
        $this->adiantiObj->setUseButton();
        $items = ['S'=>'Sim', 'N'=>'Não'];
        $this->adiantiObj->addItems($items);
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