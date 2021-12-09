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
                $estabelecimento = new Estabelecimento($cnpj_basico);
            TTransaction::close(); // fecha a transação.

            $cnpj = $estabelecimento->cnpj_basico.$estabelecimento->cnpj_ordem.$estabelecimento->cnpj_dv;
            $cnpj = StringHelper::formatCnpjCpf($cnpj);
    

            $this->form->addFields([new TLabel("CNPJ:")],[new TTextDisplay($cnpj)]
                                  ,[new TLabel("Matriz/Filial:")],[new TTextDisplay( TipoMatrizFilial::getByid($estabelecimento->identificador_matriz_filial) )]
                                  );
            $this->form->addFields([new TLabel("Nome fantasia:")],[new TTextDisplay($estabelecimento->nome_fantasia)]                                  
                                  );
            $this->form->addFields([new TLabel("Data situacao cadastral:")],[new TTextDisplay(TDate::convertToMask($estabelecimento->data_situacao_cadastral, 'yyyy-mm-dd', 'dd/mm/yyyy'))]
                                  ,[new TLabel("Situacao cadastral:")],[new TTextDisplay( TipoEmpresaSituacao::getByid($estabelecimento->situacao_cadastral) )]
                                  );
            $label9 = new TLabel("Motivo situacao cadastral:");
            $text9 = new TTextDisplay($estabelecimento->motivo_situacao_cadastral);
            $label10 = new TLabel("Nome cidade exterior:");
            $text10 = new TTextDisplay($estabelecimento->nome_cidade_exterior);
            $label11 = new TLabel("Pais:");
            $text11 = new TTextDisplay($estabelecimento->pais);
            $label12 = new TLabel("Data inicio atividade:");
            $text12 = new TTextDisplay(TDateTime::convertToMask($estabelecimento->data_inicio_atividade, 'yyyy-mm-dd hh:ii', 'dd/mm/yyyy hh:ii'));
            $label13 = new TLabel("Cnae fiscal principal:");
            $text13 = new TTextDisplay($estabelecimento->cnae_fiscal_principal);
            $label14 = new TLabel("Cnae fiscal secundaria:");
            $text14 = new TTextDisplay($estabelecimento->cnae_fiscal_secundaria);
            $label15 = new TLabel("Tipo logradouro:");
            $text15 = new TTextDisplay($estabelecimento->tipo_logradouro);
            $label16 = new TLabel("Logradouro:");
            $text16 = new TTextDisplay($estabelecimento->logradouro);
            $label17 = new TLabel("Numero:");
            $text17 = new TTextDisplay($estabelecimento->numero);
            $label18 = new TLabel("Complemento:");
            $text18 = new TTextDisplay($estabelecimento->complemento);
            $label19 = new TLabel("Bairro:");
            $text19 = new TTextDisplay($estabelecimento->bairro);
            $label20 = new TLabel("Cep:");
            $text20 = new TTextDisplay($estabelecimento->cep);
            $label21 = new TLabel("Uf:");
            $text21 = new TTextDisplay($estabelecimento->uf);
            $label22 = new TLabel("Municipio:");
            $text22 = new TTextDisplay($estabelecimento->municipio);
            $label23 = new TLabel("Ddd 1:");
            $text23 = new TTextDisplay($estabelecimento->ddd_1);
            $label24 = new TLabel("Telefone 1:");
            $text24 = new TTextDisplay($estabelecimento->telefone_1);
            $label25 = new TLabel("Ddd 2:");
            $text25 = new TTextDisplay($estabelecimento->ddd_2);
            $label26 = new TLabel("Telefone 2:");
            $text26 = new TTextDisplay($estabelecimento->telefone_2);
            $label27 = new TLabel("Ddd fax:");
            $text27 = new TTextDisplay($estabelecimento->ddd_fax);
            $label28 = new TLabel("Fax:");
            $text28 = new TTextDisplay($estabelecimento->fax);
            $label29 = new TLabel("Correio eletronico:");
            $text29 = new TTextDisplay($estabelecimento->correio_eletronico);
            $label30 = new TLabel("Situacao especial:");
            $text30 = new TTextDisplay($estabelecimento->situacao_especial);
            $label31 = new TLabel("Data situacao especial:");
            $text31 = new TTextDisplay(TDate::convertToMask($estabelecimento->data_situacao_especial, 'yyyy-mm-dd', 'dd/mm/yyyy'));
    
    
            $row9 = $this->form->addFields([$label9],[$text9]);
            $row10 = $this->form->addFields([$label10],[$text10]);
            $row11 = $this->form->addFields([$label11],[$text11]);
            $row12 = $this->form->addFields([$label12],[$text12]);
            $row13 = $this->form->addFields([$label13],[$text13]);
            $row14 = $this->form->addFields([$label14],[$text14]);
            $row15 = $this->form->addFields([$label15],[$text15]);
            $row16 = $this->form->addFields([$label16],[$text16]);
            $row17 = $this->form->addFields([$label17],[$text17]);
            $row18 = $this->form->addFields([$label18],[$text18]);
            $row19 = $this->form->addFields([$label19],[$text19]);
            $row20 = $this->form->addFields([$label20],[$text20]);
            $row21 = $this->form->addFields([$label21],[$text21]);
            $row22 = $this->form->addFields([$label22],[$text22]);
            $row23 = $this->form->addFields([$label23],[$text23]);
            $row24 = $this->form->addFields([$label24],[$text24]);
            $row25 = $this->form->addFields([$label25],[$text25]);
            $row26 = $this->form->addFields([$label26],[$text26]);
            $row27 = $this->form->addFields([$label27],[$text27]);
            $row28 = $this->form->addFields([$label28],[$text28]);
            $row29 = $this->form->addFields([$label29],[$text29]);
            $row30 = $this->form->addFields([$label30],[$text30]);
            $row31 = $this->form->addFields([$label31],[$text31]);

            
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