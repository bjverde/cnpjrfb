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
        
        $html = null;
        if($resultado[GeraGrafoController::GERAL] == true){
            $html = GeraGrafoController::leGrafo($resultado[GeraGrafoController::ARQUIVO]);
        }
        if($html !== null){
            parent::__construct();

            // A pasta dos grafos é bloqueada no Apache (.htaccess). O grafo.html é
            // autocontido (d3 embutido), então vai inteiro no srcdoc do iframe.
            $botao = new TElement('button');
            $botao->type = 'button';
            $botao->class = 'btn btn-default btn-sm';
            $botao->style = 'margin-bottom: 8px';
            $botao->onclick = "var h=document.getElementById('iframe_grafo').srcdoc;"
                             ."window.open(URL.createObjectURL(new Blob([h],{type:'text/html'})),'_blank');";
            $botao->add('Abrir o grafo em nova aba');
            parent::add($botao);

            $iframe = new TElement('iframe');
            $iframe->id = "iframe_grafo";
            $iframe->srcdoc = htmlspecialchars($html, ENT_QUOTES, 'UTF-8');
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
