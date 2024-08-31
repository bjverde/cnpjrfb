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
 * @author Pabla Dall'Oglio
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

use Adianti\Validator\TEmailValidator;

class TFormDinEmailField  extends TFormDinTextField
{
    protected $adiantiObj;
    protected $alwaysValidate;
    
    /**
     * Adiciona campo para entrada de endereço eletrônico - e-mail
     * Reconstruido FormDin 4 Sobre o Adianti 7
     *
	 * @param string  $id            -01: ID do campo
	 * @param string  $label         -02: Label do campo, que irá aparecer na tela do usuario
	 * @param integer $intMaxLength  -03: Tamanho maximo de caracteres
	 * @param boolean $boolRequired  -04: Obrigatorio
	 * @param integer $intSize       -05: Tamanho do campo na tela
	 * @param boolean $boolNewLine   -06: Campo em nova linha
	 * @param string  $strValue      -07: valor inicial do campo
	 * @param boolean $boolLabelAbove-08: Label acima, DEFAULT is FALSE na mesma linha
     * @param string $placeholder    -09: FORMDIN5: Texto do Place Holder
     *
     * @return TFormDinEmailField Field
     */   
    public function __construct(  string $id
                                , string $label=null
                                , int $intMaxLength=null
                                , $boolRequired=null
                                , int $intSize=null
                                , $boolNewLine=null
                                , string $strValue=null
                                , $boolLabelAbove=null
                                , $boolNoWrapLabel=null 
                                , string $placeholder=null
                                )
    {
        parent::__construct($id
                           ,$label
                           ,$intMaxLength
                           ,$boolRequired
                           ,$intSize
                           ,$strValue
                           ,$placeholder
                        );
        $this->setAlwaysValidate(TRUE); 
        return $this->getAdiantiObj();
    }

    public function setAlwaysValidate($boolAlwaysValidate=true)
	{
		$this->alwaysValidate = $boolAlwaysValidate;
        if($boolAlwaysValidate == true){
            $strLabel = $this->getLabelTxt();
            $this->getAdiantiObj()->addValidation($strLabel, new TEmailValidator); 
        }
	}

	public function getAlwaysValidate()
	{
		return $this->alwaysValidate;
	}
}