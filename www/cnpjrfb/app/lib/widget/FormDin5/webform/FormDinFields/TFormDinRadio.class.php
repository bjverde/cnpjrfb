<?php
/*
 * ----------------------------------------------------------------------------
 * Formdin 5 Framework
 * SourceCode https://github.com/bjverde/formDin5
 * @author Reinaldo A. Barrêto Junior
 * 
 * É uma reconstrução do FormDin 4 Sobre o Adianti 7.X
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
 * Classe para criação campo do tipo Radio
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
class TFormDinRadio extends TFormDinOption
{
    protected $adiantiObj;
    
    /**
     * Cria um RadioGroup com efeito visual de Switch
     * Reconstruido FormDin 4 Sobre o Adianti 7
     * 
     * @param string $strName         - 1: field ID
     * @param string $strLabel        - 2: Label field
     * @param boolean $boolRequired   - 3: TRUE = Required, FALSE = not Required
     * @param array   $mixOptions     - 4: Array Options ou String FormDin 'S=SIM,N=Não'
     * @param boolean $boolNewLine    - 5: TRUE = new line, FALSE = no, DEFAULT ou NULL = FALSE
     * @param boolean $boolLabelAbove - 6: TRUE = Titulo em cima das opções, FALSE = titulo lateral
     * @param string  $mixValue       - 7: Informe o ID do array. Array no formato "key=>key" para identificar a(s) opção(ões) selecionada(s)
     * @param integer $intQtdColumns  - 8: Quantidade de colunas, valor DEFAULT = 1;
     * @param integer $intWidth       - 9: DEPRECATED. Informe NULL para evitar o warning. Largura em Pixels
     * @param integer $intHeight      -10: DEPRECATED. Informe NULL para evitar o warning. Altura em Pixels
     * @param integer $intPaddingItems-11: EPRECATED. Informe NULL para evitar o warning. 
     * @param boolean $boolNoWrapLabel-12: NOT_IMPLEMENTED
     * @param boolean $boolNowrapText -13: NOT_IMPLEMENTED
     * @param boolean $useButton      -14: FORMDIN5 Default FALSE = estilo radio comum, TRUE = estilo tipo botões
     * @param mixed   $strKeyColumn   -15: FORMDIN5 Nome da coluna que será utilizada para preencher os valores das opções
     * @param mixed   $strDisplayColumn-16: FORMDIN5 Nome da coluna que será utilizada para preencher as opções que serão exibidas para o usuário 
     * @return mixed TRadioGroup
     */
    public function __construct($id
                               ,$label=null
                               ,$boolRequired=null
                               ,$mixOptions=null
                               ,$boolNewLine=null
                               ,$boolLabelAbove=null
                               ,$mixValue=null
                               ,$intQtdColumns=null
                               ,$intWidth=null
                               ,$intHeight=null
                               ,$intPaddingItems=null
                               ,$boolNoWrapLabel=null
                               ,$boolNowrapText=null
                               ,$useButton = null
                               ,$strKeyColumn = null
                               ,$strDisplayColumn = null
                               )
    {
        $this->setWidth( $intWidth );
        $this->setHeight( $intHeight );
        $this->setPaddingItems( $intPaddingItems );

        $adiantiObj = new TRadioGroup($id);
        parent::__construct($adiantiObj            //01: Objeto de campo do Adianti
                           ,$id                    //02: ID do campo
                           ,$label                 //03: Label do campo
                           ,$boolRequired          //04: Campo obrigatório. Default FALSE = não obrigatório, TRUE = obrigatório
                           ,$mixOptions            //05: String "S=SIM,N=NAO,..." ou Array dos valores nos formatos: PHP "id=>value", FormDin, PDO ou Adianti
                           ,$boolNewLine           //06: Default TRUE = cria nova linha , FALSE = fica depois do campo anterior
                           ,$boolLabelAbove        //07: Label sobre o campo. Default FALSE = Label mesma linha, TRUE = Label acima
                           ,$mixValue              //08: Informe o ID do array. Array no formato "key=>key" para identificar a(s) opção(ões) selecionada(s)
                           ,null                   //09: Default FALSE = SingleSelect, TRUE = MultiSelect
                           ,$intQtdColumns         //10: Default 1. Num itens que irão aparecer no MultiSelect
                           ,TFormDinOption::CHECK  //11: Define o tipo de input a ser gerado. Ex: select, radio ou check
                           ,null                   //12: Frist Value in Display, use value NULL for required. Para o valor DEFAULT informe o ID do $mixOptions e $strFirstOptionText = '' e não pode ser null
                           ,$strKeyColumn          //13: Nome da coluna que será utilizada para preencher os valores das opções
                           ,$strDisplayColumn      //14: Nome da coluna que será utilizada para preencher as opções que serão exibidas para o usuário
                           ,null
                           ,null                   //16: informações extras do banco de dados que deverão ser adicionadas na tag option do campo select
                        );
        $this->setBreakItems($intQtdColumns);
        $this->setUseButton($useButton);
        $this->setLayout('horizontal');
    }

    public function setWidth($intWidth)
    {
        ValidateHelper::validadeParam('intWidth',$intWidth
                                     ,ValidateHelper::WARNING
                                     ,ValidateHelper::MSG_DECREP
                                     ,__CLASS__,__METHOD__,__LINE__);
    }
    
    public function setHeight($intHeight)
    {
        ValidateHelper::validadeParam('intHeight',$intHeight
                                     ,ValidateHelper::WARNING
                                     ,ValidateHelper::MSG_DECREP
                                     ,__CLASS__,__METHOD__,__LINE__);
    }
    
    public function setPaddingItems($intPaddingItems)
    {
        ValidateHelper::validadeParam('intPaddingItems',$intPaddingItems
                                     ,ValidateHelper::WARNING
                                     ,ValidateHelper::MSG_DECREP
                                     ,__CLASS__,__METHOD__,__LINE__);
	}

}