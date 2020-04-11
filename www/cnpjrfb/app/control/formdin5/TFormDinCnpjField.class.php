<?php

class TFormDinCnpjField
{
    protected $adiantiObj;
    
    /**
     * Campo de entrada de dados do tipo CNPJ
     * Reconstruido FormDin 4 Sobre o Adianti 7
     *
     * @param string $id            - 1: ID do campo
     * @param string $strLabel      - 2: Label do campo, usado para validações
     * @param boolean $boolRequired - 3: Obrigatorio. DEFAULT = False.
     * @param boolean $boolSendMask - 4: Se as mascara deve ser enviada ou não para o post. DEFAULT = False.
     * @param string $strValue      - 5: Texto preenchido ou valor default
     * @param string $strExampleText- 6: Texto de exemplo ou placeholder 
     * @return TEntry
     */
    public function __construct(string $id
                               ,string $strLabel
                               ,$boolRequired = false
                               ,$boolSendMask = true
                               ,string $strValue=null
                               ,string $strExampleText =null)
    {
        $this->adiantiObj = new TEntry($id);
        $this->adiantiObj->addValidation($strLabel, new TCNPJValidator);
        $this->adiantiObj->setMask('99.999.999/9999-99', $boolSendMask);
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