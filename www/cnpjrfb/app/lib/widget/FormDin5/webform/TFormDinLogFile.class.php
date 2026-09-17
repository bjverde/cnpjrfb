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
 * Classe para facilitar a criação de arquivos de log
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
class TFormDinLogFile {

    protected $filePath;

    public function __construct($filePath = null)
    {
        if( !empty($filePath) ){
            $this->setFilePath($filePath);
        }
    }
    public function getFilePath(){
        return $this->filePath;
    }    
    public function setFilePath($filePath){
        if( empty($filePath) ){
            throw new InvalidArgumentException(TFormDinMessage::ERROR_EMPTY_INPUT);
        }else{
            $this->filePath=$filePath;
        }
    }
    
    public function gravarMsg($msg){
        $this->gravarMsgNormal($msg);
    }

    public function gravarMsgNormal($msg){
        // Adiciona a data e hora à mensagem de log
        $msg = "[".date("Y-m-d H:i:s")."] ".$msg.PHP_EOL;

        $msg = StringHelper::convert_encoding($msg,'UTF-8','ASCII');
        $filePath = $this->getFilePath();
        error_log($msg, 3, $filePath);
    }

    /**
     * Grava um arquivo de log com mensagens invertidas do final para o inicio. 
     * CUIDADO ao usar pois pode gerar estouro de memoria
     *
     * @param string $msg
     * @return void
     */
    public function gravarMsgInvertido($msg){
        $filePath = $this->getFilePath();
        // Adiciona a data e hora à mensagem de log
        $log_message_with_timestamp = "[".date("Y-m-d H:i:s")."] ".$msg."\n";

        // Lê o conteúdo atual do arquivo de log, se existir
        $current_content = FileHelper::exists($filePath)?file_get_contents($filePath):'';

        // Concatena a nova mensagem com o conteúdo atual
        $new_content = $log_message_with_timestamp . $current_content;

        // Escreve o novo conteúdo no arquivo de log
        file_put_contents($filePath, $new_content);
    }
}
?>