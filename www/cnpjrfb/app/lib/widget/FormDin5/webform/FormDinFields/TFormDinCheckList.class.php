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
 * Classe para criação de Botões 
 * ------------------------------------------------------------------------
 * Esse é o FormDin 5, que é uma reconstrução do FormDin 4 Sobre o Adianti 7.X
 * os parâmetros do metodos foram marcados com:
 * 
 * NOT_IMPLEMENTED = Parâmetro não implementados, talvez funcione em 
 *                   verões futuras do FormDin. Não vai fazer nada
 * DEPRECATED = Parâmetro que não vai funcionar no Adianti e foi mantido
 *              para o impacto sobre as migrações. Vai gerar um Warning
 * FORMDIN5 = Parâmetro novo disponivel apenas na nova versão
 * ------------------------------------------------------------------------
 * 
 * @author Reinaldo A. Barrêto Junior
 */ 
class TFormDinCheckList {

    private $id;
    private $objCheck;
    private $label;
    private $objLabel;
    private $objTitle;
    private $listColumn;
    private $listItems;

    /**
    * Adicionar botão no layout
    *
    * ------------------------------------------------------------------------
    * Esse é o FormDin 5, que é uma reconstrução do FormDin 4 Sobre o Adianti 7.X
    * os parâmetros do metodos foram marcados veja documentação da classe para
    * saber o que cada marca singinifica.
    * ------------------------------------------------------------------------
    *
    * Para que o botão fique alinhado na frente de um campo com labelAbove=true, basta
    * definir o parametro boolLabelAbove do botão para true tambem.
    *
    * @param object  $id                - 1 : id do campo
    * @param string  $label             - 2 : Label do Botão
    * @param boolean $boolRequired      - 3 : DEFAULT = false não obrigatório
    * @param boolean $listItems         - 4 : List Itens
    * @param int     $intHeight         - 6 : Altura 
    * @param boolean $makeScrollable    - 7 : DEFAULT = false
    * @return TFormDinCheckList
    */
    public function __construct($id
                              , $label
                              , $boolRequired=false
                              , $listItems
                              , $intHeight=null
                              )
    {

        $this->setId($id);
        $this->setObjCheck($id);
        $this->setLabel($label);
        $this->setRequired($boolRequired);
        $this->addItems( $listItems );
        $this->setHeight( $intHeight );
        $this->listColumn = array();
    }

    public function setId($id){
        $this->id = $id;
    }
    public function getId(){
        return $this->id;
    }
    //--------------------------------------------------------------------
    public function setObjCheck($id)
    {
        $orderlist = new TCheckList($id);
        return $this->objCheck=$orderlist;
    }
    public function getObjCheck(){
        return $this->objCheck;
    }
    //--------------------------------------------------------------------
    public function setRequired($boolRequired)
    {
        if($boolRequired==true){
            $label = $this->getLabel();
            $this->getObjCheck()->addValidation($label, new TRequiredValidator);
        }
    }
    public function getRequired(){
        return $this->objCheck;
    }
    //--------------------------------------------------------------------
    public function setLabel($label)
    {
        $this->label = $label;
        $this->objLabel =  new TLabel($label);
    }
    public function getLabel(){
        return $this->label;
    }
    public function getObjLabel(){
        return $this->objLabel;
    }
    //--------------------------------------------------------------------
    public function addItems( $listItems )
    {
        ValidateHelper::isArray($listItems,__METHOD__,__LINE__);
        $this->listItems = $listItems;
    }
    //--------------------------------------------------------------------
    public function setHeight( $intHeight )
    {
        if( !(empty($intHeight)) ){
            ValidateHelper::isNumeric($intHeight,__METHOD__,__LINE__);
            $this->getObjCheck()->setHeight( $intHeight );
            $this->getObjCheck()->makeScrollable();
        }
    }
    //--------------------------------------------------------------------
    /**
     * Add list column Hidden, usado para não mostrar a id ou PK na lista de colunas
     * @param  $name  = Name of the column in the database
     * @param  $label = Text label that will be shown in the header
     * @param  $align = Column align (left, center, right)
     * @param  $width = Column Width (pixels)
     * @param  $enableSearch = include field on search
     * @return TDataGridColumn
     */
    public function addColumnHidden($name, $label, $align, $width, $enableSearch=false)
    {
        $objColumn = $this->addColumn($name, $label, $align, $width, $enableSearch);
        $objColumn->setVisibility(false);
        return $objColumn;
    }
    //--------------------------------------------------------------------
    /**
     * Add list column
     * @param  $name  = Name of the column in the database
     * @param  $label = Text label that will be shown in the header
     * @param  $align = Column align (left, center, right)
     * @param  $width = Column Width (pixels)
     * @param  $enableSearch = include field on search
     * @return TDataGridColumn
     */
    public function addColumn($name, $label, $align, $width, $enableSearch=true)
    {
        $column = new \stdClass;
        $column->name = $name;
        $column->label = $label;
        $column->align = $align;
        $column->width = $width;
        $column->enableSearch = $enableSearch;
        $this->listColumn[] = $column;
        $objColumn = $this->getObjCheck()->addColumn($column->name, $column->label,$column->align,$column->width);
        return $objColumn;
    }
    //--------------------------------------------------------------------
    public function getStringSearch()
    {
        $stringSearch = null;
        foreach( $this->listColumn as $column) {
            if( $column->enableSearch == true ){
                $stringSearch = $stringSearch.','.$column->name;
            }
        }
        $stringSearch = substr($stringSearch, 1); 
        return $stringSearch;
    }
    public function getInputSearch($id)
    {
        $input_search = new TEntry($id);
        $input_search->placeholder = _t('Search');
        $input_search->setSize('100%');
        return $input_search;
    }
    public function showTitle()
    {
        $stringSearch = $this->getStringSearch();
        $objLabel = $this->getObjLabel();
        $hbox = new THBox;
        $hbox->style = 'border-bottom: 1px solid gray;padding-bottom:10px';
        $hbox->add( $objLabel );
        if( !empty($stringSearch) ){
            $id = $this->getId();
            $id = $id.'Search';
            $input_search = $this->getInputSearch($id);
            $this->getObjCheck()->enableSearch($input_search, $stringSearch);
            $hbox->add( $input_search )->style = 'float:right;width:30%;';
        }
        $this->objTitle = $hbox;

        return $this->objTitle;
    }
    //--------------------------------------------------------------------
    public function showBody()
    {
        $this->getObjCheck()->addItems( $this->listItems );
        return $this->getObjCheck();
    }
}
?>