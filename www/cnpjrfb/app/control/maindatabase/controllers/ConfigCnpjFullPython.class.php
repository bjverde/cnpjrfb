<?php
/**
 * Lê a configuração do banco no .env do CNPJ-full (Python), para o PHP e o
 * Python usarem o mesmo banco com a configuração num lugar só.
 *
 * - A pasta do CNPJ-full vem de app/config/application.php > 'cnpj_full' > 'path'
 *   (a mesma usada pela tela do grafo, ver GeraGrafoController).
 * - O .env fica na raiz dessa pasta. Nomes das variáveis: .env.example do CNPJ-full.
 *
 * Usado pelo app/config/maindatabase.php:
 *   return ConfigCnpjFullPython::getConfigBanco();
 *
 * Veja docs/arquitetura.md.
 */
class ConfigCnpjFullPython
{
    const ARQUIVO_ENV = '.env';

    /** CNPJ_DB_TIPO (nomes do CNPJ-full) => 'type' do Adianti */
    const TIPOS = array(
        'postgres'  => 'pgsql',
        'mysql'     => 'mysql',
        'sqlserver' => 'sqlsrv',
        'oracle'    => 'oracle',
        'sqlite'    => 'sqlite',
    );

    /** Variáveis de banco do .env e o valor usado quando não estão no arquivo */
    const VARIAVEIS = array(
        'CNPJ_DB_TIPO'    => 'sqlite',
        'CNPJ_DB_HOST'    => '',
        'CNPJ_DB_PORTA'   => '',
        'CNPJ_DB_NOME'    => '',
        'CNPJ_DB_USUARIO' => '',
        'CNPJ_DB_SENHA'   => '',
        'CNPJ_DB_SCHEMA'  => '',
        'CNPJ_DB_ARQUIVO' => '',
    );

    /** Pasta do CNPJ-full, informada em application.php > 'cnpj_full' > 'path'. */
    public static function getPathCnpjFull()
    {
        return GeraGrafoController::getPathCnpjFull();
    }

    /** Caminho do .env do CNPJ-full. */
    public static function getPathEnv()
    {
        return self::getPathCnpjFull().DIRECTORY_SEPARATOR.self::ARQUIVO_ENV;
    }

    /**
     * Verifica se o .env do CNPJ-full existe.
     *
     * @throws Exception quando o arquivo não existe
     */
    public static function verificarEnv()
    {
        $pathEnv = self::getPathEnv();
        if( !is_file($pathEnv) ){
            throw new Exception('Arquivo .env do CNPJ-full não encontrado em '.$pathEnv
                .'. Copie o .env.example do CNPJ-full para .env e configure o banco.'
                .' A pasta do CNPJ-full é informada em app/config/application.php > cnpj_full > path.');
        }
    }

    /**
     * Todas as variáveis de banco do .env, sempre com todas as chaves de VARIAVEIS
     * (as que não estão no arquivo vêm com o valor padrão).
     *
     * @return array CNPJ_DB_TIPO, CNPJ_DB_HOST, CNPJ_DB_PORTA, CNPJ_DB_NOME,
     *               CNPJ_DB_USUARIO, CNPJ_DB_SENHA, CNPJ_DB_SCHEMA, CNPJ_DB_ARQUIVO
     * @throws Exception quando o .env não existe
     */
    public static function getVariaveis()
    {
        self::verificarEnv();
        $env = self::lerEnv(self::getPathEnv());
        $variaveis = array();
        foreach (self::VARIAVEIS as $chave => $padrao) {
            $valor = isset($env[$chave]) ? $env[$chave] : '';
            $variaveis[$chave] = ($valor === '') ? $padrao : $valor;
        }
        return $variaveis;
    }

    /**
     * Array de conexão no formato do Adianti (app/config/maindatabase.php).
     *
     * @return array host, port, name, user, pass, type, prep, slog
     * @throws Exception quando o .env não existe ou CNPJ_DB_TIPO é inválido
     */
    public static function getConfigBanco()
    {
        $variaveis = self::getVariaveis();
        GeraGrafoController::protegePasta(self::getPathCnpjFull()); // o .env tem a senha do banco

        $tipo = strtolower($variaveis['CNPJ_DB_TIPO']);
        if( !isset(self::TIPOS[$tipo]) ){
            throw new Exception('CNPJ_DB_TIPO inválido no .env do CNPJ-full: '.$variaveis['CNPJ_DB_TIPO']
                .'. Use: '.implode(', ', array_keys(self::TIPOS)).'.');
        }

        $config = array(
            'host' => $variaveis['CNPJ_DB_HOST'],
            'port' => $variaveis['CNPJ_DB_PORTA'],
            'name' => $variaveis['CNPJ_DB_NOME'],
            'user' => $variaveis['CNPJ_DB_USUARIO'],
            'pass' => $variaveis['CNPJ_DB_SENHA'],
            'type' => self::TIPOS[$tipo],
            'prep' => '1',
            'slog' => 'SystemSqlLogService',
        );
        if( $tipo === 'sqlite' ){
            $config['name'] = self::getPathSqlite($variaveis['CNPJ_DB_ARQUIVO']);
        }
        return $config;
    }

    /**
     * Arquivo do SQLite: CNPJ_DB_ARQUIVO relativo à pasta do CNPJ-full (igual ao
     * Python) ou absoluto.
     *
     * @throws Exception quando CNPJ_DB_ARQUIVO está vazio
     */
    public static function getPathSqlite($arquivo)
    {
        if( $arquivo === '' ){
            throw new Exception('Com CNPJ_DB_TIPO=sqlite, informe CNPJ_DB_ARQUIVO no .env do CNPJ-full.');
        }
        if( self::isPathAbsoluto($arquivo) ){
            return $arquivo;
        }
        return self::getPathCnpjFull().DIRECTORY_SEPARATOR.$arquivo;
    }

    /** /caminho, \caminho ou C:\caminho (C:/caminho) */
    public static function isPathAbsoluto($path)
    {
        return preg_match('#^([A-Za-z]:)?[\\\\/]#', $path) === 1;
    }

    /**
     * Lê o .env linha a linha: CHAVE=valor, linhas com # são comentário e aspas
     * em volta do valor são removidas. Não usar parse_ini_file(): ele dá erro de
     * sintaxe com os comentários do .env.example do CNPJ-full.
     */
    public static function lerEnv($pathEnv)
    {
        $env = array();
        foreach (file($pathEnv, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES) as $linha) {
            $linha = trim($linha);
            if( $linha === '' || $linha[0] === '#' || strpos($linha, '=') === false ){
                continue;
            }
            list($chave, $valor) = array_map('trim', explode('=', $linha, 2));
            $aspas = substr($valor, 0, 1);
            if( strlen($valor) > 1 && ($aspas === '"' || $aspas === "'") && substr($valor, -1) === $aspas ){
                $valor = substr($valor, 1, -1);
            }
            $env[$chave] = $valor;
        }
        return $env;
    }
}
