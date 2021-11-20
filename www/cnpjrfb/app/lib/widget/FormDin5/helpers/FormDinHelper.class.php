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
 * Várias funções de usu do FormDin
 * ------------------------------------------------------------------------
 * Esse é o FormDin 5, que é uma reconstrução do FormDin 4 Sobre o Adianti 7
 * 
 * FormDin 5 - Alguns parametros estão marcados como DEPRECATED por não 
 * funcionar no Adianti foram mantidos para diminuir o impacto sobre
 * as migrações.
 *
 * FORMDIN5 = Parametro novo disponivel apenas na nova versão
 * NOT_IMPLEMENTED = parametros que serão implementados em versões futuras
 * ------------------------------------------------------------------------
 * 
 * @author Reinaldo A. Barrêto Junior
 */
class FormDinHelper
{

    const FORMDIN_VERSION = '5.0.0-alpha24';
    const GRID_SIMPLE = 'GRID_SIMPLE';
    const GRID_SCREEN_PAGINATION = 'GRID_SCREEN_PAGINATION';
    const GRID_SQL_PAGINATION    = 'GRID_SQL_PAGINATION';

    /**
     * Return FormDin version
     * @return string
     */
    public static function version()
    {
        return self::FORMDIN_VERSION;
    }
    /***
     * Returns if the current formDin version meets the minimum requirements
     * 
     * Retorna se a versão atual do formDin atende aos requisitos mínimos 
     * @param string $version
     * @return boolean
     */
    public static function versionMinimum($version)
    {
        $formVersion = explode("-", self::version());
        $formVersion = $formVersion[0];
        return version_compare($formVersion,$version,'>=');
    }

	/***
     * Sets the minimum formDin version for the system to work
	 * 
	 * Define a versão minima do formDin para o sistema funcionar
     * @param string $minimumVersion
     */
	public static function setFormDinMinimumVersion($minimumVersion) {		
		if ( empty($minimumVersion) ) {
		    throw new DomainException(TFormDinMessage::FORM_MIN_VERSION_BLANK);			
		} else {
		    $t = explode(".", $minimumVersion);
		    if( CountHelper::count($t) != 3 ){
		        throw new DomainException(TFormDinMessage::FORM_MIN_VERSION_INVALID_FORMAT);
			}
			$t = explode("-", $minimumVersion);
			$minimumVersion = $t[0];
			if( !FormDinHelper::versionMinimum($minimumVersion) ){
                $msg = TFormDinMessage::FORM_MIN_YOU_VERSION.self::version().TFormDinMessage::FORM_MIN_VERSION_NOT.$minimumVersion;
			    throw new DomainException($msg);
			}
		}
	}


    //--------------------------------------------------------------------------------
    /**
     * Recebe um array PHP que pode ser o corpo de um request e um objeto VO. Quando o id do array bodyRequest for
     * igual ao nome de um atributo no objeto Vo esse deverá ser setado.
     *
     * @param array $bodyRequest - Array TYPE_PHP = array no formato '[KEY_NAME]=Value,[KEY_NAME]=Value' 
     * @param object $vo - objeto VO para setar os valores
     * @return object
     */
    public static function setPropertyVo($bodyRequest,$vo)
    {
        //convert case do bodyRequest para Lower
        $arrayTmp = array();
        foreach ($bodyRequest as $key => $value) {
            $arrayTmp[strtolower($key)] = $value;
        }
        $bodyRequest = $arrayTmp;

        $class = new \ReflectionClass($vo);
        $properties   = $class->getProperties();        
        foreach ($properties as $attribut) {
            $name =  $attribut->getName();
            if (array_key_exists($name, $bodyRequest)) {
                $reflection = new \ReflectionProperty(get_class($vo), $name);
                $reflection->setAccessible(true);
                $reflection->setValue($vo, $bodyRequest[$name]);
                //echo $bodyRequest[$name];
            }
        }
        return $vo;
    }    
    //--------------------------------------------------------------------------------
    /***
     * Convert Object Vo to Array FormDin 
     * @param object $vo
     * @throws InvalidArgumentException
     * @return array
     */
    public static function convertVo2ArrayFormDin($vo)
    {
        $isObject = is_object( $vo );
        if( !$isObject ){
            throw new InvalidArgumentException('Not Object .class:'.__METHOD__);
        }
        $class = new \ReflectionClass($vo);
        $properties   = $class->getProperties();
        $arrayFormDin = array();
        foreach ($properties as $attribut) {
            $name =  $attribut->getName();
            $property = $class->getProperty($name);
            $property->setAccessible(true);
            $arrayFormDin[strtoupper($name)][0] = $property->getValue($vo);
        }
        return $arrayFormDin;
    }
    
    /**
     * @deprecated chante to ValidateHelper::methodLine
     * @param string $method
     * @param string $line
     * @param string $nameMethodValidate
     * @throws InvalidArgumentException
     */
    public static function validateMethodLine($method,$line,$nameMethodValidate)
    {
        ValidateHelper::methodLine($method, $line, $nameMethodValidate);
    }
    //--------------------------------------------------------------------------------
    /**
     *  @deprecated chante to ValidateHelper::objTypeTPDOConnectionObj
     * Validate Object Type is Instance Of TPDOConnectionObj
     *
     * @param object $tpdo instanceof TPDOConnectionObj
     * @param string $method __METHOD__
     * @param string $line __LINE__
     * @throws InvalidArgumentException
     * @return void
     */
    public static function validateObjTypeTPDOConnectionObj($tpdo,$method,$line)
    {
        ValidateHelper::objTypeTPDOConnectionObj($tpdo, $method, $line);
    }
    //--------------------------------------------------------------------------------
    /**
     * @deprecated chante to ValidateHelper::isNumeric
     * Validade ID is numeric and not empty
     * @param integer $id
     * @param string $method
     * @param string $line
     * @throws InvalidArgumentException
     * @return void
     */
    public static function validateIdIsNumeric($id,$method,$line)
    {
        ValidateHelper::isNumeric($id, $method, $line);
    }
    /***
     * 
     * @param mixed $variable
     * @param boolean $testZero
     * @return boolean
     */
    public static function issetOrNotZero($variable,$testZero=true)
    {
        $result = false;
        if( is_array($variable) ){
            if( !empty($variable) ) {
                $result = true;
            }
        }else{
            if(isset($variable) && !($variable==='') ) {
                if($testZero) {
                    if($variable<>'0' ) {
                        $result = true;
                    }
                }else{
                    $result = true;
                }
            }
        }
        return $result;
    }

    /**
     * @codeCoverageIgnore
     * Alias da função debug.
     * Para depuração. Exibe o modulo a linha e a variável/objeto solicitado
     * Retirado do FormDin 4.9.0
     * https://github.com/bjverde/formDin/blob/master/base/includes/funcoes.inc
     *
     * @param [type] $mixExpression váriavel que irá mostrar o resultado
     * @param string $strComentario Titulo que irá aparecer
     * @param boolean $boolExit
     * @return void
     */
    public static function d( $mixExpression,$strComentario='Debug', $boolExit=FALSE )
    {        
        return self::debug($mixExpression,$strComentario,$boolExit);
    }

    /**
     * @codeCoverageIgnore
     * função para depuração. Exibe o modulo a linha e a variável/objeto solicitado
     * Retirado do FormDin 4.9.0
     * https://github.com/bjverde/formDin/blob/master/base/includes/funcoes.inc
     *
     * @param [type] $mixExpression váriavel que irá mostrar o resultado
     * @param string $strComentario Titulo que irá aparecer
     * @param boolean $boolExit
     * @return void
     */
    public static function debug( $mixExpression,$strComentario='Debug', $boolExit=FALSE ) {
        ini_set("xdebug.var_display_max_children", -1);
        ini_set("xdebug.var_display_max_data", -1);
        ini_set("xdebug.var_display_max_depth", -1);
        //ini_set ( 'xdebug.max_nesting_level', 150 );
        if (defined('DEBUGAR') && !DEBUGAR){
            return;
        }
        $arrBacktrace = debug_backtrace();
        if( isset($_REQUEST['ajax']) && $_REQUEST['ajax'] ){
            echo '<div class="formDinDebug">';
            echo '<pre>';
            foreach ( $arrBacktrace[0] as $strAttribute => $mixValue ){
                if ( !is_array($mixValue) ){
                    echo $strAttribute .'='. $mixValue ."\n";
                }
            }
            echo "---------------\n";
            print_r( $mixExpression );
            echo '</pre>';
            echo '</div>';
        } else {
            echo '<hr />';
            echo '<div class="formDinDebug">';
            echo "<script>try{fwUnblockUI();}catch(e){try{top.app_unblockUI();}catch(e){}}</script>";
            echo "<fieldset style='text-align:left;'><legend><font color=\"#007000\">".$strComentario."</font></legend><pre>" ;
            echo "<b>file</b>: ".$arrBacktrace[0]['file']."\n";
            echo "<b>line</b>: ".$arrBacktrace[0]['line']."\n";
            /*
            foreach ( $arrBacktrace[0] as $strAttribute => $mixValue ) {
                if( !is_array($mixValue) ) {
                    echo "<b>" . $strAttribute . "</b> ". $mixValue ."\n";
                }
            }
            */
            echo "<br>";
            echo "<br>";
            if( is_object($mixExpression) ) {
                var_dump( $mixExpression );
            } else {
                print_r($mixExpression);
            }
            echo "</pre></fieldset>";
            echo '</div>';
            if ( $boolExit ) {
                echo "<br /><font color=\"#700000\" size=\"4\"><b>D I E</b></font>";
                exit();
            }
        }
    }

    /**
     * Recebe um valor e testa se pode ser usado como unidades CSS para width ou height
     * https://desenvolvimentoparaweb.com/css/unidades-css-rem-vh-vw-vmin-vmax-ex-ch/
     * @param string $value
     * @return void
     */
    public static function validateSizeWidthAndHeight($value,$enablePx=false)
    {
        $value = trim($value);
        if( !empty($value) ){
            if( $enablePx==true ){
                $regexWithPx = '/\d+(px|\%|em|rem|vh|vw)/';
                if(  !preg_match($regexWithPx, $value,$output) ){
                    throw new InvalidArgumentException('use px ou % ou em ou rem ou vh ou vw');
                }
            }else{
                $regex = '/\d+(\%|em|rem|vh|vw)/';
                if(  !preg_match($regex, $value,$output) ){
                    throw new InvalidArgumentException('use % ou em ou rem ou vh ou vw');
                }
            }
        }
    }

    public static function sizeWidthInPercent($value)
    {
        $value = trim($value);
        if( is_numeric($value) ){
            if( $value>=100 ){
                $value = '100%';
            }else{
                $value = $value.'%';
            }
        }
        FormDinHelper::validateSizeWidthAndHeight($value);
        return $value;
    }

}
