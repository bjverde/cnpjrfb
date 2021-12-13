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
        
    }

    public static function onClose($param)
    {
        TScript::create("Template.closeRightPanel()");
    }

    public function onView($param)
    {
        try{
            $cnpj_basico = ArrayHelper::getArray($param,'cnpj_basico');
            $nome_socio_razao_social = ArrayHelper::getArray($param,'nome_socio_razao_social');
            $cpf_cnpj_socio = ArrayHelper::getArray($param,'cpf_cnpj_socio');

            $sociosController = new SociosController();
            $socio = $sociosController->selectBySocio($cnpj_basico,$nome_socio_razao_social);            

            $this->form = new BootstrapFormBuilder(__CLASS__);            
            $this->form->generateAria(); // automatic aria-label
            $this->form->addHeaderActionLink('Fechar',  new TAction([$this, 'onClose']), 'fa:times red');
            $this->form->addActionLink('Fechar',  new TAction([$this, 'onClose']), 'fa:times red');
        

            if( empty($socio) ){
                $this->form->setFormTitle('Dados do Sócio na empresa');
                $this->form->addFields( [new TLabel('Nome')],[new TTextDisplay('ERRO ao carregar socio')]);
                parent::add($this->form);
            }else{
                $estabelecimentoController = new EstabelecimentoController();
                $listEstabelecimento = $estabelecimentoController->selectById($cnpj_basico);
                $estabelecimento = ArrayHelper::getArray($listEstabelecimento,0);
                $cnpj =  $estabelecimento->CNPJ_BASICO.$estabelecimento->CNPJ_ORDEM.$estabelecimento->CNPJ_DV;
                $cnpjEmpresa = StringHelper::formatCnpjCpf($cnpj);

                $this->form->setFormTitle('Dados do Sócio na empresa: '.$cnpjEmpresa);                
                $this->form->addFields( [new TLabel('CNPJ')],[new TTextDisplay($cnpjEmpresa)]);

                $this->form->addContent([new TFormSeparator("Sócio", '#333', '18', '#eee')]);
                $this->form->addFields( [new TLabel('Nome')],[new TTextDisplay($socio->NOME_SOCIO_RAZAO_SOCIAL)]
                                      , [new TLabel('CPF')],[new TTextDisplay($socio->CPF_CNPJ_SOCIO)]
                                      ) ;
                $tipoSocio = new TTextDisplay(TipoSocio::getByid($socio->IDENTIFICADOR_SOCIO));
                $tipoSocioQualificacao = new TTextDisplay(TipoSocioQualificacao::getByid($socio->QUALIFICACAO_SOCIO));            
                $this->form->addFields( [new TLabel('Tipo Sócio')],[$tipoSocio]
                                      , [new TLabel('Qualificação')],[$tipoSocioQualificacao]
                                      );
                $this->form->addFields( [new TLabel('País')],[new TTextDisplay($socio->PAIS)]
                                      , [new TLabel('Data Entrada')],[new TTextDisplay($socio->DATA_ENTRADA_SOCIEDADE)]
                                      );
                $this->form->addFields( [new TLabel('Faixa Etária')],[new TTextDisplay($socio->FAIXA_ETARIA)]);

                $this->form->addContent([new TFormSeparator("Representante Legal", '#333', '18', '#eee')]);
                $this->form->addFields( [new TLabel('CPF')],[new TTextDisplay($socio->REPRESENTANTE_LEGAL)]
                                      , [new TLabel('Nome')],[new TTextDisplay($socio->NOME_DO_REPRESENTANTE)]
                                      );
                $this->form->addFields( [new TLabel('Qualificação')],[new TTextDisplay($socio->QUALIFICACAO_REPRESENTANTE_LEGAL)]);

                // add the table inside the page
                parent::add($this->form);


                $listEmpresasSocio = $sociosController->selectBySocioAdianti(null, $nome_socio_razao_social,$cpf_cnpj_socio);
                $panel = $this->getGridEmpresa($listEmpresasSocio);
                parent::add($panel);
            }
        }
        catch(Exception $e)
        {
            new TMessage('error', $e->getMessage());
        }
    }

    public function getGridEmpresa(array $listEmpresa){
        // create the datagrid
        $grid = new BootstrapDatagridWrapper(new TDataGrid);
        $grid->width = '100%';
        $cnpj = new TDataGridColumn('cnpj_basico','CNPJ Básico','left');
        /*
        $cnpj->setTransformer(function ($value) {
            return StringHelper::formatCnpjCpf($value);
        });
        */
        $grid->addColumn( $cnpj );
        //$grid->addColumn( new TDataGridColumn('razao_social','Razão Social','left') );
        //$grid->addColumn( new TDataGridColumn('nome_fantasia','Nome Fantasia','left') );

        $action = Transforme::getDataGridActionDetalharEmpresa();
        $grid->addAction($action);

        $grid->createModel();
        $grid->addItems($listEmpresa);
        $panel = TPanelGroup::pack('Lista de Empresas que é socio', $grid);
        
        return $panel;
    }
}