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
class TFormDinSelectFieldDB extends TFormDinGenericField
{
    /**
     * ------------------------------------------------------------------------
     * FormDin 5, que é uma reconstrução do FormDin 4 sobre o Adianti 7.X
     * Alguns parâmetros têm uma TAG, veja documentação da classe para saber
     * o que cada marca significa.
     * ------------------------------------------------------------------------
     *
     * @param string $id           - 01: ID do campo
     * @param string $label        - 02: Label do campo
     * @param boolean $boolRequired- 03: Campo obrigatório. Default FALSE = não obrigatório, TRUE = obrigatório
     * @param string $value        - 04: Valor inicial 
     * @param string $database     - 05: Nome da conexão
     * @param string $model        - 06: Nome arquivo model, precisa ser do tipo TRecord
     * @param string $key          - 07: Nome da chave, será o valor enviado para o banco
     * @param string $name         - 08: Nome do balor que vai aparecer para o usuário
     * @param string $ordercolumn  - 09: Nome da colune de ordenação
     * @param TCriteria $criteria  - 10: Objeto do tipo TCriteria para fazer filtros 
     * @param string $enableSearch - 11: Define se o campo será tipo autocomplete
     * @param string $placeholder   -12: PlaceHolder é um Texto de exemplo
     * @return TDBCombo
     */
    public function __construct(string $id
                               ,string $label
                               ,bool|null   $boolRequired
                               ,string|null $value
                               ,string $database
                               ,string $model
                               ,string $key
                               ,string $name
                               ,string $ordercolumn = null
                               ,TCriteria $criteria = null
                               ,bool $enableSearch = true
                               ,bool $placeholder = null
                               )
    {
        $boolRequired   = empty($boolRequired)?false:$boolRequired;

        $adiantiObj = new TDBCombo($id, $database, $model, $key, $name, $ordercolumn, $criteria);
        parent::__construct($adiantiObj,$id,$label,$boolRequired,$value,$placeholder);
        $this->enableSearch($enableSearch);
        return $this->getAdiantiObj();
    }

    public function enableSearch($enableSearch)
    {
        $enableSearch = is_null($enableSearch)?true:$enableSearch;
        if($enableSearch){
            $this->getAdiantiObj()->enableSearch();
        }
    }
}