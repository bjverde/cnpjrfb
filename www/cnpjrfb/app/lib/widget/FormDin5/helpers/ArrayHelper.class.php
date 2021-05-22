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

class ArrayHelper
{
    const TYPE_FORMDIN = 'ARRAY_TYPE_FORMDIN';
    const TYPE_FORMDIN_STRING = 'STRING_TYPE_FORMDIN';
    const TYPE_FORMDIN_STRING_GRID_ACTION = 'STRING_TYPE_FORMDIN_GRID_ACTION_PARAMETERS';
    
    const TYPE_PDO     = 'ARRAY_TYPE_PDO';
    const TYPE_PHP     = 'ARRAY_PHP';
    
    const TYPE_ADIANTI = 'ARRAY_TYPE_ADIANTI';
    const TYPE_ADIANTI_GRID_ACTION = 'ARRAY_TYPE_ADIANTI_GRID_ACTION_PARAMETERS';

    public static function validateUndefined($array,$atributeName) 
    {
        if(!isset($array[$atributeName])) {
            $array[$atributeName]=null;
        }
        return is_null($array[$atributeName])?null:trim($array[$atributeName]);
    }

    public static function isArrayNotEmpty($array) 
    {
        $value = false;
        if (is_array($array) && !empty($array)) {
            $value = true;
        }
        return $value;
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
    public static function getDefaultValue($array,$atributeName,$DefaultValue) 
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
        $result = self::getDefaultValue($array, $atributeName, null);
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
        ValidateHelper::isArray($array, __METHOD__, __LINE__);
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
    public static function formDinDeleteRowByKeyIndex($array,$keyIndex)
    {
        ValidateHelper::isArray($array, __METHOD__, __LINE__);
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
        ValidateHelper::isArray($array, __METHOD__, __LINE__);
        
        $result = array();
        $result['result'] = false;
        $result['formarray'] = $array;
        
        if( !self::has($attributeName, $array) ) {
            $result['message'] = TFormDinMessage::ARRAY_ATTRIBUTE_NOT_EXIST;
        }else{
            if( !self::formDinGetValue($array, $attributeName, $keyIndex) ){
                $result['message'] = TFormDinMessage::ARRAY_KEY_NOT_EXIST;
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
    //--------------------------------------------------------------------------------
    //--------------------------------------------------------------------------------
    //--------------------------------------------------------------------------------
    /**
     * @codeCoverageIgnore
     * Mostra um exemplo do tipo de array ou string conforme a constente informada
     *  - TYPE_FORMDIN = array no formato FormDin
     *  - TYPE_FORMDIN_STRING = string no formato 'KEY=VALUE,KEY=VALUE' ou 'KEY=>VALUE,KEY=>VALUE'
     *  - TYPE_FORMDIN_STRING_GRID_ACTION = string no formato '<campo_tabela> | <campo_formulario> , <campo_tabela> | <campo_formulario>'
     * 
     *  - TYPE_FORMDIN = array no formato FormDin [NAME_COLUM][KEY_NUM][VALUE]
     *  - TYPE_PDO = array no formato '[KEY_NUM]=ARRAY,[KEY_NUM]=ARRAY'
     *  - TYPE_ADIANTI = array no formato 'KEY=OBJ,KEY=OBJ'
     *
     * @param string
     * @return array
     */
    public static function showExempleByType($type)
    {
        $arrayResult = array();
        switch ($type) {
            case self::TYPE_FORMDIN:
                $arrayFormDin  = array();
                $arrayFormDin['ID'][] = 1010;
                $arrayFormDin['NAME'][] = 'aaaa aaa';
                $arrayFormDin['SIGLA'][] = 'a';

                $arrayFormDin['ID'][] = 2020;
                $arrayFormDin['NAME'][] = 'bbb bbb';
                $arrayFormDin['SIGLA'][] = 'b';

                $arrayFormDin['ID'][] = 3030;
                $arrayFormDin['NAME'][] = 'ccc ccc';
                $arrayFormDin['SIGLA'][] = 'c';

                $arrayResult['type']='array';
                $arrayResult['mensagem']='array no formato FormDin';
                $arrayResult['exemple']='array';
            break;
            case self::TYPE_FORMDIN_STRING:
                $arrayResult['type']='string';
                $arrayResult['mensagem']="string no formato 'KEY=VALUE,KEY=VALUE' ou 'KEY=>VALUE,KEY=>VALUE'";
                $arrayResult['exemple']='1=A,2=B,3=C';
            break;
            case self::TYPE_FORMDIN_STRING_GRID_ACTION:
                $arrayResult['type']='string';
                $arrayResult['mensagem']="string no formato '<campo_tabela> | <campo_formulario> , <campo_tabela> | <campo_formulario>'";
                $arrayResult['exemple']='1=A,2=B,3=C';
            break;
            case self::TYPE_PDO:
                $arrayFormDin  = array();
                $arrayFormDin[0]['ID'] = 1010;
                $arrayFormDin[0]['NAME'] = 'aaaa aaa';
                $arrayFormDin[0]['SIGLA'] = 'a';

                $arrayFormDin[1]['ID'] = 2020;
                $arrayFormDin[1]['NAME'] = 'bbb bbb';
                $arrayFormDin[1]['SIGLA'] = 'b';

                $arrayFormDin[2]['ID'] = 3030;
                $arrayFormDin[2]['NAME'] = 'ccc ccc';
                $arrayFormDin[2]['SIGLA'] = 'c';

                $arrayResult['type']='array';
                $arrayResult['mensagem']='array no formato FormDin';
                $arrayResult['exemple']='array';
            break;            
            default:
                throw new InvalidArgumentException(TFormDinMessage::ERROR_TYPE_WRONG);
          }
        return $arrayResult;
    }
    //--------------------------------------------------------------------------------
    /**
     * Determina o tipo do input conforme as constantes declarada
     *  - TYPE_FORMDIN_STRING_GRID = string no formato 'KEY|VALUE,KEY|VALUE'
     *  - TYPE_FORMDIN_STRING = string no formato 'KEY=VALUE,KEY=VALUE' ou 'KEY=>VALUE,KEY=>VALUE'
     * 
     *  - TYPE_FORMDIN = array no formato FormDin [NAME_COLUM][KEY_NUM][VALUE]
     *  - TYPE_PDO     = array no formato '[KEY_NUM]=ARRAY,[KEY_NUM]=ARRAY'
     *  - TYPE_ADIANTI = array no formato 'KEY=OBJ,KEY=OBJ'
     *  - TYPE_PHP     = array no formato 'KEY=STRING,KEY=STRING'
     *
     * user ArrayHelper::showExempleByType para ver exemplos
     * @param string|array $array
     * @return const
     */    
    public static function getType($input)
    {
        $type = null;
        if( ArrayHelper::isArrayNotEmpty($input) ){
            $type = self::getArrayType($input);
        }elseif( is_string($input) ){
            $type = self::getStringType($input);
        }else{
            throw new InvalidArgumentException(TFormDinMessage::ERROR_TYPE_WRONG);
        }
        return $type;
    }
    //--------------------------------------------------------------------------------
    /**
     * Determina o tipo  de entrada que pode ser um dos 4 tipos
     *  - TYPE_FORMDIN_STRING_GRID = string no formato 'KEY|VALUE,KEY|VALUE'
     *  - TYPE_FORMDIN_STRING = string no formato 'KEY=VALUE,KEY=VALUE'
     *
     * @param string
     * @return void
     */
    public static function getStringType($input)
    {
        $type = null;
        if( is_string($input) ){
            if( preg_match('/\|/i',$input) == true ){
                $type = self::TYPE_FORMDIN_STRING_GRID_ACTION;
            }else if( preg_match('/=/i',$input) == true ){
                $type = self::TYPE_FORMDIN_STRING;
            }else{
                throw new InvalidArgumentException(TFormDinMessage::ERROR_TYPE_WRONG);
            }
        }else{
            throw new InvalidArgumentException(TFormDinMessage::ERROR_TYPE_WRONG);
        }
        return $type;
    }
    /**
     * Determina o tipo  de entrada que pode ser um dos 4 tipos
     *  - TYPE_FORMDIN = array no formato FormDin [NAME_COLUM][KEY_NUM][VALUE]
     *  - TYPE_PDO     = array no formato 'KEY=ARRAY,KEY=ARRAY'
     *  - TYPE_ADIANTI = array no formato 'KEY=OBJ,KEY=OBJ'
     *  - TYPE_PHP     = array no formato 'KEY=STRING,KEY=STRING'
     *
     * @param mix|array $array
     * @return void
     */
    public static function getArrayType(array $array)
    {
        $type = self::TYPE_ADIANTI;
        $qtd = CountHelper::count($array);
        if ( $qtd > 0 ){
            //$listKey = array_keys($array);
            $keyFirst = array_key_first($array);
            $KeyLast  = array_key_last($array);
            if( is_int($keyFirst) && is_int($KeyLast) ){
                $firstType = is_object($array[$keyFirst]);
                $lastType  = is_object($array[$KeyLast]);
                if( $firstType &&  $lastType ){
                    $type = self::TYPE_ADIANTI;
                }else{
                    $firstType = is_array($array[$keyFirst]);
                    $lastType  = is_array($array[$KeyLast]);
                    if( $firstType &&  $lastType ){
                        $type = self::TYPE_PDO;
                    }else{
                        $type = self::TYPE_PHP;
                    }
                }
            }else{
                $type = self::TYPE_FORMDIN;
            }
        }
        return $type;
    }
    //--------------------------------------------------------------------------------
    /**
     * Convert Array FormDin,PDO ou Adianti para Adianti Format (TYPE_ADIANTI)
     *
     * @param  array $array       - 01: Array
     * @param const $typeCase    - 02: Type Case. Default = PDO::CASE_NATURAL, PDO::CASE_UPPER, PDO::CASE_LOWER
     * @return array
     */
    public static function convertArray2Adianti($dataArray,$typeCase = PDO::CASE_NATURAL) 
    {
        $typeArray = self::getArrayType($dataArray);
        if($typeArray == self::TYPE_FORMDIN){
            $dataArray = self::convertArrayFormDin2Adianti($dataArray,$typeCase);
        }elseif($typeArray == self::TYPE_PDO){
            $dataArray = self::convertArrayPDO2Adianti($dataArray,$typeCase);
        }
        return $dataArray;
    }
    //--------------------------------------------------------------------------------
    /**
     * Convert Array PDO Format (TYPE_PDO) to FormDin format (TYPE_FORMDIN)
     *
     * @param  array $array      - 01:
     * @param const $typeCase    - 02: Type Case. Default = PDO::CASE_NATURAL, PDO::CASE_UPPER, PDO::CASE_LOWER
     * @return array
     */
    public static function convertArrayPdo2FormDin($dataArray,$typeCase = PDO::CASE_NATURAL) 
    {
        $result = false;
        if(is_array($dataArray) ) {
            foreach( $dataArray as $k => $arr ) {
                foreach( $arr as $fieldName => $value ) {
                    if( $typeCase == PDO::CASE_NATURAL ){
                        $result[ $fieldName ][ $k ] = $value;
                    }elseif($typeCase == PDO::CASE_UPPER){
                        $result[ strtoupper($fieldName) ][ $k ] = $value;
                    }else{
                        $result[ strtolower($fieldName) ][ $k ] = $value;
                    }
                }//Fim ForEach Interno
            }
        }
        return $result;
    }
    //--------------------------------------------------------------------------------
    /**
     * Convert Array FormDin Format (TYPE_FORMDIN) to PDO format(TYPE_PDO)
     *
     * @param  array $array      - 01: Array FormDin4 
     * @param const $typeCase    - 02: Type Case. Default = PDO::CASE_NATURAL, PDO::CASE_UPPER, PDO::CASE_LOWER
     * @return array
     */
    public static function convertArrayFormDin2Pdo($dataArray,$typeCase = PDO::CASE_NATURAL) 
    {
        $result = false;
        if(is_array($dataArray) ) {
            $listKeys = array_keys($dataArray);
            $firstKey = $listKeys[0];
            foreach( $dataArray[$firstKey] as $keyNumber => $value ) {
                foreach( $listKeys as $keyName ) {
                    if( $typeCase == PDO::CASE_NATURAL ){
                        $result[ $keyNumber ][ $keyName ] = $dataArray[$keyName][$keyNumber];
                    }elseif($typeCase == PDO::CASE_UPPER){
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
     * Convert Array FormDin Format to Adianti format
     *
     * @param  array $array      - 01: Array FormDin4 
     * @param const $typeCase    - 02: Type Case. Default = PDO::CASE_NATURAL, PDO::CASE_UPPER, PDO::CASE_LOWER
     * @return array
     */
    public static function convertArrayFormDin2Adianti($dataArray,$typeCase = PDO::CASE_NATURAL) 
    {
        $result = array();
        if(self::isArrayNotEmpty($dataArray)){
            $result = self::convertArrayFormDin2Pdo($dataArray,$typeCase);
            foreach( $result as $keyNumber => $value ) {
                $result[$keyNumber] = (object)$value;
            }
        }
        return $result;
    }
    //--------------------------------------------------------------------------------
    /**
     * Convert Array PDO format to Adianti format
     *
     * @param  array $array        - 1: Array FormDin4 
     * @param const $typeCase      - 2: Type Case. Default = PDO::CASE_NATURAL, PDO::CASE_UPPER, PDO::CASE_LOWER
     * @return array
     */
    public static function convertArrayPDO2Adianti( $dataArray ,$typeCase = PDO::CASE_NATURAL ) 
    {
        $resultPDO = null;
        if( $typeCase == PDO::CASE_NATURAL ){
            $resultPDO = $dataArray;
        }else{
            foreach( $dataArray as $key => $arrayInterno ) {
                foreach( $arrayInterno as $atributo => $value ) {
                    if($typeCase == PDO::CASE_UPPER) {
                        $resultPDO[ $key ][ strtoupper($atributo) ] = $value;
                    }else{
                        $resultPDO[ $key ][ strtolower($atributo) ] = $value;
                    }                        
                }
            }//Fim foreach externo
        }
        $resultAdianti = null;
        foreach( $resultPDO as $keyNumber => $value ) {
            $resultAdianti[$keyNumber] = (object)$value;
        }
        return $resultAdianti;
    }
    //--------------------------------------------------------------------------------
    /**
     * Convert String FormDin 'S=SIM,N=Não' para Array PHP
     * @param  string $array   - 1: string ou array de entrada
     * @return array
     */
    public static function convertString2Array($string,$showSimpleStringError=true) 
    {
        $result = null;
        if( self::isArrayNotEmpty($string) ) {
            $result = $string;
        }else{
            if( !is_string($string) ){
                throw new InvalidArgumentException(TFormDinMessage::ERROR_TYPE_WRONG);
            }else{
                $string = trim($string);
                $string = preg_replace('/\=\>/','=',$string);
                $pos  = strpos($string, '=');
                if( empty($pos) && $showSimpleStringError===true){
                    if(strlen($string)==1){
                        $result[$string]='';
                    }else{
                        throw new InvalidArgumentException(TFormDinMessage::ERROR_TYPE_WRONG);
                    }                    
                }elseif( empty($pos) && $showSimpleStringError===false){
                    $result = $string;
                }else{
                    //'S=SIM,N=Não,T=Talvez'
                    $string = explode(',',$string);
                    foreach( $string as $value ) {
                        $intString = explode('=',$value);
                        $result[$intString[0]]=$intString[1];
                    }
                }
            }
        }
        return $result;
    }
    //-------------------------------------------------------------------------
    /**
     * Converte array Adianti Grid Action ['key0'=>'{value0}','key1' => '{value1}']
     * para uma string no formato FormDin Grid
     * <campo_tabela> | <campo_formulario> , <campo_tabela> | <campo_formulario>
     * 
     * @param array $arrayData
     * @return array 
     */     
    private static function convertArrayParametersAdianti2FormDin($arrayData){
        $arrayData = self::convertArrayGridActionParametersAdianti2PHP($arrayData);
        $arrayData = self::convertArrayGridActionParametersPHP2FormDin($arrayData);
        return $arrayData;
    }
    /**
     * Converte array Adianti Grid Action ['key0'=>'{value0}','key1' => '{value1}']
     * para um array comum PHP (key0=>value0,key1=>value1)
     * 
     * @param array $arrayData
     * @return array 
     */    
    private static function convertArrayGridActionParametersAdianti2PHP($arrayData){
        foreach( $arrayData as $k => $v ) {
            $v = mb_substr($v, 0, mb_strlen($v,'utf-8')-1, 'utf-8');
            $v = mb_substr($v, 1, mb_strlen($v,'utf-8'), 'utf-8');
            $arrayData[$k] = $v;
        }
        return $arrayData;
    }
    /**
     * Converte uma string no formato FormDin Grid
     * <campo_tabela> | <campo_formulario> , <campo_tabela> | <campo_formulario>
     * para um array Adianti Grid Action ['key0'=>'{value0}','key1' => '{value1}']
     *
     * @param array $arrayData
     * @return array 
     */
    private static function convertArrayGridActionParametersFormDin2Adianti($arrayData){
        $result = array();
        $listFields = explode( ',', $arrayData );
        foreach( $listFields as $k => $field ) {
            $field = explode('|',$field);
            $result[ $field[0] ] = '{'.$field[1].'}';
        }
        return $result;
    }
    /**
     * Converte uma string no formato FormDin Grid
     * <campo_tabela> | <campo_formulario> , <campo_tabela> | <campo_formulario>
     * para um array PHP (key0=>value0,key1=>value1)
     *
     * @param array $arrayData
     * @return array 
     */
    private static function convertArrayGridActionParametersFormDin2PHP($arrayData){
        $result = array();
        $listFields = explode( ',', $arrayData );
        foreach( $listFields as $k => $field ) {
            $field = explode('|',$field);
            $result[ $field[0] ] = $field[1];
        }
        return $result;
    }
    /**
     * Converte um array comum PHP (key0=>value0,key1=>value1) para um
     * string no formato FormDin Grid Action Parameters 
     * <campo_tabela> | <campo_formulario> , <campo_tabela> | <campo_formulario>
     *
     * @param array $arrayData
     * @return array 
     */
    private static function convertArrayGridActionParametersPHP2FormDin($arrayData){
        $result = null;
        foreach( $arrayData as $k => $v ) {
            $result = $result.','.$k.'|'.$v;
        }
        $result  = mb_substr($result, 1, mb_strlen($result,'utf-8'), 'utf-8');
        return $result;
    }
    /**
     * Converte um array comum PHP (key0=>value0,key1=>value1) para um
     * array no formato Adianti Grid Action Parameters 
     * ['key0'=>'{value0}','key1' => '{value1}']
     *
     * @param array $arrayData
     * @return array 
     */
    private static function convertArrayGridActionParametersPHP2Adianit($arrayData){
        foreach( $arrayData as $k => $v ) {
            $arrayData[$k] = '{'.$v.'}';
        }
        return $arrayData;
    }
    //--------------------------------------------------------------------------------
    /**
     * Recebe um Array e deterimina se ou não um array do tipo
     * TYPE_ADIANTI_GRID_ACTION = array no formato Adianti Grid Action Parameters 
     * ['key0'=>'{value0}','key1' => '{value1}']
     * @param array $arrayData
     * @return bolean
     */
    public static function arrayTypeIsAdiantiGrid( array $arrayData)
    {
        $result = false;
        $lastElement = end($arrayData);
        if( is_string($lastElement) ){
            $fristChar = mb_substr($lastElement, 0, 1, 'utf-8');
            $lastChar  = mb_substr($lastElement, -1, 1, 'utf-8');
            if( ($fristChar=='{') && ($lastChar=='}') ){
                $result = true;
            }
        }
        return $result;
    }    
    //--------------------------------------------------------------------------------    
    /**
     * Detecta o tipo de array de para o MixUpdateFields e retorna o tipo
     * conforme as constantes de classe
     * @param array $arrayData
     * @return mix
     */
    public static function getTypeArrayMixUpdateFields($arrayData){
        $result = null;
        if( empty($arrayData) ){
            $result = null;
        }elseif( ArrayHelper::isArrayNotEmpty($arrayData) ){
            if( ArrayHelper::arrayTypeIsAdiantiGrid($arrayData) ){
                $result = self::TYPE_ADIANTI_GRID_ACTION;
            }else{
                $result = self::TYPE_PHP;
            }
        }elseif( is_string($arrayData) && (strpos( $arrayData,'|')!== false) ){
            $result = self::TYPE_FORMDIN_STRING_GRID_ACTION;
        }else{
            $result = false;
        }
        return $result;
    }
    //--------------------------------------------------------------------------------    
    /**
     * Detecta o tipo de array do MixUpdateFields e converte para o formato
     * de saída informado
     * @param array $arrayData
     * @param const $outputFormat
     * @return array
     */
    public static function convertArrayMixUpdate2OutputFormat($arrayData,$outputFormat = ArrayHelper::TYPE_ADIANTI_GRID_ACTION){
        $inputFormt = self::getTypeArrayMixUpdateFields($arrayData);
        if($inputFormt===false){
            throw new InvalidArgumentException(TFormDinMessage::ERROR_OBJ_TYPE_WRONG);
        }
        $result = $arrayData;
        
        if($inputFormt == ArrayHelper::TYPE_PHP){
            if($outputFormat == ArrayHelper::TYPE_FORMDIN_STRING_GRID_ACTION){
                $result = self::convertArrayGridActionParametersPHP2FormDin($arrayData);
            }elseif($outputFormat == ArrayHelper::TYPE_ADIANTI_GRID_ACTION){
                $result = self::convertArrayGridActionParametersPHP2Adianit($arrayData);
            }
        }elseif($inputFormt == ArrayHelper::TYPE_FORMDIN_STRING_GRID_ACTION){
            if($outputFormat == ArrayHelper::TYPE_PHP){
                $result = self::convertArrayGridActionParametersFormDin2PHP($arrayData);
            }elseif($outputFormat == ArrayHelper::TYPE_ADIANTI_GRID_ACTION){
                $result = self::convertArrayGridActionParametersFormDin2Adianti($arrayData);
            }
        }else{
            if($outputFormat == ArrayHelper::TYPE_PHP){
                $result = self::convertArrayGridActionParametersAdianti2PHP($arrayData);
            }elseif($outputFormat == ArrayHelper::TYPE_FORMDIN_STRING_GRID_ACTION){
                $result = self::convertArrayParametersAdianti2FormDin($arrayData);
            }
        }
        return $result;
    }
    //--------------------------------------------------------------------------------
    /**
     * Converte um array Adianti para o Padrão FormDin
     * @param array $arrayData    - 1:
     * @param const $typeCase     - 2: Type Case. Default = PDO::CASE_NATURAL, PDO::CASE_UPPER, PDO::CASE_LOWER
     * @return array
     */
    public static function convertAdianti2Pdo($arrayData,$typeCase = PDO::CASE_NATURAL){
        $result  = array();
        $resultA = array();
        if( self::isArrayNotEmpty($arrayData) ){
            $obj = $arrayData[0];
            if($obj instanceof TRecord){
                foreach( $arrayData as $key => $obj ) {
                    $resultA[$key] = $obj->toArray();
                }
            }else{
                foreach( $arrayData as $key => $obj ) {
                    $resultA[$key] = get_object_vars($obj);
                }
            }
            if( $typeCase == PDO::CASE_NATURAL ){
                $result = $resultA;
            }else{
                foreach( $resultA as $key => $arrayInterno ) {
                    foreach( $arrayInterno as $atributo => $value ) {
                        if($typeCase == PDO::CASE_UPPER) {
                            $result[ $key ][ strtoupper($atributo) ] = $value;
                        }else{
                            $result[ $key ][ strtolower($atributo) ] = $value;
                        }                        
                    }
                }//Fim foreach externo
            }
        }//fim test array
        return $result;
    }
    //--------------------------------------------------------------------------------
    /**
     * Converte um array Adianti para o Padrão FormDin
     * @param array $arrayData    - 1:
     * @param const $typeCase     - 2: Type Case. Default = PDO::CASE_NATURAL, PDO::CASE_UPPER, PDO::CASE_LOWER
     * @return array
     */
    public static function convertAdianti2FormDin($arrayData,$typeCase = PDO::CASE_NATURAL){
        $arrayData = self::convertAdianti2Pdo($arrayData,$typeCase);
        $result    = self::convertArrayPdo2FormDin($arrayData,$typeCase);
        return $result;
    }    
    //--------------------------------------------------------------------------------
    /**
     * Detecta o tipo de array e converte para o formato de saída informado
     * @param array $arrayData    - 1:
     * @param const $outputFormat - 2: Type OutPut Format. Default = ArrayHelper::TYPE_ADIANTI, ArrayHelper::TYPE_PDO, ArrayHelper::TYPE_FORMDIN
     * @param const $typeCase     - 3: Type Case. Default = PDO::CASE_NATURAL, PDO::CASE_UPPER, PDO::CASE_LOWER
     * @return array
     */
    public static function convertArray2OutputFormat($arrayData,$outputFormat = ArrayHelper::TYPE_ADIANTI,$typeCase = PDO::CASE_NATURAL){
        $result = null;
        if( !empty($arrayData) ){
            $inputFormat = self::getType($arrayData);

            if($inputFormat == ArrayHelper::TYPE_ADIANTI){
                if( $outputFormat == ArrayHelper::TYPE_PDO ){
                    $result = self::convertAdianti2Pdo($arrayData,$typeCase);
                }elseif($outputFormat == ArrayHelper::TYPE_FORMDIN){
                    $result = self::convertAdianti2FormDin($arrayData,$typeCase);
                }elseif( $outputFormat == ArrayHelper::TYPE_ADIANTI){
                    $result = $arrayData;
                }
            }elseif($inputFormat == ArrayHelper::TYPE_PDO ){
                if( $outputFormat == ArrayHelper::TYPE_ADIANTI ){
                    $result = self::convertArrayPDO2Adianti($arrayData,$typeCase);
                }elseif($outputFormat == ArrayHelper::TYPE_FORMDIN ){
                    $result = self::convertArrayPdo2FormDin($arrayData,$typeCase);
                }elseif( $outputFormat == ArrayHelper::TYPE_PDO ){
                    $result = $arrayData;
                }
            }elseif($inputFormat == ArrayHelper::TYPE_FORMDIN ){
                if( $outputFormat == ArrayHelper::TYPE_ADIANTI ){
                    $result = self::convertArrayFormDin2Adianti($arrayData,$typeCase);
                }elseif($outputFormat == ArrayHelper::TYPE_PDO ){
                    $result = self::convertArrayFormDin2Pdo($arrayData,$typeCase);
                }elseif( $outputFormat == ArrayHelper::TYPE_FORMDIN ){
                    $result = $arrayData;
                }
            }elseif( $inputFormat == ArrayHelper::TYPE_PHP ){
                $result = $arrayData;
            }else{
                throw new InvalidArgumentException(TFormDinMessage::ERROR_TYPE_WRONG);
            }
        }
        return $result;
    }
    //--------------------------------------------------------------------------------
    /**
     * Recebe um array do tipo ArrayHelper::TYPE_ADIANTI, ArrayHelper::TYPE_PDO, ArrayHelper::TYPE_FORMDIN
     * para um array TYPE_PHP no formato 'KEY=VALUE,KEY=VALUE'
     *
     * @param array  $arrayData   - 1: Array de entrada
     * @param string $keyColumn   - 2: String nome da coluna chave
     * @param string $valueColumn - 3: String nome da coluna valor
     * @param const  $typeCase    - 4: Type Case. Default = PDO::CASE_NATURAL, PDO::CASE_UPPER, PDO::CASE_LOWER
     * @return array
     */
    public static function convertArray2PhpKeyValue($arrayData,$keyColumn,$valueColumn,$typeCase = PDO::CASE_NATURAL)
    {
        ValidateHelper::isString($keyColumn,__METHOD__,__LINE__);
        ValidateHelper::isString($valueColumn,__METHOD__,__LINE__);
        $arrayData   = ArrayHelper::convertArray2OutputFormat($arrayData,ArrayHelper::TYPE_PDO,$typeCase);
        if( !array_key_exists($keyColumn, $arrayData[0]) ) {
            throw new InvalidArgumentException(TFormDinMessage::ERROR_TYPE_WRONG);
        }
        if( !array_key_exists($valueColumn, $arrayData[0]) ) {
            throw new InvalidArgumentException(TFormDinMessage::ERROR_TYPE_WRONG);
        }
        $arrayResult = array();
        foreach( $arrayData as $key => $arrayInterno ) {
            $arrayResult[ $arrayInterno[$keyColumn] ] = $arrayInterno[$valueColumn];
        }
        return $arrayResult;
    }

}
?>