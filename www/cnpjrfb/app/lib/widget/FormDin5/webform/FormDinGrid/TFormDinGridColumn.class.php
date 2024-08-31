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

class TFormDinGridColumn
{
    private   $objForm;
    protected $adiantiObj;
    protected $action;
    protected $name;
    protected $sortable;
    
    /**
     * Coluna do Grid Padronizado em BoorStrap
     * Reconstruido FormDin 4 Sobre o Adianti 7.1
     *
     * @param object $objForm    - 01: FORMDIN5 Objeto do Adianti da classe do Form, é repassado pela classe TFormDinGrid
     * @param string $name       - 02: Name of the column in the database
     * @param string $label      - 03: Text label that will be shown in the header
     * @param string $width      - 04: Column Width (pixels)
     * @param string $align      - 05: Column align (left|right|center|justify)
     * @param bool $boolReadOnly - 06: FORMDIN5: NOT_IMPLEMENTED Somente leitura. DEFAULT = false
	 * @param bool $boolSortable - 07: FORMDIN5: Coluna ordenavel. DEFAULT = true
	 * @param bool $boolVisivle  - 08: FORMDIN5: Coluna visivel ou não. DEFAULT = true
     * @param string $autoHide   - 09: FORMDIN5: Largura em pix que a coluna não ficará visivel, se a largura da tela ficar menor que o valor informado a coluna irá desaparer.
     * @return TDataGridColumn
     */
    public function __construct(object $objForm
                              , string $name
                              , string $label
                              , string $width = NULL
                              , string $align = 'left'
                              , bool $boolReadOnly = false
                              , bool $boolSortable = true
                              , bool $boolVisivle = true
                              , string $autoHide = null
                              )
    {
        if( !is_object($objForm) ){
            $track = debug_backtrace();
            $msg = 'A classe TFormDinGridColumn MUDOU! o primeiro parametro agora recebe object form! o Restante está igual ;-)';
            ValidateHelper::migrarMensage($msg
                                         ,ValidateHelper::ERROR
                                         ,ValidateHelper::MSG_CHANGE
                                         ,$track[0]['class']
                                         ,$track[0]['function']
                                         ,$track[0]['line']
                                         ,$track[0]['file']
                                        );
        }else{
            $this->setObjForm($objForm);
            $column = new TDataGridColumn($name, $label,$align,$width);
            $this->setAdiantiObj($column);
            $this->setName($name);
            $this->setSortable($boolSortable);
            $this->setVisibility($boolVisivle);
            $this->enableAutoHide($autoHide);
            return $this->getAdiantiObj();
        }
    }
	//-------------------------------------------------------------------------------------------
    public function setObjForm($objForm)
    {
        if( empty($objForm) ){
            throw new InvalidArgumentException(TFormDinMessage::ERROR_FD5_OBJ_ADI);
        }
        if( !is_object($objForm) ){
            throw new InvalidArgumentException(TFormDinMessage::ERROR_FD5_OBJ_ADI);
        }        
        return $this->objForm=$objForm;
    }
    public function getObjForm(){
        return $this->objForm;
    }
	//-------------------------------------------------------------------------------------------
    public function setAdiantiObj($adiantiObj){
        if( empty($adiantiObj) ){
            throw new InvalidArgumentException(TFormDinMessage::ERROR_FD5_OBJ_ADI);
        }        
        return $this->adiantiObj=$adiantiObj;
    }
    public function getAdiantiObj(){
        return $this->adiantiObj;
    }
	//-------------------------------------------------------------------------------------------
    public function setName($name){       
        return $this->name=$name;
    }
    public function getName(){
        return $this->name;
    }
	//-------------------------------------------------------------------------------------------
    public function setVisibility($bool){
        return $this->getAdiantiObj()->setVisibility($bool);
    }
    /**
     * Largura em pixels que a coluna não ficará visivel, se a largura da tela ficar menor que o valor informado a coluna irá desaparer.
     *
     * @param string $width
     * @return void
     */
    public function enableAutoHide($width)
    {
        if( !empty($width) ){
            $this->getAdiantiObj()->enableAutoHide($width);
        }
    }
	//-------------------------------------------------------------------------------------------
    /**
     * Crie uma função de callback para formatar os dados da coluna da grid
     * 
     * Poder uma função anonimoa do tipo
     *    function($value, $object, $row){ //codigo retun $valorAparecerGrid}
     *    Existem diversas funções prontas em TFormDinGridTransformer 
     * 
     * Pode ser tambem com array array('nomeClasse', 'nomeFuncao') veja o exemplo
     * TFormDinGridColumnFormatDate nomeClasse pode receber o valor $this
     * nomeFuncao vai receber apenas um valor como parametro $fieldValue
     * 
     * @param $callback  A function name of a method of an object
     */    
    public function setTransformer(Callable $callback){
        return $this->getAdiantiObj()->setTransformer($callback);
    }
    public function getTransformer(){
        return $this->getAdiantiObj()->getTransformer();
    }
	//-------------------------------------------------------------------------------------------
    public function setAction(TAction $action, $parameters = null){
        return $this->getAdiantiObj()->setAction($action, $parameters);
    }
    public function getAction(){
        return $this->getAdiantiObj()->getAction();
    }
	//-------------------------------------------------------------------------------------------
	public function setSortable($boolNewValue=null)
	{
        $this->sortable = $boolNewValue;
        if($boolNewValue){
            $order = new TAction(array($this->getObjForm(), 'onReload'));
            $order->setParameter('order', $this->getName() );
            $this->setAction($order);
        }
	}
	public function getSortable()
	{
		return is_null($this->sortable) ? true : $this->sortable;
	}
}