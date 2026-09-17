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

class TFormDinVideoStreamPhoto extends TFormDinGenericField
{
    protected $adiantiObj;
    private $class = array();

    /**
     * Video stream em HTML 5 com canvas para capiturar uma foto da Camera
     * 
     * Algo semelhante ao exemplo abaixo
     * https://doug2k1.github.io/javascript-camera/
     *
     * @param string  $id              -01: ID do campo
     * @param string  $label           -02: Label do campo, usado para validações
     * @param boolean $boolRequired    -03: Campo obrigatório ou não. Default FALSE = não obrigatório, TRUE = obrigatório
     * @param string  $enableChangeCam -04: NOT_IMPLEMENTED TRUE (Default) or FALSE, Enable Change Cam
     * @param boolean $width           -05: NOT_IMPLEMENTED Default Null, largura em % ou px
     * @param integer $height          -06: Default 350, altura em px. Informe apenas o número
     * @param string  $imgPathFeedBack -07: Caminho da imagem que vai aparece com FeedBack visual. Valor defualt é app/images/mark-cheque-green.png
     * @param string  $imgPercent      -08: Percentual do tamanho da imagem
     * @return TElement
     */
    public function __construct(string $idField
                               ,string $label
                               ,$boolRequired = false
                               ,$enableChangeCam = true
                               ,$width = null
                               ,$height= null
                               ,$imgPathFeedBack = null
                               ,$imgPercent = null
                               )
    {
        $imgPathFeedBack = empty($imgPathFeedBack)?'app/images/mark-cheque-green.png':$imgPathFeedBack;
        $imgPercent = empty($imgPercent)?'0.45':$imgPercent;
        $height = empty($height)?'350':preg_replace("/[^0-9]/", "", $height);

        //$adiantiObjHiden = new THidden($idField);
        //$adiantiObjHiden->setId($idField);
        $fd5Hidden = new TFormDinHiddenField($idField,null,$boolRequired);
        $adiantiObjHiden = $fd5Hidden->getAdiantiObj();

        $adiantiObjWebCam = new TElement('video');
        $adiantiObjWebCam->class = 'fd5Video';
        $adiantiObjWebCam->setProperty('id',$idField.'_video');
        $adiantiObjWebCam->setProperty('name',$idField.'_video');
        $adiantiObjWebCam->setProperty('style','height: '.$height.'px;');
        //$adiantiObjWebCam->setProperty('style','display: none;');
        $adiantiObjWebCam->add('autoplay');
        $adiantiObjWebCam->add('Your browser does not support HTML video.');

        $adiantiObjVideoCanvas = new TElement('canvas');
        $adiantiObjVideoCanvas->class = 'fd5VideoCanvas';
        $adiantiObjVideoCanvas->setProperty('id',$idField.'_videoCanvas');
        $adiantiObjVideoCanvas->setProperty('name',$idField.'_videoCanvas');
        $adiantiObjVideoCanvas->setProperty('style','display: none; height: '.$height.'px;');

        $adiantiObjVideoCanvasUpload = new TElement('canvas');
        $adiantiObjVideoCanvasUpload->class = 'fd5VideoCanvasUpload';
        $adiantiObjVideoCanvasUpload->setProperty('id',$idField.'_videoCanvasUpload');
        $adiantiObjVideoCanvasUpload->setProperty('name',$idField.'_videoCanvasUpload');
        $adiantiObjVideoCanvasUpload->setProperty('style','display: none; height: '.$height.'px;');

        $scriptJswebCam = new TElement('script');
        $scriptJswebCam->setProperty('src', 'app/lib/widget/FormDin5/javascript/FormDin5WebCams.js?appver='.FormDinHelper::version());


        $btnStart = new TButton('btnStart');
        $btnStart->class = 'btn btn-success btn-sm';
        $btnStart->setLabel('Ligar Câmera');
        $btnStart->setImage('fa:power-off');
        $btnStart->addFunction("fd5VideoStart('".$idField."')");

        $btnChangeCamera = new TButton('btnChangeCamera');
        $btnChangeCamera->class = 'btn btn-light btn-sm';
        $btnChangeCamera->setLabel('Alterar Camera');
        $btnChangeCamera->setImage('fa:sync-alt');
        $btnChangeCamera->addFunction("fd5VideoCampiturar('".$idField."')");

        $btnScreenshot = new TButton('btnScreenshot');
        $btnScreenshot->class = 'btn btn-primary btn-sm';
        $btnScreenshot->setLabel('Capturar Foto');
        $btnScreenshot->setImage('fa:camera');
        $btnScreenshot->addFunction("fd5VideoCampiturar('".$idField."','".$imgPathFeedBack."',".$imgPercent.")");

        $divButton = new TElement('div');
        $divButton->class = 'fd5DivVideoButton';
        $divButton->setProperty('id',$idField.'_videoDivButton');
        $divButton->add($btnStart);
        //$divButton->add($btnChangeCamera);
        $divButton->add($btnScreenshot);


        $idDivWebCam = $idField.'_videodiv';
        $divWebCam = new TElement('div');
        $divWebCam->class = 'fd5DivVideo';
        $divWebCam->setProperty('id',$idDivWebCam);

        if( ServerHelper::isHTTPS(false,false) ){
            $divWebCam->add($adiantiObjHiden);
            $divWebCam->add($adiantiObjWebCam);
            $divWebCam->add($adiantiObjVideoCanvas);
            $divWebCam->add($adiantiObjVideoCanvasUpload);
            $divWebCam->add($scriptJswebCam);
            $divWebCam->add($divButton);
        }else{
            $strValue = '<b>Para usar o recurso de captura de foto da câmera,';
            $strValue = $strValue.' é necessário que a página esteja sendo acessada via HTTPS.';
            $formField = new TFormDinHtmlField('idAvisoVideoStream',$strValue);
            $objField = $formField->getAdiantiObj();
            $divWebCam->add($objField);
        }

        $adiantiObj = $divWebCam;
        parent::__construct($adiantiObj,$idDivWebCam,$label,false,null,null);
        $this->setLabel($label,$boolRequired);
        return $this->getAdiantiObj();
    }

    private function setProperty($property, $valeu)
    {
        if($valeu==true){
            $this->getAdiantiObj()->setProperty($property, 'true');
        }
    }
    public function loop($valeu)
    {
        $this->setProperty('loop', $valeu);
    }    
    public function controls($valeu)
    {
        $this->setProperty('controls', $valeu);
    }
    public function muted($valeu)
    {
        $this->setProperty('muted', $valeu);
    }
    public function autoplay($valeu)
    {
        $this->setProperty('autoplay', $valeu);
        $this->setProperty('muted', $valeu);
    }
}