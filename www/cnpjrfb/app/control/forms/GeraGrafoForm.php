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

    /***
     * 1 - sudo docker-compose exec apache_php bash
     * 2 - chown -R www-data:www-data cnpjrfb/app/cnpj_full/graficos
     */
    public function gerarGrafo($param){
        Util::debug($param,'Param');
        if( empty($param['nome_socio']) ){
            $cnpj = $param['cnpj'];
            $command = 'python3 consulta.py cnpj 00000000000191 graficos/00000000000191 --viz';
            //$command = 'ls -l';
        }else{
            $nome_socio = $param['nome_socio'];
            $cpf = null;
            $command = 'python3 consulta.py nome_socio '.$nome_socio. ' graficos/00000000000191 --viz';            
            //$command = 'ls -l';
        }
        if (! defined ( 'DS' )) {
            define ( 'DS', DIRECTORY_SEPARATOR );
        }
        $path = dirname ( __FILE__ );
        $path = $path.DS.'..'.DS.'..'.DS.'cnpj_full'.DS.'CNPJ-full'.DS;
        $command = 'cd '.$path.';'.$command.' 2>&1';
        //$command = 'cd '.$path.';python3 consulta.py 2>&1';
        //$command = 'cd '.$path.';ls -l > t.txt'.' 2>&1';
        $result01 = exec($command, $output, $result);
        //$result = exec($command, $output);
        Util::debug($command,'Command');
        Util::debug($output,'Output');
        Util::debug($result,'Result');
        Util::debug($result01,'Result01');
    }
}
