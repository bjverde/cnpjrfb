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
 * Classe para criação campo com mascará
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
class TFormDinHtmlField extends TFormDinGenericField
{    
    /**
     * Campo de uso geral para insersão manual de códigos html na página
     * ------------------------------------------------------------------------
     * Esse é o FormDin 5, que é uma reconstrução do FormDin 4 Sobre o Adianti 7.X
     * os parâmetros do metodos foram marcados veja documentação da classe para
     * saber o que cada marca singinifica.
     * ------------------------------------------------------------------------
     *
      * Se o label for null, não será criado o espaço referente a ele no formulário, para criar
     * um label invisível defina como "" o seu valor
     *
     * criado o espaço
     * @param string $strName        - 1: ID do campo
     * @param string $strValue       - 2: Texto puro ou HTML que irá aparece dentro
     * @param string $strIncludeFile - 3: Arquivo que será incluido, o arquivo prevalece sobre o valor. Pode ser HTML, HTM, PHP, TXT.
     * @param string $strLabel       - 4: label do campo
     * @param string $strWidth       - 5: largura em %
     * @param string $strHeight      - 6: altura em % ou px
     * @return THtml Field
     */     
    public function __construct( string $id
                               , $value=null
                               , $strIncludeFile=null
                               , $label=null
                               , $strWidth='100%'
                               , $strHeight=null                               
                               , $boolNewLine=null
                               )
    {
        $label = is_null($label)?'':$label;

        if( empty($strIncludeFile) ){
            $adiantiObj = new TElement('div');
            $adiantiObj->add($value);
        } else {
            if( !file_exists($strIncludeFile) ) {
                $adiantiObj = new TElement('div');
                $adiantiObj->add('Arquivo '.$strIncludeFile.' não encontrado');
            } else {
                $url = ServerHelper::getHomeUrl();
                $url = $url . $strIncludeFile;
                $adiantiObj = new TElement('iframe');
                $adiantiObj->id = $id;
                $adiantiObj->src = $url;
                $adiantiObj->frameborder = "0";
                $adiantiObj->scrolling = "yes";
                $adiantiObj->width  = FormDinHelper::sizeWidthInPercent($strWidth);
                $adiantiObj->height = "700px";
            }
        }
        parent::__construct($adiantiObj,$id,$label,null,null,null);
        return $this->getAdiantiObj();
    }

    public function add($element){
        $this->getAdiantiObj()->add($element);
    }
}