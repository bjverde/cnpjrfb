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
class TFormDinButton {

    protected $adiantiObj;
    protected $objForm;
    protected $objAction;
    protected $label;
    protected $confirmMessage;
    protected $strOnClick;
    protected $strHint;

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
    * @param object  $objForm           - 1 : FORMDIN5 Objeto do Form, é só informar $this
    * @param string  $mixValue          - 2 : Label do Botão. No FormDin5 não aceita array('Gravar', 'Limpar')
    * @param string  $strNameId         - 3 : Id do Botão. Se ficar null será utilizado o $strAction
    * @param mixed   $strAction         - 4 : Nome do metodo da ação (string) no mesmo Form ou  Array [FormDestino,actionsName]
    * @param string  $strOnClick        - 5 : Nome da função javascript que será executada no onClick ou script da função. Vai desativar o parametro 5
    * @param string  $strConfirmMessage - 6 : Mensagem de confirmação, para utilizar o confirme sem utilizar javaScript explicito. Se o parametro 4 for informado não vai executar
    * @param boolean $boolNewLine       - 7 : Em nova linha. DEFAULT = true
    * @param boolean $boolFooter        - 8 : Mostrar o botão no rodapé do form. DEFAULT = true
    * @param string  $strImage          - 9 : Imagem no botão. Evite usar no lugar procure usar a propriedade setClass. Busca pasta imagens do base ou no caminho informado
    * @param string  $strImageDisabled  -10 : NOT_IMPLEMENTED Imagem no desativado. Evite usar no lugar procure usar a propriedade setClass. Busca pasta imagens do base ou no caminho informado
    * @param string  $strHint           -11 : Texto hint para explicar
    * @param string  $strVerticalAlign  -12 : NOT_IMPLEMENTED
    * @param boolean $boolLabelAbove    -13 : NOT_IMPLEMENTED Position text label. DEFAULT is false. NULL = false. 
    * @param string  $strLabel          -14 : NOT_IMPLEMENTED Text label 
    * @param string  $strHorizontalAlign-15 : NOT_IMPLEMENTED Text Horizontal align. DEFAULT = center. Values center, left, right
    * @return TButton|string|array
    */
    public function __construct($objForm
                                , $label
                                , $strNameId
                                , $strAction
                                , $strOnClick=null
                                , $strConfirmMessage=null
                                , $boolNewLine=null
                                , $boolFooter=null
                                , $strImage=null
                                , $strImageDisabled=null
                                , $strHint=null
                                , $strVerticalAlign=null
                                , $boolLabelAbove=null
                                , $strLabel=null
                                , $strHorizontalAlign=null)
    {
        $adiantiObj = null;
        if( is_array($strAction) ){
            $strNameId = empty($strNameId)?$strAction[0].$strAction[1]:$strNameId;
        }else{
            $strNameId = empty($strNameId)?$strAction:$strNameId;
        }
        $adiantiObj = new TButton('btn'.$strNameId);
        $this->setObjForm($objForm);
        $this->setAdiantiObj($adiantiObj);
        $this->setLabel($label);
        $this->addFunction($strOnClick);
        $this->setAction($strAction);
        $this->setImage($strImage);
        $this->setConfirmMessage($strConfirmMessage);
        $this->setHint($strHint);
        return $this->getAdiantiObj();
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

    public function setLabel($label)
    {
        if( is_array($label) ){
            $msg = 'O parametro $mixValue não recebe mais um array! Faça uma chamada por Action';
            ValidateHelper::migrarMensage($msg
                                         ,ValidateHelper::ERROR
                                         ,ValidateHelper::MSG_CHANGE
                                         ,__CLASS__,__METHOD__,__LINE__);
        }else{
            $this->label=$label;
            $this->getAdiantiObj()->setLabel($label);
        }
    }
    public function getLabel(){
        return $this->label;
    }

    public function setAdiantiObj($adiantiObj)
    {
        if( empty($adiantiObj) ){
            throw new InvalidArgumentException(TFormDinMessage::ERROR_FD5_OBJ_ADI);
        }
        if( !is_object($adiantiObj) ){
            $msg = 'o metodo addButton MUDOU! o primeiro parametro agora recebe $this! o Restando está igual ;-)';
            ValidateHelper::migrarMensage($msg
                                         ,ValidateHelper::ERROR
                                         ,ValidateHelper::MSG_CHANGE
                                         ,__CLASS__,__METHOD__,__LINE__);
        }
        return $this->adiantiObj=$adiantiObj;
    }
    public function getAdiantiObj(){
        return $this->adiantiObj;
    }

    public function setAction($strAction)
    {
        if( empty($strAction) && empty($this->getStrOnClick()) ){
            throw new InvalidArgumentException(TFormDinMessage::ERROR_EMPTY_INPUT.': strAction');
        }else if( !empty($strAction) && !empty($this->getStrOnClick()) ){
            throw new InvalidArgumentException(TFormDinMessage::ERROR_INPUT_PARAMETER_CONFLICT.' não informe os paramentros 4 (strAction) e 5 (strOnClick) aos mesmo tempo');
        }else if( !empty($strAction) && empty($this->getStrOnClick()) ){
            $action = null;
            if( is_array($strAction) ){
                $action = new TAction(array($strAction[0],$strAction[1]));
            }else{
                $objForm = $this->getObjForm();
                $action = new TAction(array($objForm, $strAction));
            }        
            $label = $this->getLabel();
            $this->getAdiantiObj()->setAction($action,$label);
        }
    }
    public function getAction()
    {
        $this->getAdiantiObj()->getAction();
    }

    public function setImage($strImage)
    {
        if( !empty($strImage) ){
            $this->getAdiantiObj()->setImage($strImage);
        }
    }

    /**
     * Define um PopOver
     *
     * @param string $title = título que irá aparecer
     * @param string $side  = top, bottom, left, right
     * @param string $content = conteudo em HTML
     * @return void
     */
    public function setPopover($title,$side='top',$content=null)
    {
        $this->getAdiantiObj()->popover  = 'true';
        $this->getAdiantiObj()->popside  = $side;
        $this->getAdiantiObj()->poptitle = $title;
        if( !empty($content) ){
            $this->getAdiantiObj()->popcontent = $content;
        }
    }

    /**
     * Adds a parameter to the action
     * @param  $param = parameter name
     * @param  $value = parameter value
     */    
    public function setParameter($param,$value)
    {
        $action = $this->getAdiantiObj()->getAction();
        $action->setParameter($param,$value);
    }

    /**
     * Returns a parameter
     * @param  $param = parameter name
     */
    public function getParameter($param)
    {
        $action = $this->getAdiantiObj()->getAction();
        return $action->getParameter($param);
    }

    /**
     * Return the Action Parameters
     */
    public function getParameters()
    {
        $action = $this->getAdiantiObj()->getAction();
        return $action->getParameters();
    }

	public function getStrOnClick()
	{
		return $this->strOnClick;
	}
	private function setStrOnClick($strOnClick)
	{
		return $this->strOnClick = $strOnClick;
	}

    /**
     * Add a JavaScript function to be executed by the button
     * @param $function A piece of JavaScript code
     */
    public function addFunction($strOnClick)
    {
        if (!empty($strOnClick)){
            $this->setStrOnClick($strOnClick);
            $this->getAdiantiObj()->addFunction($strOnClick);
        }
    }

    public function setConfirmMessage($confirmMessage)
    {

        if( !empty($confirmMessage) && !empty($this->getStrOnClick()) ){
            throw new InvalidArgumentException(TFormDinMessage::ERROR_INPUT_PARAMETER_CONFLICT.' não informe os paramentros 5 (strOnClick) e 6 (confirmMessage) aos mesmo tempo');
        }else if( !empty($confirmMessage) && empty($this->getStrOnClick()) ){
            $this->confirmMessage=$confirmMessage;
            $class = get_class ( $this->getObjForm() );
            $stringJs = 'if (confirm(\''.$confirmMessage.'\') == true) { __adianti_load_page(\'index.php?class='.$class.'\'); }';
            $this->getAdiantiObj()->addFunction($stringJs);
        }
    }
    public function getConfirmMessage()
    {
        return $this->confirmMessage;
    }

    public function getHint()
	{
		return $this->strHint;
	}
	private function setHint($strHint)
	{
		$this->strHint = $strHint;
        $this->setPopover(null,'top',$strHint);
	}
}
?>