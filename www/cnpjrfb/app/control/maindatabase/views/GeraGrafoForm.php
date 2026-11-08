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
        
        $url = null;
        $html = null;
        if($resultado[GeraGrafoController::GERAL] == true){
            $url = GeraGrafoController::getUrlGrafo($resultado[GeraGrafoController::ARQUIVO]);
            if($url === null){
                // pasta dos grafos fora do sistema web: o PHP lê o arquivo
                $html = GeraGrafoController::leGrafo($resultado[GeraGrafoController::ARQUIVO]);
            }
        }
        if($url !== null || $html !== null){
            parent::__construct();

            $iframe = new TElement('iframe');
            $iframe->id = "iframe_grafo";
            if($url !== null){
                $link = new TElement('a');
                $link->href = $url;
                $link->target = '_blank';
                $link->class = 'btn btn-default btn-sm';
                $link->style = 'margin-bottom: 8px';
                $link->add('Abrir o grafo em nova aba');
                parent::add($link);
                $iframe->src = $url;
            }else{
                // grafo.html é autocontido (d3 embutido): vai inteiro no srcdoc
                $iframe->srcdoc = htmlspecialchars($html, ENT_QUOTES, 'UTF-8');
            }
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
            $msg = $msg.'<b><pre>'.htmlspecialchars((string) $resultado[GeraGrafoController::COMANDO], ENT_QUOTES, 'UTF-8').'</pre></b>';
            $msg = $msg.'<br>';
            $msg = $msg.'<br> abaixo a mensagem de retorno da exeução';
            FormDinHelper::debug($msg);
            FormDinHelper::debug(htmlspecialchars((string) $resultado[GeraGrafoController::INFO], ENT_QUOTES, 'UTF-8'));
        }
    }
}
