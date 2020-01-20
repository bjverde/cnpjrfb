<?php

class EmpresaForm extends TPage
{
    private $form;
    
    /**
     * Class constructor
     * Creates the page
     */
    function __construct()
    {
        parent::__construct();

        $this->form = new BootstrapFormBuilder;
        $this->form->setFormTitle('Empresas');
        $this->form->generateAria(); // automatic aria-label

        //$situacaoCadastralControler = new SituacaoCadastralControler();
        //$listSituacaoCadastral = $situacaoCadastralControler->getList();
        $listSituacaoCadastral = array(1=>'Item 1',2=>'Item 2');

        $comboSituacao  = new TCombo('motivo_situacao');
        $comboSituacao->addItems($listSituacaoCadastral);

        $this->form->addFields( [new TLabel('Situação')], [$comboSituacao] );

        // wrap the page content using vertical box
        $vbox = new TVBox;
        $vbox->style = 'width: 100%';
        $vbox->add(new TXMLBreadCrumb('menu.xml', __CLASS__));
        $vbox->add($this->form);
        parent::add($vbox);
    }
}
