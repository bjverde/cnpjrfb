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
class IpHelper
{

    /**
     * Verifica se IP está no internvavo informado. Retornando true ou false
     *
     * @param string $ip
     * @param string $intervalo_inicio
     * @param string $intervalo_fim
     * @return bollena
     */
    public static function ipDentroIntervalo($ip, $intervalo_inicio, $intervalo_fim) {
        $ip_num = ip2long($ip);
        $intervalo_inicio_num = ip2long($intervalo_inicio);
        $intervalo_fim_num = ip2long($intervalo_fim);        
        return ($ip_num >= $intervalo_inicio_num && $ip_num <= $intervalo_fim_num);
    }

    /**
     * Função para calcular o primeiro e o último endereço IP com base
     * notação CIDR (Classless Inter-Domain Routing) Exemplo "10.35.0.0/16"
     *
     * @param string $endereco_ip_cidr
     * @return array
     */
    public static function calculaPrimeiroUltimoIpCidr($endereco_ip_cidr) {
        // Divide o endereço IP e a máscara de rede
        list($endereco_ip, $mascara_cidr) = explode('/', $endereco_ip_cidr);

        // Calcula a máscara de rede a partir do prefixo CIDR
        $mascara_binaria = ~((1 << (32 - $mascara_cidr)) - 1);
        
        // Converte o endereço IP para formato numérico
        $endereco_ip_num = ip2long($endereco_ip);

        // Calcula o endereço de rede aplicando a máscara
        $endereco_rede_num = $endereco_ip_num & $mascara_binaria;

        // Calcula o número máximo de hosts na rede (excluindo o endereço de rede e o de broadcast)
        $numero_max_hosts = ~$mascara_binaria & 0xffffffff;

        // Calcula o primeiro endereço IP adicionando 1 ao endereço de rede
        $primeiro_endereco_ip_num = $endereco_rede_num + 1;

        // Calcula o último endereço IP adicionando o número máximo de hosts ao endereço de rede e subtraindo 1
        $ultimo_endereco_ip_num = $endereco_rede_num + $numero_max_hosts - 1;

        // Converte os endereços IP de volta para formato de string
        $primeiro_endereco_ip = long2ip($primeiro_endereco_ip_num);
        $ultimo_endereco_ip = long2ip($ultimo_endereco_ip_num);

        return array($primeiro_endereco_ip, $ultimo_endereco_ip);
    }
}