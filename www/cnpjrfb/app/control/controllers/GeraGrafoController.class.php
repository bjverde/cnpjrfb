<?php
class GeraGrafoController
{
    const GERAL = 'geral';
    const INFO = 'info';
    const ARQUIVO = 'arquvio';
    const COMANDO = 'comando';

    const TAMANHO_MAXIMO_NOME = 150;

    /***
     * 1 - sudo docker-compose exec apache_php bash
     * 2 - chown -R www-data:www-data cnpjrfb/app/cnpj_full/graficos
     *
     * Todos os valores que vêm do formulário/URL são validados e passam por
     * escapeshellarg() antes de entrar no comando, para impedir a execução
     * de comandos arbitrários no servidor.
     */
    public static function executa($param)
    {
        $resultado = array();
        $resultado[GeraGrafoController::GERAL]   = false;
        $resultado[GeraGrafoController::INFO]    = null;
        $resultado[GeraGrafoController::COMANDO] = null;
        $resultado[GeraGrafoController::ARQUIVO] = null;

        try {
            $command = self::montaComando($param, $nome_arquivo);
        } catch (InvalidArgumentException $e) {
            $resultado[GeraGrafoController::INFO] = $e->getMessage();
            return $resultado;
        }

        $result = null;
        $result01 = null;
        //POG para funcionar no windows
        //https://stackoverflow.com/questions/12757891/enable-shell-exec-in-wamp-server
        //https://qastack.com.br/programming/14062055/composer-warning-openssl-extension-is-missing-how-to-enable-in-wamp
        //https://php.docow.com/habilite-shell_exec-no-servidor-wamp.html
        try {
            if(self::isLinux()){
                $result01 = exec($command, $output, $result);
            }else{
                //https://stackoverflow.com/questions/49124196/execute-powershell-script-from-php
                $output = shell_exec('powershell.exe -command '.$command);
                $msg = '<b>Infelizmente esse sistema não funciona muito bem windows :-( . Para um ambiente mais automatizado rode no Linux ou Usando Docker no Windows</b>';
                $msg = $msg.'<br>';
                $msg = $msg.'<br> Execute o comando abaixo em um terminal para gerar o grafo.';
                $msg = $msg.'<br>';
                $msg = $msg.'<b><pre>'.htmlspecialchars($command, ENT_QUOTES, 'UTF-8').'</pre></b>';
                $msg = $msg.'<br>';
                $msg = $msg.'<br>Depois de executar o comando o grafo vai aparecer';
                $msg = $msg.'<br>';
                FormDinHelper::debug($msg);
            }
        } catch (Exception $e) {
            echo htmlspecialchars($e->getMessage(), ENT_QUOTES, 'UTF-8');
        }
        $resultado[GeraGrafoController::GERAL]   = $result==0?true:false;
        $resultado[GeraGrafoController::INFO]    = StringHelper::str2utf8($result01);
        $resultado[GeraGrafoController::COMANDO] = $command;
        $resultado[GeraGrafoController::ARQUIVO] = DS.'graficos'.DS.$nome_arquivo.DS.'grafo.html';
        return $resultado;
    }

    /**
     * Monta o comando do consulta.py com todos os argumentos validados e escapados.
     *
     * @param array  $param        - 01: parâmetros do formulário (cnpj, nome_socio, cnpj_cpf_socio)
     * @param string $nome_arquivo - 02: (saída) nome da pasta onde o grafo será gravado
     * @return string
     * @throws InvalidArgumentException quando algum valor é inválido
     */
    public static function montaComando($param, &$nome_arquivo = null)
    {
        if (! defined ( 'DS' )) {
            define ( 'DS', DIRECTORY_SEPARATOR );
        }
        $pythonCommand = self::isLinux()?'python3':'python'; //Alteração devido o windows10

        $cnpj = self::validaCnpj(self::getParam($param, 'cnpj'));
        $nome_socio = self::getParam($param, 'nome_socio');

        if( empty($nome_socio) ){
            $nome_arquivo = $cnpj;
            $argumentos = array('cnpj', $cnpj);
        }else{
            $nome_socio = self::validaNomeSocio($nome_socio);
            $cpf_cnpj_socio = StringHelper::limpaCnpjCpf(self::getParam($param, 'cnpj_cpf_socio'));
            if( $cpf_cnpj_socio === '' || strlen($cpf_cnpj_socio) > 14 ){
                throw new InvalidArgumentException('CPF/CNPJ do sócio inválido.');
            }
            $nome_arquivo = $cnpj.'_'.$cpf_cnpj_socio;
            $argumentos = array('nome_socio', $nome_socio);
        }
        $argumentos[] = 'graficos/'.$nome_arquivo;

        $command = $pythonCommand.' consulta.py';
        foreach ($argumentos as $argumento) {
            $command = $command.' '.escapeshellarg($argumento);
        }
        $command = $command.' --nivel 3 --viz';

        $path = dirname ( __FILE__ );
        $path = $path.DS.'..'.DS.'..'.DS.'CNPJ-full'.DS;
        $separador = self::isLinux() ? ' && ' : ';';
        $command = 'cd '.escapeshellarg($path).$separador.$command.' 2>&1';
        return $command;
    }

    /**
     * Aceita o CNPJ com ou sem máscara e devolve só os 14 dígitos.
     */
    public static function validaCnpj($cnpj)
    {
        $cnpj = StringHelper::limpaCnpjCpf((string) $cnpj);
        if( !preg_match('/^\d{14}$/', $cnpj) ){
            throw new InvalidArgumentException('CNPJ inválido. Informe os 14 dígitos.');
        }
        return $cnpj;
    }

    /**
     * Nome do sócio: tira espaços das pontas, limita o tamanho e recusa
     * caracteres de controle (quebra de linha, NUL etc.).
     */
    public static function validaNomeSocio($nome_socio)
    {
        $nome_socio = trim((string) $nome_socio);
        if( $nome_socio === '' ){
            throw new InvalidArgumentException('Nome do sócio não informado.');
        }
        if( mb_strlen($nome_socio, 'UTF-8') > self::TAMANHO_MAXIMO_NOME ){
            throw new InvalidArgumentException('Nome do sócio muito longo.');
        }
        if( preg_match('/[\x00-\x1F\x7F]/', $nome_socio) ){
            throw new InvalidArgumentException('Nome do sócio contém caracteres inválidos.');
        }
        return $nome_socio;
    }

    private static function getParam($param, $chave)
    {
        if( !is_array($param) || !isset($param[$chave]) || is_array($param[$chave]) ){
            return '';
        }
        return (string) $param[$chave];
    }

    private static function isLinux()
    {
        return stristr(PHP_OS, 'LINUX') ? true : false;
    }
}
