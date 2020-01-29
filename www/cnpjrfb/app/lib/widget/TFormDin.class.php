<?php

class TFormDin
{
    protected $adiantiObj;
    
    /**
     * Formulario Padronizado em BoorStrap
     * Reconstruido FormDin 4 Sobre o Adianti 7
     *
     * @param string $strName       - 1: Name do Form
     * @param string $strTitle      - 2: Titulo que irá aparecer no Form
     * @param boolean $boolRequired - 3: Se vai fazer validação no Cliente (Navegador)
     * @return BootstrapFormBuilder
     */
    public function __construct(string $strName
                               ,string $strTitle
                               ,$boolClientValidation = true)
    {
        $this->adiantiObj = new BootstrapFormBuilder($strName);
        $this->adiantiObj->setFormTitle($strTitle);
        $this->adiantiObj->setClientValidation($boolClientValidation);
        return $this->getAdiantiObj();
    }

    public function getAdiantiObj(){
        return $this->adiantiObj;
    }
}