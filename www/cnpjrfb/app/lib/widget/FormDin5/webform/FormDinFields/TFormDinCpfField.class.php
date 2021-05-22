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

use Adianti\Validator\TCPFValidator;

class TFormDinCpfField extends TFormDinMaskField
{

    private $alwaysValidate =  true;

   /**
    * Campo de entrada de dados do tipo CPF
    * Reconstruido FormDin 4 Sobre o Adianti 7
    *
    * @param string  $strName           - 1: ID do campo
    * @param string  $strLabel          - 2: Label do campo, que irá aparecer na tela do usuario
    * @param boolean $boolRequired      - 3: Campo obrigatório ou não. Default FALSE = não obrigatório, TRUE = obrigatório
    * @param string  $strValue          - 4: Valor inicial do campo
    * @param boolean $boolNewLine       - 5: Default TRUE = campo em nova linha, FALSE continua na linha anterior
    * @param boolean $boolLabelAbove    - 6: Label sobre o campo. Default FALSE = Label mesma linha, TRUE = Label acima
    * @param boolean $boolNoWrapLabel   - 7: NOT_IMPLEMENTED true ou false para quebrar ou não o valor do label se não couber na coluna do formulario
    * @param string  $strInvalidMessage - 8: Mensagem que vai aparece em caso de CPF inválido
    * @param boolean $boolAlwaysValidate- 9: NOT_IMPLEMENTED
    * @param string  $strJsCallback     -10: NOT_IMPLEMENTED Js Callback
    * @param string  $strExampleText    -11: FORMDIN5: PlaceHolder é um Texto de exemplo
    * @param boolean $boolSendMask      -12: FORMDIN5: Se as mascara deve ser enviada ou não para o post. DEFAULT = False.    
    *
    * @return TFormDinCpfField Field
    */    
    public function __construct( $id
                               , $strLabel=null
                               , $boolRequired=false
                               , $strValue=null
                               , $boolNewLine=null
                               , $boolLabelAbove=null
                               , $boolNoWrapLabel=null
                               , $strInvalidMessage=null
                               , $boolAlwaysValidate=true
                               , $strJsCallback=null
                               , $strExampleText=null
                               , $boolSendMask=false )
    {
        parent::__construct($id
                           ,$strLabel
                           ,$boolRequired
                           ,'999.999.999-99'
                           ,$boolNewLine
                           ,$strValue
                           ,$boolLabelAbove
                           ,$boolNoWrapLabel
                           ,$strExampleText
                           ,$boolSendMask
                        );
        $this->setAlwaysValidate($boolAlwaysValidate); 
        return $this->getAdiantiObj();
    }

    public function setAlwaysValidate($boolAlwaysValidate=true)
	{
		$this->alwaysValidate = $boolAlwaysValidate;
        if($boolAlwaysValidate == true){
            $strLabel = $this->getLabelTxt();
            $this->getAdiantiObj()->addValidation($strLabel, new TCPFValidator); 
        }
	}

	public function getAlwaysValidate()
	{
		return $this->alwaysValidate;
	}
}