<?php
/*
 * ----------------------------------------------------------------------------
 * Formdin 5 Framework
 * SourceCode https://github.com/bjverde/formDin5
 * @author Reinaldo A. Barrêto Junior
 * 
 * É uma reconstrução do FormDin 4 Sobre o Adianti 7.X
 * @author Luís Eugênio Barbosa do FormDin 4
 * 
 * Adianti Framework é uma criação Adianti Solutions Ltd
 * @author Pablo Dall'Oglio
 * ----------------------------------------------------------------------------
 * This file is part of Formdin Framework.
 *
 * Formdin Framework is free software; you can redistribute it and/or
 * modify it under the terms of the GNU Lesser General Public License version 3
 * as published by the Free Software Foundation.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU Lesser General Public License version 3
 * along with this program; if not,  see <http://www.gnu.org/licenses/>
 * or write to the Free Software Foundation, Inc., 51 Franklin Street,
 * Fifth Floor, Boston, MA  02110-1301, USA.
 * ----------------------------------------------------------------------------
 * Este arquivo é parte do Framework Formdin.
 *
 * O Framework Formdin é um software livre; você pode redistribuí-lo e/ou
 * modificá-lo dentro dos termos da GNU LGPL versão 3 como publicada pela Fundação
 * do Software Livre (FSF).
 *
 * Este programa é distribuído na esperança que possa ser útil, mas SEM NENHUMA
 * GARANTIA; sem uma garantia implícita de ADEQUAÇÃO a qualquer MERCADO ou
 * APLICAÇÃO EM PARTICULAR. Veja a Licença Pública Geral GNU/LGPL em português
 * para maiores detalhes.
 *
 * Você deve ter recebido uma cópia da GNU LGPL versão 3, sob o título
 * "LICENCA.txt", junto com esse programa. Se não, acesse <http://www.gnu.org/licenses/>
 * ou escreva para a Fundação do Software Livre (FSF) Inc.,
 * 51 Franklin St, Fifth Floor, Boston, MA 02111-1301, USA.
 */

/**
 * Classe para criação de Grid para apresentar os dados
 * ------------------------------------------------------------------------
 * Esse é o FormDin 5, que é uma reconstrução do FormDin 4 Sobre o Adianti 7.X
 * os parâmetros do metodos foram marcados com:
 * 
 * NOT_IMPLEMENTED = Parâmetro não implementados, talvez funcione em 
 *                   verões futuras do FormDin. Não vai fazer nada
 * DEPRECATED = Parâmetro que não vai funcionar no Adianti e foi mantido
 *              para diminuir o impacto sobre as migrações. Vai gerar um Warning
 * FORMDIN5 = Parâmetro novo disponivel apenas na nova versão
 * ------------------------------------------------------------------------
 * 
 * @author Reinaldo A. Barrêto Junior
 */
class TFormDinGrid
{

    const TYPE_SIMPLE   = 'simple';
    const TYPE_CHECKOUT = 'checkout';
    const ROWS_PER_PAGE = 20;

    private $adiantiObj;
    private $panelGroupGrid;
    private $pageNavigation;
    private $objForm;
    private $listColumn = array();
    private $dataGrid;
    private $bootstrapGrid;

    protected $idGrid;
    protected $title;
    protected $updateFields;
    protected $key;
    protected $width;
    protected $minWidth;
    protected $action_group;
    protected $enableActionGroup;
    private $maxRows;
    private $realTotalRowsSqlPaginator;    

    protected $data;

    protected $listGridAction = array();
    protected $createDefaultButtons;
    protected $createDefaultEditButton;
    protected $createDefaultDeleteButton;
    protected $exportShowGroup;
    protected $exportCsv;
    protected $exportExcel;
    protected $exportPdf;
    protected $exportXml;
    protected $columns;
    protected $qtdColumns;
    protected $onDrawActionButton;


    /**
     * Classe para criação de grides, Padronizado em BoorStrap
     * Reconstruido FormDin 4 Sobre o Adianti 7
     * 
     * Parametros do evento onDrawHeaderCell
     * 	1) $th			- objeto TElement
     * 	2) $objColumn 	- objeto TGridColum
     * 	3) $objHeader 	- objeto TElement
     *
     * Parametros do envento onDrawRow
     * 	1) $row 		- objeto TGridRow
     * 	2) $rowNum 		- número da linha corrente
     * 	3) $aData		- o array de dados da linha ex: $res[''][n]
     *
     * Parametros do envento onDrawCell
     * 	1) $rowNum 		- número da linha corrente
     * 	2) $cell		- objeto TTableCell
     * 	3) $objColumn	- objeto TGrideColum
     * 	4) $aData		- o array de dados da linha ex: $res[''][n]
     * 	5) $edit		- o objeto campo quando a coluna for um campo de edição
     *   ex: function ondrawCell($rowNum=null,$cell=null,$objColumn=null,$aData=null,$edit=null)
     *
     * Parametros do evento onDrawActionButton
     * 	1) $rowNum 		- número da linha corrente
     * 	2) $button 		- objeto TButton
     * 	3) $objColumn	- objeto TGrideColum
     * 	4) $aData		- o array de dados da linha ex: $res[''][n]
     *   Ex: function tratarBotoes($rowNum,$button,$objColumn,$aData);
     *
     * Parametros do evento onGetAutocompleteParameters
     * 	1) $ac 			- classe TAutocomplete
     * 	2) $aData		- o array de dados da linha ex: $res[''][n]
     * 	3) $rowNum 		- número da linha corrente
     * 	3) $cell		- objeto TTableCell
     * 	4) $objColumn	- objeto TGrideColum
     *
     * @param object $objForm             - 1: FORMDIN5 Objeto do Adianti da classe do Form, é só informar $this
     * @param string $strName             - 2: ID da grid
     * @param string $strTitle            - 3: Titulo da grid
     * @param array $mixData              - 4: Array de dados. Pode ser form formato Adianti, FormDin ou PDO
     * @param mixed $strHeight            - 5: Altura em pixels isso poder ligar o scroll vertical
     * @param mixed $strWidth             - 6: Largura do grid em % porcentagem
     * @param mixed $strKeyField          - 7: NOT_IMPLEMENTED Chave primaria
     * @param array $mixUpdateFields      - 8: Campos do form origem que serão atualizados ao selecionar o item desejado. Separados por virgulas seguindo o padrão <campo_tabela> | <campo_formulario> , <campo_tabela> | <campo_formulario>
     * @param mixed $intMaxRows           - 9: NOT_IMPLEMENTED Qtd Max de linhas
     * @param mixed $strRequestUrl        -10: NOT_IMPLEMENTED Url request do form
     * @param mixed $strOnDrawCell        -11: NOT_IMPLEMENTED
     * @param mixed $strOnDrawRow         -13: NOT_IMPLEMENTED
     * @param mixed $strOnDrawHeaderCell  -14: NOT_IMPLEMENTED
     * @param mixed $strOnDrawActionButton-15: NOT_IMPLEMENTED
     * @return TGrid
     */     
    public function __construct( $objForm
                               , string $strName
                               , string $strTitle = null
                               , $mixData = null
                               , $strHeight = null
                               , $strWidth = null
                               , string $strKeyField = null
                               , $mixUpdateFields = null
                               , $intMaxRows = null
                               , $strRequestUrl = null
                               , $strOnDrawCell = null
                               , $strOnDrawRow = null
                               , $strOnDrawHeaderCell = null
                               , $strOnDrawActionButton = null )
    {
        if( !is_object($objForm) ){
            $track = debug_backtrace();
            $msg = 'A classe GRID MUDOU! o primeiro parametro agora recebe $this! o Restante está igual ;-)';
            ValidateHelper::migrarMensage($msg
                                         ,ValidateHelper::ERROR
                                         ,ValidateHelper::MSG_CHANGE
                                         ,$track[0]['class']
                                         ,$track[0]['function']
                                         ,$track[0]['line']
                                         ,$track[0]['file']
                                        );
        }else{
            $this->setObjForm($objForm);

            $strWidth = empty($strWidth)?'100%':$strWidth;

            $this->dataGrid = new TDataGrid();
            $this->bootstrapGrid = new BootstrapDatagridWrapper($this->dataGrid);
            $this->setAdiantiObj($this->bootstrapGrid);
            $this->setWidth($strWidth);
            $this->setActionSide('left');
            $this->setId($strName);
            $this->setHeight($strHeight);
            $this->setData($mixData);
            $this->setUpdateFields( $mixUpdateFields );
            $this->setExportShowGroup(true);
            $this->setExportCsv(true);
            $this->setExportExcel(true);
            $this->setExportPdf(true);
            $this->setExportXml(true);

            $this->setTitle($strTitle);
            $panel = new TPanelGroup($this->getTitle());
            $this->setPanelGroupGrid($panel);
        }
    }


    public function setObjForm($objForm)
    {
        if( empty($objForm) ){
            throw new InvalidArgumentException(TFormDinMessage::ERROR_FD5_OBJ_ADI);
        }
        if( !is_object($objForm) ){
            throw new InvalidArgumentException(TFormDinMessage::ERROR_FD5_OBJ_ADI);
        }        
        return $this->objForm=$objForm;
    }
    public function getObjForm(){
        return $this->objForm;
    }
    //---------------------------------------------------------------
    public function setAdiantiObj( $bootgrid )
    {
        if( !($bootgrid instanceof BootstrapDatagridWrapper) ){
            throw new InvalidArgumentException(TFormDinMessage::ERROR_FD5_OBJ_BOOTGRID);
        }
        $this->adiantiObj = $bootgrid;
    }
    public function getAdiantiObj(){
        return $this->adiantiObj;
    }
    //---------------------------------------------------------------
    public function getId(){
        return $this->idGrid;
    }
    public function setId($idGrid){
        if(empty($idGrid)){
            throw new InvalidArgumentException(TFormDinMessage::ERROR_EMPTY_INPUT);
        }
        $this->getAdiantiObj()->setId($idGrid);
        $this->idGrid = $idGrid;
    }
    //---------------------------------------------------------------
    private function getDataGrid(){
        return $this->dataGrid;
    }
    private function setDataGrid($dataGrid){
        if( !is_object($dataGrid) ){
            throw new InvalidArgumentException(TFormDinMessage::ERROR_FD5_OBJ_ADI);
        }         
        $this->dataGrid = $dataGrid;
    }
    /**
     * Define o lado que ação irá aparecer. O Default é 'left'
     * Os valores possíveis são: left, right, esquerda, direita
     * @param string $side
     */
    public function setActionSide($side){
        $side = empty($side)?'left':$side;
        $validando = $side=='left'||$side=='esquerda'||$side=='right'||$side=='direita';
        if( !$validando ){
            throw new InvalidArgumentException(TFormDinMessage::ERROR_VALEU_NOT_VALID);
        }
        $this->getDataGrid()->setActionSide($side);
    }    
    //---------------------------------------------------------------
    /**
     * Adciona um Objeto Adianti na lista de objetos que compeen o Formulário.
     * 
     * @param string $type    - 1: Type column constante
     * @param string $idcolumn- 2: idcolumn
     * @param string $label   - 3: Label da coluna
     * @param string $width   - 4: 
     * @param string $align   - 5: 
     * @return void
     */
    public function addElementColumnList(string $type
                                        ,string $idcolumn
                                        ,string $label
                                        ,string $width = NULL
                                        ,string $align = 'left'
                                        )
    {
        $type = empty($type)?self::TYPE_SIMPLE:$type;
        $element = array();
        $element['type']=$type;
        $element['idcolumn']=$idcolumn;
        $element['label']=$label;
        $element['align']=$align;
        $element['width']=$width;
        $this->listColumn[]=$element;
    }
    //------------------------------------------------------------------------------------
    /**
     * Retorna o numero de regristros quando for usar uma pagina de banco e não apenas paginação de tela
     *
     * @return int
     */
    public function getRealTotalRowsSqlPaginator(){
        return $this->realTotalRowsSqlPaginator;
    }
    
    //------------------------------------------------------------------------------------
    /**
     * Numero real de registros, deve ser utilizando somento com paginação de banco
     * está relacionado com setMaxRows
     *
     * @param int $realTotalRowsSqlPaginator
     * @return void
     */
    public function setRealTotalRowsSqlPaginator($realTotalRowsSqlPaginator){
        $this->realTotalRowsSqlPaginator = $realTotalRowsSqlPaginator;
    }
    
    //------------------------------------------------------------------------------------
    public function getQtdColumns(){
        return $this->qtdColumns;
    }
    
    //------------------------------------------------------------------------------------
    public function setQtdColumns($qtdColumns){
        $this->qtdColumns = $qtdColumns;
    }


    public function showGridColumn()
    {
        $listColumn = $this->getListColumn();
        if( ArrayHelper::isArrayNotEmpty($listColumn) ){
            foreach( $listColumn as $formDinGridColumn ) {
                $column = $formDinGridColumn->getAdiantiObj();
                $this->getAdiantiObj()->addColumn($column);
            }
        }
    }

    /**
     * Monta as ações no grid
     * @return void
     */
    public function showGridAction()
    {
        $listGridAction = $this->getListGridAction();
        $listGridActionNotEmpty = ArrayHelper::isArrayNotEmpty($listGridAction);
        if( $listGridActionNotEmpty && $this->getEnableActionGroup()==false ){
            foreach( $listGridAction as $itemGridAction ) {
                $this->getAdiantiObj()->addAction($itemGridAction->getAdiantiObj()
                                                 ,$itemGridAction->getActionLabel()
                                                 ,$itemGridAction->getImage()
                                                 );
            }
        }else if( $listGridActionNotEmpty && $this->getEnableActionGroup()==true ){
            $action_group = $this->getActionGroup();
            foreach( $listGridAction as $itemGridAction ) {
                $action_group->addAction($itemGridAction->getAdiantiObj());
            }            
            $this->getAdiantiObj()->addActionGroup( $action_group );
        }else{
            if( $this->getCreateDefaultButtons() ){
                if( $this->getCreateDefaultEditButton() ){
                    $itemGridAction = $this->addButton(_t('Edit'),'onEdit',null,null,null,'far:edit blue');
                    $this->getAdiantiObj()->addAction($itemGridAction->getAdiantiObj()
                                                     ,$itemGridAction->getActionLabel()
                                                     ,$itemGridAction->getImage()
                                                     );
                }

                if( $this->getCreateDefaultDeleteButton() ){
                    $itemGridAction = $this->addButton(_t('Delete'),'onDelete',null,null,null,'far:trash-alt red');
                    $this->getAdiantiObj()->addAction($itemGridAction->getAdiantiObj()
                                                     ,$itemGridAction->getActionLabel()
                                                     ,$itemGridAction->getImage()
                                                     );
                }
            }
        }
    }

    public function showGridExport()
    {
        $showExport = $this->getExportCsv() || $this->getExportExcel() || $this->getExportPdf() || $this->getExportXml();
        $showExportGroup = $this->getExportShowGroup();

        if( $showExport && $showExportGroup ){
            // header actions
            $dropdown = new TDropDown('Exportar', 'fa:list');
            $dropdown->setButtonClass('btn btn-default waves-effect dropdown-toggle');
            if( $this->getExportCsv() ){
                $taction = new TAction([$this->getObjForm(), 'onExportCSV'], ['register_state' => 'false', 'static'=>'1']);
                $dropdown->addAction( 'CSV', $taction, 'fas:file-csv #66ccff' );
            }
            if( $this->getExportPdf() ){
                $taction = new TAction([$this->getObjForm(), 'onExportPDF'], ['register_state' => 'false', 'static'=>'1']);
                $dropdown->addAction( 'PDF', $taction, 'far:file-pdf #e74c3c' );
            }
            if( $this->getExportExcel() ){
                $taction = new TAction([$this->getObjForm(), 'onExportXls'], ['register_state' => 'false', 'static'=>'1']);
                $dropdown->addAction( 'XLS', $taction, 'fas:file-excel #4CAF50' );
            }
            if( $this->getExportXml() ){
                $taction = new TAction([$this->getObjForm(), 'onExportXML'], ['register_state' => 'false', 'static'=>'1']);
                $dropdown->addAction( 'XML', $taction, 'far:file-code #95a5a6' );
            }
            $this->getPanelGroupGrid()->addHeaderWidget( $dropdown );
        }elseif( $showExport && !$showExportGroup ){
            if( $this->getExportCsv() ){
                $taction = new TAction([$this->getObjForm(), 'onExportCSV'], ['register_state' => 'false', 'static'=>'1']);
                $this->getPanelGroupGrid()->addHeaderActionLink( 'CSV', $taction, 'fas:file-csv #66ccff' );
            }
            if( $this->getExportPdf() ){
                $taction = new TAction([$this->getObjForm(), 'onExportPDF'], ['register_state' => 'false', 'static'=>'1']);
                $this->getPanelGroupGrid()->addHeaderActionLink( 'PDF', $taction, 'far:file-pdf #e74c3c' );
            }
            if( $this->getExportExcel() ){
                $taction = new TAction([$this->getObjForm(), 'onExportXls'], ['register_state' => 'false', 'static'=>'1']);
                $this->getPanelGroupGrid()->addHeaderActionLink( 'XLS', $taction, 'fas:file-excel #4CAF50' );
            }
            if( $this->getExportXml() ){
                $taction = new TAction([$this->getObjForm(), 'onExportXML'], ['register_state' => 'false', 'static'=>'1']);
                $this->getPanelGroupGrid()->addHeaderActionLink( 'XML', $taction, 'far:file-code #95a5a6' );
            }
        }
    }

    /**
     * Monta o objeto do Grid Adianti com tudo que precisa
     * @return void
     */
    public function show()
    {
        $this->showGridColumn();
        $this->showGridAction();
        $this->showGridExport();

        $this->getAdiantiObj()->createModel();
        if( !empty($this->getData()) ){
            $this->getAdiantiObj()->addItems( $this->getData() );
        }
        $this->getPanelGroupGrid()->add($this->getAdiantiObj())->style = 'overflow-x:auto';


        // the creation of the navigation page must come after createModel
        $pageNavigation = new TPageNavigation;
        $pageNavigation->setAction(new TAction(array($this->getObjForm(), 'onReload')));
        $pageNavigation->enableCounters();
        $pageNavigation->setLimit($this->getMaxRows()); // número máximo de itens por página
        $this->setPageNavigation($pageNavigation);
        $this->getPanelGroupGrid()->addFooter($pageNavigation);

        return $this->getAdiantiObj();
    }
    //---------------------------------------------------------------
    public function getListColumn(){
        return $this->listColumn;
    }
    public function setListColumn($listColumn){
        $this->listColumn = $listColumn;
    }
    public function addListColumn($itemColumn){
        if ( !($itemColumn instanceof TFormDinGridColumn)) {
            throw new InvalidArgumentException(TFormDinMessage::ERROR_OBJ_TYPE_WRONG.' use TFormDinGridColumn');
         }
        $this->listColumn[$itemColumn->getName()] = $itemColumn;
    }
    //---------------------------------------------------------------
    public function getTitle(){
        return $this->title;
    }
    public function setTitle($title){
        $this->title = $title;
    }
    //---------------------------------------------------------------
    public function getKey(){
        return $this->key;
    }
    public function setKey(string $key){
        $this->key = $key;
    }
    //---------------------------------------------------------------
    public function getListGridAction(){
        return $this->listGridAction;
    }
    public function setListGridAction($listGridAction){
        $this->listGridAction = $listGridAction;
    }
    public function addListGridAction($itemGridAction){
        if ( !($itemGridAction instanceof TFormDinGridAction)) {
            throw new InvalidArgumentException(TFormDinMessage::ERROR_OBJ_TYPE_WRONG.' use TFormDinGridAction');
         }
        $this->listGridAction[$itemGridAction->getActionName()] = $itemGridAction;
    }
    //---------------------------------------------------------------
    public function getPanelGroupGrid(){
        return $this->panelGroupGrid;
    }
    public function setPanelGroupGrid($panel){
        if( !($panel instanceof TPanelGroup) ){
            throw new InvalidArgumentException(TFormDinMessage::ERROR_OBJ_TYPE_WRONG.' use TPanelGroup');
        }
        $this->panelGroupGrid = $panel;
    }
    //---------------------------------------------------------------
    public function getPageNavigation(){
        return $this->pageNavigation ;
    }
    public function setPageNavigation($pageNavigation){
        if( !($pageNavigation instanceof TPageNavigation) ){
            throw new InvalidArgumentException(TFormDinMessage::ERROR_OBJ_TYPE_WRONG.' use TPanelGroup');
        }
        $this->pageNavigation = $pageNavigation;
    }
    //---------------------------------------------------------------
    public function getFooter(){
        return $this->getPanelGroupGrid()->getFooter();
    }
    public function addFooter($footer){
        return $this->getPanelGroupGrid()->addFooter($footer);
    }
    //---------------------------------------------------------------
    public function enableDataTable(){
        $this->getAdiantiObj()->datatable = 'true';
    }
    public function disableDataTable(){
        $this->getAdiantiObj()->datatable = 'false';
    }
    //---------------------------------------------------------------
    /**
     * Coluna do Grid Padronizado em BoorStrap
     * Reconstruido FormDin 4 Sobre o Adianti 7.1
     *
     * @param string $name  - 01: Name of the column in the database
     * @param string $label - 02: Text label that will be shown in the header
     * @param string $width - 03: Column Width (pixels)
     * @param string $align - 04: Column align (left|right|center|justify)
     * @param bool $boolReadOnly - 05: FORMDIN5: NOT_IMPLEMENTED Somente leitura. DEFAULT = false
	 * @param bool $boolSortable - 06: FORMDIN5: Coluna ordenavel. DEFAULT = true
	 * @param bool $boolVisivle  - 07: FORMDIN5: Coluna visivel ou não. DEFAULT = true
     * @param string $autoHide   - 08: FORMDIN5: Largura em pix que a coluna não ficará visivel, se a largura da tela ficar menor que o valor informado a coluna irá desaparer.
     * @return TDataGridColumn
     */
    public function addColumn(string $name
                            , string $label
                            , string $width = NULL
                            , string $align='left'
                            , bool $boolReadOnly = false
                            , bool $boolSortable = true
                            , bool $boolVisivle = true
                            , string $autoHide = null
                            )
    {
        $formDinGridColumn = new TFormDinGridColumn( $this->getObjForm()
                                                   , $name
                                                   , $label
                                                   , $width
                                                   , $align
                                                   , $boolReadOnly
                                                   , $boolSortable
                                                   , $boolVisivle
                                                   , $autoHide
                                                );
        $this->addListColumn($formDinGridColumn);
        return $formDinGridColumn;
    }
    //---------------------------------------------------------------
    /**
     * Coluna do Grid Padronizado em BoorStrap
     * Reconstruido FormDin 4 Sobre o Adianti 7.1
     *
     * @param  string $name   - 1: Name of the column in the database
     * @param  string $label  - 2: Text label that will be shown in the header
     * @param  string $width  - 3: Column Width (pixels)
     * @param  string $align  - 4: Column align (left|right|center|justify)
     * @param  string $format - 5: Date Format. DEFAULT = d/m/Y (Brazil). Exemplo: United States = m/d/Y. Aceita o formato Adianti dd/mm/yyyy ou DateTime do PHP d/m/Y. 
     * @param bool $boolReadOnly - 06: FORMDIN5: NOT_IMPLEMENTED Somente leitura. DEFAULT = false
	 * @param bool $boolSortable - 07: FORMDIN5: Coluna ordenavel. DEFAULT = true
	 * @param bool $boolVisivle  - 08: FORMDIN5: NOT_IMPLEMENTED Coluna visivel. DEFAULT = true
     * @param string $autoHide   - 09: FORMDIN5: Largura em pix que a coluna não ficará visivel, se a largura da tela ficar menor que o valor informado a coluna irá desaparer.
     * @return TDataGridColumn
     */
    public function addColumnFormatDate(string $name
                                      , string $label
                                      , string $width = NULL
                                      , string $align ='left'
                                      , string $format='d/m/Y'
                                      , bool $boolReadOnly = false
                                      , bool $boolSortable = true
                                      , bool $boolVisivle = true
                                      , string $autoHide = null
                                      )
    {
        $formDinGridColumn = new TFormDinGridColumnFormatDate($this->getObjForm()
                                                            ,$name
                                                            ,$label
                                                            ,$width
                                                            ,$align
                                                            ,$format
                                                            ,$boolReadOnly
                                                            ,$boolSortable
                                                            ,$boolVisivle
                                                            ,$autoHide
                                                        );
        $this->addListColumn($formDinGridColumn);
        return $formDinGridColumn;
    }
    //---------------------------------------------------------------
    /**
     * Coluna com campo CPF ou CNPJ formatado. Se valor informado não for uma
     * string com tamanho 11 ou 14 vai retorna NULL
     *
     * @param string $name   - 01: Name of the column in the database
     * @param string $label  - 02: Text label that will be shown in the header
     * @param string $width  - 03: Column Width (pixels)
     * @param string $align  - 04: Column align (left|right|center|justify)
     * @param bool $boolReadOnly - 05: FORMDIN5: NOT_IMPLEMENTED Somente leitura. DEFAULT = false
	 * @param bool $boolSortable - 06: FORMDIN5: Coluna ordenavel. DEFAULT = true
	 * @param bool $boolVisivle  - 07: FORMDIN5: NOT_IMPLEMENTED Coluna visivel. DEFAULT = true
     * @param string $autoHide   - 08: FORMDIN5: Largura em pix que a coluna não ficará visivel, se a largura da tela ficar menor que o valor informado a coluna irá desaparer.
     * @return TDataGridColumn
     */
    public function addColumnFormatCpfCnpj( string $name
                                          , string $label
                                          , string $width = NULL
                                          , string $align ='left'
                                          , bool $boolReadOnly = false
                                          , bool $boolSortable = true
                                          , bool $boolVisivle = true
                                          , string $autoHide = null
                                          )
    {
        $formDinGridColumn = new TFormDinGridColumnFormatCpfCnpj($this->getObjForm()
                                                                ,$name
                                                                ,$label
                                                                ,$width
                                                                ,$align
                                                                ,$boolReadOnly
                                                                ,$boolSortable
                                                                ,$boolVisivle
                                                                ,$autoHide
                                                            );
        $this->addListColumn($formDinGridColumn);
        return $formDinGridColumn;
    }     

    //---------------------------------------------------------------------------------------
    /**
     * coluna tipo checkbox. Irá criar no gride uma coluno do tipo checkbox. Quando é feito o POST
     * será criado uma nova variavel com valor de strName
     *
     *
     * @param string $strName       - Nome do variavel no POST
     * @param string $strTitle      - Titulo que aparece no grid
     * @param string $strKeyField   - Valor que será passado no POST
     * @param string $strDescField  - Descrição do campo, valor que irá aparecer o gride
     * @param boolean $boolReadOnly
     * @param boolean $boolAllowCheckAll  - TRUE = pode selecionar todos , FALSE = não permite multiplas seleções
     * @return TGridCheckColumn
     */
    public function addCheckColumn( string $strName
                                  , string $strTitle = null
                                  , string $strKeyField
                                  , string $strDescField = null
                                  , mixed $boolReadOnly = null
                                  , mixed $boolAllowCheckAll = null )
    {
        if ( !$strKeyField ){
            $strKeyField = strtoupper( $strName );
        }
        $this->getAdiantiObj()->disableDefaultClick(); //IMPORTANTE DESATIVAR
        $col = new TGridCheckColumn( $strName, $strTitle, $strKeyField, $strDescField, $boolReadOnly, $boolAllowCheckAll );
        $this->columns[ strtolower( $strName )] = $col;
        return $col;
    }

    //------------------------------------------------------------------------------------
    public function getMixUpdateButton($mixUpdateButton){
        $mixUpdateButton = empty($mixUpdateButton)?$this->getUpdateFields():$mixUpdateButton;

        if( empty($mixUpdateButton) ){
            $listKeyColumnId = array_keys($this->getListColumn());            
            $mixUpdateButton = null;
            if( ArrayHelper::isArrayNotEmpty($listKeyColumnId) ){
                foreach(  $listKeyColumnId as $id) {
                    $mixUpdateButton[$id] = $id;
                }                
                $mixUpdateButton = ArrayHelper::convertArrayMixUpdate2OutputFormat($mixUpdateButton);
            }            
        }
        return $mixUpdateButton;
    }    
    /**
     * Adicionar botão na linha do gride. Se o usuário adicionar um botão, 
     * cancelar a criação dos botões padrão de alterar e excluir
     *
     * $boolSubmitAction = adicionar/remover a função fwFazerAcao(). Padrão=true
     *
     * @param string $strRotulo         - 1: Nome do Label do Botão ou Hint que irá aparecer na imagem se o Hint estiver em branco
     * @param string $strAction         - 2: Nome da ação captura no formDinAcao
     * @param string $strName           - 3: NOT_IMPLEMENTED Nome
     * @param string $strOnClick        - 4: NOT_IMPLEMENTED JavaScript que será chamado no evento OnClick
     * @param string $strConfirmMessage - 5: NOT_IMPLEMENTED Mensagem com caixa de confirmação
     * @param string $strImage          - 6: Imagem que irá aparecer
     * @param string $strImageDisabled  - 7: NOT_IMPLEMENTED Imagem quado desabilitado
     * @param string $strHint           - 8: NOT_IMPLEMENTED
     * @param boolean $boolSubmitAction - 9: NOT_IMPLEMENTED
     * @param mixed   $mixUpdateButton  -10: FORMDIN5: MixUpdateFields do Botão 
     * @param string $classDestiny      -11: FORMDIN5: nome da classe que vai tratar ação. o Valor Defualt é propria classe
     * @return object TFormDinGridAction
     */
    public function addButton( string $strRotulo
                             , string $strAction = null
                             , string $strName = null
                             , string $strOnClick = null
                             , string $strConfirmMessage = null
                             , string $strImage = null
                             , string $strImageDisabled = null
                             , string $strHint = null
                             , mixed $boolSubmitAction = null
                             , mixed $mixUpdateButton = null
                             , $classDestiny   = null
                             ){
            $mixUpdateButton = $this->getMixUpdateButton($mixUpdateButton);
            if( empty($mixUpdateButton) ){
                throw new InvalidArgumentException(TFormDinMessage::ERROR_GRID_UPDATEFIELD.$strAction);
            }
            $classDestiny    = empty($classDestiny)?$this->getObjForm():$classDestiny;
            $itemGridAction = new TFormDinGridAction($classDestiny
                                                    ,$strRotulo
                                                    ,$strAction
                                                    ,$mixUpdateButton
                                                    ,$strImage
                                                    );
            $this->addListGridAction($itemGridAction);
            return $itemGridAction;
    }
    //------------------------------------------------------------------------------------
    /**
     * Seta o nome de uma função que será usada para desenhar os botões de uma linha da gride.
     * Um exemplo tipico é desabilitar determinado botão se o Registro estiver no estado X
     * 
     * Parametros do evento onDrawActionButton
     * 	1) $rowNum 		- número da linha corrente
     * 	2) $button 		- objeto TButton
     * 	3) $objColumn	- objeto TGrideColum
     * 	4) $aData		- o array de dados da linha ex: $res[''][n]
     *   Ex: function tratarBotoes($rowNum,$button,$objColumn,$aData);
     * 
     * @param string $newValue
     */
    public function setOnDrawActionButton( $newValue = null )
    {
        $this->onDrawActionButton = $newValue;
    }
    public function getOnDrawActionButton()
    {
        return $this->onDrawActionButton;
    }
    //------------------------------------------------------------------------------------
    /**
     * Campos do form origem que serão atualizados ao selecionar o item desejado.
     * Pode receber 3 tipos de entrada
     *   - FormDin: Separados por pipe e virgulas seguindo o padrão 
     *      <campo_tabela> | <campo_formulario> , <campo_tabela> | <campo_formulario>
     *   - PHP array ( <campo_tabela>=><campo_formulario>,  <campo_tabela>=><campo_formulario>)
     *   - Adianti ['key0'=>'{value0}','key1' => '{value1}']
     * @param string $mixUpdateFields
     */
    public function setUpdateFields( $mixUpdateFields = null )
    {
        $mixUpdateFields = ArrayHelper::convertArrayMixUpdate2OutputFormat($mixUpdateFields);
        $this->updateFields = $mixUpdateFields;
    }
    /**
     * Retorna a lista de campos que serão atualizados
     *
     * @param const $outputFormat - Formato de saída conforme TFormDinGridAction
     * @return mix
     */
    public function getUpdateFields($outputFormat = ArrayHelper::TYPE_ADIANTI_GRID_ACTION)
    {
        $mixUpdateFields = ArrayHelper::convertArrayMixUpdate2OutputFormat($this->updateFields,$outputFormat);
        return $mixUpdateFields;
    }
    //------------------------------------------------------------------------------------
    public function clearUpdateFields()
    {
        $this->updateFields = null;
    }
    //---------------------------------------------------------------------------------------
    /**
     * Qtd Max de linhas
     *
     * @param int $intNewValue
     * @return void
     */
    public function setMaxRows( $intNewValue = null ) {
        $this->maxRows = $intNewValue;
    }
    /**
     * Qtd Max de linhas, nome no padrão Adianti
     *
     * @param int $intNewValue
     * @return void
     */
    public function setLimit( $intNewValue = null ) {
        $this->setMaxRows($intNewValue);
    }
    public function getMaxRows() {
        $maxRows =  empty($this->maxRows)?self::ROWS_PER_PAGE:$this->maxRows;
        return ( int ) $maxRows;
    }
    //---------------------------------------------------------------------------------------
    public function getCreateDefaultButtons()
    {
        return is_null( $this->createDefaultButtons ) ? true : $this->createDefaultButtons;
    }
    //---------------------------------------------------------------
    public function getHeight(){
        return $this->getAdiantiObj()->height;
    }
    public function setHeight($height){
        if( !empty($height) ){
            $this->getAdiantiObj()->setHeight($height);
            $this->getAdiantiObj()->makeScrollable();
        }
    }
    //---------------------------------------------------------------
    public function getWidth(){
        return $this->width;
    }
    public function setWidth(string $width){
        if(empty($width)){
            throw new InvalidArgumentException(TFormDinMessage::ERROR_EMPTY_INPUT);
        }
        $this->width = $width;
        $this->getAdiantiObj()->width=$width;
    }
    //---------------------------------------------------------------
    public function getMinWidth()
    {
        return $this->minWidth;
    }
    public function setMinWidth( $width )
    {
        $this->minWidth = $width;
        $this->getAdiantiObj()->style = 'min-width: '.$width.'px';
    }
    //---------------------------------------------------------------
    public function setData( $data )
    {
        if(!empty($data)){
            $data = ArrayHelper::convertArray2Adianti($data);
        }
        $this->data = $data;
    }
    public function getData()
    {
        return $this->data;
    }
    //---------------------------------------------------------------------------------------
    public function setExportShowGroup( $boolNewValue = null )
    {
        $this->exportShowGroup = is_null( $boolNewValue ) ? true : $boolNewValue;
    }
    public function getExportShowGroup() {
        return $this->exportShowGroup;
    }
    //---------------------------------------------------------------------------------------
    public function setExportCsv( $boolNewValue = null )
    {
        $this->exportCsv = is_null( $boolNewValue ) ? true : $boolNewValue;
    }
    public function getExportCsv() {
        return $this->exportCsv;
    }    
    //---------------------------------------------------------------------------------------
    public function setExportExcel( $boolNewValue = null )
    {
        $this->exportExcel = is_null( $boolNewValue ) ? true : $boolNewValue;
    }
    public function getExportExcel() {
        return $this->exportExcel;
    }
    //---------------------------------------------------------------------------------------
    public function setExportPdf( $boolNewValue = null )
    {
        $this->exportPdf = is_null( $boolNewValue ) ? true : $boolNewValue;
    }
    public function getExportPdf() {
        return $this->exportPdf;
    }
    //---------------------------------------------------------------------------------------
    public function setExportXml( $boolNewValue = null )
    {
        $this->exportXml = is_null( $boolNewValue ) ? true : $boolNewValue;
    }
    public function getExportXml() {
        return $this->exportXml;
    }
    //------------------------------------------------------------------------------------
    /**
     * Define se os botoes Alterar e Excluir serão exibidos quando não for
     * adicionado nenhum botão
     *
     * @param mixed $boolNewValue
     */
    public function enableDefaultButtons( $boolNewValue = null )
    {
        $this->createDefaultButtons = is_null( $boolNewValue ) ? true : $boolNewValue;
    }
    //------------------------------------------------------------------------------------
    public function setCreateDefaultEditButton( $boolNewValue = null )
    {
        $this->createDefaultEditButton = is_null( $boolNewValue ) ? true : $boolNewValue;
    }
    public function getCreateDefaultEditButton( $boolNewValue = null )
    {
        return is_null( $this->createDefaultEditButton ) ? true : $this->createDefaultEditButton;
    }
    //------------------------------------------------------------------------------------
    public function setCreateDefaultDeleteButton( $boolNewValue = null )
    {
        $this->createDefaultDeleteButton = is_null( $boolNewValue ) ? true : $boolNewValue;
    }    
    public function getCreateDefaultDeleteButton( $boolNewValue = null )
    {
        return is_null( $this->createDefaultDeleteButton ) ? true : $this->createDefaultDeleteButton;
    }
    //------------------------------------------------------------------------------------
    /**
     * Ativa um grupo de ações padrão 
     *
     * @param boolean $enable - Default = FALSE, sem grupo de ações. TRUE = Habilita grupo de ações
     */
    public function enableActionGroup( $enable )
    {
        $enable=empty($enable)?false:$enable;
        if ($enable==true){
            $this->setActionGroup( 'Ações', 'fa:th' );
        }
        $this->enableActionGroup = $enable;
    }
    public function getEnableActionGroup()
    {
        return $this->enableActionGroup;
    }
    /**
     * Cria um Action Group
     * @param $label Action Group label
     * @param $icon  Action Group icon
     */
    public function setActionGroup( $label,$icon )
    {
        $this->action_group = new TDataGridActionGroup($label,$icon);
    }
    public function getActionGroup()
    {
        return $this->action_group;
    }    
}