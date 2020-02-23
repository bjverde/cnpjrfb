<?php

class GeraGrafoForm extends TPage
{
    protected $form;      // form
    


    function __construct()
    {
        parent::__construct();


        $this->form = new BootstrapFormBuilder;
        $this->form->setFormTitle('Gerador de Grafó');
        $this->form->generateAria(); // automatic aria-label

        $cnpjLabel = 'CNPJ';
        $formDinCnpjField = new TFormDinCnpjField('cnpj',$cnpjLabel,true,true);
        $cnpj = $formDinCnpjField->getAdiantiObj();
        
        $nome_socio = new TEntry('nome_socio');

        $this->form->addFields( [new TLabel('CNPJ')],[$cnpj]);
        $this->form->addFields( [new TLabel('Nome')],[$nome_socio]);

        $this->form->addAction('Gera Grafo',  new TAction(array($this, 'gerarGrafo')), 'fa:magic fa-fw red');

        // wrap the page content using vertical box
        $vbox = new TVBox;
        $vbox->style = 'width: 100%';
        $vbox->add(new TXMLBreadCrumb('menu.xml', __CLASS__));
        $vbox->add($this->form);
        parent::add($vbox);
    }

    public function gerarGrafo($param){
        var_dump($param);
        $path = 'app/cnpj_full/CNPJ-full/';
        if( empty($param['nome_socio']) ){
            //$command = 'python3 consulta.py cnpj 00000000000191 graficos --gexf --viz';
            $command = 'ls -l';
        }else{
            //$command = 'python3 consulta.py cnpj 00000000000191 graficos --gexf --viz';
            $command = 'ls -l';
        }
        //$result = exec($path.$command, $output);
        $result = exec($command, $output);
        echo('<pre>');
        var_dump($output);
        var_dump($result);
        echo('</pre>');
    }
}
