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

class TFormDinNumericField extends TFormDinGenericField
{
    const COMMA = ',';
    const DOT = '.';

    protected $adiantiObj;
    protected $label;
    protected $decimalsSeparator;
    protected $thousandSeparator;

    /**
     * Campo de entrada de dados texto livre
     * Reconstruido FormDin 4 Sobre o Adianti 7
     *
     * @param string $id                 - 1: ID do campo
     * @param string $strLabel           - 2: Label do campo, que irá aparecer na tela do usuario
     * @param integer $intMaxLength      - 3: Quantidade maxima de digitos.
     * @param boolean $boolRequired      - 4: Obrigatorio
     * @param integer $decimalPlaces     - 5: Quantidade de casas decimais.
     * @param boolean $boolNewLine       - 6: Campo em nova linha. Default = true = inicia em nova linha, false = continua na linha anterior 
     * @param string $value              - 7: valor inicial do campo
     * @param string $strMinValue        - 8: valor minimo permitido. Null = não tem limite.
     * @param string $strMaxValue        - 9: valor maxima permitido. Null = não tem limite.
     * @param boolean $boolFormatInteger -10: Inteiros com ou sem ponto de separação. Recebe: (virgula), (ponto), true = ponto, false = sem nada
     * @param string $strDirection       -11: NOT_IMPLEMENTED
     * @param boolean $boolAllowZero     -12: NOT_IMPLEMENTED
     * @param boolean $boolAllowNull     -13: NOT_IMPLEMENTED
     * @param boolean $boolLabelAbove    -14: Label sobre o campo. Default FALSE = Label mesma linha, TRUE = Label acima
     * @param boolean $boolNoWrapLabel   -15: NOT_IMPLEMENTED
     * @param string $strHint            -16: Texto Tooltip
     * @param boolean $replaceOnPost     -17: FORMDIN5: TRUE: process mask when editing and saving
     * @param string $placeholder        -18: FORMDIN5: Texto do Place Holder
     * @param string $decimalsSeparator  -19: FORMDIN5: separador decimal
     * @return TFormDinNumericField
     */
    public function __construct(string $id
                               ,string $label
                               ,int $intMaxLength = null
                               ,$boolRequired = false
                               ,int $decimalPlaces=null
                               ,$boolNewLine=null
                               ,$value=null
                               ,$strMinValue=null
                               ,$strMaxValue=null
                               ,$boolFormatInteger=null
                               ,$strDirection=null
                               ,$boolAllowZero=null
                               ,$boolAllowNull=null
                               ,$boolLabelAbove=null
                               ,$boolNoWrapLabel=null
                               ,string $strHint=null
                               ,$replaceOnPost=true
                               ,$placeholder=null
                               ,string $decimalsSeparator=null
                               )
    {
        $this->setThousandSeparator($boolFormatInteger);
        $this->setDecimalsSeparator($decimalsSeparator);
        $decimalsSeparator = $this->getDecimalsSeparator();
        $thousandSeparator = $this->getThousandSeparator();

        $adiantiObj = new TNumeric($id, $decimalPlaces, $decimalsSeparator, $thousandSeparator, $replaceOnPost);
        parent::__construct($adiantiObj,$id,$label,$boolRequired,$value,$placeholder);

        $this->setMaxLength($intMaxLength);
        $this->setMinValue($strMinValue);
        $this->setMaxValue($strMaxValue);
        $this->setExampleText($strHint);
        return $this->getAdiantiObj();
    }

    public function getDecimalsSeparator(){
        return $this->decimalsSeparator;
    }

    public function setDecimalsSeparator($decimalsSeparator){
        $separator = null;
        if(empty($decimalsSeparator)){
            $separator = self::COMMA;
        }elseif($decimalsSeparator === true){
            $separator = self::COMMA;
        }elseif($decimalsSeparator == self::DOT){
            $separator = self::DOT;
        }elseif($decimalsSeparator == self::COMMA){
            $separator = self::COMMA;
        }
        $this->decimalsSeparator = $separator;
    }

    public function getThousandSeparator(){
        return $this->thousandSeparator;
    }

    public function setThousandSeparator($thousandSeparator){
        $separator = null;
        if($thousandSeparator === true){
            $separator = self::DOT;
        }elseif($thousandSeparator == self::DOT){
            $separator = self::DOT;
        }elseif($thousandSeparator == self::COMMA){
            $separator = self::COMMA;
        }
        $this->thousandSeparator = $separator;
    }

    public function setMaxLength($intMaxLength)
    {
        if($intMaxLength){
            $strLabel = $this->getLabelTxt();
            $this->getAdiantiObj()->addValidation($strLabel, new TMaxLengthValidator, array($intMaxLength));
        }
    }
    public function setMinValue($strMinValue)
    {
        if($strMinValue){
            $strLabel = $this->getLabelTxt();
            $this->getAdiantiObj()->addValidation($strLabel, new TMinValueValidator, array($strMinValue));
        }
    }

    public function setMaxValue($strMaxValue)
    {
        if($strMaxValue){
            $strLabel = $this->getLabelTxt();
            $this->getAdiantiObj()->addValidation($strLabel, new TMaxValueValidator, array($strMaxValue));
        }
    }
}