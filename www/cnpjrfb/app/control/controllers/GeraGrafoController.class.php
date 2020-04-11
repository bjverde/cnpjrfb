<?php
class GeraGrafoController
{
    const GERAL = 'geral';
    const INFO = 'info';
    const ARQUIVO = 'arquvio';

    /***
     * 1 - sudo docker-compose exec apache_php bash
     * 2 - chown -R www-data:www-data cnpjrfb/app/cnpj_full/graficos
     */
    public static function executa($param)
    {
        //FormDinHelper::debug($param,'Param');
        $nome_arquivo=null;
        if( empty($param['nome_socio']) ){
            $cnpj = StringHelper::limpaCnpjCpf($param['cnpj']);
            $nome_arquivo = $cnpj;
            $command = 'python3 consulta.py cnpj '.$cnpj.' graficos/'.$nome_arquivo.' --nivel 3 --viz';
        }else{
            $nome_socio = $param['nome_socio'];
            $nome_arquivo = $param['cnpj'].'_'.$param['cnpj_cpf_socio'];
            $command = 'python3 consulta.py nome_socio "'.$nome_socio.'" graficos/'.$nome_arquivo.' --nivel 3 --viz';
        }
        if (! defined ( 'DS' )) {
            define ( 'DS', DIRECTORY_SEPARATOR );
        }
        $path = dirname ( __FILE__ );
        $path = $path.DS.'..'.DS.'..'.DS.'cnpj_full'.DS.'CNPJ-full'.DS;
        $command = 'cd '.$path.';'.$command.' 2>&1';
        $result01 = exec($command, $output, $result);        
        //FormDinHelper::debug($command,'Command');
        //FormDinHelper::debug($output,'Output');
        //FormDinHelper::debug($result,'Result');
        //FormDinHelper::debug($result01,'Result01');
        $resultado = array();
        $resultado[GeraGrafoController::GERAL]   = $result==0?true:false;
        $resultado[GeraGrafoController::INFO]    = $result01;
        $resultado[GeraGrafoController::ARQUIVO] = DS.'graficos'.DS.$nome_arquivo.DS.'grafo.html';
        return $resultado;
    }
}
