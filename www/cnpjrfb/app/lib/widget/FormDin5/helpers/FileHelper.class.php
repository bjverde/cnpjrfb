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
class FileHelper
{
    /**
     * Retorna o nome da pasta que está o sistema
     * @return string
     */
    public static function getNomePastaSistema() 
    {
        $partes = explode(DIRECTORY_SEPARATOR, __DIR__);
        array_pop($partes);//helpers
        array_pop($partes);//FormDin5
        array_pop($partes);//widget
        array_pop($partes);//lib
        array_pop($partes);//app
    	return end($partes);
    }

    /**
     * Retorna o caminho completo do sistema no SO
     * @return string
     */
    public static function getCaminhoSistema() 
    {
        $nomeSitema   = self::getNomePastaSistema();
        $partes = explode($nomeSitema, __DIR__);
        $caminho= $partes[0].$nomeSitema;
    	return $caminho;
    }

    /**
     * Avoid the problem Deprecated of PHP 8.1.X
     * @param string $filePath
     * @return void
     */
    public static function exists($filePath) 
    {
    	if( empty($filePath) ){
    		$result = FALSE;
    	}else{
            $result = file_exists($filePath);
        }
    	return $result;
    }

    /**
     * Move um arquivo do ponto A para o ponto B.
     * Verifica o arquivo de origem existe. Se não existir vai gerar uma Exception 
     * Verifica se o caminho de destino existe, se não existe vai criar todo o caminho. Depois 
     *
     * @param string $from  caminho da origem. Sugestão getcwd().DIRECTORY_SEPARATOR.'tmp'.DIRECTORY_SEPARATOR .'imprensa.png';
     * @param string $to    caminho da destino.Sugestão getcwd().DIRECTORY_SEPARATOR.'app/images/pessoas/'.DIRECTORY_SEPARATOR .'imprensa.png';
     * @return void
     */
    public static function move(string $from, string $to) 
    {
        if( !self::exists($from) ){
            throw new Exception('File not exist: '.$from);
        }
        $dirname = dirname($to);
        if( !is_dir($dirname) ){
            if (!mkdir($dirname, 0755, true)) {
                throw new Exception('Falha ao criar os diretórios: '.$dirname);
            }
        }
        $result = rename($from,$to);	
    	return $result;
    }
}
?>
