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
class ValidateHelper
{
    const DEPRECATED = 'DEPRECATED';
    const NOTICIE = 'NOTICIE';
    const WARNING = 'WARNING';
    const ERROR   = 'ERROR';
    const EXECEPTION = 'EXECEPTION';
    const MSG_DECREP = 'MSG_DECREP';
    const MSG_NOT_IMPLEMENTED = 'MSG_NOT_IMPLEMENTED';
    const MSG_CHANGE = 'MSG_CHANGE';
    
    public static function methodLine($method,$line,$nameMethodValidate)
    {
        if( empty($method) ){
            throw new InvalidArgumentException(TFormDinMessage::ERROR_EMPTY_INPUT.' variable method is null. '.$nameMethodValidate);
        }
        if( empty($line) ){
            throw new InvalidArgumentException(TFormDinMessage::ERROR_EMPTY_INPUT.' variable line is null. '.$nameMethodValidate);
        }
    }
    
    public static function isString($string,$method,$line)
    {
        self::methodLine($method, $line, __METHOD__);
        if( empty($string) || !is_string($string) ){
            throw new InvalidArgumentException(TFormDinMessage::ERROR_TYPE_NOT_STRING.'See the method: '.$method.' in the line: '.$line);
        }
    }

    public static function isNumeric($id,$method,$line)
    {
        self::methodLine($method, $line, __METHOD__);
        if( empty($id) || !is_numeric($id) ){
            throw new InvalidArgumentException(TFormDinMessage::ERROR_TYPE_NOT_INT.'See the method: '.$method.' in the line: '.$line);
        }
    }
    
    public static function isSet($variable,$method,$line)
    {
        self::methodLine($method, $line, __METHOD__);
        if( is_null($variable) ){
            throw new InvalidArgumentException(TFormDinMessage::ERROR_TYPE_NOT_SET.'See the method: '.$method.' in the line: '.$line);
        }
    }
    //--------------------------------------------------------------------------------
    /**
     * Validade is array and not empty
     * @param array $array
     * @param string $method
     * @param string $line
     * @throws InvalidArgumentException
     * @return void
     */
    public static function isArray($array,$method,$line, $validadeEmptyArray = true)
    {
        self::methodLine($method, $line, __METHOD__);
        if( !is_array($array) ){
            throw new InvalidArgumentException(TFormDinMessage::ERROR_TYPE_NOT_ARRAY.'See the method: '.$method.' in the line: '.$line);
        }
        if( $validadeEmptyArray && empty($array)){
            throw new InvalidArgumentException(TFormDinMessage::ERROR_TYPE_ARRAY_EMP.'See the method: '.$method.' in the line: '.$line);
        }        
    }    
    //--------------------------------------------------------------------------------
    /**
     * Validate Object Type is Instance Of TFormDinPdoConnection
     *
     * @param object $tpdo instanceof TFormDinPdoConnection
     * @param string $method __METHOD__
     * @param string $line __LINE__
     * @throws InvalidArgumentException
     * @return void
     */
    public static function objTypeTFormDinPdoConnection($tpdo,$method,$line)
    {
        self::methodLine($method, $line, __METHOD__);
        $typeObjWrong = !($tpdo instanceof TFormDinPdoConnection);
        $notNull = !is_null($tpdo);
        if( $notNull && $typeObjWrong ){
            throw new InvalidArgumentException('Informed class is not an instance of TPDOConnectionObj. See the method: '.$method.' in the line: '.$line);
        }
    }
    //--------------------------------------------------------------------------------
    public static function triggerError($msg,$typeErro)
    {
        if($typeErro == self::EXECEPTION){
            throw new InvalidArgumentException($msg);
        }else if($typeErro == self::ERROR){
            trigger_error($msg, E_USER_ERROR);
        }else if($typeErro == self::WARNING){
            trigger_error($msg, E_USER_WARNING);
        }else{
            trigger_error($msg, E_USER_NOTICE);
        }
    }
    //--------------------------------------------------------------------------------
    public static function typeErrorMsg($typeErroMsg)
    {
        $complemento = null;
        if($typeErroMsg==self::MSG_NOT_IMPLEMENTED){
            $complemento = ' não foi implementado!';
        }else if($typeErroMsg == self::MSG_CHANGE){
            $complemento = ' FOI ALTERADO!';
        }else{
            $complemento = ' FOI DESCONTINUADO!!';
        }
        return $complemento;
    }    
    //--------------------------------------------------------------------------------
    /**
     * Usado para fazer a validação de um parametro do metodo da migração do FormDin4 para o FormDin5
     *
     * @param string $paramName  nome do parametro
     * @param [type] $paramValue valor informado
     * @param const $typeErro ValidateHelper::NOTICIE, ValidateHelper::WARNING e ValidateHelper::ERROR
     * @param const $typeErroMsg
     * @param [type] $class
     * @param [type] $method
     * @param string $line
     * @return void
     */
    public static function validadeParam($paramName,$paramValue,$typeErro,$typeErroMsg,$class,$method,$line)
    {
        $test = isset($paramValue) && !empty($paramValue);
        if($test){
            $complemento = self::typeErrorMsg($typeErroMsg);

            $msg = TFormDinMessage::ERROR_FD5_PARAM_MIGRA
                .' O parametro: '.$paramName
                .$complemento
                .', na classe: '.$class
                .', no metodo: '.$method
                .', na linha: '.$line
                ;
            self::triggerError($msg,$typeErro);
        }
    }
    //--------------------------------------------------------------------------------
    /**
     * Usado para fazer a validação de um metodo da migração do FormDin4 para o FormDin5
     *
     * @param const $typeErro ValidateHelper::NOTICIE, ValidateHelper::WARNING e ValidateHelper::ERROR
     * @param const $typeErroMsg
     * @param const $method 
     * @param string $complementoMsg
     * @param string $file
     * @param string $line
     */
    public static function validadeMethod($typeErro,$typeErroMsg,$method,$complementoMsg,$file,$line)
    {
        $complemento = self::typeErrorMsg($typeErroMsg);
        $complemento = !empty($complementoMsg)?$complemento.' '.$complementoMsg:$complemento;

        $msg = TFormDinMessage::ERROR_FD5_PARAM_MIGRA
            .' O metodo: '.$method
            .$complemento
            .', no arquivo: '.$file
            .', na linha: '.$line
            ;
        self::triggerError($msg,$typeErro);
    }    
    //--------------------------------------------------------------------------------
    public static function migrarMensage($mensagem,$typeErro,$typeErroMsg,$class,$method,$line,$arquivo=null)
    {
        $test = isset($mensagem) && !empty($mensagem);
        if($test){
            $complemento = self::typeErrorMsg($typeErroMsg);
            
            if(!empty($arquivo)){
                $arquivo = explode(DIRECTORY_SEPARATOR, $arquivo);
                $arquivo = ', no arquivo: '.end($arquivo);
            }
            $msg = TFormDinMessage::ERROR_FD5_PARAM_MIGRA
                .$complemento
                .': '.$mensagem
                .'. Classe: '.$class
                .', Metodo: '.$method
                .', na linha: '.$line
                .$arquivo
                ;
            self::triggerError($msg,$typeErro);
        }
    }
}
?>
