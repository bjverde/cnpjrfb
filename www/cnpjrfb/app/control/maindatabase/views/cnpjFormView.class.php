<?php

class cnpjFormView extends TPage
{
    protected $form; // form
    private static $database = 'maindatabase';
    private static $formName = 'formView_Estabelecimento';

    /**
     * Form constructor
     * @param $param Request
     */
    public function __construct( $param )
    {
        parent::__construct();
        parent::setTargetContainer('adianti_right_panel');

        if(!empty($param['target_container']))
        {
            $this->adianti_target_container = $param['target_container'];
        }

        $this->form = new BootstrapFormBuilder(__CLASS__);            
        $this->form->generateAria(); // automatic aria-label
        $this->form->addHeaderActionLink('Fechar',  new TAction([$this, 'onClose']), 'fa:times red');
        $this->form->addActionLink('Fechar',  new TAction([$this, 'onClose']), 'fa:times red');
        $this->form->setFormTitle("Ficha CNPJ");
    }

    public static function onClose($param = null) 
    {
        TScript::create("Template.closeRightPanel()");
    }

    public function onView($param = null)
    {     
        try{
            $cnpj_basico = ArrayHelper::getArray($param,'cnpj_basico');

            TTransaction::open(self::$database);
            $estabelecimento = estabelecimento::find($cnpj_basico);
            $empresa = empresa::find($cnpj_basico);
            
            if( empty($estabelecimento) ){
                $this->form->addFields( [new TLabel('Mensagem')],[new TTextDisplay('ERRO ao carregar Estabelecimento')]);
                $this->form->addFields( [new TLabel('CNPJ Básico')],[new TTextDisplay($cnpj_basico)]);
            }else{
                $cnpj = $estabelecimento->cnpj_basico.$estabelecimento->cnpj_ordem.$estabelecimento->cnpj_dv;
                $cnpj = StringHelper::formatCnpjCpf($cnpj);
        
                $this->form->addFields([new TLabel("CNPJ:")],[new TTextDisplay($cnpj)]
                                      ,[new TLabel("Matriz/Filial:")],[new TTextDisplay( TipoMatrizFilial::getByid($estabelecimento->identificador_matriz_filial) )]
                                      );
                $this->form->addFields([new TLabel("Razão social:")],[new TTextDisplay($empresa->razao_social)]                                  
                                    );                                  
                $this->form->addFields([new TLabel("Nome fantasia:")],[new TTextDisplay($estabelecimento->nome_fantasia)]                                  
                                      );
                $this->form->addFields([new TLabel("Porte empresa:")],[new TTextDisplay( TipoPorteEmpresa::getByid($empresa->porte_empresa) )]
                                      ,[new TLabel("Natureza jurídica:")],[new TTextDisplay($empresa->fk_natureza_juridica->descricao)]
                                      );
                $this->form->addFields([new TLabel("Capital social:")],[new TTextDisplay( Transforme::numeroBrasil($empresa->capital_social) )]
                                      ,[new TLabel("Qualificação responsável:")],[new TTextDisplay($empresa->fk_qualificacao_responsavel->descricao)]
                                      );                                  
                $this->form->addFields([new TLabel("Data situação cadastral:")],[new TTextDisplay( Transforme::date($estabelecimento->data_situacao_cadastral) )]
                                      ,[new TLabel("Situação cadastral:")],[new TTextDisplay( $estabelecimento->situacao_cadastral )]
                                      );
                $this->showEmpresaSimples($cnpj_basico);                
                $this->form->addContent([new TFormSeparator("Endereço", '#333', '18', '#eee')]);
    
                $this->form->addFields([new TLabel("Tipo logradouro:", null, '14px', null)],[$estabelecimento->tipo_logradouro]
                                      ,[new TLabel("Logradouro:", null, '14px', null)],[$estabelecimento->logradouro]);
                $this->form->addFields([new TLabel("Numero:", null, '14px', null)],[$estabelecimento->numero]
                                      ,[new TLabel("Complemento:", null, '14px', null)],[$estabelecimento->complemento]);
                $this->form->addFields([new TLabel("Bairro:", null, '14px', null)],[$estabelecimento->bairro]
                                      ,[new TLabel("Cep:", null, '14px', null)],[$estabelecimento->cep]);
                $municipio = munic::find($estabelecimento->municipio);
                $municipioNome = empty($municipio)?' Municipio código: '.$estabelecimento->municipio.' não encontrado':$municipio->descricao;
                $this->form->addFields([new TLabel("Uf:", null, '14px', null)],[$estabelecimento->uf]
                                      ,[new TLabel("Municipio:", null, '14px', null)],[$municipioNome]);
                $this->form->addFields([new TLabel("Ddd 1:", null, '14px', null)],[$estabelecimento->ddd_1]
                                      ,[new TLabel("Telefone 1:", null, '14px', null)],[$estabelecimento->telefone_1]);
                $this->form->addFields([new TLabel("Ddd 2:", null, '14px', null)],[$estabelecimento->ddd_2]
                                      ,[new TLabel("Telefone 2:", null, '14px', null)],[$estabelecimento->telefone_2]);
                $this->form->addFields([new TLabel("Ddd fax:", null, '14px', null)],[$estabelecimento->ddd_fax]
                                      ,[new TLabel("Fax:", null, '14px', null)],[$estabelecimento->fax]);
                $this->form->addFields([new TLabel("Correio eletronico:", null, '14px', null)],[$estabelecimento->correio_eletronico]
                                      ,[new TLabel("Situação especial:", null, '14px', null)],[$estabelecimento->situacao_especial]);
            }
            TTransaction::close(); // fecha a transação.
            parent::add($this->form);            
            $this->showGridSocios($cnpj_basico);
            //$this->showGridCnae($empresa->getCnaesSecundarios());
        }
        catch(Exception $e)
        {
            new TMessage('error', $e->getMessage());
        }
    }
    public function showEmpresaSimples($cnpj_basico){
        $simples = simples::find($cnpj_basico);
        
        $this->form->addContent([new TFormSeparator("Simples", '#333', '18', '#eee')]);
        if( empty($simples) ){
            $this->form->addFields( [new TLabel('Mensagem')],[new TTextDisplay('ERRO ao carregar informações sobre o simples')]);
        }else{
            $this->form->addFields([new TLabel("Opção pelo simples:", null, '14px', null)],[ new TTextDisplay( Transforme::simNao($simples->opcao_pelo_simples) ) ]);
            $this->form->addFields([new TLabel("Data opção simples:", null, '14px', null)],[ new TTextDisplay( Transforme::date($simples->data_opcao_simples) ) ]
                                  ,[new TLabel("Data exclusão simples:", null, '14px', null)],[ new TTextDisplay( Transforme::date($simples->data_exclusao_simples) ) ]
                                  );
            $this->form->addFields([new TLabel("Opção MEI:", null, '14px', null)],[ new TTextDisplay( Transforme::simNao($simples->opcao_mei) ) ]);
            $this->form->addFields([new TLabel("Data opção MEI:", null, '14px', null)],[ new TTextDisplay( Transforme::date($simples->data_opcao_mei) ) ]
                                  ,[new TLabel("Data exclusão MEI:", null, '14px', null)],[ new TTextDisplay( Transforme::date($simples->data_exclusao_mei) ) ]);
        }
    }

    public function showGridSocios($cnpj_basico){
        $sociosController = new SociosController();
        $listSocios = $sociosController->selectBySocioAdianti($cnpj_basico, null,null);

        // create the datagrid
        $gridSocios = new BootstrapDatagridWrapper(new TDataGrid);
        $gridSocios->width = '100%';    
        $gridSocios->addColumn(new TDataGridColumn('cpf_cnpj_socio', "CPF/CNPJ", 'left'));
        $gridSocios->addColumn(new TDataGridColumn('nome_socio_razao_social', "Nome socio razao social", 'left'));
        $gridSocios->addColumn(new TDataGridColumn('identificador_socio', "Tip Sócio", 'left'));

        $actionSocioView = Transforme::getDataGridActionDetalharSocio();
        $gridSocios->addAction($actionSocioView);
        
        $gridSocios->createModel();
        $gridSocios->addItems($listSocios);
        $panel = TPanelGroup::pack('Lista de Socios', $gridSocios);
        parent::add($panel); 
    }

    public function showGridCnae($cnae){
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