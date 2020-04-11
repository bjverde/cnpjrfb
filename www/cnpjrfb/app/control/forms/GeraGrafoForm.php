<?php

class GeraGrafoForm extends TPage
{
    protected $form;      // form
    


    function __construct()
    {
        parent::__construct();


        $this->form = new BootstrapFormBuilder('form_interaction');
        $this->form->setFormTitle('Gerador de Grafó');
        $this->form->generateAria(); // automatic aria-label

        $cnpjLabel = 'CNPJ';
        $formDinCnpjField = new TFormDinCnpjField('cnpj',$cnpjLabel,true,true);
        $cnpj = $formDinCnpjField->getAdiantiObj();
        
        $nome_socio = new TEntry('nome_socio');

        $this->form->addFields( [new TLabel($cnpjLabel)],[$cnpj]);
        //$this->form->addFields( [new TLabel('Nome Sócio')],[$nome_socio]);

        $this->form->addAction('Gera Grafo',  new TAction(array($this, 'gerarGrafo')), 'fa:magic fa-fw red');

        // wrap the page content using vertical box
        $vbox = new TVBox;
        $vbox->style = 'width: 100%';
        $vbox->add(new TXMLBreadCrumb('menu.xml', __CLASS__));
        $vbox->add($this->form);
        parent::add($vbox);
    }

    public function gerarGrafo($param){
        $obj = new StdClass;
        $obj->cnpj = $param['cnpj'];
        $obj->nome_socio = 'a';
        // envia os dados ao formulário
        TForm::sendData('form_interaction', $obj);

        $resultado = GeraGrafoController::executa($param);
        
        if($resultado[GeraGrafoController::GERAL] == true){
            parent::__construct();

            $iframe = new TElement('iframe');
            $iframe->id = "iframe_external";
            $iframe->src = "app/cnpj_full/CNPJ-full".$resultado[GeraGrafoController::ARQUIVO];
            $iframe->frameborder = "0";
            $iframe->scrolling = "yes";
            $iframe->width = "100%";
            $iframe->height = "700px";
            
            parent::add($iframe);
        }else{
            FormDinHelper::debug($resultado[GeraGrafoController::INFO]);
        }
    }
}
