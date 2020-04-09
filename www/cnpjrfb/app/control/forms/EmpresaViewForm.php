<?php

use Adianti\Registry\TSession;

class EmpresaViewForm extends TPage
{
    protected $form; // registration form
    protected $datagrid; // listing
    
    // trait com onReload, onSearch, onDelete...
    use Adianti\Base\AdiantiStandardListTrait;

    function __construct()
    {
        parent::__construct();

        $this->adianti_target_container = 'adianti_right_panel';

        $this->form = new BootstrapFormBuilder;
        $this->form->setFormTitle('Detalhar Empresa');
        $this->form->generateAria(); // automatic aria-label


        

        $this->form->addHeaderActionLink('Fechar',  new TAction([$this, 'onClose']), 'fa:times red');
        

        // add the table inside the page
        parent::add($this->form);
    }

    public static function onClose($param)
    {
        TScript::create("Template.closeRightPanel()");
    }

    function onView($param)
    {
        try{
            // abre transação com a base de dados
            TTransaction::open('cnpj_full');
            $empresa = new Empresa($param['key']);
    
            $this->form->addFields( [new TLabel('CNPJ')],[new TTextDisplay(StringHelper::formatCnpjCpf($empresa->cnpj))], [new TLabel('Matriz')],[new TTextDisplay(TipoMatrizFilial::getByid($empresa->matriz_filial))]);
            $this->form->addFields( [new TLabel('Razão Social')],[new TTextDisplay($empresa->razao_social)]);
            $this->form->addFields( [new TLabel('Nome Fantasia')],[new TTextDisplay($empresa->nome_fantasia)]);
            $this->form->addFields( [new TLabel('Situação')],[new TTextDisplay(TipoEmpresaSituacao::getByid($empresa->situacao))]
                                  , [new TLabel('Motico Situação')],[new TTextDisplay(SituacaoCadastralEmpresa::getByid($empresa->motivo_situacao))]
                                  );
            $this->form->addFields( [new TLabel('Dat inicio Atividade')],[new TTextDisplay($empresa->data_inicio_ativ)]
                                  , [new TLabel('Dat Situação')],[new TTextDisplay($empresa->data_situacao)]
                                  );
            $this->form->addFields( [new TLabel('Cod Nat Juridica')],[new TTextDisplay($empresa->cod_nat_juridica)]);
            
            $cnae = new TTextDisplay(EmpresaController::getLink($empresa->cnae_fiscal));
            $this->form->addFields( [new TLabel('CNAE (Link IBGE)')],[$cnae]);
            $this->form->addFields( [new TLabel('Tip Lograd')],[new TTextDisplay($empresa->tipo_logradouro)],[new TLabel('Logradouro')],[new TTextDisplay($empresa->logradouro)]);

            $this->form->addFields( [new TLabel('Número')],[new TTextDisplay($empresa->numero)]);
            $this->form->addFields( [new TLabel('Complemento')],[new TTextDisplay($empresa->complemento)]);
            $this->form->addFields( [new TLabel('Bairro')],[new TTextDisplay($empresa->bairro)],[new TLabel('CEP')],[new TTextDisplay($empresa->cep)]);
    

            $this->form->addFields( [new TLabel('UF')],[new TTextDisplay($empresa->uf)], [new TLabel('Cod Município')],[new TTextDisplay($empresa->cod_municipio)],[new TLabel('Município')],[new TTextDisplay($empresa->municipio)]);
            
            
            $this->form->addFields( [new TLabel('DDD1')],[new TTextDisplay($empresa->ddd_1)],[new TLabel('Telefone1')],[new TTextDisplay($empresa->telefone_1)]);
            $this->form->addFields( [new TLabel('DDD2')],[new TTextDisplay($empresa->ddd_2)],[new TLabel('Telefone2')],[new TTextDisplay($empresa->telefone_2)]);
            $this->form->addFields( [new TLabel('DDD Fax')],[new TTextDisplay($empresa->ddd_fax)],[new TLabel('Fax')],[new TTextDisplay($empresa->num_fax)]);

            $this->form->addFields( [new TLabel('E-mail')],[new TTextDisplay($empresa->email)]);
            $this->form->addFields( [new TLabel('CNPJ')],[new TTextDisplay($empresa->qualif_resp)]);
            $this->form->addFields( [new TLabel('Capital Social')],[new TTextDisplay($empresa->capital_social)]);
            $this->form->addFields( [new TLabel('Porte')],[new TTextDisplay($empresa->porte)]
                                  , [new TLabel('Opção MEI')],[new TTextDisplay($empresa->opc_mei)]
                                  );
            $this->form->addFields( [new TLabel('Opção simples')],[new TTextDisplay($empresa->opc_simples)]
                                  , [new TLabel('Dat Opção simples')],[new TTextDisplay($empresa->data_opc_simples)]
                                  , [new TLabel('Dat exc Simples')],[new TTextDisplay($empresa->data_exc_simples)]
                                  );
            $this->form->addFields( [new TLabel('Sit Especial')],[new TTextDisplay($empresa->sit_especial)], [new TLabel('Dat Sit Especial')],[new TTextDisplay($empresa->data_sit_especial)]);

            $this->form->addFields( [new TLabel('Cod País')],[new TTextDisplay($empresa->cod_pais)]
                                  , [new TLabel('País')],[new TTextDisplay($empresa->nome_pais)]
                                  );
            $this->form->addFields( [new TLabel('Nome Cidade Exterior')],[new TTextDisplay($empresa->nm_cidade_exterior)] );


            $this->showGridSocios($empresa->getSocios());
            $this->showGridCnae($empresa->getCnaesSecundarios());
            $this->form->addActionLink('Fechar',  new TAction([$this, 'onClose']), 'fa:times red');
            TTransaction::close(); // fecha a transação
        }
        catch(Exception $e)
        {
            new TMessage('error', $e->getMessage());
        }
    }

    function showGridSocios($socios){
        // create the datagrid
        $listSocios = new BootstrapDatagridWrapper(new TDataGrid);
        $listSocios->width = '100%';    
        $listSocios->addColumn(new TDataGridColumn('nome_socio', 'Nome', 'left'));
        $listSocios->addColumn(new TDataGridColumn('cnpj_cpf_socio', 'CPF', 'left'));

        $action1 = new TDataGridAction(['SocioViewForm', 'onView'],  ['cnpj_cpf_socio' => '{cnpj_cpf_socio}','nome_socio' => '{nome_socio}'], ['register_state' => 'false']  );
        $listSocios->addAction($action1, 'Detalhar Sócio', 'fa:user green');

        $listSocios->createModel();
        $listSocios->addItems($socios);
        $panel = TPanelGroup::pack('Lista de Socios', $listSocios);
        parent::add($panel);
    }

    function showGridCnae($cnae){
        // create the datagrid
        $list = new BootstrapDatagridWrapper(new TDataGrid);
        $list->width = '100%';

        $col_cnae_ibge   = new TDataGridColumn('cnae', 'CNAE (link IBGE)', 'left');
        $col_cnae_ibge->setTransformer( function ($value) {
            return EmpresaController::getLink($value);
        });
        $col_cnae_coube  = new TDataGridColumn('cnae', 'CNAE (link Conube)', 'left');
        $col_cnae_coube->setTransformer( function ($value) {
            return EmpresaController::getLink($value,false);
        });

        $list->addColumn(new TDataGridColumn('cnae_ordem', 'CNAE Ordem', 'left'));
        $list->addColumn($col_cnae_ibge);
        $list->addColumn($col_cnae_coube);
        $list->createModel();        
        $list->addItems($cnae);
        $panel = TPanelGroup::pack('Lista de CNAE', $list);
        parent::add($panel);
    }
}
