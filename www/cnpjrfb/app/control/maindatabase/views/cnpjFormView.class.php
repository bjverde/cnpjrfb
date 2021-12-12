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
                $estabelecimento = new estabelecimento($cnpj_basico);
                $empresa = new empresa($cnpj_basico);
            

            $cnpj = $estabelecimento->cnpj_basico.$estabelecimento->cnpj_ordem.$estabelecimento->cnpj_dv;
            $cnpj = StringHelper::formatCnpjCpf($cnpj);
    

            $this->form->addFields([new TLabel("CNPJ:")],[new TTextDisplay($cnpj)]
                                  ,[new TLabel("Matriz/Filial:")],[new TTextDisplay( TipoMatrizFilial::getByid($estabelecimento->identificador_matriz_filial) )]
                                  );
            $this->form->addFields([new TLabel("Razao social:")],[new TTextDisplay($empresa->razao_social)]                                  
                                );                                  
            $this->form->addFields([new TLabel("Nome fantasia:")],[new TTextDisplay($estabelecimento->nome_fantasia)]                                  
                                  );
            $this->form->addFields([new TLabel("Porte empresa:")],[new TTextDisplay($empresa->porte_empresa)]
                                  ,[new TLabel("Natureza juridica:")],[new TTextDisplay($empresa->fk_natureza_juridica->descricao)]
                                  );
            $this->form->addFields([new TLabel("Capital social:")],[new TTextDisplay($empresa->capital_social)]
                                  ,[new TLabel("Qualificacao responsavel:")],[new TTextDisplay($empresa->fk_qualificacao_responsavel->descricao)]
                                  );                                  
            $this->form->addFields([new TLabel("Data situacao cadastral:")],[new TTextDisplay(TDate::convertToMask($estabelecimento->data_situacao_cadastral, 'yyyy-mm-dd', 'dd/mm/yyyy'))]
                                  ,[new TLabel("Situacao cadastral:")],[new TTextDisplay( $estabelecimento->situacao_cadastral )]
                                  );

            $this->form->addContent([new TFormSeparator("Endereço", '#333', '18', '#eee')]);

            $this->form->addFields([new TLabel("Tipo logradouro:", null, '14px', null)],[$estabelecimento->tipo_logradouro]
                                  ,[new TLabel("Logradouro:", null, '14px', null)],[$estabelecimento->logradouro]);
            $this->form->addFields([new TLabel("Numero:", null, '14px', null)],[$estabelecimento->numero]
                                  ,[new TLabel("Complemento:", null, '14px', null)],[$estabelecimento->complemento]);
            $this->form->addFields([new TLabel("Bairro:", null, '14px', null)],[$estabelecimento->bairro]
                                  ,[new TLabel("Cep:", null, '14px', null)],[$estabelecimento->cep]);
            $this->form->addFields([new TLabel("Uf:", null, '14px', null)],[$estabelecimento->uf]
                                  ,[new TLabel("Municipio:", null, '14px', null)],[$estabelecimento->municipio]);
            $this->form->addFields([new TLabel("Ddd 1:", null, '14px', null)],[$estabelecimento->ddd_1]
                                  ,[new TLabel("Telefone 1:", null, '14px', null)],[$estabelecimento->telefone_1]);
            $this->form->addFields([new TLabel("Ddd 2:", null, '14px', null)],[$estabelecimento->ddd_2]
                                  ,[new TLabel("Telefone 2:", null, '14px', null)],[$estabelecimento->telefone_2]);
            $this->form->addFields([new TLabel("Ddd fax:", null, '14px', null)],[$estabelecimento->ddd_fax]
                                  ,[new TLabel("Fax:", null, '14px', null)],[$estabelecimento->fax]);
            $this->form->addFields([new TLabel("Correio eletronico:", null, '14px', null)],[$estabelecimento->correio_eletronico]
                                  ,[new TLabel("Situacao especial:", null, '14px', null)],[$estabelecimento->situacao_especial]);

            TTransaction::close(); // fecha a transação.
            parent::add($this->form);            
        }
        catch(Exception $e)
        {
            new TMessage('error', $e->getMessage());
        }
    }

    public function onShow($param = null)
    {     

    }
}