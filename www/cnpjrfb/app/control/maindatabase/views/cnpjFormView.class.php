<?php

class cnpjFormView extends TPage
{
    protected $form; // form
    private static $database = 'maindatabase';
    private static $activeRecord = 'Estabelecimento';
    private static $primaryKey = 'id';
    private static $formName = 'formView_Estabelecimento';

    /**
     * Form constructor
     * @param $param Request
     */
    public function __construct( $param )
    {
        parent::__construct();

        if(!empty($param['target_container']))
        {
            $this->adianti_target_container = $param['target_container'];
        }

        TTransaction::open(self::$database);
        // creates the form
        $this->form = new BootstrapFormBuilder(self::$formName);
        $this->form->setTagName('div');

        $estabelecimento = new Estabelecimento($param['key']);
        // define the form title
        $this->form->setFormTitle("Ficha CNPJ");

        $label1 = new TLabel("Cnpj basico:", '#333', '12px', '');
        $text1 = new TTextDisplay($estabelecimento->cnpj_basico, '#333', '12px', '');
        $label2 = new TLabel("Id:", '#333', '12px', '');
        $text2 = new TTextDisplay($estabelecimento->id, '#333', '12px', '');
        $label3 = new TLabel("Cnpj ordem:", '#333', '12px', '');
        $text3 = new TTextDisplay($estabelecimento->cnpj_ordem, '#333', '12px', '');
        $label4 = new TLabel("Cnpj dv:", '#333', '12px', '');
        $text4 = new TTextDisplay($estabelecimento->cnpj_dv, '#333', '12px', '');
        $label5 = new TLabel("Identificador matriz filial:", '#333', '12px', '');
        $text5 = new TTextDisplay($estabelecimento->identificador_matriz_filial, '#333', '12px', '');
        $label6 = new TLabel("Nome fantasia:", '#333', '12px', '');
        $text6 = new TTextDisplay($estabelecimento->nome_fantasia, '#333', '12px', '');
        $label7 = new TLabel("Situacao cadastral:", '#333', '12px', '');
        $text7 = new TTextDisplay($estabelecimento->situacao_cadastral, '#333', '12px', '');
        $label8 = new TLabel("Data situacao cadastral:", '#333', '12px', '');
        $text8 = new TTextDisplay(TDate::convertToMask($estabelecimento->data_situacao_cadastral, 'yyyy-mm-dd', 'dd/mm/yyyy'), '#333', '12px', '');
        $label9 = new TLabel("Motivo situacao cadastral:", '#333', '12px', '');
        $text9 = new TTextDisplay($estabelecimento->motivo_situacao_cadastral, '#333', '12px', '');
        $label10 = new TLabel("Nome cidade exterior:", '#333', '12px', '');
        $text10 = new TTextDisplay($estabelecimento->nome_cidade_exterior, '#333', '12px', '');
        $label11 = new TLabel("Pais:", '#333', '12px', '');
        $text11 = new TTextDisplay($estabelecimento->pais, '#333', '12px', '');
        $label12 = new TLabel("Data inicio atividade:", '#333', '12px', '');
        $text12 = new TTextDisplay(TDateTime::convertToMask($estabelecimento->data_inicio_atividade, 'yyyy-mm-dd hh:ii', 'dd/mm/yyyy hh:ii'), '#333', '12px', '');
        $label13 = new TLabel("Cnae fiscal principal:", '#333', '12px', '');
        $text13 = new TTextDisplay($estabelecimento->cnae_fiscal_principal, '#333', '12px', '');
        $label14 = new TLabel("Cnae fiscal secundaria:", '#333', '12px', '');
        $text14 = new TTextDisplay($estabelecimento->cnae_fiscal_secundaria, '#333', '12px', '');
        $label15 = new TLabel("Tipo logradouro:", '#333', '12px', '');
        $text15 = new TTextDisplay($estabelecimento->tipo_logradouro, '#333', '12px', '');
        $label16 = new TLabel("Logradouro:", '#333', '12px', '');
        $text16 = new TTextDisplay($estabelecimento->logradouro, '#333', '12px', '');
        $label17 = new TLabel("Numero:", '#333', '12px', '');
        $text17 = new TTextDisplay($estabelecimento->numero, '#333', '12px', '');
        $label18 = new TLabel("Complemento:", '#333', '12px', '');
        $text18 = new TTextDisplay($estabelecimento->complemento, '#333', '12px', '');
        $label19 = new TLabel("Bairro:", '#333', '12px', '');
        $text19 = new TTextDisplay($estabelecimento->bairro, '#333', '12px', '');
        $label20 = new TLabel("Cep:", '#333', '12px', '');
        $text20 = new TTextDisplay($estabelecimento->cep, '#333', '12px', '');
        $label21 = new TLabel("Uf:", '#333', '12px', '');
        $text21 = new TTextDisplay($estabelecimento->uf, '#333', '12px', '');
        $label22 = new TLabel("Municipio:", '#333', '12px', '');
        $text22 = new TTextDisplay($estabelecimento->municipio, '#333', '12px', '');
        $label23 = new TLabel("Ddd 1:", '#333', '12px', '');
        $text23 = new TTextDisplay($estabelecimento->ddd_1, '#333', '12px', '');
        $label24 = new TLabel("Telefone 1:", '#333', '12px', '');
        $text24 = new TTextDisplay($estabelecimento->telefone_1, '#333', '12px', '');
        $label25 = new TLabel("Ddd 2:", '#333', '12px', '');
        $text25 = new TTextDisplay($estabelecimento->ddd_2, '#333', '12px', '');
        $label26 = new TLabel("Telefone 2:", '#333', '12px', '');
        $text26 = new TTextDisplay($estabelecimento->telefone_2, '#333', '12px', '');
        $label27 = new TLabel("Ddd fax:", '#333', '12px', '');
        $text27 = new TTextDisplay($estabelecimento->ddd_fax, '#333', '12px', '');
        $label28 = new TLabel("Fax:", '#333', '12px', '');
        $text28 = new TTextDisplay($estabelecimento->fax, '#333', '12px', '');
        $label29 = new TLabel("Correio eletronico:", '#333', '12px', '');
        $text29 = new TTextDisplay($estabelecimento->correio_eletronico, '#333', '12px', '');
        $label30 = new TLabel("Situacao especial:", '#333', '12px', '');
        $text30 = new TTextDisplay($estabelecimento->situacao_especial, '#333', '12px', '');
        $label31 = new TLabel("Data situacao especial:", '#333', '12px', '');
        $text31 = new TTextDisplay(TDate::convertToMask($estabelecimento->data_situacao_especial, 'yyyy-mm-dd', 'dd/mm/yyyy'), '#333', '12px', '');


        $row1 = $this->form->addFields([$label1],[$text1]);
        $row2 = $this->form->addFields([$label2],[$text2]);
        $row3 = $this->form->addFields([$label3],[$text3]);
        $row4 = $this->form->addFields([$label4],[$text4]);
        $row5 = $this->form->addFields([$label5],[$text5]);
        $row6 = $this->form->addFields([$label6],[$text6]);
        $row7 = $this->form->addFields([$label7],[$text7]);
        $row8 = $this->form->addFields([$label8],[$text8]);
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

        $btn_oncloseAction = new TAction([$this, 'onClose'],['key'=>$estabelecimento->id]);
        $btn_oncloseLabel = new TLabel("Fechar");
        $btn_oncloseLabel->setFontSize('12px'); 
        $btn_oncloseLabel->setFontColor('#333'); 

        $btn_onclose = $this->form->addHeaderAction($btn_oncloseLabel, $btn_oncloseAction, 'fas:window-close #F44336'); 

        parent::setTargetContainer('adianti_right_panel');

        $btnClose = new TButton('closeCurtain');
        $btnClose->class = 'btn btn-sm btn-default';
        $btnClose->style = 'margin-right:10px;';
        $btnClose->onClick = "Template.closeRightPanel();";
        $btnClose->setLabel("Fechar");
        $btnClose->setImage('fas:times');

        $this->form->addHeaderWidget($btnClose);

        parent::add($this->form);

    }

    public static function onClose($param = null) 
    {
        try 
        {
            TScript::create("Template.closeRightPanel()");

        }
        catch (Exception $e) 
        {
            new TMessage('error', $e->getMessage());    
        }
    }

    public function onView($param = null)
    {     
        try{
            $cnpj_cpf_socio = $param['cnpj_cpf_socio'];
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