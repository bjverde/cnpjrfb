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
 * Classe para criação campo do tipo Switch
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
class TFormDinSwitch extends TFormDinRadio
{
    protected $adiantiObj;
    protected $items = ['S'=>'Sim', 'N'=>'Não'];
    
    /**
     * Cria um RadioGroup com efeito visual de Switch
     * Reconstruido FormDin 4 Sobre o Adianti 7
     * 
     * @param string  $id            - 1: ID do campo
     * @param string  $strLabel      - 2: Label do campo
     * @param boolean $boolRequired  - 3: TRUE = Required, FALSE = not Required
     * @param array   $mixOptions    - 4: Array Options ou String FormDin 'S=SIM,N=Não'
     * @param boolean $boolNewLine   - 5: TRUE = new line, FALSE = no, DEFAULT ou NULL = FALSE
     * @param boolean $boolLabelAbove- 6: TRUE = Titulo em cima das opções, FALSE = titulo lateral
     * @param string  $mixValue      - 7: Valor DEFAULT, informe o ID do array
     * @return mixed TRadioGroup
     */
    public function __construct(string $id,string $label,$boolRequired = false,array $mixOptions= null, $boolNewLine=true,$boolLabelAbove=false,$mixValue=null)
    {        
        if( !empty($mixOptions) ){
            $this->items = $mixOptions;
        }else{
            $mixOptions = $this->items;
        }
        parent::__construct($id
                            ,$label
                            ,$boolRequired
                            ,$mixOptions
                            ,$boolNewLine
                            ,$boolLabelAbove
                            ,$mixValue
                            ,2
                            ,null
                            ,null
                            ,null
                            ,null
                            ,null
                            ,true
                            ,null
                            ,null
                            );    
        return $this->getAdiantiObj();
    }
}