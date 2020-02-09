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

        $this->form->addFields( [new TLabel('CNPJ')],[$cnpj],[new TLabel('Tipo Sócio')],[$tipo_socio]);
        $this->form->addFields( [new TLabel('Nome')],[$nome_socio]);

        // add the table inside the page
        parent::add($this->form);
    }

    function onView($param)
    {
        try{
            var_dump($param);

            $cnpj_cpf_socio = $param['cnpj_cpf_socio'];
            $nome_socio = $param['nome_socio'];

            $socioController = new SocioController();
            $socio = $socioController->selectBySocio($cnpj_cpf_socio,$nome_socio);
            var_dump($socio);
            $this->form->setData($socio);
            //$this->showGridSocios($empresa->getSocios());
        }
        catch(Exception $e)
        {
            new TMessage('error', $e->getMessage());
        }
    }
    
    public function showGridEmpresa($listSocio){
        $empresaController = new EmpresaController();
        $listEmpresa = $empresaController->selectBySocio($listSocio);
        $panel = $empresaController->getGridEmpresa($listEmpresa);
        parent::add($panel);
    }
}