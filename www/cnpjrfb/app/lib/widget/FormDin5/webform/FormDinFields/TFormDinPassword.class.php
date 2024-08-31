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

class TFormDinPassword extends TFormDinGenericField
{
    protected $adiantiObj;
    private $class = array();
    

    /**
     * Video HTML 5, com a classe CSS fd5Video basta criar um css para
     * determinar o tamanho do vídeo
     * 
     * Vídeos no HTML5 só tem autoplay SE SOMENTE SE o video for mutado
     * https://developer.chrome.com/blog/autoplay/
     *
     * @param string $id                     -01: id do campo
     * @param string $label                  -02: Rotulo do campo que irá aparece na tela
     * @param boolean $boolRequired          -03: Campo obrigatório ou não. Default FALSE = não obrigatório, TRUE = obrigatório
     * @param integer $intmaxLength          -04: Tamanho maximo
     * @param string  $value                 -05: Valor inicial
     * @param boolean $enableToggleVisibility-06: FORMDIN5 DEFALUT is TRUE mostra a senha ou não
     * @return TLabel
     */
    public function __construct(string $id
                               ,string $label=null
                               ,$boolRequired=null
                               ,$intmaxLength=null
                               ,$value=null
                               ,$enableToggleVisibility=true
                               )
    {

        $adiantiObj = new TPassword($id);
        parent::__construct($adiantiObj,$id,$label,$boolRequired,$value,null);
        $this->setMaxLength($intmaxLength);
        $this->enableToggleVisibility($enableToggleVisibility);
    }

    public function setMaxLength($intmaxLength)
    {
        $this->getAdiantiObj()->setMaxLength($intmaxLength);
    }

    public function enableToggleVisibility($enableToggleVisibility)
    {
        $this->getAdiantiObj()->enableToggleVisibility($enableToggleVisibility);
    }

    public function setExitAction(TAction $action)
    {
        $this->getAdiantiObj()->setExitAction($action);
    }    
}