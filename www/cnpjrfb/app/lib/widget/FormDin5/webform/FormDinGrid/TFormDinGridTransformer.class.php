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

class TFormDinGridTransformer
{
    public static function getDataGridActionTurnOnOff($class, $field)
    {
        $action = new TDataGridAction(array($class, 'onTurnOnOff'));
        $action->setButtonClass('btn btn-default');
        $action->setLabel(_t('Activate/Deactivate'));
        $action->setImage('fa:power-off orange');
        $action->setField($field);
        return $action;
    }

    public static function getDataGridActionOnDelete($class, $field)
    {
        $action = new TDataGridAction(array($class, 'onDelete'));
        $action->setUseButton(false);
        $action->setButtonClass('btn btn-default btn-sm');
        $action->setLabel("Excluir");
        $action->setImage('fas:trash-alt #dd5a43');
        $action->setField($field);
        return $action;
    }

    public static function getDataGridActionOnEdit($class, $field)
    {
        $action = new TDataGridAction(array($class, 'onEdit'));
        $action->setUseButton(false);
        $action->setButtonClass('btn btn-default btn-sm');
        $action->setLabel("Editar");
        $action->setImage('far:edit #478fca');
        $action->setField($field);
        return $action;
    }    

    public static function simNao($value)
    {
        if($value === true || $value == 't' || $value === 1 || $value == '1' || $value == 's' || $value == 'S' || $value == 'T'){
            return 'Sim';
        }
        return 'Não';
    }

    public static function simNaoComLabel($value, $object, $row)
    {
        $class = ($value=='N') ? 'danger' : 'success';
        $label = ($value=='N') ? _t('No') : _t('Yes');
        $div = new TElement('span');
        $div->class="label label-{$class}";
        $div->style="text-shadow:none; font-size:12px; font-weight:lighter";
        $div->add($label);
        return $div;
    }

    /**
     * Formata um número de celular com um link para WhatsApp API para enviar uma mensagem
     *
     * @param [type] $value   01 - número de celular, formatado ou não. É recomendavél enviar com DDI e DDD
     * @param object $object  02 - objeto adianti
     * @param [type] $row     03 - linha do grid
     * @param string $msg     04 - texto da mensagem
     * @param boolean $iconeVerde 05 - Mostra icone verde ou mostra icone na cor da fonte
     * @return void
     */    
    public static function linkApiWhatsApp($value, $object, $row, $msg, $iconeVerde)
    {
        if ($value){
            $string = HtmlHelper::linkApiWhatsApp($value,$msg,$iconeVerde);
            return $string;
        }
        return $value;
    }

    public static function date($value)
    {
        if( !empty(trim($value)) && $value!='0000-00-00'){
            try{
                $date = new DateTime($value);
                return $date->format('d/m/Y');
            }catch (Exception $e){
                return $value;
            }
        }
    }

    public static function gridDate($value, $object, $row)
    {
        return  self::date($value);
    }

     /**
      * Recebe o conteudo de um setTransformer do Grid e mostra imagem 
      *
      * @param mixed $value   - 01: Coluna com o nome da imagem
      * @param object $object - 02: Objeto com todos os elmentos da linha
      * @param mixed $row     - 03: informação da linha
      * @param string $imgPath- 04: Default = app/images/. caminho relativo da imagem depois da pasta rais do sistema. Exemplo app/images/
      * @param string $maxWidth 05: Default = 100px. largura maxima da imagem, altura será proporcional
      * @param string $imgNotfound 06: imagem alternativa caso o arquivo da imagem não exista. informar o caminho relativo partindo da raiz do sistema. Exemplo app/images/semimg.png
      * @return string
      */
    public static function gridImg($value, $object, $row,$imgPath,$maxWidth,$imgNotfound)
    {
        $imgPath = empty($imgPath)?'app/images/':$imgPath;
        $maxWidth = empty($maxWidth)?'100px':$maxWidth;
        $value = $imgPath.$value;
        if (is_file($value)==true){
            $image = new TImage($value);
            $image->style = 'max-width: '.$maxWidth;
            return $image;
        }else{
            if( empty($imgNotfound) ){
                return '';
            }else{
                $image = new TImage($imgNotfound);
                $image->style = 'max-width: '.$maxWidth;
                return $image;
            }
        }
    }

    public static function dateTime($value)
    {
        if( !empty(trim($value)) && $value!='0000-00-00'){
            try{
                $date = new DateTime($value);
                return $date->format('d/m/Y H:i');
            }catch (Exception $e){
                return $value;
            }
        }
    }

    public static function gridDateTime($value, $object, $row)
    {
        return  self::dateTime($value);
    }
    
    public static function gridNumeroBrasil($value, $object, $row)
    {
        if(!$value){
            $value = 0;
        }
        $value = StringHelper::numeroBrasil($value);
        return  $value;
    }
    public static function gridNumeroBrasilFormatStyle($value, $object, $row)
    {
        if(!$value){
            $value = 0;
        }
        $number = StringHelper::numeroBrasil($value);
        if ($value > 0){
            return "<span style='color:blue'>$number</span>";
        }else{
            //$row->style = "background: #FFF9A7";
            return "<span style='color:red'>$number</span>";
        }
    }
}