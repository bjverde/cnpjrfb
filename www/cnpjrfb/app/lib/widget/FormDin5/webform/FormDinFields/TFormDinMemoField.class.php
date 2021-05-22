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
 * Classe para criação campo texto simples
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
class TFormDinMemoField extends TFormDinGenericField
{
    private $showCountChar;
    private $intMaxLength;

    private $adiantiObjTText; //Somente obj Adianti TText
    private $adiantiObjFull;  //obj Adianti completo com todos os elementos para fazer o memo

    /**
     * Adicionar campo de entrada de texto com multiplas linhas ( memo ) equivalente ao html textarea
     * ------------------------------------------------------------------------
     * Esse é o FormDin 5, que é uma reconstrução do FormDin 4 Sobre o Adianti 7.X
     * os parâmetros do metodos foram marcados veja documentação da classe para
     * saber o que cada marca singinifica.
     * ------------------------------------------------------------------------
     *
     * @param string  $strName         - 1: ID do campo
     * @param string  $strLabel        - 2: Label
     * @param integer $intMaxLength    - 3: Tamanho maximos
     * @param boolean $boolRequired    - 4: Campo obrigatório ou não. Default FALSE = não obrigatório, TRUE = obrigatório
     * @param integer $intColumns      - 5: Largura use unidades responsivas % ou em ou rem ou vh ou vw. Valores inteiros até 100 serão convertidos para % , acima disso será 100%
     * @param integer $intRows         - 6: Altura use px ou %, valores inteiros serão multiplicados 4 e apresentado em px
     * @param boolean $boolNewLine     - 7: NOT_IMPLEMENTED nova linha
     * @param boolean $boolLabelAbove  - 8: NOT_IMPLEMENTED Label sobre o campo
     * @param boolean $boolShowCounter - 9: NOT_IMPLEMENTED Contador de caracteres ! Só funciona em campos não RichText
     * @param string  $strValue       - 10: texto preenchido
     * @param string $boolNoWrapLabel - 11: NOT_IMPLEMENTED
     * @param string $placeholder     - 12: FORMDIN5 PlaceHolder é um Texto de exemplo
     * @param string $boolShowCountChar 13: FORMDIN5 Mostra o contador de caractes.  Default TRUE = mostra, FASE = não mostra
     * @return TFormDinMemoField
     */
    public function __construct($id
                              , $label=null
                              , $intMaxLength
                              , $boolRequired=null
                              , $intColumns='100%'
                              , $intRows='100%'
                              , $boolNewLine=null
                              , $boolLabelAbove=null
                              , $boolShowCounter=null
                              , $value=null
                              , $boolNoWrapLabel=null
                              , $placeholder=null
                              , $boolShowCountChar=true)
    {
        $this->setAdiantiObjTText($id);
        parent::__construct($this->getAdiantiObjTText(),$id,$label,$boolRequired,$value,$placeholder);
        $this->setSize($intColumns, $intRows);
        $this->setMaxLength($label,$intMaxLength);
        $this->setShowCountChar($boolShowCountChar);
        $this->setAdiantiObjTFull($id,$boolShowCountChar,$intMaxLength);
        return $this->getAdiantiObj();
    }

    public function setAdiantiObjTText($id){
        $this->adiantiObjTText = new TText($id);
    }
    public function getAdiantiObjTText(){
        return $this->adiantiObjTText;
    }

    private function setAdiantiObjTFull( $idField, $boolShowCountChar,$intMaxLength )
    {
        $adiantiObjTText = $this->getAdiantiObjTText();
        $adiantiObj = null;
        if( $boolShowCountChar && ($intMaxLength>=1) ){
            $adiantiObjTText->maxlength = $intMaxLength;
            $adiantiObjTText->setId($idField);
            $adiantiObjTText->setProperty('onkeyup', 'fwCheckNumChar(this,'.$intMaxLength.');');

            $charsText  = new TElement('span');
            $charsText->setProperty('id',$idField.'_counter');
            $charsText->setProperty('name',$idField.'_counter');
            $charsText->setProperty('class', 'tformdinmemo_counter');
            $charsText->add('caracteres: 0 / '.$intMaxLength);

            $script = new TElement('script');
            $script->setProperty('src', 'app/lib/include/FormDin5.js');

            $div = new TElement('div');
            $div->add($adiantiObjTText);
            $div->add('<br>');
            $div->add($charsText);
            $div->add($script);
            $adiantiObj = $div;
        }
        $adiantiObj = empty($adiantiObj)?$adiantiObjTText:$adiantiObj;
        $this->adiantiObjFull = $adiantiObj;
    }
    public function getAdiantiObjFull(){
        return $this->adiantiObjFull;
    }

    public function setMaxLength($label,$intMaxLength)
    {
        $this->intMaxLength = (int) $intMaxLength;
        if($intMaxLength>=1){
            $this->getAdiantiObj()->addValidation($label, new TMaxLengthValidator, array($intMaxLength));
        }
    }

    public function getMaxLength()
    {
        return $this->intMaxLength;
    }

    public function setSize($intColumns, $intRows)
    {
        if(is_numeric($intRows)){
            $intRows = $intRows * 4;
        }else{
            FormDinHelper::validateSizeWidthAndHeight($intRows,true);
        }
        $intColumns = FormDinHelper::sizeWidthInPercent($intColumns);
        $this->getAdiantiObj()->setSize($intColumns, $intRows);
    }

    public function setShowCountChar($showCountChar)
    {
        $this->showCountChar = $showCountChar;
    }
    public function getShowCountChar()
    {
        return $this->showCountChar;
    }
}