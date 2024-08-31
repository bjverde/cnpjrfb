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
 * Classe para criação campo do tipo select
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
class TFormDinSelectField  extends TFormDinOption
{
    protected $adiantiObj;
    private $multiSelect;
    private $enableSearch;
    
    /**
     * Adicionar campo tipo combobox ou menu select
     * ------------------------------------------------------------------------
     * Esse é o FormDin 5, que é uma reconstrução do FormDin 4 Sobre o Adianti 7.X
     * os parâmetros do metodos foram marcados veja documentação da classe para
     * saber o que cada marca singinifica.
     * ------------------------------------------------------------------------
     *
     * $mixOptions = array no formato "key=>value". No FormDin 5 só permite array PHP
     * $strKeyColumn = nome da coluna que será utilizada para preencher os valores das opções
     * $strDisplayColumn = nome da coluna que será utilizada para preencher as opções que serão exibidas para o usuário
     * $strDataColumns = informações extras do banco de dados que deverão ser adicionadas na tag option do campo select
     *
     * <code>
     * 	// exemplos
     * 	$frm->addSelectField('tipo','Tipo:',false,'1=Tipo 1,2=Tipo 2');
     * 	$frm->addSelectField('tipo','Tipo:',false,'tipo');
     * 	$frm->addSelectField('tipo','Tipo:',false,'select * from tipo order by descricao');
     * 	$frm->addSelectField('tipo','Tipo:',false,'tipo|descricao like "F%"');
     *
     *  //Exemplo espcial - Campo obrigatorio e sem senhum elemento pre selecionado.
     *  $frm->addSelectField('tipo','Tipo',true,$tiposDocumentos,null,null,null,null,null,null,' ','');
     * </code>
     *
     * @param string  $id             - 1: ID do campo
     * @param string  $strLabel       - 2: Label do campo
     * @param boolean $boolRequired   - 3: Campo obrigatório. Default FALSE = não obrigatório, TRUE = obrigatório
     * @param mixed   $mixOptions     - 4: String "S=SIM,N=NAO,..." ou Array dos valores nos formatos: PHP "id=>value", FormDin, PDO ou Adianti
     * @param boolean $boolNewLine        - 05: Default TRUE = cria nova linha , FALSE = fica depois do campo anterior
     * @param boolean $boolLabelAbove     - 06: Label sobre o campo. Default FALSE = Label mesma linha, TRUE = Label acima
     * @param mixed   $mixValue           - 07: Informe o ID do array ou array com a lista de ID's no formato "key=>id" para identificar a(s) opção(ões) selecionada(s)
     * @param boolean $boolMultiSelect    - 08: Default FALSE = SingleSelect, TRUE = MultiSelect
     * @param integer $intQtdColumns      - 09: NOT_IMPLEMENTED Default 1. Num itens que irão aparecer no MultiSelect
     * @param integer $intWidth           - 10: DEPRECATED. Informe NULL para evitar o warning. Largura em Pixels
     * @param string  $strFirstOptionText - 11: NOT_IMPLEMENTED First Key in Display. Informe um valor diferente de '' ou null para mostrar um Label.
     * @param string  $strFirstOptionValue- 12: Frist Value in Display, use value NULL for required. Para o valor DEFAULT informe o ID do $mixOptions e $strFirstOptionText = '' e não pode ser null
     * @param string  $strKeyColumn       - 13: Nome da coluna que será utilizada para preencher os valores das opções
     * @param string  $strDisplayColumn   - 14: Nome da coluna que será utilizada para preencher as opções que serão exibidas para o usuário
     * @param string  $boolNoWrapLabel    - 15: NOT_IMPLEMENTED
     * @param string  $strDataColumns     - 16: NOT_IMPLEMENTED Informações extras do banco de dados que deverão ser adicionadas na tag option do campo select
     * @param string  $enableSearch       - 17: FORMDIN5: Define se o campo select terá autocomplete
     * @return TCombo
     */
    public function __construct(string $id
                               ,string $label
                               ,$boolRequired
                               ,$mixOptions
                               ,$boolNewLine = true
                               ,$boolLabelAbove = false
                               ,$mixValue = null
                               ,$boolMultiSelect = false
                               ,int $intQtdColumns = null
                               ,int $intWidth = null
                               ,string $strFirstOptionText = null
                               ,string $strFirstOptionValue = null
                               ,string $strKeyColumn = null
                               ,string $strDisplayColumn = null
                               ,$boolNoWrapLabel = null
                               ,string $strDataColumns = null
                               ,bool $enableSearch = false
                               )
    {
        $this->setWidth( $intWidth );
        $this->setEnableSearch($enableSearch);

        $boolRequired   = empty($boolRequired)?false:$boolRequired;
        $boolNewLine    = empty($boolNewLine)?true:$boolNewLine;
        $boolMultiSelect = is_null($boolMultiSelect)?false:$boolMultiSelect;
        
        $this->setMultiSelect($boolMultiSelect);

        if($this->getMultiSelect()){
            $adiantiObj = new TSelect($id);
            //$adiantiObj->tag->{'size'} = $intSize;
            //$adiantiObj->setProperty('size', $intSize);
        }else{
            if( $this->getEnableSearch() == true ){
                $adiantiObj = new TUniqueSearch($id);
            }else{
                $adiantiObj = new TCombo($id);
            }
        }

        parent::__construct($adiantiObj            //01: Objeto de campo do Adianti
                           ,$id                    //02: ID do campo
                           ,$label                 //03: Label do campo
                           ,$boolRequired          //04: Campo obrigatório. Default FALSE = não obrigatório, TRUE = obrigatório
                           ,$mixOptions            //05: String "S=SIM,N=NAO,..." ou Array dos valores nos formatos: PHP "id=>value", FormDin, PDO ou Adianti
                           ,$boolNewLine           //06: Default TRUE = cria nova linha , FALSE = fica depois do campo anterior
                           ,$boolLabelAbove        //07: Label sobre o campo. Default FALSE = Label mesma linha, TRUE = Label acima
                           ,$mixValue              //08: Informe o ID do array. Array no formato "key=>key" para identificar a(s) opção(ões) selecionada(s)
                           ,$boolMultiSelect       //09: Default FALSE = SingleSelect, TRUE = MultiSelect
                           ,$intQtdColumns         //10: Default 1. Num itens que irão aparecer no MultiSelect
                           ,TFormDinOption::SELECT //11: Define o tipo de input a ser gerado. Ex: select, radio ou check
                           ,$strFirstOptionValue   //12: Frist Value in Display, use value NULL for required. Para o valor DEFAULT informe o ID do $mixOptions e $strFirstOptionText = '' e não pode ser null
                           ,$strKeyColumn          //13: Nome da coluna que será utilizada para preencher os valores das opções
                           ,$strDisplayColumn      //14: Nome da coluna que será utilizada para preencher as opções que serão exibidas para o usuário
                           ,null                   //15: 
                           ,$strDataColumns        //16: informações extras do banco de dados que deverão ser adicionadas na tag option do campo select
                           ,$enableSearch          //17: FORMDIN5: Define se o campo select terá autocomplete
                        );        
        return $this->getAdiantiObj();
    }


    //-----------------------------------------------------------
    public function getMultiSelect()
    {
        return $this->multiSelect;
    }
    private function setMultiSelect($boolMultiSelect)
    {
        $this->multiSelect = $boolMultiSelect;
    }
    //-----------------------------------------------------------
    public function getEnableSearch()
    {
        return $this->enableSearch;
    }
    private function setEnableSearch($enableSearch)
    {
        $this->enableSearch = $enableSearch;
    }
    public function enableSearch(bool $enableSearch=true)
    {
        $enableSearch = is_null($enableSearch)?true:$enableSearch;
        $adiantiObj = $this->getAdiantiObj();
        if($enableSearch && (($adiantiObj instanceof TUniqueSearch)) ){
            $this->getAdiantiObj()->enableSearch();
        }
    }
    //-----------------------------------------------------------
    public function setWidth($intWidth)
    {
        ValidateHelper::validadeParam('intWidth',$intWidth
                                     ,ValidateHelper::WARNING
                                     ,ValidateHelper::MSG_DECREP
                                     ,__CLASS__,__METHOD__,__LINE__);
	}
}