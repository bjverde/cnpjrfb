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
 * Classe para criação campo do tipo TFormDinDate
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
class TFormDinTime extends TFormDinGenericField
{    
    private $minValue;
    private $maxValue;
    private $maskType;
    private $databaseMask;
    
    /**
     * Método para criar campo de edição de horas
     * ------------------------------------------------------------------------
     * Esse é o FormDin 5, que é uma reconstrução do FormDin 4 Sobre o Adianti 7.X
     * os parâmetros do metodos foram marcados veja documentação da classe para
     * saber o que cada marca singinifica.
     * ------------------------------------------------------------------------
     *
     * @param string  $strName        -  1: id do campo
     * @param string  $strLabel       -  2: Rotulo do campo que irá aparece na tela
     * @param boolean $boolRequired   -  3: True = Obrigatorio; False (Defalt) = Não Obrigatorio
     * @param string  $strMinValue    -  4: Menor Valor
     * @param string  $strMaxValue    -  5: Maior valor
     * @param string  $strMaskType    -  6: HM, HMS
     * @param boolean $boolNewLine    -  7: Em nova linha. DEFAULT = true
     * @param string  $strValue       -  8:
     * @param boolean $boolLabelAbove -  9:
     * @param boolean $boolNoWrapLabel- 10:
     * @return TTime
     */
    public function __construct(string $id
                              , string $label=null
                              , $boolRequired=false
                              , $strMinValue=null
                              , $strMaxValue=null
                              , $strMaskType=null
                              , $boolNewLine=null
                              , $strValue=null
                              , $boolLabelAbove=null
                              , $boolNoWrapLabel=null
                              , $strExampleText=null
                              )
    {
        $adiantiObj = new TTime($id);
        parent::__construct($adiantiObj,$id,$label,$boolRequired,$strValue,$strExampleText);
        $this->setMask($strMaskType);
        $this->setMaxValue($strMaxValue);
        $this->setMinValue($strMinValue);
        return $this->getAdiantiObj();
    }

    public function getMask(){
        return $this->maskType;
    }
    public function setMask($strMaskType){
        if( is_null($strMaskType) ){
            $strMaskType = 'hh:ii';
        }
        $strMaskType = DateTimeHelper::maskDateFormDin4ToAdianit($strMaskType);
        $this->maskType = $strMaskType;
        $this->getAdiantiObj()->setMask($strMaskType);
    }
	//--------------------------------------------------------------------------   
    //--------------------------------------------------------------------------
	public function getMaxValue()
	{
		return $this->maxValue;
	}    
    public function setMaxValue($strMaxValue)
    {
        if( !empty($strMaxValue) ){
            $strLabel = $this->getLabelTxt();
            $parameters[0] = $this->getMask();
            $parameters[1] = $strMaxValue;
            $this->getAdiantiObj()->addValidation($strLabel, new TFormDinDateValidatorMax, $parameters);
            $this->maxValue = $strMaxValue;
        }
    }
    //--------------------------------------------------------------------------
	public function getMinValue()
	{
		return $this->minValue;
	}
    public function setMinValue($strMinValue)
    {
        if( !empty($strMinValue) ){
            $strLabel = $this->getLabelTxt();
            $parameters[0] = $this->getMask();
            $parameters[1] = $strMinValue;
            $this->getAdiantiObj()->addValidation($strLabel, new TFormDinDateValidatorMin, $parameters);
            $this->minValue = $strMinValue;
        }
    }
}