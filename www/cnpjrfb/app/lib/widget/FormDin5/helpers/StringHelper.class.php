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
    
    /**
     * Converte qualquer string para UTF-8
     * independente do encoding de origem.
     *
     * @param string $string
     * @return string
     */
    public static function str2utf8(string $string): string
    {
        if ($string === '') {
            return $string; // string vazia, nada a fazer
        }

        // Lista de encodings comuns que podem aparecer
        $encodings = [
            'UTF-8',
            'ISO-8859-1',
            'ISO-8859-15',
            'Windows-1252',
            'ASCII'
        ];

        // Detecta encoding de origem
        $detected = mb_detect_encoding($string, $encodings, true);

        // @codeCoverageIgnoreStart
        if ($detected === false) {
            // fallback: força interpretação como ISO-8859-1
            $detected = 'ISO-8859-1';
        }
        // @codeCoverageIgnoreEnd

        // Converte para UTF-8 só se não estiver já em UTF-8
        if ($detected !== 'UTF-8') {
            $string = mb_convert_encoding($string, 'UTF-8', $detected);
        } else {
            // garante que não exista "UTF-8 sujo"
            $string = mb_convert_encoding($string, 'UTF-8', 'UTF-8');
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
     * Se string não respeitar o formato do CPF ou CNPJ vai devolver a mesma string
     * https://gist.github.com/davidalves1/3c98ef866bad4aba3987e7671e404c1e
     * Em 2026 foi criado um novo tipo de CNPJ que pode conter letras
     * os dois ultimos digitos do CNPJ devem ser numeros
     * @param string $value
     * @return string
     */
    public static function formatCnpjCpf($value) 
    {
        $cnpj_cpf = self::limpaCnpjNovo($value);
        if (strlen($cnpj_cpf) === 11) {
            $value = preg_replace("/(\d{3})(\d{3})(\d{3})(\d{2})/", "\$1.\$2.\$3-\$4", $cnpj_cpf);
        } else if(strlen($cnpj_cpf) === 14 && is_numeric(substr($cnpj_cpf, -2))){
            $value = preg_replace("/([A-Z0-9]{2})([A-Z0-9]{3})([A-Z0-9]{3})([A-Z0-9]{4})(\d{2})/", "\$1.\$2.\$3/\$4-\$5", $cnpj_cpf);
        }
        return $value;
    }

    /**
     * Recebe uma string e deixar apenas os números sem formatação
     * Para CNPJ use a função limpaCnpjNovo()
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
     * Recebe uma string e verifica se é um CPF válido
     * 
     * @param string $value
     * @return boolean
     */
	public static function validarCpf($value){
		$dv 		= false;
		$cpf 		= self::limpaCnpjCpf($value);
		if($cpf=='') {
			return false;
		}

		$cpf_dv 	= substr($cpf,-2);
		$controle 	= '';
		// evitar sequencias de número. Ex:11111111111
		for ( $i = 0; $i < 10; $i++ ) {
			if( $cpf == str_repeat($i,11)){
				$cpf_dv = '99'; // causar erro de validação
				break;
			}
		}

        $digito = null;
		for ( $i = 0; $i < 2; $i++ ) {
			$soma = 0;
			for ( $j = 0; $j < 9; $j++ )
			$soma += substr($cpf,$j,1)*(10+$i-$j);
			if ( $i == 1 ) $soma += $digito * 2;
			$digito = ($soma * 10) % 11;
			if ( $digito == 10 ) $digito = 0;
			$controle .= $digito;
		}

		if ( $controle != $cpf_dv ){
		    return false;
		}
		return true;
	}

    /**
     * Recebe uma string limpar tudo que não seja número ou letra
     * para o novo tipo de CNPJ que inicia em 2026     
     *
     * @param string $value
     * @return string
     */
    public static function limpaCnpjNovo($value) 
    {
        $value = self::strtoupper_utf8($value); // garante que seja maiúsculo
        $limpo = preg_replace("/[^A-Z0-9]/", '', $value);
        return $limpo;
    }

    /**
     * Valida um CNPJ (Numérico ou Alfanumérico) seguindo o novo padrão da Receita Federal.
     * * @param string $value O CNPJ a ser validado (formatado ou não)
     * @return bool True se for válido, false caso contrário
     */
    public static function validarCnpj(string $value): bool
    {
        // 1. Remove caracteres não alfanuméricos e converte para maiúsculo
        $cnpj = self::limpaCnpjNovo($value);

        // 2. Verifica se possui o tamanho correto [cite: 61]
        if (strlen($cnpj) !== 14) {
            return false;
        }

        // 3. Evita sequências de números iguais conhecidas (ex: 11111111111111)
        if (preg_match('/^(\d)\1{13}$/', $cnpj)) {
            return false;
        }

        // Pesos para os cálculos dos dígitos verificadores [cite: 73, 86]
        $pesos1 = [5, 4, 3, 2, 9, 8, 7, 6, 5, 4, 3, 2];
        $pesos2 = [6, 5, 4, 3, 2, 9, 8, 7, 6, 5, 4, 3, 2];

        // Validação do Primeiro Dígito (DV1)
        $soma1 = 0;
        for ($i = 0; $i < 12; $i++) {
            // Converte caractere para valor (ASCII - 48) 
            $valor = ord($cnpj[$i]) - 48;
            $soma1 += $valor * $pesos1[$i];
        }
        $resto1 = $soma1 % 11;
        $dv1Expected = ($resto1 < 2) ? 0 : 11 - $resto1; // [cite: 78, 79]

        if ((int)$cnpj[12] !== $dv1Expected) {
            return false;
        }

        // Validação do Segundo Dígito (DV2)
        $soma2 = 0;
        for ($i = 0; $i < 13; $i++) {
            $valor = ord($cnpj[$i]) - 48;
            $soma2 += $valor * $pesos2[$i];
        }
        $resto2 = $soma2 % 11;
        $dv2Expected = ($resto2 < 2) ? 0 : 11 - $resto2; // [cite: 88, 89]

        return (int)$cnpj[13] === $dv2Expected;
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
        $naoNumero = preg_match('/^-?([0-9]*)([\.]{1})(\d{1,2})$/', $value, $output_array);
        if($naoNumero===1){
            return false;
        }
        //Retira números no formato 12,1234
        $naoNumero = preg_match('/^-?([0-9]*)(...)([\.]{1})(\d{4,})$/', $value, $output_array);
        if($naoNumero===1){
            return false;
        }
        $numero= preg_match('/^-?([0-9\.]*)(,?)(\d*)$/', $value, $output_array);
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
        $naoNumero = preg_match('/^-?([0-9]*)([\,]{1})(\d{1,2})$/', $value, $output_array);
        if($naoNumero===1){
            return false;
        }
        //Retira números no formato 12,1234
        $naoNumero = preg_match('/^-?([0-9]*)(...)([\,]{1})(\d{4,})$/', $value, $output_array);
        if($naoNumero===1){
            return false;
        }
        $numero= preg_match('/^-?([0-9,]*)(\.?)(\d*)$/', $value, $output_array);
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