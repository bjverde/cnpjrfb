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
 * Classe para validações de Geolocação no BackEnd
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
class TFormDinGeo {

    private $vertices = array();

    public function __construct(){
        $this->zerarVertice();
    }
    public function zerarVertice(){
        $this->vertices = array();
    }
    public function setVerticeDf(){
        $this->setVertice(-15.7801,-47.9292);// ponto 1
        $this->setVertice(-15.7801,-47.8292);// ponto 2
        $this->setVertice(-15.8801,-47.8292);// ponto 3
        $this->setVertice(-15.8801,-47.9292);// ponto 4
    }
    public function getVertice(){
        return $this->vertices;
    }
    /**
     * Seta um ponto do quadrilatero. Use a função 4x
     *
     * @param integer $latitude
     * @param integer $longitude
     */
    public function setVertice($latitude,$longitude){
        $lastKey = array_key_last($this->vertices);
        $nextKey = 0;
        if($lastKey >= 1){
            $nextKey = $lastKey+1;
        }
        $this->vertices[$nextKey]['latitude'] =$latitude;
        $this->vertices[$nextKey]['longitude']=$longitude;
    }
    public function isPointInQuadrilateral($latitude,$longitude) {
        $qtd = CountHelper::count($this->vertices);
        if($qtd==0){
            throw new InvalidArgumentException('Vertice zerado, use o metodo setVertice 4x para definir');
        }elseif($qtd < 4){
            throw new InvalidArgumentException('Quadrilatero não está completo! Faltam '.strval(4-$qtd).' vertices');
        }elseif($qtd > 4){
            throw new InvalidArgumentException('Quadrilatero com erro tem mais de 4 pontos');
        }

        $vertices = $this->vertices;
        $x = $latitude;
        $y = $longitude;
        
        $inside = false;
        $n = count($vertices);
        
        for ($i = 0, $j = $n - 1; $i < $n; $j = $i++) {
            $xi = $vertices[$i]['longitude'];
            $yi = $vertices[$i]['latitude'];
            $xj = $vertices[$j]['longitude'];
            $yj = $vertices[$j]['latitude'];
            
            $intersect = (($yi > $y) != ($yj > $y)) && 
                         ($x < ($xj - $xi) * ($y - $yi) / ($yj - $yi) + $xi);
            
            if ($intersect) {
                $inside = !$inside;
            }
        }        
        return $inside;
    }

    /**
     * Verifica se o ponto informado está dentro do raio e ponto de referencia
     *
     * @param float $ref_latitude
     * @param float $ref_longitude
     * @param int   $raio
     * @param float $latitude
     * @param float $longitude
     * @return boolean
     */
    public function isPointWithinRadius($ref_latitude, $ref_longitude, $raio, $latitude, $longitude) {
        // Converte os graus para radianos
        $ref_latitude_rad = deg2rad($ref_latitude);
        $ref_longitude_rad = deg2rad($ref_longitude);
        $latitude_rad = deg2rad($latitude);
        $longitude_rad = deg2rad($longitude);
        
        // Raio da Terra em metros
        $earth_radius = 6371000;
        
        // Diferença entre as coordenadas
        $diff_lat = $latitude_rad - $ref_latitude_rad;
        $diff_lon = $longitude_rad - $ref_longitude_rad;
        
        // Fórmula de Haversine
        $a = sin($diff_lat / 2) * sin($diff_lat / 2) + 
             cos($ref_latitude_rad) * cos($latitude_rad) * 
             sin($diff_lon / 2) * sin($diff_lon / 2);
        $c = 2 * atan2(sqrt($a), sqrt(1 - $a));
        
        // Distância entre os pontos
        $distance = $earth_radius * $c;
        
        // Retorna true se a distância estiver dentro do raio, caso contrário false
        return $distance <= $raio;
    }
}