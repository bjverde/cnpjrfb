<?php

class SocioViewForm extends TPage
{
    protected $form; // registration form
    protected $datagrid; // listing
    
    // trait com onReload, onSearch, onDelete...
    use Adianti\Base\AdiantiStandardListTrait;

    public function __construct()
    {
        parent::__construct();

        $this->adianti_target_container = 'adianti_right_panel';

        $this->form = new BootstrapFormBuilder(__CLASS__);
        $this->form->setFormTitle('Sócio');
        $this->form->generateAria(); // automatic aria-label

        $tipoSocioControler = new TipoSocio();
        $listipoSocio = $tipoSocioControler->getList();

        $cnpjLabel = 'CNPJ';
        $formDinCnpjField = new TFormDinCnpjField('cnpj',$cnpjLabel);
        $cnpj = $formDinCnpjField->getAdiantiObj();
        
        $tipo_socio = new TCombo('tipo_socio');
        $tipo_socio->addItems($listipoSocio);
        $nome_socio = new TEntry('nome_socio');


        // add the table inside the page
        parent::add($this->form);
    }

    function onView($param)
    {
        try{
            // abre transação com a base de dados
            TTransaction::open('cnpj_full');
            //$empresa = new Empresa($param['key']);
            //$this->form->setData($empresa);
            //$this->form->addActionLink('Fechar',  new TAction([$this, 'onClose']), 'fa:times red');
            TTransaction::close(); // fecha a transação
        }
        catch(Exception $e)
        {
            new TMessage('error', $e->getMessage());
        }
    }
}