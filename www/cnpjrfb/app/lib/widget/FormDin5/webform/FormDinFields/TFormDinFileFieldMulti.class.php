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
class TFormDinFileFieldMulti extends TFormDinGenericField
{    
    private $maxSize;
    private $maxSizeKb;
    private $allowedFileTypes;
    private $msgUploadException;
    private $id;

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
     * @param string $id            -01: ID do campo
     * @param string $label         -02: Texto HTML que irá aparece dentro
     * @param boolean $boolRequired -03: Obrigatorio
     * @param mixed $strAllowedFileTypes -04: Tipos de arquivos. String separado por virgular ou array
     * @param [type] $intSize       -05:
     * @param [type] $strMaxSize    -06: Input the max size file with K, M for Megabit (Mb) or G for Gigabit (Gb). Example 2M = 2 Mb = 2048Kb.
     * @param [type] $value         -07: FORMDIN5 Valor padrão do campo
     * @param boolean $enableFileHandling-08: FORMDIN5 Habilita barra de progresso
     * @param boolean $enablePopover     -09: FORMDIN5 Habilita o preview
     * @param integer $enableImageGallery-10: FORMDIN5 Numero da Largura (width) da imagem da galaria, DEFAULT = 120. Para customizar use o metodo enableImageGallery
     */
    public function __construct( string $id
                               , string $label
                               , $boolRequired = false
                               , $strAllowedFileTypes=null
                               , $intSize=null
                               , $strMaxSize=null
                               , $value=null
                               , $enableFileHandling = false
                               , $enablePopover = false
                               , $enableImageGallery = null
                               )
    {
        $this->setId($id);
        $this->setAllowedFileTypes( $strAllowedFileTypes );
        $adiantiObj = new TMultiFile($id);
        $adiantiObj->setAllowedExtensions( $this->getAllowedFileTypes() );
        //$adiantiObj->enableFileHandling();
        //$adiantiObj->enablePopover();
        if( !empty($value) ){
            $this->setValue($value);
        }
        
        if( !empty($enableImageGallery) ){
            $this->enableImageGallery($enableImageGallery,null);
        }        
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
    //---------------------------------------------------------------
    public function setId($id){
        $this->id = $id;
    }
    public function getId(){
        return $this->id;
    }

    public function setAllowedFileTypes($strNewFileTypes=null)
    {
        if( is_string($strNewFileTypes) ){
            $strNewFileTypes = strtolower($strNewFileTypes);
            $strNewFileTypes = explode(',',$strNewFileTypes);
        }
        if( empty($strNewFileTypes) ){
            $strNewFileTypes='';
        }
        $this->allowedFileTypes = $strNewFileTypes;
    }
    public function getAllowedFileTypes()
    {
        return $this->allowedFileTypes;
    }

    /**
     * Define the TAction (static) to be executed when upload is finished
     * @param $action TAction object
     */    
    public function setCompleteAction(TAction $action)
    {
        return $this->getAdiantiObj()->setCompleteAction($action);
    }    
    /**
     * Define the TAction (static) to be executed when some error occurs
     * @param $action TAction object
     */    
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

    public function setValue($value)
    {
        $this->getAdiantiObj()->setValue($value);
    }

    public function enableImageGallery($width = null, $height = null)
    {
        $this->enableFileHandling();
        $this->getAdiantiObj()->enableImageGallery($width,$height);
    }
    
}