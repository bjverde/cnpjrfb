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
class DateTimeHelper
{
    const DEFAULT_TIME_ZONE = 'America/Sao_Paulo';
    
    /**
     * @codeCoverageIgnore
     * Getter para criar uma instância de um objeto do tipo DateTime.
     *
     * @return DateTime
     */
    public static function getCurrentDateTime() 
    {
        $dateTime = new DateTime();
        $dateTime->setTimezone(new DateTimeZone(self::DEFAULT_TIME_ZONE));
        
        return $dateTime;
    }
    
    /**
     * @codeCoverageIgnore
     */
    public static function getNowFormat($format) 
    {
        $dateTime = self::getCurrentDateTime();
        $retorno = $dateTime->format($format);
        return $retorno;
    }
    
    /**
     *  @codeCoverageIgnore
     *  Retorn Data e hora no formato 'Y-m-d H:i:s'
     *
     * @return string 'Y-m-d H:i:s'
     */
    public static function getNow() 
    {
        $retorno = self::getNowFormat('Y-m-d H:i:s');
        return $retorno;
    }
    
    /**
     * @codeCoverageIgnore
     */    
    public static function getNowYYYYMMDD() 
    {
        $retorno = self::getNowFormat('Y-m-d');
        return $retorno;
    }    
    
    public static function mesExtenso($numeroMes) 
    {
        $numeroMes = intval($numeroMes);
        $meses = array(
             '1' => 'Janeiro'
            ,'2' => 'Fevereiro'
            ,'3' => 'Março'
            ,'4' => 'Abril'
            ,'5' => 'Maio'
            ,'6' => 'Junho'
            ,'7' => 'Julho'
            ,'8' => 'Agosto'
            ,'9' => 'Setembro'
            ,'01' => 'Janeiro'
            ,'02' => 'Fevereiro'
            ,'03' => 'Março'
            ,'04' => 'Abril'
            ,'05' => 'Maio'
            ,'06' => 'Junho'
            ,'07' => 'Julho'
            ,'08' => 'Agosto'
            ,'09' => 'Setembro'
            ,'10' => 'Outubro'
            ,'11' => 'Novembro'
            ,'12' => 'Dezembro'
        );
        return $meses[$numeroMes];
    }
    
    /**
     * Gera a data por extenso.
     *
     * @param  string $date YYYY-MM-DD
     * @return string
     */
    public static function getDateTimeLong($date) 
    {
        /*
        setlocale(LC_TIME, 'portuguese-brazilian','pt_BR', 'pt_BR.utf-8');
        date_default_timezone_set(self::DEFAULT_TIME_ZONE);
        $retorno = strftime('%d de %B de %Y', strtotime($date));
        $retorno = utf8_encode($retorno);
        */
        $pieces = explode('-', $date);
        $mes = self::mesExtenso($pieces[1]);
        $retorno = $pieces[2].' de '.strtolower($mes).' de '.$pieces[0];
        $retorno = StringHelper::strtoupper_utf8($retorno);
        return $retorno;
    }
    
    /**
     * Converter data no formato dd/mm/yyyy para yyyy-mm-dd.
     * Converter data no formato dd/mm/yyyy hh:mm para yyyy-mm-dd hh:mm:00
     * Converter data no formato dd/mm/yyyy hh:mm:ss para yyyy-mm-dd hh:mm:ss
     * 
     * Verifica se a data está no formato 'yyyy-mm-dd' ou 'yyyy-mm-dd hh:mm'
     * ou 'yyyy-mm-dd hh:mm:ss' ignora e retorna igual entrada.
     * 
     * Qualquer outro formato ou entrada devolve null
     *
     * @param  string $dateSql - String da data
     * @param boolean $showTime - saída com ou sem hora. Só coloca hora se entrada tiver hora
     * @return string
     */
    public static function date2Mysql($dateSql,$showTime=false)
    {
        $retorno = null;
        $dateSql = trim($dateSql);
        if($showTime){
            
            if( preg_match('/\d{4}-\d{2}-\d{2}/', $dateSql) ){
                if( preg_match('/\d{4}-\d{2}-\d{2}$/', $dateSql) ){
                    $retorno = $dateSql;
                }elseif( $showTime && preg_match('/\d{4}-\d{2}-\d{2}\s*\d{2}:\d{2}$/', $dateSql) ){
                    $retorno = $dateSql;
                }elseif( $showTime && preg_match('/\d{4}-\d{2}-\d{2}\s*\d{2}:\d{2}:\d{2}$/', $dateSql) ){
                    $retorno = $dateSql;
                }
            }elseif( preg_match('/\d{2}\/\d{2}\/\d{4}/', $dateSql) ){
                if( preg_match('/\d{2}\/\d{2}\/\d{4}$/', $dateSql) ){
                    $ano= substr($dateSql, 6);
                    $mes= substr($dateSql, 3, -5);
                    $dia= substr($dateSql, 0, -8);
                    $retorno = $ano."-".$mes."-".$dia;
                }else{
                    $dateSql = explode(' ', $dateSql);
                    $dateSqlDia  = $dateSql[0];
                    $dateSqlHora = $dateSql[1];
                    
                    $ano= substr($dateSqlDia, 6);
                    $mes= substr($dateSqlDia, 3, -5);
                    $dia= substr($dateSqlDia, 0, -8);
                    $dateSqlDia = $ano."-".$mes."-".$dia;

                    $retorno = $dateSqlDia.' '.$dateSqlHora;                    
                }
            }
        }else{
            $dateSql = explode(' ', $dateSql);
            $dateSql = $dateSql[0];
            
            if( preg_match('/\d{4}-\d{2}-\d{2}$/', $dateSql) ){
                $retorno = $dateSql;
            }elseif( preg_match('/\d{2}\/\d{2}\/\d{4}/', $dateSql) ){
                if(isset($dateSql) && ($dateSql<>'') ) {
                    $ano= substr($dateSql, 6);
                    $mes= substr($dateSql, 3, -5);
                    $dia= substr($dateSql, 0, -8);
                    $retorno = $ano."-".$mes."-".$dia;
                }
            }
        }
        return $retorno;
    }
    
    /**
     * Converter data no formato yyyy-mm-dd para dd/mm/yyyy
     * Converter data no formato yyyy-mm-dd hh:mm para dd/mm/yyyy hh:mm
     * Converter data no formato yyyy-mm-dd hh:mm:ss para dd/mm/yyyy hh:mm:ss
     * 
     * Verifica se a data está no formato 'dd/mm/yyyy'
     * 
     * Qualquer outro formato ou entrada devolve null
     *
     * @param  string $dateSql - String da data
     * @param boolean $showTheTime - saída com ou sem hora. Só coloca hora se entrada tiver hora
     * @param boolean $showSeconds - saída hora, minuto e segundo
     * @return string
     */     
    public static function DateIso2DateBr($dateSql,$showTheTime=false,$showSeconds=false)
    {
        $retorno = null;
        if( preg_match('/\d{4}-\d{2}-\d{2}$/', $dateSql) ){
            $dateTime = new DateTime($dateSql);
            $retorno = $dateTime->format('d/m/Y');
        }elseif( preg_match('/\d{4}-\d{2}-\d{2}\s*\d{2}:\d{2}/', $dateSql) ){
            $dateTime = new DateTime($dateSql);
            if($showSeconds == true){
                $retorno = $dateTime->format('d/m/Y H:i:s');
            }elseif($showTheTime == true){
                $retorno = $dateTime->format('d/m/Y H:i');
            }else{
                $retorno = $dateTime->format('d/m/Y');
            }             
        }elseif( preg_match('/\d{2}\/\d{2}\/\d{4}/', $dateSql) ){
            $retorno = $dateSql;
        }
        return $retorno;
    }

    /**
     * Converter uma data do tipo string para uma data do tipo DateTime. Formato: dd/mm/yyyy.
     *
     * @param string $date
     * @return DateTime 
    */
    public static function convertToDateTime($date)
    {
        $result = self::date2Mysql($date);
        return new DateTime($result);
    }

    /**
     * Verifica se uma data é um dia de sábado ou domingo. Se sim, retorna TRUE. Formato: dd/mm/yyyy.
     * @param string $date 
     * @return boolean
    */
    public static function isWeekend($date)
    {
        $result = self::convertToDateTime($date);
        return ($result->format('w') % 6) == 0;
    }

    /**
     * Verifica se a data é um dia útil. Se sim, retorna TRUE. Formato: dd/mm/yyyy.
     * @param string $date
     * @return boolean
    */
    public static function isWorkingDay($date)
    {
        $result = self::isWeekend($date);
        if( !$result ) {
            // TODO verificar se a data é feriado. Provisoriamente retorna FALSE.
            $result = FALSE;
        }
        return !$result;
    }

    /**
     * Obtém a quantidade de dias úteis entre duas datas.
     * @param DateTime $startDate
     * @param DateTime $endDate
     * @return int
    */
    public static function getWorkingDaysBetweenDates($startDate,$endDate)
    {
        $count = 0;
        while($startDate <= $endDate) {
            $result = self::isWorkingDay($startDate->format('d/m/Y'));
            if($result) {
                $count++; 
            }
            $startDate->add(new DateInterval('P1D'));
        }
        return $count;
    }

    /**
     * Convert MaskDate FormDin 4 para Adianti
     * @param string $strMaskDate
     * @return string
    */
    public static function maskDateFormDin4ToAdianit($strMaskDate)
    {
        $strMaskDate = StringHelper::strtolower_utf8($strMaskDate);
        switch ($strMaskDate) {
            case 'dmy':
                $strMaskDate = 'dd-mm-yyyy';
            break;
            case 'ymd':
                $strMaskDate = 'yyyy-mm-dd';
            break;
            case 'mdy':
                $strMaskDate = 'mm-dd-yyyy';
            break;
            case 'dm':
                $strMaskDate = 'dd-mm';
            break;
            case 'md':
                $strMaskDate = 'mm-dd';
            break;
            case 'my':
                $strMaskDate = 'mm-yyyy';
            break;
            case 'ym':
                $strMaskDate = 'yyyy-mm';
            break;
            case 'd':
                $strMaskDate = 'dd';
            break;
            case 'm':
                $strMaskDate = 'mm';
            break;
            case 'y':
                $strMaskDate = 'yyyy';
            break;
            case 'hm':
                $strMaskDate = 'hh:ii';
            break;
            case 'HMS':
                $strMaskDate = 'hh:ii:ss';
            break;            
          }
        return $strMaskDate;
    }
}
