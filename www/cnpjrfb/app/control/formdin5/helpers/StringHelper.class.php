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

/**
 * Classe que faz varias transformações de data e hora
 *
 * @author reinaldo.junior
 */
class StringHelper
{
    
    public static function strtolower_utf8($inputString) 
    {
        $outputString    = utf8_decode($inputString);
        $outputString    = strtolower($outputString);
        $outputString    = utf8_encode($outputString);
        return $outputString;
    }
    
    public static function strtoupper_utf8($string)
    {
        $string = utf8_decode($string);
        $string = strtoupper($string);
        $string = utf8_encode($string);
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
     * Recebe uma string e formata com CPF ou CNPJ
     * https://gist.github.com/davidalves1/3c98ef866bad4aba3987e7671e404c1e
     * @param string $value
     * @return string
     */
    public static function formatCnpjCpf($value) 
    {
        $cnpj_cpf = preg_replace("/\D/", '', $value);
        if (strlen($cnpj_cpf) === 11) {
            $value = preg_replace("/(\d{3})(\d{3})(\d{3})(\d{2})/", "\$1.\$2.\$3-\$4", $cnpj_cpf);
        } else if(strlen($cnpj_cpf) === 14){
            $value = preg_replace("/(\d{2})(\d{3})(\d{3})(\d{4})(\d{2})/", "\$1.\$2.\$3/\$4-\$5", $cnpj_cpf);
        }
        return $value;
    }

    public static function limpaCnpjCpf($value) 
    {
        $limpo = preg_replace("/\D/", '', $value);
        return $limpo;
    }
    
}
