<?php
/**
 * Gera o grafo de relacionamentos chamando o consulta.py do CNPJ-full.
 *
 * Onde fica cada coisa (todos os caminhos são relativos à pasta app/):
 *  - Código Python: app/CNPJ-full, criado com
 *    "git clone https://github.com/bjverde/CNPJ-full.git" dentro de app/.
 *  - Grafos gerados: app/output/grafos/<cnpj> (ou <cnpj>_<cpf/cnpj do sócio>).
 *    Pode ser trocado pela variável de ambiente CNPJ_GRAFOS_PATH.
 *  - Python: "python3" no Linux e "python" no Windows. Pode ser trocado pela
 *    variável de ambiente CNPJ_PYTHON (ex.: o python de um venv).
 *
 * As duas pastas recebem um .htaccess "Require all denied": o Apache não
 * serve nada delas (.env, banco SQLite, cache.json). O grafo.html é
 * autocontido (d3 embutido), então o PHP lê o arquivo e a tela o mostra.
 *
 * No Linux/Docker o usuário do Apache precisa poder gravar em app/output/grafos:
 *   chown -R www-data:www-data www/cnpjrfb/app/output/grafos
 */
class GeraGrafoController
{
    const GERAL = 'geral';
    const INFO = 'info';
    const ARQUIVO = 'arquvio';
    const COMANDO = 'comando';

    const TAMANHO_MAXIMO_NOME = 150;

    // Mesmo nome que o "git clone" cria. No Linux maiúsculas fazem diferença.
    const PASTA_CNPJ_FULL = 'CNPJ-full';
    const PASTA_GRAFOS = 'output/grafos';
    const SCRIPT_CONSULTA = 'cnpj_full/consulta.py';
    const NIVEL = 3;

    /***
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

        $pathCnpjFull = self::getPathCnpjFull();
        if( !is_file($pathCnpjFull.DIRECTORY_SEPARATOR.self::SCRIPT_CONSULTA) ){
            $resultado[GeraGrafoController::INFO] = 'CNPJ-full não encontrado em '.$pathCnpjFull
                .'. Dentro da pasta app/, rode: git clone https://github.com/bjverde/CNPJ-full.git';
            return $resultado;
        }
        self::protegePasta($pathCnpjFull);
        $pathGrafos = self::getPathGrafos();
        if( !is_dir($pathGrafos) ){
            @mkdir($pathGrafos, 0775, true);
        }
        self::protegePasta($pathGrafos);

        try {
            $command = self::montaComando($param, $nome_arquivo);
        } catch (InvalidArgumentException $e) {
            $resultado[GeraGrafoController::INFO] = $e->getMessage();
            return $resultado;
        }

        $result = null;
        $result01 = null;
        $output = array();
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
        $arquivo = $pathGrafos.DIRECTORY_SEPARATOR.$nome_arquivo.DIRECTORY_SEPARATOR.'grafo.html';
        $resultado[GeraGrafoController::GERAL]   = ($result==0 && is_file($arquivo));
        $resultado[GeraGrafoController::INFO]    = StringHelper::str2utf8(is_array($output) ? implode("\n", $output) : (string) $output);
        $resultado[GeraGrafoController::COMANDO] = $command;
        $resultado[GeraGrafoController::ARQUIVO] = $arquivo;
        return $resultado;
    }

    /**
     * Conteúdo do grafo.html gerado, ou null se o arquivo não existir.
     * Só lê arquivos dentro da pasta de grafos.
     */
    public static function leGrafo($arquivo)
    {
        $real = realpath((string) $arquivo);
        $base = realpath(self::getPathGrafos());
        if( $real === false || $base === false || strpos($real, $base.DIRECTORY_SEPARATOR) !== 0 ){
            return null;
        }
        $html = file_get_contents($real);
        return $html === false ? null : $html;
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
        $argumentos[] = self::getPathGrafos().DIRECTORY_SEPARATOR.$nome_arquivo;

        // --cache: se a mesma consulta já foi feita com a mesma carga do banco,
        // o consulta.py reaproveita o grafo em vez de gerar de novo.
        $command = self::comandoPython().' '.self::SCRIPT_CONSULTA;
        foreach ($argumentos as $argumento) {
            $command = $command.' '.escapeshellarg($argumento);
        }
        $command = $command.' --nivel '.self::NIVEL.' --viz --cache';

        // Roda dentro da pasta do CNPJ-full para ele achar o próprio .env
        $separador = self::isLinux() ? ' && ' : ';';
        $command = 'cd '.escapeshellarg(self::getPathCnpjFull()).$separador.$command.' 2>&1';
        return $command;
    }

    /** Pasta app/ do cnpjrfb. */
    public static function getPathApp()
    {
        return dirname(__DIR__, 2);
    }

    public static function getPathCnpjFull()
    {
        return self::getPathApp().DIRECTORY_SEPARATOR.self::PASTA_CNPJ_FULL;
    }

    /** Pasta dos grafos: CNPJ_GRAFOS_PATH ou app/output/grafos. */
    public static function getPathGrafos()
    {
        $path = getenv('CNPJ_GRAFOS_PATH');
        if( empty($path) ){
            $path = self::getPathApp().DIRECTORY_SEPARATOR.str_replace('/', DIRECTORY_SEPARATOR, self::PASTA_GRAFOS);
        }
        return rtrim($path, '/\\');
    }

    /** Python: CNPJ_PYTHON ou python3 (Linux) / python (Windows). */
    public static function getPython()
    {
        $python = getenv('CNPJ_PYTHON');
        if( empty($python) ){
            $python = self::isLinux() ? 'python3' : 'python';
        }
        return $python;
    }

    /**
     * Python pronto para o comando. Um caminho simples (python3,
     * /opt/venv/bin/python, C:/Python313/python.exe) vai sem aspas; com
     * espaços vai entre aspas (no PowerShell precisa do "&" na frente).
     */
    private static function comandoPython()
    {
        $python = self::getPython();
        if( preg_match('/^[A-Za-z0-9_.\/:\\\\-]+$/', $python) ){
            return $python;
        }
        $python = escapeshellarg($python);
        return self::isLinux() ? $python : '& '.$python;
    }

    /**
     * Garante um .htaccess "Require all denied" na pasta, para o Apache não
     * servir nada dela. A pasta do CNPJ-full é ignorada pelo git do cnpjrfb,
     * então o .htaccess é criado aqui na primeira execução.
     */
    public static function protegePasta($path)
    {
        $htaccess = $path.DIRECTORY_SEPARATOR.'.htaccess';
        if( is_dir($path) && !is_file($htaccess) ){
            @file_put_contents($htaccess, "Require all denied\n");
        }
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
