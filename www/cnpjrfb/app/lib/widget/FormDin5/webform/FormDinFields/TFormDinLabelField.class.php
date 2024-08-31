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

class TFormDinLabelField
{
    protected $adiantiObj;
    private $class = array();
    

    /**
     * Label do campo de entrada
     * Reconstruido FormDin 4 Sobre o Adianti 7
     *
     * @param string $strLabel     - 1: Label do campo, usado para validações
     * @param boolean $boolRequired- 2: Obrigatorio. DEFAULT = False.
     * @param [type] $color        - 3: Define the font color
     * @param [type] $fontsize     - 4: Font size in pixels
     * @param [type] $decoration   - 5: text decorations (b=bold, i=italic, u=underline)
     * @param [type] $size         - 6: Field's width in pixels
     * @return TLabel
     */
    public function __construct(string $strLabel
                               ,$boolRequired = false
                               ,$color = null
                               ,$fontsize = null
                               ,$decoration = null
                               ,$size = null                               
                               )
    {
        if( empty($color) && ($boolRequired==true) ){
            $color = 'red';
        }
        $fontsize = empty($fontsize)?'14px':$fontsize;
        $this->adiantiObj = new TLabel($strLabel,$color,$fontsize,$decoration,$size);
    }

    public function getAdiantiObj(){
        return $this->adiantiObj;
    }
	//------------------------------------------------------------------------------    
	public function setClass($className)
	{
        $this->class[]=$className;
        $className = implode(' ', $this->class);
        $this->getAdiantiObj()->setProperty('class',$className);
	}
	public function getClass()
	{
		return $this->getAdiantiObj()->getProperty('class');
    }
    //------------------------------------------------------------------------------
	/**
	 * Set um Toolpit do FormDin para funcionar com Adianti 
     * em um determinado campo pode ser usado com
	 * @param string $strTitle - 1: Titulo
	 * @param string $strText  - 2: Texto que irá aparecer
	 * @param string $strImagem- 3: NOT_IMPLEMENTED
	 */
	public function setTooltip($strTitle=null,$strText=null,$strImagem=null)
	{
        $text = is_null($strTitle)?$strText:$strTitle;
        $this->tooltip = $text;
		$this->getAdiantiObj()->setTip($text);
	}
	public function getTooltip()
	{
		return $this->tooltip;
    }    
	//------------------------------------------------------------------------------
    /**
     * Define the font size
     * @param $size Font size in pixels
     */    
    public function setFontSize($size)
	{
		$this->getAdiantiObj()->setFontSize($size);
	}
	//------------------------------------------------------------------------------
    /**
     * Define the style
     * @param  $decoration text decorations (b=bold, i=italic, u=underline)
     */    
    public function setFontStyle($decoration)
	{
		$this->getAdiantiObj()->setFontStyle($decoration);
	}
	//------------------------------------------------------------------------------
    /**
     * Define the font face
     * @param $font Font Family Name
     */    
    public function setFontFace($font)
	{
		$this->getAdiantiObj()->setFontFace($font);
	}
	//------------------------------------------------------------------------------
    /**
     * Define the font color
     * @param $color Font Color
     */    
    public function setFontColor($color)
	{
		$this->getAdiantiObj()->setFontColor($color);
	}         
}