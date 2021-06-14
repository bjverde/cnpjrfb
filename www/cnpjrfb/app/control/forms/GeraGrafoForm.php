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

            // create the HTML Renderer
            $this->html = new THtmlRenderer('app/resources/link_grafo.html');
            
            $replaces = [];
            $replaces['link']  = ServerHelper::getHomeUrl().'app/CNPJ-full'.$resultado[GeraGrafoController::ARQUIVO];
            $this->html->enableSection('main', $replaces);
            parent::add($this->html);

            $iframe = new TElement('iframe');
            $iframe->id = "iframe_external";
            $iframe->src = "app/CNPJ-full".$resultado[GeraGrafoController::ARQUIVO];
            $iframe->frameborder = "0";
            $iframe->scrolling = "yes";
            $iframe->width = "100%";
            $iframe->height = "700px";
            
            parent::add($iframe);
        }else{            
            $msg = '<b>Provável falha de configuração do Python</b>';
            $msg = $msg.'<br>';
            $msg = $msg.'<br> abaixo o dois comandos que são excutado para gerar o grafo. Abra um terminal do seu SO, copie e cole o comando ter certeza que está funcionando.';
            $msg = $msg.'<br>';
            $msg = $msg.'<br>';
            $msg = $msg.'<b><pre>'.$resultado[GeraGrafoController::COMANDO].'</pre></b>';
            $msg = $msg.'<br>';
            $msg = $msg.'<br> abaixo a mensagem de retorno da exeução';
            FormDinHelper::debug($msg);
            FormDinHelper::debug($resultado[GeraGrafoController::INFO]);
        }
    }
}
