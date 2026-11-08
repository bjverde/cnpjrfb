<?php
/**
 * Gera o grafo de relacionamentos chamando o consulta.py do CNPJ-full.
 *
 * Tudo o que monta o comando fica na seção 'cnpj_full' do
 * app/config/application.php (versionado, igual em qualquer ambiente):
 *  - path:   pasta do CNPJ-full, criada com "git clone https://github.com/bjverde/CNPJ-full.git"
 *            dentro de app/ (padrão app/CNPJ-full; no Linux maiúsculas fazem diferença);
 *  - script: cnpj_full/consulta.py, relativo à pasta do CNPJ-full;
 *  - nivel e cache: opções --nivel e --cache do consulta.py;
 *  - grafos: pasta dos grafos gerados (padrão app/output/grafos/<cnpj>).
 * Caminhos relativos são a partir da pasta www/cnpjrfb. O Python é "python"
 * no Windows e "python3" no Linux/Mac; a versão é conferida pelo próprio CNPJ-full.
 *
 * A pasta do CNPJ-full recebe um .htaccess "Require all denied": o Apache não
 * serve nada dela (.env, banco SQLite). A pasta dos grafos é servida pelo
 * Apache e a tela abre o grafo.html (autocontido, d3 embutido) num iframe.
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

    // Padrões, usados quando o valor não está na seção 'cnpj_full' do application.php
    const PASTA_CNPJ_FULL = 'app/CNPJ-full';
    const PASTA_GRAFOS = 'app/output/grafos';
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
        if( !is_file($pathCnpjFull.DIRECTORY_SEPARATOR.self::getScript()) ){
            $resultado[GeraGrafoController::INFO] = 'CNPJ-full não encontrado em '.$pathCnpjFull
                .'. Dentro da pasta app/, rode: git clone https://github.com/bjverde/CNPJ-full.git';
            return $resultado;
        }
        self::protegePasta($pathCnpjFull);
        $pathGrafos = self::getPathGrafos();
        if( !is_dir($pathGrafos) ){
            @mkdir($pathGrafos, 0775, true);
        }

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
     * URL relativa (a partir de www/cnpjrfb) do grafo.html, para o navegador
     * abrir direto. Null se a pasta dos grafos estiver fora do sistema web.
     */
    public static function getUrlGrafo($arquivo)
    {
        $real = realpath((string) $arquivo);
        $base = realpath(self::getPathSistema());
        if( $real === false || $base === false || strpos($real, $base.DIRECTORY_SEPARATOR) !== 0 ){
            return null;
        }
        $relativo = substr($real, strlen($base) + 1);
        return implode('/', array_map('rawurlencode', explode(DIRECTORY_SEPARATOR, $relativo)));
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
        $command = self::getPython().' '.escapeshellarg(self::getScript());
        foreach ($argumentos as $argumento) {
            $command = $command.' '.escapeshellarg($argumento);
        }
        $command = $command.' --nivel '.self::getNivel().' --viz';
        if( self::getConfig('cache') !== '0' ){
            $command = $command.' --cache';
        }

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

    /** Pasta do CNPJ-full: 'cnpj_full' => 'path' do application.php, ou app/CNPJ-full. */
    public static function getPathCnpjFull()
    {
        return self::resolvePath(self::getConfig('path'), self::PASTA_CNPJ_FULL);
    }

    /** Script de consulta, relativo à pasta do CNPJ-full. */
    public static function getScript()
    {
        $script = self::getConfig('script');
        return $script === '' ? self::SCRIPT_CONSULTA : $script;
    }

    public static function getNivel()
    {
        $nivel = self::getConfig('nivel');
        return ctype_digit($nivel) && (int) $nivel > 0 ? (int) $nivel : self::NIVEL;
    }

    /** Caminho relativo à pasta www/cnpjrfb vira absoluto; absoluto fica como está. */
    private static function resolvePath($path, $padrao)
    {
        if( $path === '' ){
            $path = $padrao;
        }
        $path = str_replace(array('/', '\\'), DIRECTORY_SEPARATOR, $path);
        $absoluto = (substr($path, 0, 1) === DIRECTORY_SEPARATOR) || preg_match('/^[A-Za-z]:/', $path);
        if( !$absoluto ){
            $path = self::getPathSistema().DIRECTORY_SEPARATOR.$path;
        }
        return rtrim($path, DIRECTORY_SEPARATOR);
    }

    /** Pasta www/cnpjrfb (raiz do sistema web). */
    public static function getPathSistema()
    {
        return dirname(self::getPathApp());
    }

    /** Valor da seção 'cnpj_full' do app/config/application.php. */
    private static function getConfig($chave)
    {
        if( !class_exists('AdiantiApplicationConfig') ){
            return '';
        }
        $ini = AdiantiApplicationConfig::get();
        if( !isset($ini['cnpj_full'][$chave]) ){
            return '';
        }
        return trim((string) $ini['cnpj_full'][$chave]);
    }

    /** Pasta dos grafos: 'cnpj_full' => 'grafos' do application.php, ou app/output/grafos. */
    public static function getPathGrafos()
    {
        return self::resolvePath(self::getConfig('grafos'), self::PASTA_GRAFOS);
    }

    /** python no Windows e python3 nos demais (Linux, Mac). */
    public static function getPython()
    {
        return PHP_OS_FAMILY === 'Windows' ? 'python' : 'python3';
    }

    /**
     * Garante um .htaccess "Require all denied" na pasta, para o Apache não
     * servir nada dela. A pasta do CNPJ-full é ignorada pelo git do cnpjrfb,
     * então o .htaccess dela é criado aqui na primeira execução.
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
