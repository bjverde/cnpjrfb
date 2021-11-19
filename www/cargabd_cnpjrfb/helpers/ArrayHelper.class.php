<?php
/*
 * Formdin Framework
 * Copyright (C) 2012 Ministério do Planejamento
 * Criado por Luís Eugênio Barbosa
 * Essa versão é um Fork https://github.com/bjverde/formDin
 *
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
class ArrayHelper
{
    
    public static function validateUndefined($array,$atributeName) 
    {
        if(!isset($array[$atributeName])) {
            $array[$atributeName]=null;
        }
        return is_null($array[$atributeName])?null:trim($array[$atributeName]);
    }
    
    /**
     * Similar to array_key_exists. But it does not generate an error message
     *
     * Semelhante ao array_key_exists. Mas não gera mensagem de erro
     *
     * @param  string $atributeName
     * @param  array  $array
     * @return boolean
     */
    public static function has($atributeName,$array) 
    {
        $value = false;
        if (is_array($array) && array_key_exists($atributeName, $array)) {
            $value = true;
        }
        return $value;
    }
    
    /**
     * Similar to array_keys. But it does not generate an Warning message in PHP7.2.X
     * 
     * Semelhante to array_keys. Mas não gera mensagem de Alerta no PHP7.2.X
     * 
     * @param array $array
     * @param mixed $search_value
     * @param boolean $strict
     * @return array
     */
    public static function array_keys2($array, $search_value = null,$strict = false)
    {
        $value = array();
        if (is_array($array)) {
            $value = array_keys($array, $search_value,$strict);
        }
        return $value;
    }
    
    /***
     *
     * @param array  $array
     * @param string $atributeName
     * @param mixed  $DefaultValue
     * @return mixed
     */
    public static function getDefaultValeu($array,$atributeName,$DefaultValue) 
    {
        $value = $DefaultValue;
        if(self::has($atributeName, $array) ) {
            if(isset($array[$atributeName]) && ($array[$atributeName]<>'') ) {
                $value = $array[$atributeName];
            }
        }
        return $value;
    }
    
    public static function get($array,$atributeName) 
    {
        $result = self::getDefaultValeu($array, $atributeName, null);
        return $result;
    }
    
    public static function getArray($array,$atributeName)
    {
        if(!isset($array[$atributeName])) {
            $array[$atributeName]=array();
        }
        return is_null($array[$atributeName])?array():$array[$atributeName];
    }
    
    /***
     * @deprecated chante to formDinGetValue
     * Evita erro de notice. Recebe um array FormDin, um atributo e a chave.
     * Verifica se o atributo e achave existem e devolve o valor
     * @param array $array
     * @param string $atributeName
     * @param int $key
     * @return NULL|mixed|array
     */
    public static function getArrayFormKey($array,$atributeName,$key)
    {
        return self::formDinGetValue($array, $atributeName, $key);
    }
    //--------------------------------------------------------------------------------
    /**
     * Convert Array PDO Format to FormDin format
     *
     * @param  array $array
     * @return array
     */
    public static function convertArrayPdo2FormDin($dataArray,$upperCase = true) 
    {
        $result = false;
        if(is_array($dataArray) ) {
            foreach( $dataArray as $k => $arr ) {
                foreach( $arr as $fieldName => $value ) {
                    if($upperCase) {
                        $result[ strtoupper($fieldName) ][ $k ] = $value;
                    }else{
                        $result[ $fieldName ][ $k ] = $value;
                    }
                }
            }
        }
        return $result;
    }
    //--------------------------------------------------------------------------------
    /**
     * Convert Array FormDin Format to PDO format
     *
     * @param  array $array
     * @return array
     */
    public static function convertArrayFormDin2Pdo($dataArray,$upperCase = false) 
    {
        $result = false;
        if(is_array($dataArray) ) {
            $listKeys = array_keys($dataArray);
            $firstKey = $listKeys[0];
            foreach( $dataArray[$firstKey] as $keyNumber => $value ) {
                foreach( $listKeys as $keyName ) {
                    if($upperCase) {
                        $result[ $keyNumber ][ strtoupper($keyName) ] = $dataArray[$keyName][$keyNumber];
                    }else{
                        $result[ $keyNumber ][ strtolower($keyName) ] = $dataArray[$keyName][$keyNumber];
                    }
                }
            }
        }
        return $result;
    }
    //--------------------------------------------------------------------------------
    /**
     * Convert array FormDin para Array com Obetos VO
     *
     * @param array $arrayFormDin - array FormDin
     * @param string $classNameVoEntity - nome da classe VO
     * @return array
     */
    public static function convertArrayFormDin2ArrayVoEntity($arrayFormDin,$classNameVoEntity)
    {    
        $arrayVoEntity = array();
        $arrayPdo = self::convertArrayFormDin2Pdo($arrayFormDin,false);
        foreach ($arrayPdo as $elemento){
            $vo = new $classNameVoEntity();
            $vo = \FormDinHelper::setPropertyVo($elemento,$vo);
            $arrayVoEntity[] = $vo;
        }
        return $arrayVoEntity;
    }    
    //--------------------------------------------------------------------------------
    /**
     * @deprecated chante to ValidateHelper::isArray
     * Validade is array and not empty
     * @param integer $id
     * @param string $method
     * @param string $line
     * @throws InvalidArgumentException
     * @return void
     */
    public static function validateIsArray($array,$method,$line)
    {
        ValidateHelper::isArray($array, $method, $line);
    }
    //--------------------------------------------------------------------------------
    /***
     * Evita erro de notice. Recebe um array FormDin, um atributo e a chave.
     * Verifica se o atributo e achave existem e devolve o valor
     * @param array $array
     * @param string $atributeName
     * @param int $key
     * @return NULL|mixed|array
     */
    public static function formDinGetValue($array,$atributeName,$key)
    {        
        $value = null;
        if( self::has($atributeName, $array) ) {
            $arrayResult = self::getArray($array, $atributeName);
            $value = self::get($arrayResult, $key);
        }
        return $value;
    }
    
    
    /**
     * Remove todos os elementos de uma linha de um array FormDin
     * Recebe um array formDin e o numero da linha que será removida.
     * Retonar um novo array com:
     *         $result['result'] = true se deletou ou false se não foi possivel deletar
     *         $result['formarray'] = array com o resultado
     *         $result['message'] = motivo da não deleção
     * @param array $array
     * @param string $atributeName
     * @param int $keyIndex
     * @throws InvalidArgumentException
     * @return NULL|array
     */
    public static function formDinDeleteRowByKeyIndex($array,$keyIndex){
        self::validateIsArray($array, __METHOD__, __LINE__);
        $attributeName = array_key_first($array);
        return self::formDinDeleteRowByColumnNameAndKeyIndex($array, $attributeName, $keyIndex);
    }    
    
    /**
     * Remove todos os elementos de uma linha de um array FormDin
     * Recebe um array formDin o nome de uma coluna e o numero da linha que será removida.
     * Retonar um novo array com:
     *         $result['result'] = true se deletou ou false se não foi possivel deletar
     *         $result['formarray'] = array com o resultado
     *         $result['message'] = motivo da não deleção
     * @param array $array
     * @param string $atributeName
     * @param int $keyIndex
     * @throws InvalidArgumentException
     * @return NULL|array
     */
    public static function formDinDeleteRowByColumnNameAndKeyIndex($array,$attributeName,$keyIndex)
    {
        self::validateIsArray($array, __METHOD__, __LINE__);
        
        $result = array();
        $result['result'] = false;
        $result['formarray'] = $array;
        
        if( !self::has($attributeName, $array) ) {
            $result['message'] = TMessage::ARRAY_ATTRIBUTE_NOT_EXIST;
        }else{
            if( !self::formDinGetValue($array, $attributeName, $keyIndex) ){
                $result['message'] = TMessage::ARRAY_KEY_NOT_EXIST;
            }else{
                $arrayResult = array();
                foreach ($array as $attribute => $arrayAttribute) {
                    foreach ($arrayAttribute as $key => $value) {
                        if($keyIndex != $key ){
                            $arrayResult[$attribute][]=$value;
                        }
                    }
                }
                $result['result']    = true;
                $result['formarray'] = $arrayResult;
            }
        }
        return $result;
    }
}
?>
