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
 * Classe generica de campos do Adianti
 *
 * Junta parte das classes FormDin TControl e TElement
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
class TFormDinGenericField
{
    protected $adiantiObj;
    protected $labelTxt;
    protected $labelObj;

    private $tooltip;
    private $readOnly;
    private $class = array();
    private $id;
    
    /**
     *
     * @param object $objAdiantiField - 1: Objeto de campo do Adianti
     * @param string $id              - 2: Id do campos
     * @param string $label           - 3: Label do campo
     * @param boolean $boolRequired   - 4: Obrigatorio ou não. DEFAULT = False.
     * @param string $strValue        - 5: Texto preenchido
     * @param string $placeholder     - 6: PlaceHolder é um Texto de exemplo
     */
    public function __construct($adiantiObj
                               ,string $id
                               ,$label
                               ,$boolRequired = false
                               ,string $value=null
                               ,string $placeholder =null)
    {
        $this->setAdiantiObj($adiantiObj);
        $this->setLabelTxt($label);
        $this->setLabel($label,$boolRequired);
        $this->setId($id);
        $this->setValue($value);
        $this->setRequired($boolRequired);
        $this->setPlaceHolder($placeholder);
        return $this->getAdiantiObj();
    }
    //------------------------------------------------------------------------------    
    /**
     * Seta um objeto Adianti
     * @return object 
     */  
    public function setAdiantiObj($adiantiObj){
        if( empty($adiantiObj) ){
            throw new InvalidArgumentException(TFormDinMessage::ERROR_FD5_OBJ_ADI);
        }
        if( !is_object($adiantiObj) ){
            throw new InvalidArgumentException(TFormDinMessage::ERROR_FD5_OBJ_ADI);
        }        
        return $this->adiantiObj=$adiantiObj;
    }
    /**
     * Retorna um campo do Adianti
     * @return object 
     */    
    public function getAdiantiObj(){
        return $this->adiantiObj;
    }
    /**
     * Retorna objeto do tipo campo do Adianti
     * @return object 
     */
    public function getAdiantiField(){
        return $this->getAdiantiObj();
    }
    //------------------------------------------------------------------------------    
    /**
     * Seta o texto do Label do campo
     * @param string $label
     */
    protected function setLabelTxt($label){
        $this->labelTxt = $label;
    }
    /**
     * Retorna o texto do Label do campo
     * @return string
     */
    public function getLabelTxt(){
        return $this->labelTxt;
    }
    /**
     * Seta o Label Adianti, criando um objeto Label
     *
     * @param string $label         -1: Texto do Label
     * @param boolean $boolRequired -2: Obrigatorio ou não. DEFAULT = False.
     */
    public function setLabel($label,$boolRequired){
        if(!empty($label)){
            $this->setLabelTxt($label);
            $formDinLabelField = new TFormDinLabelField($label,$boolRequired);
            $label = $formDinLabelField->getAdiantiObj();
        }
        $this->labelObj = $label;
    }
    /**
     * Retorna objeto do tipo campo do Adianti
     * @return object
     */
    public function getLabel(){
        return $this->labelObj;
    }
    //---------------------------------------------------------------
    public function setId($id){
        $adiantiObj = $this->getAdiantiObj();
        if($adiantiObj instanceof TElement){
            $adiantiObj->id = $id;
        } elseif($adiantiObj instanceof TText){
            $adiantiObj->id = $id;
        }else{
            $adiantiObj->setId($id);
        }
        $this->id = $id;
    }
    public function getId(){
        return $this->id;
    }
    //---------------------------------------------------------------
    public function setRequired($boolRequired){
        if($boolRequired){
            $strLabel = $this->getLabel();
            $this->getAdiantiObj()->addValidation($strLabel, new TRequiredValidator);
        }
    }
    public function isRequired()
    {
        return $this->getAdiantiObj()->isRequired();
    }
    //---------------------------------------------------------------
    public function getValidations()
    {
        return $this->getAdiantiObj()->getValidations();
    }
    /**
     * Add a field validator of the Adianti 
     * @param $label Field name
     * @param $validator TFieldValidator object
     * @param $parameters Aditional parameters
     * @return void
     */
    public function addValidation($label, $validator, $parameters = NULL)
    {
        $this->getAdiantiObj()->addValidation($label, $validator, $parameters);
    }
    //---------------------------------------------------------------    
    /**
     * Remove a field validator of the Adianti 
     * @param $validator TFieldValidator object
     * @return void
     */
    public function removeValidation($validator)
    {
        //$this->validations[] = array($label, $validator, $parameters);
        $listValidation = $this->getAdiantiObj()->getValidations();
        if(CountHelper::count($listValidation)>0){
            $newListValidation = array();
            foreach ($listValidation as $validation){
                if( $validation[1] != $validator ){
                    $newListValidation[]=$validation;
                }
            }
        }

        print_r($this->getAdiantiObj());
        $refObject = new ReflectionObject( $this->getAdiantiObj() );
        $props     = $refObject->getProperties(ReflectionProperty::IS_PRIVATE);
        //$refProperty = $refObject->getProperty( 'validations' );
        //$refProperty->setAccessible( true );
        //$refProperty->setValue($this->getAdiantiObj(), $newListValidation);   
    }
    //---------------------------------------------------------------
    public function setValue($value){
        if(!empty($value)){
            $this->getAdiantiObj()->setValue($value);
        }
    }
    public function getValue(){
        return $this->getAdiantiObj()->getValue();
    }

    public function setPlaceHolder($placeholder){
        if(!empty($placeholder)){
            $this->getAdiantiObj()->placeholder = $placeholder;
        }
    }

    public function getPlaceHolder(){
        return $this->getAdiantiObj()->placeholder;
    }
    //-----------------------------------------------------------------------------
    /**
    * Método para criar ajuda on-line.
    * O parametro $strHelpFile recebe o nome de um arquivo com conteudo html/php para ser exibido.
    * O arquivo deverá estar no diretório app/resources/
    *  
    * Poder ser informada tambem o endereço (url) da pagina de help
    * 
    * <code> 
    * Exemplo01: $nom->setHelpFile('Nome da Pessoa',200,500,'ajuda_form01.html');
    * Exemplo02: $nom->setHelpFile('Nome da Pessoa',200,500,'http://localhost/sistema/texto_ajuda.html');
    * Exemplo03: $nom->setHelpFile('Nome da Pessoa',200,500,'Meu texto de ajuda', null, null, false);
    * </code>
    * 
    * @param mixed $strWindowHeader- 01:
    * @param mixed $intShowHeight  - 02: DEPRECATED: INFORME NULL para remover o Warning
    * @param mixed $intShowWidth   - 03: DEPRECATED: INFORME NULL para remover o Warning
    * @param mixed $strHelpFile    - 04: nome do arquivo que será carregado dentro do box
    * @param mixed $strButtonImage - 05: imagem que aparecerá na frente do label
    * @param boolean $boolReadOnly
    * @param boolean $showFile     - true mostra o conteudo de um arquivo, FALSE mostra a mensagem de texto informada no $strHelpFile
    */
    public function setHelpOnLine( $strWindowHeader = null
    							 , $intShowHeight = null
    		                     , $intShowWidth = null
    		                     , $strHelpFile = null
    		                     , $strButtonImage = null
    		                     , $boolReadOnly = null
    							 , $showFile = true)
    {
        /*
        $strHelpFile      =is_null( $strHelpFile ) ? $this->getId() : $strHelpFile;
        $this->helpFile[0]=$strHelpFile;

        if ( $strHelpFile )
        {
            $this->helpFile[1]=is_null( $strWindowHeader ) ? "Ajuda on-line" : $strWindowHeader;
            $this->helpFile[2]=is_null( $intShowHeight ) ? 600 : $intShowHeight;
            $this->helpFile[3]=is_null( $intShowWidth ) ? 800 : $intShowWidth;
            $this->helpFile[4]=is_null( $strButtonImage ) ? $this->getBase().'imagens/icon_help-16x16.png' : $strButtonImage;
            $this->helpFile[5]=is_null( $boolReadOnly ) ? false : $boolReadOnly;
           	$this->helpFile[6]='';
           	$this->helpFile[7]=is_null( $showFile ) ? true : $showFile;
            if( preg_match('/\.\.\//',$this->getBase()) > 0 ) {
            	$this->helpFile[6]=APLICATIVO;
			}
            if ( strpos( $this->helpFile[4], '/' ) === false ) {
            	$this->helpFile[4] = $this->getBase().'imagens/'.$this->helpFile[4];
            }
        }
        */
    }    
    //------------------------------------------------------------------------------
	/**
	 * Set um Toolpit do FormDin para funcionar com Adianti 
     * em um determinado campo pode ser usado com
	 * @param string $strTitle - 1: Titulo
	 * @param string $strText  - 2: Texto que irá aparecer
	 * @param string $strImagem- 3: NOT_IMPLEMENTED
	 * @return TControl
	 */
	public function setTooltip($strTitle=null,$strText=null,$strImagem=null)
	{
        $text = is_null($strTitle)?$strText:$strTitle;
        $this->tooltip = $text;
		$this->getAdiantiObj()->setTip($text);
	}
	public function getTooltip()
	{
		return $this->tooltip;
    }
    //------------------------------------------------------------------------------
    /**
     * Metodo criado para melhorar a retrocompatibilidade com Formdi4s
     */
    public function setExampleText($strNewValue=null)
	{
		$this->setTooltip($strNewValue);
    }
    /**
     * Metodo criado para melhorar a retrocompatibilidade com Formdi4s
     */    
	public function getExampleText()
	{
		return $this->getTooltip();
    }    
	//------------------------------------------------------------------------------    
	public function setReadOnly($boolNewValue=null)
	{
        $this->readOnly = $boolNewValue;
        if($boolNewValue){
            $this->getAdiantiObj()->setEditable(FALSE);
        }else{
            $this->getAdiantiObj()->setEditable(TRUE);
        }
	}
	public function getReadOnly()
	{
		return ( $this->readOnly === true) ? true : false;
    }
	//------------------------------------------------------------------------------    
	public function setClass($className)
	{
        $this->class[]=$className;
        $className = implode(' ', $this->class);
        $this->getAdiantiObj()->setProperty('class',$className);
	}
	public function getClass()
	{
		return $this->getAdiantiObj()->getProperty('class');
    }
    //------------------------------------------------------------------------------     
    /**
     * DEPRECADED - change to setClass.
     *
     * @deprecated
     * @codeCoverageIgnore
     *
     * @param mixed $mixProperty
     * @param string $newValue
     */
    public function setCss( $mixProperty, $newValue = null )
    {
        if( !empty($mixProperty) ){
            $arrBacktrace = debug_backtrace();
            ValidateHelper::validadeMethod(ValidateHelper::WARNING
                                          ,ValidateHelper::MSG_DECREP
                                          ,'setCss'
                                          ,'use o metodo setClass'
                                          ,$arrBacktrace[0]['file']
                                          ,$arrBacktrace[0]['line']
                                        );
        }

    }
}