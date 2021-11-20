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
 * Classe para criação do Dialogo com mensagen
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
class TFormDinMessage {

    const CSS_FILE_FORM_DEFAULT_FAIL = 'Arquivo de CSS para o Padrão dos Forms não existe ou não está no formato CSS';
    const MENU_FILE_FAIL = 'Arquivo do Menu não existe';

    const FORM_MIN_VERSION_INVALID_FORMAT = 'O formato da versão não é válido, informe no formato X.Y.Z';
    const FORM_MIN_VERSION_BLANK = 'Informe a versão minima do formDin';
    const FORM_MIN_VERSION_NOT = ' Para esse sistema funcionar a versão mínima necessária do formDin é: ';
    const FORM_MIN_YOU_VERSION = 'Sua versão do FormDin é : ';
    
    const ARRAY_EXPECTED = 'O atribruto deveria ser um array';
    const ARRAY_KEY_NOT_EXIST = 'Não existe a chave procurada no array FormDin';
    const ARRAY_ATTRIBUTE_NOT_EXIST = 'Não existe a atributo procurada no array FormDin';
    
    const DONOT_QUOTATION = 'Não use aspas simples ou duplas na pesquisa !';
    
    const ERROR_FIELD_ID_CANNOT_EMPTY = 'O id do campo não pode ficar em branco';

    const ERROR_HTML_COLOR_HEXA = 'Informe uma cor HTML no formato hexadecimal. Exemplo #efefef !';

    const ERROR_EMPTY_INPUT    = 'O Parametro não pode ficar em branco';
    const ERROR_TYPE_NOT_STRING= 'Tipo não é string! ';
    const ERROR_TYPE_NOT_INT   = 'Tipo não númerico! ';
    const ERROR_TYPE_NOT_ARRAY = 'Tipo não é um array! ';
    const ERROR_TYPE_ARRAY_EMP = 'O array está vazio! ';
    const ERROR_TYPE_NOT_SET   = 'A variable has not been defined! ';
    const ERROR_TYPE_WRONG     = 'Tipo de dados errado';
    const ERROR_SQL_NULL       = 'O SQL está em branco';
    const ERROR_SQL_PARAM      = 'Quantidade de parametros diferente da quantidade utilizada na instrução sql!';
    const ERROR_SQL_NULL_DBMA  = 'O Tipo do Database management system está em branco';

    const ERROR_FD5  = 'ERRO FormDin5: ';
    const ERROR_FD5_PARAM_MIGRA  = 'Falha na migração do FormDin 4 para 5.';
    const ERROR_FD5_OBJ_ADI      = Self::ERROR_FD5.' objeto Adianti Fieald não pode ficar em branco.';
    const ERROR_FD5_OBJ_BUILDER  = Self::ERROR_FD5.' objeto não é um Adianti BootstrapFormBuilder.';
    const ERROR_FD5_OBJ_BOOTGRID = Self::ERROR_FD5.' objeto não é um Adianti BootstrapDatagridWrapper.';
    const ERROR_FD5_OBJ_CHECKLIST= Self::ERROR_FD5.' objeto não é um FormDin5 TFormDinCheckList.';
    const ERROR_FD5_PARAM        = Self::ERROR_FD5.' o parametro não pode ficar em branco.';
    const ERROR_FD5_FORM_MIGRAT  = Self::ERROR_FD5_PARAM_MIGRA.' A classe TFORM MUDOU! o primeiro parametro agora recebe $this!.';
    
    const ERROR_OBJ_TYPE_WRONG  = 'type object is wrong';
    const ERROR_OBJ_STORED_PROC = 'Stored Procedure Name is empty';
    const ERROR_OBJ_TABLE       = 'Table Name is empty';

    const ERROR_GRID_UPDATEFIELD= 'MixUpdateField não definido para ação: ';
    const ERROR_OBJ_OPTION      = Self::ERROR_OBJ_TYPE_WRONG.' use only to TFormDinCheckField or TFormDinRadio';

    //-----------------------------------------------------------

    const MSG_CONTRIB_PROJECT  = 'Contribute to the project https://github.com/bjverde/sysgenad !';

    //------------  Adianti Mensage Type -------------------------
    const TYPE_INFO   = 'info';
    const TYPE_ERROR  = 'error';
    const TYPE_WARING = 'warning';

    //-----------------------------------------------------------
    protected $adiantiObj;
    protected $mixMessage;

    /**
     * ------------------------------------------------------------------------
     * FormDin 5, que é uma reconstrução do FormDin 4 sobre o Adianti 7.X
     * Alguns parâmetros têm uma TAG, veja documentação da classe para saber
     * o que cada marca significa.
     * ------------------------------------------------------------------------
     *
     * @param string $message   - 1: Texto da mensagem pode ser HTML
     * @param string $type      - 2: FORMDIN5 Type mensagem: DEFAULT=info, error, warning. Use TFormDinMessage::TYPE_
     * @param TAction $action   - 3: FORMDIN5 Classe TAction do Adianti
     * @param string $title_msg - 4: FORMDIN5 titulo da mensagem
     */
    public function __construct($mixMessage
                              , $type = TFormDinMessage::TYPE_INFO
                              , TAction $action = NULL
                              , $title_msg = '')
    {
        $this->setMixMessage($mixMessage);
        $mixMessage = $this->getMixMessage();
        $this->adiantiObj = new TMessage($type,$mixMessage,$action,$title_msg);
        return $this->adiantiObj;
    }

    public function setMixMessage($mixMessage){
        $this->mixMessage= self::messageTransform($mixMessage);
    }
    public function getMixMessage(){
        return $this->mixMessage;
    }

    public static function messageTransform($mixMessage){
        $result = null;
        if(is_array($mixMessage)){
            $mixMessage = implode( '<br>', $mixMessage );
            $mixMessage = preg_replace( '/' . chr( 10 ) . '/', '<br>', $mixMessage );
            $mixMessage = preg_replace( '/' . chr( 13 ) . '/', '', $mixMessage );
            $result=$mixMessage;
        }else{
            $result=$mixMessage;
        }
        return $result;
    }

    public static function logRecord(Exception $exception)
    {
        $app = $_SESSION[APPLICATION_NAME];
        $login = null;
        $grupo = null;
        if( ArrayHelper::has('USER',$_SESSION[APPLICATION_NAME]) ) {
            $login = ( ArrayHelper::has('LOGIN', $_SESSION[APPLICATION_NAME]['USER']) ? $_SESSION[APPLICATION_NAME]['USER']['LOGIN']:null );
            $grupo = ( ArrayHelper::has('GRUPO_NOME', $_SESSION[APPLICATION_NAME]['USER']) ? $_SESSION[APPLICATION_NAME]['USER']['GRUPO_NOME']:null );
        }
        $log = 'formDin: '.FormDinHelper::version().' ,sistem: '.APPLICATION_NAME.' v:'.SYSTEM_VERSION.' ,usuario: '.$login
        .PHP_EOL.'type: '.get_class($exception).' ,Code: '.$exception->getCode().' ,file: '.$exception->getFile().' ,line: '.$exception->getLine()
        .PHP_EOL.'mensagem: '.$exception->getMessage()
        .PHP_EOL."Stack trace:"
        .PHP_EOL.$exception->getTraceAsString();
        
        error_log($log);
    }
    
    public static function logRecordSimple($message)
    {
        $log = 'formDin: '.FormDinHelper::version().' ,sistem: '.APPLICATION_NAME.' v:'.SYSTEM_VERSION
        .PHP_EOL.TAB.'mensagem: '.$message;
        error_log($log);
    }
}
?>