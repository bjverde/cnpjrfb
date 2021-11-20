<?php
/**
 * System generated by SysGen (System Generator with Formdin Framework) 
 * Download SysGenAd: https://github.com/bjverde/sysgenad
 * Download Formdin5 Framework: https://github.com/bjverde/formDin5
 * 
 * SysGen  Version: 0.6.0
 * FormDin Version: 5.0.0
 * 
 * System cnpjrfb created in: 2021-11-19 22:41:13
 */

class motiForm extends TPage
{

    protected $form; //Registration form Adianti
    protected $frm;  //Registration component FormDin 5
    protected $datagrid; //Listing
    protected $pageNavigation;

    // trait com onReload, onSearch, onDelete, onClear, onEdit, show
    use Adianti\Base\AdiantiStandardFormTrait;
    // trait com onReload, onSearch, onDelete...
    use Adianti\Base\AdiantiStandardListTrait;

    public function __construct()
    {
        parent::__construct();
        // $this->adianti_target_container = 'adianti_right_panel';

        $this->setDatabase('maindatabase'); // define the database
        $this->setActiveRecord('moti'); // define the Active Record
        $this->setDefaultOrder('CODIGO', 'asc'); // define the default order

        $primaryKey = 'CODIGO';
        $this->frm = new TFormDin($this,'moti');
        $frm = $this->frm;
        $frm->enableCSRFProtection(); // Protection cross-site request forgery 
        $frm->addHiddenField( $primaryKey );   // coluna chave da tabela
        $frm->addMemoField('DESCRICAO', 'Descrição',1000,true,80,3);

        // O Adianti permite a Internacionalização - A função _t('string') serve
        //para traduzir termos no sistema. Veja ApplicationTranslator escrevendo
        //primeiro em ingles e depois traduzindo
        $frm->setAction( _t('Save'), 'onSave', null, 'fa:save', 'green' );
        $frm->setActionLink( _t('Clear'), 'onClear', null, 'fa:eraser', 'red');

        $this->form = $frm->show();
        $this->form->setData( TSession::getValue(__CLASS__.'_filter_data'));

        $mixUpdateFields = $primaryKey.'|'.$primaryKey
                        .',DESCRICAO|DESCRICAO'
                        ;
        $grid = new TFormDinGrid($this,'gd','Data Grid');
        $grid->setUpdateFields($mixUpdateFields);
        $grid->addColumn($primaryKey,'id');
        $grid->addColumn('DESCRICAO','Descrição');

        $this->datagrid = $grid->show();
        $this->pageNavigation = $grid->getPageNavigation();
        $panelGroupGrid = $grid->getPanelGroupGrid();


        // creates the page structure using a table
        $formDinBreadCrumb = new TFormDinBreadCrumb(__CLASS__);
        $vbox = $formDinBreadCrumb->getAdiantiObj();
        $vbox->add($this->form);
        $vbox->add($panelGroupGrid);

        // add the table inside the page
        parent::add($vbox);
    }

    //--------------------------------------------------------------------------------
    /**
     * Close right panel
     */
     /*
    public function onClose()
    {
        TScript::create("Template.closeRightPanel()");
    } //END onClose
     */

    //--------------------------------------------------------------------------------
    public function onSave($param)
    {
        $data = $this->form->getData();
        //Função do FormDin para Debug
        FormDinHelper::d($param,'$param');
        FormDinHelper::debug($data,'$data');
        FormDinHelper::debug($_REQUEST,'$_REQUEST');

        try{
            $this->form->validate();
            $this->form->setData($data);
            $vo = new MotiVO();
            $this->frm->setVo( $vo ,$data ,$param );
            $controller = new MotiController();
            $resultado = $controller->save( $vo );
            if( is_int($resultado) && $resultado!=0 ) {
                //$text = TFormDinMessage::messageTransform($text); //Tranform Array in Msg Adianti
                $this->onReload();
                $this->frm->addMessage( _t('Record saved') );
                //$this->frm->clearFields();
            }else{
                $this->frm->addMessage($resultado);
            }
        }catch (Exception $e){
            new TMessage(TFormDinMessage::TYPE_ERROR, $e->getMessage());
        } //END TryCatch
    } //END onSave

}