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

class ServerHelper
{

    const DEVICE_TYPE_MOBILE  = 'mobile';
    const DEVICE_TYPE_TABLE   = 'tablet';
    const DEVICE_TYPE_DESKTOP = 'desktop';

    /**
     * @codeCoverageIgnore
     */     
    public static function get($atributeName) 
    {
        $result = ArrayHelper::get($_SERVER,$atributeName);
        return $result;
    }    
    
    /**
     * https://stackoverflow.com/questions/6768793/get-the-full-url-in-php
     *
     * @param  boolean $trim_query_string
     * @return string|mixed
     * @codeCoverageIgnore
     */
    public static function getCurrentUrl( $trim_query_string = false ) 
    {
        $pageURL = ( self::isHTTPS(true,true) ) ? "https://" : "http://";
        $pageURL = $pageURL.$_SERVER["SERVER_NAME"];
        $pageURL = $pageURL.( ( $_SERVER["SERVER_PORT"] != 80 ) ? ":".$_SERVER["SERVER_PORT"] : "") ;
        $pageURL = $pageURL.$_SERVER["REQUEST_URI"];
        if(! $trim_query_string ) {
            return $pageURL;
        } else {
            $url = explode('?', $pageURL);
            return $url[0];
        }
    }

    public static function getQueryString() 
    {
        $pageURL = self::getCurrentUrl(true);
        $url = explode('?', $pageURL);
        return $url[0];
    }

    public static function getHomeUrl() 
    {
        $pageURL = self::getCurrentUrl();
        $url = explode('engine.php', $pageURL);
        $url =  $url[0];
        $url = explode('index.php', $url);
        return $url[0];
    }

    /**
     * Return string with IP client
     * https://pt.stackoverflow.com/questions/179389/como-pegar-ip-de-um-usuario-usando-php/179455
     *
     * @return string
     */
    public static function getClientIP()
    {
        $client = ArrayHelper::get($_SERVER,'HTTP_CLIENT_IP');
        $forward= ArrayHelper::get($_SERVER,'HTTP_X_FORWARDED_FOR');
        $remote = ArrayHelper::get($_SERVER,'REMOTE_ADDR');
        if ( filter_var($client, FILTER_VALIDATE_IP) ){
            $ip = $client;
        } elseif ( filter_var($forward, FILTER_VALIDATE_IP) ){
            $ip = $forward;
        } else {
            $ip = $remote;
        }
        return $ip;
    }

    /**
     * Return string with IP client
     * https://pt.stackoverflow.com/questions/179389/como-pegar-ip-de-um-usuario-usando-php/179455
     *
     * @return string
     */
    public static function getClientIPStringAll()
    {
        $client = ArrayHelper::get($_SERVER,'HTTP_CLIENT_IP');
        $forward= ArrayHelper::get($_SERVER,'HTTP_X_FORWARDED_FOR');
        $remote = ArrayHelper::get($_SERVER,'REMOTE_ADDR');
        $clientIPStringAll = 'cliente: '.$client.',remote: '.$remote.',forwarded: '.$forward;
        return $clientIPStringAll;
    }

    public static function getClientAgent(){
        $agent = ArrayHelper::get($_SERVER,'HTTP_USER_AGENT');
        return $agent; 
    }     

    /**
     * Return string with Device Type of client
     * https://code-boxx.com/detect-mobile-desktop-in-php/
     *
     * @return string
     */
    public static function getClientDeviceType()
    {
        $agent = strtolower(self::getClientAgent());
        $type = null;
        if (is_numeric(strpos($agent, self::DEVICE_TYPE_MOBILE))) {
            $type = self::DEVICE_TYPE_MOBILE;
        } else if( is_numeric(strpos($agent, self::DEVICE_TYPE_TABLE)) ) {
            $type = self::DEVICE_TYPE_TABLE;
        }else{
            $type = self::DEVICE_TYPE_DESKTOP;
        }
        return $type;
    }

    /**
     * Verifica se a conexão é HTTPS
     *
     * @param bool $testFrontEnd
     * @param bool $testPort
     * @return bool
     */
    public static function isHTTPS(bool $testFrontEnd = false, bool $testPort = false): bool {
        // 1. Verificação padrão para $_SERVER['HTTPS'].
        // É o método mais comum e confiável em configurações de servidor simples.
        if (isset($_SERVER['HTTPS']) && strtolower($_SERVER['HTTPS']) !== 'off') {
            return true;
        }

        // 2. Verificação para cabeçalho X-Forwarded-Proto, comum em proxies.
        // Essencial para ambientes de nuvem e balanceadores de carga.
        if (isset($_SERVER['HTTP_X_FORWARDED_PROTO']) && $_SERVER['HTTP_X_FORWARDED_PROTO'] === 'https') {
            return true;
        }

        if($testFrontEnd==true){
            // 3. Verificação alternativa para outros tipos de cabeçalhos de proxy.
            $front = ArrayHelper::get($_SERVER,'HTTP_FRONT_END_HTTPS');
            if (isset($front) && strtolower($front) !== 'off') {
                return true;
            }
        }
        
        if($testPort==true){
            // 4. Fallback: verificar a porta do servidor. A porta 443 é a padrão para HTTPS.
            // É menos comum, mas pode ser um último recurso útil.
            $port = ArrayHelper::get($_SERVER,'SERVER_PORT');
            if (isset($port) && (int)$port === 443) {
                return true;
            }
        }
        return false;
    }
}
?>
