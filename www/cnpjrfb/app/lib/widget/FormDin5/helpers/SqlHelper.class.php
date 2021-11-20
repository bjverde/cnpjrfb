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

if(!defined('ROWS_PER_PAGE')) { define('ROWS_PER_PAGE', 20); 
}

class SqlHelper
{
	const SQL_TYPE_NUMERIC    = 'numeric';
	const SQL_TYPE_TEXT_LIKE  = 'like';
	const SQL_TYPE_TEXT_EQUAL = 'text';
	const SQL_TYPE_IN_TEXT    = 'text with IN';
	const SQL_TYPE_IN_NUMERIC = 'numeric with IN';
	
	const SQL_CONNECTOR_AND = ' AND ';
	const SQL_CONNECTOR_OR  = ' OR ';
	
	private static $dbms;	
	
	public static function setDbms($dbms)
	{
	    self::$dbms = $dbms;	    
	}
	public static function getDbms()	
    {
        $dbms =  null;
        if ( !empty(self::$dbms) ){
            $dbms = self::$dbms;
        } else {
            $dbms = BANCO;
        }
        return $dbms;
    }
    //--------------------------------------------------------------------------------
    public static function getRowStart($page,$rowsPerPage) 
    {
        $rowStart = 0;
        $page = isset($page) ? $page : null;
        $rowsPerPage = isset($rowsPerPage) ? $rowsPerPage : ROWS_PER_PAGE;
        if(!empty($page)) {
            $rowStart = ($page-1)*$rowsPerPage;
        }        
        return $rowStart;
    }
    //--------------------------------------------------------------------------------
    public static function attributeIsset($attribute,$isTrue,$isFalse)
    {
        $retorno = $isFalse;
        if(isset($attribute) && ($attribute<>'') ){
            if( $attribute<>'0' ){
                $retorno = $isTrue;
            }
        }
        return $retorno;
    }
    //--------------------------------------------------------------------------------
    /***
     * Verify that a given attribute has an attribute value. Returns the string 
     * that corresponds to the $isTrue or $isFalse situation.
     * 
     * Verificar se um determinado atributo tem um valor atributo. Retorna a 
     * string corresponde a situação $isTrue ou $isFalse.
     * 
     * @param array   $whereGrid  1: array with all the attributes that should be checked 
     * @param string  $attribute  2: attribute that will be verified
     * @param string  $isTrue     3: return string if positive
     * @param string  $isFalse    4: 
     * @param boolean $testZero   5: the zero must be forehead or not. True = attribute must be diverge from zero
     * @return string
     */
    public static function attributeIssetOrNotZero($whereGrid,$attribute,$isTrue,$isFalse,$testZero=true)
    {
        $retorno = $isFalse;
        $has = ArrayHelper::has($attribute, $whereGrid);
        if($has ) {
            if(isset($whereGrid[$attribute]) && !($whereGrid[$attribute]==='') ) {
                if($testZero) {
                    if($whereGrid[$attribute]<>'0' ) {
                        $retorno = $isTrue;
                    }
                }else{
                    $retorno = $isTrue;
                }
            }
        }
        return $retorno;
    }
    //----------------------------------------
    public static function transformValidateString( $string , $dbms)
    {        
        if ( $dbms == TFormDinPdoConnection::DBMS_MYSQL ) {
            //$string = addslashes($string);
            //$patterns = '/(%)/';
            $doubleQuotes = chr(34);
            $patterns = '/(%|\'|"|'.$doubleQuotes.')/';
            $replacements = '\\\$1';
            $string = preg_replace($patterns, $replacements, $string);
        } else {
            if ( preg_match('/(\'|")/', $string ) > 0 ) {
                throw new DomainException(TFormDinMessage::DONOT_QUOTATION);
            }
        }
        return $string;
    }
    //----------------------------------------
    /**
     * Replace spaces with % to make it easier to search with like.
     * 
     * Substitua os espaços por % para facilitar a busca com like.
     * 
     * @param string $string
     * @return string`
     */
    public static function explodeTextString( $string, $dbms )
    {
        $dataBaseWithLike = ($dbms == TFormDinPdoConnection::DBMS_MYSQL) 
                         || ($dbms == TFormDinPdoConnection::DBMS_POSTGRES)
                         || ($dbms == TFormDinPdoConnection::DBMS_SQLITE)
                         || ($dbms == TFormDinPdoConnection::DBMS_SQLSERVER);
        if ( $dataBaseWithLike ) {
            $string = trim($string);
            $string = preg_replace('/\s/', '%', $string);
        }
        return $string;
    }
    
    /***
     * Return string sql for query with numeric
     * @param string $stringWhere
     * @param array $arrayWhereGrid
     * @param string $atribute
     * @param string $type
     * @param boolean $testZero
     * @param string $value
     * @param string $connector
     * @return string
     */
    public static function getSqlTypeNumeric( $stringWhere
                                            , $arrayWhereGrid
                                            , $attribute
                                            , $testZero=true
                                            , $value
                                            , $connector=self::SQL_CONNECTOR_AND
                                            ) {
        $isTrue = EOL.' AND '.$attribute.' = '.$value.'  ';
        $attribute = self::attributeIssetOrNotZero($arrayWhereGrid,$attribute,$isTrue,null,$testZero);
        $stringWhere = $stringWhere.$attribute;
        return $stringWhere;
    }
    //--------------------------------------------------------------------------
    public static function getSqlTypeTextLike( $stringWhere
                                             , $arrayWhereGrid
                                             , $attribute
                                             , $testZero=true
                                             , $value
                                             , $connector=self::SQL_CONNECTOR_AND
                                             , $dbms
                                             ) {
        $value = self::explodeTextString($value, $dbms);
        $isTrue = EOL.' AND '.$attribute.' like \'%'.$value.'%\' ';
        $attribute = self::attributeIssetOrNotZero($arrayWhereGrid,$attribute,$isTrue,null,$testZero);
        $stringWhere = $stringWhere.$attribute;
        return $stringWhere;
    }
    //--------------------------------------------------------------------------
    public static function getSqlTypeText( $stringWhere
                                         , $arrayWhereGrid
                                         , $attribute
                                         , $testZero=true
                                         , $value
                                         , $connector=self::SQL_CONNECTOR_AND
                                         ) {
        $isTrue = EOL.' AND '.$attribute.' = \''.$value.'\'  ';
        $attribute = self::attributeIssetOrNotZero($arrayWhereGrid,$attribute,$isTrue,null,$testZero);
        $stringWhere = $stringWhere.$attribute;
        return $stringWhere;
    }
    //--------------------------------------------------------------------------
    public static function getSqlTypeNotIn( $stringWhere
                                          , $arrayWhereGrid
                                          , $attribute
                                          , $testZero=true
                                          , $value
                                          , $connector=self::SQL_CONNECTOR_AND
                                          , $type
                                          )
    {
        if($type == self::SQL_TYPE_IN_NUMERIC){
            $stringWhere = self::getSqlTypeNumeric($stringWhere, $arrayWhereGrid, $attribute, $testZero ,$value, $connector);
        }else{
            $stringWhere = self::getSqlTypeText($stringWhere, $arrayWhereGrid, $attribute, $testZero ,$value, $connector);
        }
        return $stringWhere;
    }
    public static function getSqlTypeIn( $stringWhere
                                       , $arrayWhereGrid
                                       , $attribute
                                       , $testZero=true
                                       , $value
                                       , $connector=self::SQL_CONNECTOR_AND
                                       , $type
                                       )
    {
       $connector = empty($connector)?self::SQL_CONNECTOR_AND:$connector;
       If(is_array($value)){
           $qtdElement = CountHelper::count($value);
           if( $qtdElement == 1 ){
               $value = $value[0];
               if( FormDinHelper::issetOrNotZero($value) ){
                   $stringWhere = self::getSqlTypeNotIn($stringWhere, $arrayWhereGrid, $attribute, $testZero ,$value, $connector, $type);
               }
           } else if( $qtdElement > 1 ) {
               if($type == self::SQL_TYPE_IN_NUMERIC){
                   $value = implode(",",$value);
                   $isTrue = EOL.$connector.$attribute.' in ('.$value.') ';
                   $stringWhere = $stringWhere.$isTrue;
               }else{
                   $value = implode("','",$value);
                   $isTrue = EOL.$connector.$attribute.' in (\''.$value.'\') ';
                   $stringWhere = $stringWhere.$isTrue;
               }
           }
       } else {
           $stringWhere = self::getSqlTypeNotIn($stringWhere, $arrayWhereGrid, $attribute, $testZero ,$value, $connector, $type);
       }
       return $stringWhere;
    }
    //----------------------------------------    
    /***
     * Generates the SQL string for the where clauses, according to the parameters
     * 
     * Gera a string SQL para as cláusulas where, conforme os parametros informados
     * 
     * @param string  $stringWhere     1: Existing SQL String that will be concatenated
     * @param array   $arrayWhereGrid  2: array with all attributes and values
     * @param string  $attribute       3: name of the attribute to be verified
     * @param string  $type            4: Type of clauses
     * @param boolean $testZero        5: 
     * @param string  $connector       6: Connector self::SQL_CONNECTOR_AND or self::SQL_CONNECTOR_OR
     * @param string  $dbms            7: Type of Database management system, see const of TFormDinPdoConnection
     * @return string
     */
    public static function getAtributeWhereGridParameters( $stringWhere
                                                         , $arrayWhereGrid
                                                         , $attribute
                                                         , $type 
                                                         , $testZero=true
                                                         , $connector=self::SQL_CONNECTOR_AND 
                                                         , $dbms
                                                         ) {
        if( ArrayHelper::has($attribute, $arrayWhereGrid) ){
            if( empty($dbms) ){
                throw new InvalidArgumentException(TFormDinMessage::ERROR_SQL_NULL_DBMA);
            }
    	    $value = $arrayWhereGrid[$attribute];
    	    if ( !empty($value) && !is_array($value)){
    		    $value = self::transformValidateString($value,$dbms);
    		}
    		
    		switch ($type) {
    		    case self::SQL_TYPE_NUMERIC:
    		        $stringWhere = self::getSqlTypeNumeric($stringWhere, $arrayWhereGrid, $attribute, $testZero ,$value, $connector);
    		    break;
    		    case self::SQL_TYPE_TEXT_LIKE:
    		        $stringWhere = self::getSqlTypeTextLike($stringWhere ,$arrayWhereGrid ,$attribute ,$testZero ,$value ,$connector ,$dbms);
    		    break;
    		    case self::SQL_TYPE_TEXT_EQUAL:
    		        $stringWhere = self::getSqlTypeText($stringWhere, $arrayWhereGrid, $attribute, $testZero ,$value, $connector);
    		    break;
    		    case self::SQL_TYPE_IN_TEXT:
    		        $stringWhere = self::getSqlTypeIn($stringWhere, $arrayWhereGrid, $attribute, $testZero ,$value, $connector, $type);
    		    break;
    		    case self::SQL_TYPE_IN_NUMERIC:
    		        $stringWhere = self::getSqlTypeIn($stringWhere, $arrayWhereGrid, $attribute, $testZero ,$value, $connector, $type);
    		    break;
    		}
    	}
    	return $stringWhere;
    }
    
}
?>
