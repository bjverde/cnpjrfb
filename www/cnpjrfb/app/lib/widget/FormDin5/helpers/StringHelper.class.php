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
 * Classe que faz varias transformações de data e hora
 *
 * @author reinaldo.junior
 */
class StringHelper
{
    
    public static function strtolower_utf8($string) 
    {
        //$string = utf8_decode($string);
        //$string = strtolower($string);
        //$string = utf8_encode($string);
        $string = mb_strtolower($string, 'UTF-8');
        return $string;
    }
    
    public static function strtoupper_utf8($string)
    {
        //$string = utf8_decode($string);
        //$string = strtoupper($string);
        //$string = utf8_encode($string);
        $string = mb_strtoupper($string, 'UTF-8');
        return $string;
    }
    
    /***
     * Checks if text is different from UTF8 and converts to UTF-8
     * @param string $string
     * @return string
     */
    public static function str2utf8($string)
    {
        if ( mb_detect_encoding($string, 'UTF-8', true)!='UTF-8' ){
            //$string= iconv('ISO-8859-1', 'UTF-8', $string);
            $string = utf8_encode($string);
            //$string = mb_convert_encoding($string, 'UTF-8');
        }
        return $string;
    }

    /**
     * Converte uma string para outro encoding usando MB 
     *
     * @param string $string        01 - string que será convertida
     * @param string $to_encoding   02 - enconding de destino
     * @param string $from_encoding 03 - enconding de origem
     * @return string
     */
    public static function convert_encoding($string,$to_encoding='UTF-8',$from_encoding='ASCII')
    {
        if ( mb_detect_encoding($string, $to_encoding, true)!=$to_encoding ){
            $string = mb_convert_encoding($string,$to_encoding,$from_encoding);
        }
        return $string;
    }

    /**
     * Recebe uma string e formata com CPF ou CNPJ
     * https://gist.github.com/davidalves1/3c98ef866bad4aba3987e7671e404c1e
     * @param string $value
     * @return string
     */
    public static function formatCnpjCpf($value) 
    {
        $cnpj_cpf = self::limpaCnpjCpf($value);
        if (strlen($cnpj_cpf) === 11) {
            $value = preg_replace("/(\d{3})(\d{3})(\d{3})(\d{2})/", "\$1.\$2.\$3-\$4", $cnpj_cpf);
        } else if(strlen($cnpj_cpf) === 14){
            $value = preg_replace("/(\d{2})(\d{3})(\d{3})(\d{4})(\d{2})/", "\$1.\$2.\$3/\$4-\$5", $cnpj_cpf);
        }
        return $value;
    }

    /**
     * Recebe uma string e deixar apenas os números sem formatação
     *
     * @param string $value
     * @return string
     */
    public static function limpaCnpjCpf($value) 
    {
        $limpo = preg_replace("/\D/", '', $value);
        return $limpo;
    }

    /**
     * Recebe uma string e formata o numero telefone em dos 4 formatos, conforme o tamanho da string
     * (61) 91234-5678
     * (61) 1234-5678
     * 91234-5678
     * 1234-5678
     * @param string $value
     * @return string|int
     */
    public static function formatPhoneNumber($value) 
    {
        $cnpj_cpf = self::limpaCnpjCpf($value);
        if (strlen($cnpj_cpf) === 11) {
            $value = preg_replace("/(\d{2})(\d{5})(\d{4})/", "(\$1) \$2-\$3", $cnpj_cpf);
        } else if(strlen($cnpj_cpf) === 10){
            $value = preg_replace("/(\d{2})(\d{4})(\d{4})/", "(\$1) \$2-\$3", $cnpj_cpf);
        } else if(strlen($cnpj_cpf) === 9){
            $value = preg_replace("/(\d{5})(\d{4})/", "\$1-\$2", $cnpj_cpf);
        } else if(strlen($cnpj_cpf) === 8){
            $value = preg_replace("/(\d{4})(\d{4})/", "\$1-\$2", $cnpj_cpf);
        }
        return $value;
    }

    /**
     * Recebe um string e verfica se está no formato de número brasileiro
     * 999.999.999,00000 ou 999999999,00000
     * 
     * @param string|int|float|null $value
     * @return boolean
     */    
    public static function is_numeroBrasil($value)
    {
        if( empty($value) ){
            return false;
        }
        //Retira números no formato 12,00
        $naoNumero = preg_match('/^([0-9]*)([\.]{1})(\d{1,2})$/', $value, $output_array);
        if($naoNumero===1){
            return false;
        }
        //Retira números no formato 12,1234
        $naoNumero = preg_match('/^([0-9]*)(...)([\.]{1})(\d{4,})$/', $value, $output_array);
        if($naoNumero===1){
            return false;
        }
        $numero= preg_match('/^([0-9\.]*)(,?)(\d*)$/', $value, $output_array);
        $result= ($numero===1)?true:false;
        return $result;
    }

    /**
     * Recebe um string e verfica se está no formato de número americano
     * 999,999,999.00000 ou 999999999.00000
     * 
     * @param string|int|float $value
     * @return boolean
     */
    public static function is_numeroEua($value)
    {
        if( empty($value) ){
            return false;
        }
        //Retira números no formato 12,00
        $naoNumero = preg_match('/^([0-9]*)([\,]{1})(\d{1,2})$/', $value, $output_array);
        if($naoNumero===1){
            return false;
        }
        //Retira números no formato 12,1234
        $naoNumero = preg_match('/^([0-9]*)(...)([\,]{1})(\d{4,})$/', $value, $output_array);
        if($naoNumero===1){
            return false;
        }
        $numero= preg_match('/^([0-9,]*)(\.?)(\d*)$/', $value, $output_array);
        $result= ($numero===1)?true:false;
        return $result;
    }

    /**
     * Recebe uma string com numero formato brasil ou eua e devolver no formato Brasil
     * Qualquer outro formato vai retorna null
     *
     * @param numeric|string $value  valor que deve ser convertido
     * @param integer $decimals numero de casas decimais
     * @return string|null
     */
    public static function numeroBrasil($value,$decimals=2)
    {
        if(is_numeric($value)){
            $value=number_format($value, $decimals,',','.');
        }else if( is_string($value) && self::is_numeroBrasil($value) ){
            if ( (strlen($value)==5) && str_contains($value,',') ) {
                return $value;
            }else if( (strlen($value)==5) && str_contains($value,'.') ) {
                $value=str_replace('.',',', $value);
                return $value;
            }else{
                $search =array('.',',');
                $replace=array('', '.');
                $value=str_replace($search, $replace, $value);
                $value=number_format($value, $decimals,',','.');
                return $value;
            }
        }else if( is_string($value) && self::is_numeroEua($value) ){
            $value=str_replace(',','', $value);
            $value=number_format($value, $decimals,',','.');
        }else{
            $value = null;
        }
        return $value;
    }

    /**
     * Recebe uma string com numero formato EUA ou Brasil e devolver no formato EUA.
     * Qualquer outro formato vai retorna null
     *
     * @param numeric|string $value  valor que deve ser convertido
     * @param integer $decimals numero de casas decimais
     * @return string|null
     */    
    public static function numeroEua($value,$decimals=2)
    {
        if(is_numeric($value)){
            $value=number_format($value, $decimals,'.',',');
        }else if( is_string($value) && self::is_numeroEua($value) ){
            if ( (strlen($value)==5) && str_contains($value,'.') ) {
                return $value;
            }else if( (strlen($value)==5) && str_contains($value,',') ) {
                $value=str_replace(',','.', $value);
                return $value;
            }else{
                $value=str_replace(',','', $value);
                $value=number_format($value, $decimals,'.',',');
                return $value;
            }
        }else if( is_string($value) && self::is_numeroBrasil($value) ){
            $search =array('.',',');
            $replace=array('', '.');
            $value=str_replace($search, $replace, $value);
            $value=number_format($value, $decimals,'.',',');
        }else{
            $value = null;
        }
        return $value;
    }
    
    /**
     * Recebe uma string do tipo "olá à mim! ñ" e retona "ola a mim! n"
     * https://pt.stackoverflow.com/questions/49645/remover-acentos-de-uma-string-em-phps
     * https://pt.stackoverflow.com/questions/858/refatora%c3%a7%c3%a3o-de-fun%c3%a7%c3%a3o-para-remover-pontua%c3%a7%c3%a3o-espa%c3%a7os-e-caracteres-especiais
     * https://stackoverflow.com/questions/13614622/transliterate-any-convertible-utf8-char-into-ascii-equivalent
     * @param string $string
     * @return string
     */
    public static function tirarAcentos($string) 
    {
        $string = iconv('UTF-8', 'ASCII//TRANSLIT//IGNORE', $string);
        $string = self::removeCaracteresEspeciais($string);
        return $string;
    }

    /**
     * Recebe uma string remove tudo que não faz partes das palavras Brasileira
     * e espaços em branco
     * @param string $string
     * @return string
     */
    public static function removeCaracteresEspeciais($string) 
    {
        $string = preg_replace('/[^a-zA-Z0-9\sÀÁÃÂÉÊÍÓÕÔÚÜÇÑàáãâéêíóõôúüçñ]/', '', $string);        
        return $string;
    }

    /**
     * Recebe uma string remove espaços em branco
     * @param string $string
     * @return string
     */
    public static function removeEspacoBranco($string) 
    {
        $string = preg_replace('/[\s]/', '', $string);
        return $string;
    }

    /**
     * Recebe uma string "minha string"e converte para o formato PascalCase
     * "MinhaString"
     * https://medium.com/better-programming/string-case-styles-camel-pascal-snake-and-kebab-case-981407998841
     *
     * @param string $string
     * @return string
     */
    public static function string2PascalCase($string) 
    {
        $string = self::tirarAcentos($string);
        $string = mb_convert_case ( $string, MB_CASE_TITLE );
        $string = self::removeEspacoBranco($string);
        return $string;
    }

    /**
     * Recebe uma string "minha string"e converte para o formato PascalCase
     * "MinhaString"
     * https://medium.com/better-programming/string-case-styles-camel-pascal-snake-and-kebab-case-981407998841
     *
     * @param string $string
     * @param string $separator 
     * @return string
     */
    public static function string2PascalCaseWithSeparator($string,$separator) 
    {
        $listSeparator = array('-','_',';',',');
        if (!in_array($separator, $listSeparator)) {
            throw new InvalidArgumentException('Use um separador valido: - _ ; ,');
        }
        $separator = '/'.$separator.'/';
        $string = preg_replace($separator, ' ', $string);
        $string = self::string2PascalCase($string);
        return $string;
    }

    /**
     * Recebe uma string "minha string"e converte para o formato CamelCase
     * "minhaString"
     * https://medium.com/better-programming/string-case-styles-camel-pascal-snake-and-kebab-case-981407998841
     *
     * @param string $string
     * @return string
     */
    public static function string2CamelCase($string) 
    {
        $string = self::string2PascalCase($string);
        $string = lcfirst($string);
        return $string;
    }

    /**
     * Recebe uma string "minha string"e converte para o formato KebabCase
     * "minha-string"
     * https://medium.com/better-programming/string-case-styles-camel-pascal-snake-and-kebab-case-981407998841
     *
     * @param string $string
     * @return string
     */
    public static function string2KebabCase($string) 
    {
        $string = self::removeCaracteresEspeciais($string);
        $string = self::tirarAcentos($string);
        $string = mb_convert_case ( $string,  MB_CASE_LOWER );
        $string = preg_replace('/[\s]/', '-', $string);
        return $string;
    }

    /**
     * Recebe uma string "minha string"e converte para o formato SnakeCase
     * "minha_string"
     * https://medium.com/better-programming/string-case-styles-camel-pascal-snake-and-kebab-case-981407998841
     *
     * @param string $string
     * @return string
     */
    public static function string2SnakeCase($string) 
    {
        $string = self::string2KebabCase($string);
        $string = preg_replace('/[-]/', '_', $string);
        return $string;
    }
}
