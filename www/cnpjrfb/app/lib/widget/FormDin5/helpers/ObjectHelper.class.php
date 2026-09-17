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

class ObjectHelper
{
    /**
     * Transfere um atributo de um objeto para outro de forma segura.
     *
     * @param object $origem O objeto do qual o valor será lido.
     * @param object $destino O objeto no qual o valor será escrito.
     * @param string $nomeAtributo O nome do atributo a ser transferido.
     * @return object Retorna o objeto de destino modificado.
     */
    public static function transferirAtributoSeExistir(object $origem, object $destino, string $nomeAtributo): object
    {
        // A função isset() já verifica em uma só etapa se:
        // 1. A propriedade existe no objeto $origem.
        // 2. O seu valor não é nulo.
        // A função property_exists() adiciona uma camada de segurança para garantir
        // que não estamos criando propriedades inesperadas no objeto $destino.
        if (isset($origem->{$nomeAtributo}) && property_exists($destino, $nomeAtributo)) {
            $destino->{$nomeAtributo} = $origem->{$nomeAtributo};
        }

        return $destino;
    }

    /**
     * Transfere uma lista de atributos de um objeto de origem para um de destino.
     *
     * @param object $origem
     * @param object $destino
     * @param array $nomesAtributos
     * @return object
     */
    public static function transferirAtributos(object $origem, object $destino, array $nomesAtributos): object
    {
        foreach ($nomesAtributos as $nomeAtributo) {
            self::transferirAtributoSeExistir($origem, $destino, $nomeAtributo);
        }
        return $destino;
    }
}