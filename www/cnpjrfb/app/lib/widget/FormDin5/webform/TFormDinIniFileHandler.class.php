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
 * Classe para pegar valores de Arquivo INI ou gravar valores em arquivo INI
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
class TFormDinIniFileHandler {
    private $filePath;
    private $iniData;

    public function __construct($filePath = null){
        if( !empty($filePath) ){
            $this->setfilePath($filePath);
        }
    }

    private function load() {
        if (!file_exists($this->filePath)) {
            throw new Exception("Arquivo INI não encontrado: " . $this->filePath);
        }

        $this->iniData = parse_ini_file($this->filePath, true);
        if ($this->iniData === false) {
            throw new Exception("Falha ao ler o arquivo INI: " . $this->filePath);
        }
    }

    public function setfilePath($filePath){
        $this->filePath = $filePath;
        $this->load();
    }
    public function getfilePath(){
        return $this->filePath;
    }

    /**
     * Recupera o valor de uma chave em uma seção
     *
     * @param string $section 01 - nome da seção
     * @param string $key     02 - nome da chave
     * @return string
     */
    public function getValue($section, $key) {
        if (!isset($this->iniData[$section])) {
            throw new Exception("Seção '$section' não encontrada no arquivo INI.");
        }
        if (!isset($this->iniData[$section][$key])) {
            throw new Exception("Chave '$key' não encontrada na seção '$section'.");
        }
        return $this->iniData[$section][$key];
    }

    /**
     * Recupera o valor boleano de uma chave em uma seção
     *
     * @param string $section 01 - nome da seção
     * @param string $key     02 - nome da chave
     * @return bolean
     */    
    public function getValueWithBolean($section, $key) {
        $valor = $this->getValue($section, $key);
        return $this->testBolean($valor);
    }

    public function setValue($section, $key, $value) {
        if (!isset($this->iniData[$section])) {
            $this->iniData[$section] = [];
        }
        $this->iniData[$section][$key] = $value;
    }

    public function save() {
        $newContent = '';
        foreach ($this->iniData as $section => $data) {
            $newContent .= "[$section]\n";
            foreach ($data as $key => $value) {
                $newContent .= "$key = \"$value\"\n";
            }
            $newContent .= "\n";
        }

        if (file_put_contents($this->filePath, $newContent) === false) {
            throw new Exception("Falha ao escrever no arquivo INI.");
        }
    }

    /**
     * Verifica se o valor do parametro do arquivo tem um valor boleano. Pode ser
     * 1 ou true ou sim ou S ou yes ou Y
     *
     * @param mix|string $valor
     * @return bolean
     */
    public static function testBolean($valor){
        $result = false;
        $valor = strtoupper($valor);
        if( $valor==1 || $valor==true || $valor=='TRUE' || $valor=='SIM' || $valor=='S' || $valor=='YES' || $valor=='Y' ){
            $result = true;
        }
        return $result;
    }
}
