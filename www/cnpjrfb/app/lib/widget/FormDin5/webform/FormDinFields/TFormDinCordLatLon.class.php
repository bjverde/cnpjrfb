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

class TFormDinCordLatLon extends TFormDinGenericField
{
    const BUTTON_CLASS = 'btn btn-primary btn-sm';
    const BUTTON_LABEL = 'Informar Geolocalização';
    const BUTTON_ICON = 'fas:map-marker';
    const FEEDBACK_ICON = 'fas fa-check-circle';
    const FEEDBACK_COLOR= 'green';
    const FEEDBACK_SIZE = '25px';


    protected $adiantiObj;
    private $class = array();
    private $idDivGeo = null;
    private $showFields = null;
    private $showAltitude = null;
    private $fieldsReadOnly = null;
    private $fieldAllJson = null;
    private $btnGeo = null;
    private $buttonClass= null;
    private $buttonLabel= null;
    private $buttonIcon = null;
    private $feedBackIcon = null;
    private $feedBackColor= null;
    private $feedBackSize = null;

    /**
     * Pegar informações geolocalização do navegador
     *
     * @param string  $idField         -01: ID do campo
     * @param string  $label           -02: Label do campo, usado para validações
     * @param boolean $boolRequired    -03: Campo obrigatório ou não. Default FALSE = não obrigatório, TRUE = obrigatório
     * @param boolean $showFields      -04: TRUE (Default) or FALSE, Show fields latitude and longitude
     * @param boolean $showAltitude    -05: TRUE (Default) or FALSE, Show field  altitude
     * @param boolean $fieldsReadOnly  -06: TRUE (Default) or FALSE, Field read only
     * @param boolean $fieldAllJson    -07: TRUE (Default) or FALSE, Cria um campo oculta que vai receber um JSON com todos os atributos
     * @return TElement
     */
    public function __construct(string $idField
                               ,string $label
                               ,$boolRequired  =null
                               ,$showFields    =null
                               ,$showAltitude  =null
                               ,$fieldsReadOnly=null
                               ,$fieldAllJson  =null
                               )
    {
        $this->setIdDivGeo($idField);
        $this->setShowFields($showFields);
        $this->setShowAltitude($showAltitude);
        $this->setFieldsReadOnly($fieldsReadOnly);
        $this->setShowAllJson($fieldAllJson);

        
        
        $adiantiObj = $this->getDivGeo($idField,$boolRequired);
        parent::__construct($adiantiObj,$this->getIdDivGeo(),$label,false,null,null);
        $this->setLabel($label,$boolRequired);

        return $this->getAdiantiObj();
    }
    //--------------------------------------------------------------------
    public function setShowFields($showFields)
    {
        $showFields = is_null($showFields)?true:$showFields;
        $this->showFields = $showFields;
    }
    public function getShowFields(){
        return $this->showFields;
    }
    //--------------------------------------------------------------------
    public function setShowAltitude($showAltitude)
    {
        $showAltitude = is_null($showAltitude)?true:$showAltitude;
        $this->showAltitude = $showAltitude;
    }
    public function getShowAltitude(){
        return $this->showAltitude;
    }
    //--------------------------------------------------------------------
    public function setFieldsReadOnly($fieldsReadOnly)
    {
        $fieldsReadOnly = is_null($fieldsReadOnly)?true:$fieldsReadOnly;
        $this->fieldsReadOnly = $fieldsReadOnly;
    }
    public function getFieldsReadOnly(){
        return $this->fieldsReadOnly;
    }
    //--------------------------------------------------------------------
    public function setShowAllJson($fieldAllJson)
    {
        $fieldAllJson = is_null($fieldAllJson)?true:$fieldAllJson;
        $this->fieldAllJson = $fieldAllJson;
    }
    public function getShowAllJson(){
        return $this->fieldAllJson;
    }
    //--------------------------------------------------------------------
    public function setButtonClass($buttonClass)
    {
        $buttonClass = is_null($buttonClass)?self::BUTTON_CLASS:$buttonClass;
        $this->buttonClass = $buttonClass;
        $btnGeo = $this->getBtnGeo();
        $btnGeo->class = $buttonClass;
    }
    public function getButtonClass(){
        return $this->buttonClass;
    }
    public function setButtonLabel($buttonLabel)
    {
        $buttonLabel = is_null($buttonLabel)?self::BUTTON_LABEL:$buttonLabel;
        $this->buttonLabel = $buttonLabel;
        $btnGeo = $this->getBtnGeo();
        $btnGeo->setLabel($buttonLabel);
    }
    public function getButtonLabel(){
        return $this->buttonLabel;
    }
    public function setButtonIcon($buttonIcon)
    {
        $buttonIcon = is_null($buttonIcon)?self::BUTTON_ICON:$buttonIcon;
        $this->buttonIcon = $buttonIcon;
        $btnGeo = $this->getBtnGeo();
        $btnGeo->setImage($buttonIcon);
    }
    public function getButtonIcon(){
        return $this->buttonIcon;
    }
    //--------------------------------------------------------------------
    public function setFeedBackIcon($feedBackIcon)
    {
        $this->feedBackIcon = $feedBackIcon;
    }
    public function getFeedBackIcon(){
        $this->feedBackIcon = is_null($this->feedBackIcon)?self::FEEDBACK_ICON:$this->feedBackIcon;
        return $this->feedBackIcon;
    }
    public function setFeedBackColor($feedBackColor)
    {
        $this->feedBackColor = $feedBackColor;
    }
    public function getFeedBackColor(){
        $this->feedBackColor = is_null($this->feedBackColor)?self::FEEDBACK_COLOR:$this->feedBackColor;
        return $this->feedBackColor;
    }
    public function setFeedBackSize($feedBackSize)
    {
        $this->feedBackSize = $feedBackSize;
    }
    public function getFeedBackSize(){
        $this->feedBackSize = is_null($this->feedBackSize)?self::FEEDBACK_SIZE:$this->feedBackSize;
        return $this->feedBackSize;
    }
    //--------------------------------------------------------------------
    public function setIdDivGeo($idDivGeo)
    {
        $this->idDivGeo = $idDivGeo;
    }
    public function getIdDivGeo(){
        return $this->idDivGeo;
    }
    //--------------------------------------------------------------------
    private function setBtnGeo(){
        $id = $this->getIdDivGeo();
        $btnGeo = new TButton('btnGeo');
        $btnGeo->class = self::BUTTON_CLASS;
        $btnGeo->setLabel(self::BUTTON_LABEL);
        $btnGeo->setImage(self::BUTTON_ICON);
        $btnGeo->addFunction("fd5GetLocation('".$id."',".json_encode($this->getShowAltitude()).",".json_encode($this->getShowAllJson()).")");
        $this->btnGeo = $btnGeo;
        return $btnGeo;
    }
    private function getBtnGeo(){
        return $this->btnGeo;
    }
    //--------------------------------------------------------------------
    private function getDivFeedBack(){
        $id = $this->getIdDivGeo();
        $html = '<i class="'.$this->getfeedBackIcon().'" style="color: '.$this->getfeedBackColor().'; font-size: '.$this->getfeedBackSize().';"></i>';
        $div = new TElement('div');
        $div->class = 'fd5FeedBackCordLat';
        $div->setProperty('id',$id.'_feedback');
        $div->setProperty('style', 'display:none;');        
        $div->add($html);
        return $div;
    }
    private function getNumericField($idField,$label,$boolRequired){
        $numericField = new TFormDinNumericField($idField,$label,18,$boolRequired,16,false,null,-90,90,false,null,null,null,null,null,null,true,null,'.');
        if( $this->getFieldsReadOnly() ){
            $numericField->setReadOnly(true);
        }
        $adiantiObj = $numericField->getAdiantiObj();
        return $adiantiObj;
    }
    private function getHiddenField($idField,$boolRequired){
        $hiddenField = new TFormDinHiddenField($idField,null,$boolRequired);
        if( $this->getFieldsReadOnly() ){
            $hiddenField->setReadOnly(true);
        }
        $adiantiObj = $hiddenField->getAdiantiObj();
        return $adiantiObj;
    }
    private function getAltitudeField($idField,$label,$boolRequired){
        $adiantiObj = null;
        if( $this->getShowAltitude() == true){
            if( $this->getShowFields() == true){
                $adiantiObj = $this->getNumericField($idField,$label,$boolRequired);
            }else{
                $adiantiObj = $this->getHiddenField($idField,$boolRequired);
            }
        }
        return $adiantiObj;
    }
    private function getFieldAllJson($idField,$boolRequired){
        $adiantiObj = null;
        if( $this->getShowAllJson() == true){
            $adiantiObj = $this->getHiddenField($idField,$boolRequired);
        }
        return $adiantiObj;
    }
    //--------------------------------------------------------------------
    private function getDivGeo($idField,$boolRequired){
        $this->setBtnGeo();
        
        $scriptJsGeo = new TElement('script');
        $scriptJsGeo->setProperty('src', 'app/lib/widget/FormDin5/javascript/FormDin5GeoLocation.js?appver='.FormDinHelper::version());
        
        $divGeo = new TElement('div');
        $divGeo->class = 'fd5DivCordLat';
        $divGeo->setProperty('id',$this->getIdDivGeo().'_cordlatdiv');
            
        $adiantiObjLat = null;
        $adiantiObjLon = null;
        if( $this->getShowFields() == true){
            $adiantiObjLat = $this->getNumericField($idField.'_lat','Latitude',$boolRequired);    
            $adiantiObjLon = $this->getNumericField($idField.'_lon','Longitude',$boolRequired);
        }else{
            $adiantiObjLat = $this->getHiddenField($idField.'_lat',$boolRequired);
            $adiantiObjLon = $this->getHiddenField($idField.'_lon',$boolRequired);
        }

        $divGeo->add($this->getBtnGeo());
        $divGeo->add($this->getDivFeedBack());
        $divGeo->add($this->getFieldAllJson($idField.'_json',$boolRequired));
        $divGeo->add($adiantiObjLat);
        $divGeo->add($adiantiObjLon);
        $divGeo->add($this->getAltitudeField($idField.'_alt','Altitude',$boolRequired));
        $divGeo->add($scriptJsGeo);
        return $divGeo;
    }    
}