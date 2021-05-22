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
 * Classe para criação de um campo telefone
 * 
 * Esse é o FormDin 5, que é um reconstrução do 
 * FormDin 4.8 sobre o Adianti 7.1
 * 
 * @author Reinaldo A. Barrêto Junior
 */
class TFormDinFoneField
{
    protected $adiantiObj;
    protected $label;    

	/**
	 * Adiciona campo de entrada para telefone e fax
	 *
     * FormDin 5 - Alguns parametros foram DESATIVADO
     * por não funcionar no Adianti 7.1 e foram mantidos
     * para diminuir o impacto sobre a migração
     * 
	 * @param string $strName       - 1: ID do campo
	 * @param string $strLabel      - 2: Label do campo, que irá aparecer na tela do usuario
	 * @param boolean $boolRequired - 3: Obrigatorio
	 * @param boolean $boolNewLine  - 4: Campo em nova linha
	 * @param string $strValue
	 * @param boolean $boolLabelAbove
     * @param boolean $boolNoWrapLabel
     * @return TEntry
     */
    public function __construct(string $id
                               ,string $strLabel
                               ,$boolRequired = false
                               ,$boolSendMask = true
                               ,string $strValue=null
                               ,string $strExampleText =null)
    {
        $this->setLabel($strLabel);
        $this->adiantiObj = new TEntry($id);
        $this->adiantiObj->setId($id);
        $this->adiantiObj->setMask('99999-9999', $boolSendMask);
        $this->setRequired($boolRequired);
        if(!empty($strValue)){
            $this->adiantiObj->setValue($strValue);
        }
        $this->setExampleText($strExampleText);
        return $this->getAdiantiObj();
    }

    public function getAdiantiObj(){
        return $this->adiantiObj;
    }

    public function getLabel(){
        return $this->label;
    }
    public function setLabel($label){
        $this->label = $label;
    }

    public function setRequired($boolRequired){
        if($boolRequired){
            $strLabel = $this->getLabel();
            $this->adiantiObj->addValidation($strLabel, new TRequiredValidator);
        }
    }

    public function setExampleText($placeholder){
        if(!empty($placeholder)){
            $this->adiantiObj->placeholder = $placeholder;
        }
    }
}