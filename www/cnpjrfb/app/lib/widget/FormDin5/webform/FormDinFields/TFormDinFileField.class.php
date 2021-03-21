<?php
/*
 * ----------------------------------------------------------------------------
 * Formdin 5 Framework
 * SourceCode https://github.com/bjverde/formDin5
 * @author Reinaldo A. Barrêto Junior
 * 
 * É uma reconstrução do FormDin 4 Sobre o Adianti 7.X
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
class TFormDinFileField extends TFormDinGenericField
{    
    private $maxSize;
    private $maxSizeKb;
    private $allowedFileTypes;
    private $msgUploadException;


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
     * @param string $strName        - 01: ID do campo
     * @param string $strValue       - 02: Texto HTML que irá aparece dentro
     * @param boolean $boolRequired  - 03: Obrigatorio
     * @param mixed   $strAllowedFileTypes - 04: Tipos de arquivos. String separado por virgular ou array
     * @param string $strIncludeFile - 05: NOT_IMPLEMENTED Arquivo que será incluido
     * @param string $strLabel       - 06: Label do campo
     * @param string $strWidth       - 07: NOT_IMPLEMENTED
     * @param string $strHeight      - 09: NOT_IMPLEMENTED
     * @param boolean $enableFileHandling -13: FORMDIN5 Habilita barra de progresso
     * @param boolean $enablePopover      -14: FORMDIN5 Habilita o preview
     * @return THtml Field
     */     
    public function __construct( string $id
                               , string $label
                               , $boolRequired = false
                               , $strAllowedFileTypes=null
                               , $intSize=null
                               , $strMaxSize=null
                               , $enableFileHandling = false
                               , $enablePopover = false
                               )
    {
        $this->setAllowedFileTypes( $strAllowedFileTypes );
        
        $adiantiObj = new TFile($label);
        $adiantiObj->setAllowedExtensions( $this->getAllowedFileTypes() );
        //$adiantiObj->enableFileHandling();
        //$adiantiObj->enablePopover();
        $this->enableFileHandling($enableFileHandling);
        if( $enablePopover==true ){
            $this->enablePopover();
        }

        $post = $adiantiObj->getPostData();
        //FormDinHelper::debug($post);

        $label = is_null($label)?'':$label;
        parent::__construct($adiantiObj,$id,$label,$boolRequired,null,null);

        return $this->getAdiantiObj();
    }

    public function setAllowedFileTypes($strNewFileTypes=null)
    {
        if( is_string($strNewFileTypes) ){
            $strNewFileTypes = strtolower($strNewFileTypes);
            $strNewFileTypes = explode(',',$strNewFileTypes);
        }
        $this->allowedFileTypes = $strNewFileTypes;
    }
    public function getAllowedFileTypes()
    {
        return $this->allowedFileTypes;
    }

    public function setCompleteAction(TAction $action)
    {
        return $this->getAdiantiObj()->setCompleteAction($action);
    }
    public function setErrorAction(TAction $action)
    {
        return $this->getAdiantiObj()->setErrorAction($action);
    }

    /**
     * Habilita barra de progresso
     *
     * @param boolean $enableFileHandling
     */
    public function enableFileHandling($enableFileHandling=true)
    {
        if( $enableFileHandling==true ){
            $this->getAdiantiObj()->enableFileHandling();
        }
    }

    /**
     * Habilita o preview
     *
     * @param string $title titulo
     * @param string $content
     */
    public function enablePopover($title = null, $content = '')
    {
        $this->getAdiantiObj()->enablePopover($title,$content);
    }

    /**
     * Define a classe de serviço que irá processar o upload dos arquivos. O Valor padrão é AdiantiUploaderService
     *
     * @param string $service
     */
    public function setService($service)
    {
        $this->getAdiantiObj()->setService($service);
    }
    
}