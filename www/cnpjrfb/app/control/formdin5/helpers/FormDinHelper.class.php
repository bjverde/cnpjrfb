<?php
class FormDinHelper
{
    public static function d( $mixExpression,$strComentario,$boolExit )
    {        
        return self::debug($mixExpression,$strComentario,$boolExit);
    }

    /***
     * função para depuração. Exibe o modulo a linha e a variável/objeto solicitado
     * Retirado do FormDin 4.9.0
     * https://github.com/bjverde/formDin/blob/master/base/includes/funcoes.inc
     */
    public static function debug( $mixExpression,$strComentario='Debug', $boolExit=FALSE ) {
        ini_set ( 'xdebug.max_nesting_level', 150 );
        if (defined('DEBUGAR') && !DEBUGAR){
            return;
        }
        $arrBacktrace = debug_backtrace();
        if( isset($_REQUEST['ajax']) && $_REQUEST['ajax'] ){
            echo '<pre>';
            foreach ( $arrBacktrace[0] as $strAttribute => $mixValue ){
                if ( !is_array($mixValue) ){
                    echo $strAttribute .'='. $mixValue ."\n";
                }
            }
            echo "---------------\n";
            print_r( $mixExpression );
            echo '</pre>';

        } else {
            echo "<script>try{fwUnblockUI();}catch(e){try{top.app_unblockUI();}catch(e){}}</script>";
            echo "<fieldset style='text-align:left;'><legend><font color=\"#007000\">".$strComentario."</font></legend><pre>" ;
            foreach ( $arrBacktrace[0] as $strAttribute => $mixValue ) {
                if( is_string( $mixValue ) ) {
                    echo "<b>" . $strAttribute . "</b> ". $mixValue ."\n";
                }
            }
            echo "</pre><hr />";
            echo '<span style="color:red;"><blink>'.$strComentario.'</blink></span>'."\n";;
            echo '<pre>';
            if( is_object($mixExpression) ) {
                var_dump( $mixExpression );
            } else {
            print_r($mixExpression);
            }
            echo "</pre></fieldset>";
            if ( $boolExit ) {
                echo "<br /><font color=\"#700000\" size=\"4\"><b>D I E</b></font>";
                exit();
            }
        }
    }

}
