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
 * Classe para facilitar fazer requisições Rest
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
class TFormDinRest {

    /**
     * Cria uma Request com CURL
     *
     * @param string $url      01 - url que será feito o request
     * @param string $method   02 - POST = Valor Default, PUT, DELETE, GET
     * @param array  $params   03 - array de parametros, com chave e valor
     * @param string $filePath 04 - camiho do arquivo que serã enviado
     * @param string $userName 05 - usuário para basic Authorization
     * @param string $password 06 - senha para basic Authorization
     * @return string
     */
    public function request($url, $method = 'POST', $params = [], $filePath = null, $userName = null, $password = null)
    {
        $ch = curl_init();
        
        if ($method == 'POST' OR $method == 'PUT'){
            curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($params));
            curl_setopt($ch, CURLOPT_POST, true);
        } else if ( ($method == 'GET' OR $method == 'DELETE') AND !empty($params)) {
            $url .= '?'.http_build_query($params);
        }
        
        $defaults = array(
            CURLOPT_URL => $url, 
            CURLOPT_CUSTOMREQUEST => $method,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_SSL_VERIFYPEER => false,
            CURLOPT_CONNECTTIMEOUT => 10
        );

        if( !empty($filePath) ){
            curl_setopt($ch, CURLOPT_POSTFIELDS, ['file' => new CURLFile($filePath)]);
        }
        
        if (!empty($userName)){
            $defaults[CURLOPT_HTTPHEADER] = ['Authorization: Basic '.base64_encode($userName . ':' . $password)];
        }
        
        curl_setopt_array($ch, $defaults);
        $output = curl_exec ($ch);
        curl_close ($ch);
        return $output;
    }

    /**
     * Cria uma Request com CURL, esperando um json de retorno
     *
     * @param string $url      01 - url que será feito o request
     * @param string $method   02 - POST = Valor Default, PUT, DELETE, GET
     * @param array  $params   03 - array de parametros, com chave e valor
     * @param string $filePath 04 - camiho do arquivo que serã enviado
     * @param string $userName 05 - usuário para basic Authorization
     * @param string $password 06 - senha para basic Authorization
     * @return string
     */
    public function requestJson($url, $method = 'POST', $params = [], $filePath = null, $userName = null, $password = null)
    {
        $output = $this->request($url, $method, $params, $filePath, $userName, $password);
        $return = (array) json_decode($output);
        
        if (json_last_error() !== JSON_ERROR_NONE){
            throw new Exception('Return is not JSON. Check the URL: ' . $output);
        }
        return $return;
    }
}