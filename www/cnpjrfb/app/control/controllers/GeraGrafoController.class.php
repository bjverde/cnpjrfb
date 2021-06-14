<?php
class GeraGrafoController
{
    const GERAL = 'geral';
    const INFO = 'info';
    const ARQUIVO = 'arquvio';
    const COMANDO = 'comando';

    /***
     * 1 - sudo docker-compose exec apache_php bash
     * 2 - chown -R www-data:www-data cnpjrfb/app/cnpj_full/graficos
     */
    public static function executa($param)
    {
        //FormDinHelper::debug($param,'Param');
        $pythonCommand = stristr(PHP_OS, 'LINUX')?'python3':'python'; //Alteração devido o windows10
        $nome_arquivo=null;
        if( empty($param['nome_socio']) ){
            $cnpj = StringHelper::limpaCnpjCpf($param['cnpj']);
            $nome_arquivo = $cnpj;
            $command = $pythonCommand.' consulta.py cnpj '.$cnpj.' graficos/'.$nome_arquivo.' --nivel 3 --viz';
        }else{
            $nome_socio = $param['nome_socio'];
            $nome_arquivo = $param['cnpj'].'_'.$param['cnpj_cpf_socio'];
            $command = $pythonCommand.' consulta.py nome_socio "'.$nome_socio.'" graficos/'.$nome_arquivo.' --nivel 3 --viz';
        }
        if (! defined ( 'DS' )) {
            define ( 'DS', DIRECTORY_SEPARATOR );
        }
        $path = dirname ( __FILE__ );
        $path = $path.DS.'..'.DS.'..'.DS.'CNPJ-full'.DS;
        $command = 'cd '.$path.';'.$command.' 2>&1';

        //POG para funcionar no windows
        //https://stackoverflow.com/questions/12757891/enable-shell-exec-in-wamp-server
        //https://qastack.com.br/programming/14062055/composer-warning-openssl-extension-is-missing-how-to-enable-in-wamp
        //https://php.docow.com/habilite-shell_exec-no-servidor-wamp.html
        try {
            if(stristr(PHP_OS, 'LINUX')){
                $result01 = exec($command, $output, $result);
            }else{
                $output = shell_exec('powershell.exe -command '.$command);
                //https://stackoverflow.com/questions/49124196/execute-powershell-script-from-php
                //$output = shell_exec('powershell.exe -command '.$command);
                $output = shell_exec($command);
                $result = null;
                $result01 = null;
                $msg = '<b>Infelizmente esse sistema não funciona muito bem windows :-( . Para um ambiente mais automatizado rode no Linux ou Usando Docker no Windows</b>';
                $msg = $msg.'<br>';
                $msg = $msg.'<br> Execute o comando abaixo em um terminal para gerar o grafo.';
                $msg = $msg.'<br>';
                $msg = $msg.'<b><pre>'.$command.'</pre></b>';
                $msg = $msg.'<br>';
                $msg = $msg.'<br>Depois de executar o comando o grafo vai aparecer';
                $msg = $msg.'<br>';                
                FormDinHelper::debug($msg);
            }
        } catch (Exception $e) {
            echo $e->getMessage();
        }
        //FormDinHelper::debug($command,'Command');
        //FormDinHelper::debug($output,'Output');
        //FormDinHelper::debug($result,'Result');
        //FormDinHelper::debug($result01,'Result01');
        $resultado = array();
        $resultado[GeraGrafoController::GERAL]   = $result==0?true:false;
        $resultado[GeraGrafoController::INFO]    = StringHelper::str2utf8($result01);
        $resultado[GeraGrafoController::COMANDO] = $command;
        $resultado[GeraGrafoController::ARQUIVO] = DS.'graficos'.DS.$nome_arquivo.DS.'grafo.html';
        return $resultado;
    }
}
