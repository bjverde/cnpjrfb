<?php
class GeraGrafoController
{

    /***
     * 1 - sudo docker-compose exec apache_php bash
     * 2 - chown -R www-data:www-data cnpjrfb/app/cnpj_full/graficos
     */
    public static function executa($param)
    {
        Util::debug($param,'Param');
        if( empty($param['nome_socio']) ){
            $cnpj = $param['cnpj'];
            $command = 'python3 consulta.py cnpj '.$cnpj.' graficos/'.$cnpj.' --viz';
        }else{
            $nome_socio = $param['nome_socio'];
            $cnpj = $param['cnpj'];
            $cpf = $param['cnpj_cpf_socio'];
            $command = 'python3 consulta.py nome_socio '.$nome_socio.' graficos/'.$cnpj.'_'.$cpf.' --viz';            
        }
        if (! defined ( 'DS' )) {
            define ( 'DS', DIRECTORY_SEPARATOR );
        }
        $path = dirname ( __FILE__ );
        $path = $path.DS.'..'.DS.'..'.DS.'cnpj_full'.DS.'CNPJ-full'.DS;
        $command = 'cd '.$path.';'.$command.' 2>&1';
        //$command = 'cd '.$path.';python3 consulta.py 2>&1';
        //$command = 'cd '.$path.';ls -l > t.txt'.' 2>&1';
        $result01 = exec($command, $output, $result);
        Util::debug($command,'Command');
        Util::debug($output,'Output');
        Util::debug($result,'Result');
        Util::debug($result01,'Result01');
        return true;
    }
}
