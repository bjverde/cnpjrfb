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

class TFormDinGridColumnFormatDate extends TFormDinGridColumn
{
    protected $format;
    
    /**
     * Coluna do Grid Padronizado em BoorStrap
     * Reconstruido FormDin 4 Sobre o Adianti 7.1
     *
     * @param  string $name   - 1: Name of the column in the database
     * @param  string $label  - 2: Text label that will be shown in the header
     * @param  string $width  - 3: Column Width (pixels)
     * @param  string $align  - 4: Column align (left|right|center|justify)
     * @param  string $format - 5: Date Format. DEFAULT = d/m/Y (Brazil). Exemplo: United States = m/d/Y. Aceita o formato Adianti dd/mm/yyyy ou DateTime do PHP d/m/Y. 
     * @return BootstrapFormBuilder
     */
    public function __construct(string $name
                              , string $label
                              , string $width = NULL
                              , string $align ='left'
                              , string $format='d/m/Y'
                              )
    {
        $column = new TDataGridColumn($name, $label,$align,$width);
        $this->setAdiantiObj($column);
        $this->setName($name);
        $this->setFormat($format);
        $this->setTransformer(array($this, 'formatDate'));
    }

    public function setFormat($format){       
        return $this->format=$format;
    }
    public function getFormat(){
        return $this->format;
    }

    /**
     * Format the date according to the country
     */
    public function formatDate($fieldValue)
    {
        //$name = $this->getName();
        //$date = new DateTime($object->$name);
        $date = new DateTime($fieldValue);
        $format = $this->getFormat();
        $format = str_replace( ['dd','mm', 'yyyy', 'hh', 'ii', 'ss'], ['d','m','Y', 'H', 'i', 's'], $format);
        $dateFormat = $date->format($format);
        return $dateFormat;
    }
}