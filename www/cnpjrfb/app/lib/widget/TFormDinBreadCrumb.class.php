<?php

class TFormDinBreadCrumb
{
    protected $adiantiObj;
    
    public function __construct($controller, $boolShowBread=true)
    {
        $this->adiantiObj = new TVBox;
        $this->adiantiObj->style = 'width: 100%';
        if($boolShowBread){
            $this->adiantiObj->add(new TXMLBreadCrumb('menu.xml', $controller));
        }
        return $this->getAdiantiObj();
    }

    public function getAdiantiObj(){
        return $this->adiantiObj;
    }
}