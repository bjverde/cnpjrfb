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

class TFormDinGridAction
{
    const TYPE_FORMDIN = 'TYPE_FORMDIN';
    const TYPE_PHP     = 'TYPE_PHP';
    const TYPE_ADIANTI = 'TYPE_ADIANTI';

    protected $adiantiObj;
    protected $actionLabel;
    protected $actionName;
    protected $action;
    protected $image;
    
    /**
     * Acões do botão da Grid
     *
     * @param object $objForm     - 1: FORMDIN5 Objeto do Adianti da classe do Form, é só informar $this
     * @param string $actionLabel - 2: Text do Label que aparece para o usuário. 
     * @param string $actionName  - 3: Text nome da ação deve ter um metodo com o mesmo nome. 
     * @param array $parameters   - 4: parametro do mixupdate fileds
     * @param array $image        - 5: imagem que irá aparecer
     */
    public function __construct($objForm
                               ,$actionLabel
                               ,$actionName
                               ,$parameters = null
                               ,$image = null
                               )
    {
        $arrayAction = [$objForm, $actionName];
        $adiantiObj = new TDataGridAction($arrayAction, $parameters);
        $this->setAdiantiObj($adiantiObj);
        $this->setActionLabel($actionLabel);
        $this->setActionName($actionName);
        $this->setImage($image);
        return $this->getAdiantiObj();
    }
    //-------------------------------------------------------------------------
    public function setAdiantiObj($adiantiObj){
        if( empty($adiantiObj) ){
            throw new InvalidArgumentException(TFormDinMessage::ERROR_FD5_OBJ_ADI);
        }        
        return $this->adiantiObj=$adiantiObj;
    }
    public function getAdiantiObj(){
        return $this->adiantiObj;
    }
    //-------------------------------------------------------------------------
    public function getActionLabel(){
        return $this->actionLabel;
    }
    public function setActionLabel($actionLabel){
        $this->getAdiantiObj()->setLabel($actionLabel);
        $this->actionLabel = $actionLabel;
    }
    //-------------------------------------------------------------------------
    public function getActionName(){
        return $this->actionName;
    }
    public function setActionName($actionName){
        $this->actionName = $actionName;
    }    
    //-------------------------------------------------------------------------
    public function getImage(){
        return $this->image;
    }
    public function setImage($image){
        $this->getAdiantiObj()->setImage($image);
        $this->image = $image;
    }
    //-------------------------------------------------------------------------
    public function setDisplayCondition( /*Callable*/ $displayCondition )
    {
        $this->getAdiantiObj()->setDisplayCondition($displayCondition);
    }        
    /**
     * Returns the action display condition
     */
    public function getDisplayCondition()
    {
        return $this->getAdiantiObj()->getDisplayCondition();
    }
}