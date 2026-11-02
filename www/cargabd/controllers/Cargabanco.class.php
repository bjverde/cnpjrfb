<?php

class Cargabanco
{
    private $is_cli  = false;
    private $pathExtractedFiles  = null;

    /**
     * Tabelas na ordem da carga: nome da tabela => [DAO, parte do nome do arquivo da RFB]
     */
    private $tabelas = array();

    public function __construct()
    {
        if (php_sapi_name() == "cli") {
            $this->is_cli = true;
        }
        $this->pathExtractedFiles=ConfigHelper::getExtractedFilesPath();
        $tpdo = New TPDOConnection();
        $tpdo::connect();

        $this->tabelas = array(
             'cnae'            => array(new CnaeDAO($tpdo)           , 'CNAECSV')
            ,'moti'            => array(new MotiDAO($tpdo)           , 'MOTICSV')
            ,'munic'           => array(new MunicDAO($tpdo)          , 'MUNICCSV')
            ,'natju'           => array(new NatjuDAO($tpdo)          , 'NATJUCSV')
            ,'pais'            => array(new PaisDAO($tpdo)           , 'PAISCSV')
            ,'quals'           => array(new QualsDAO($tpdo)          , 'QUALSCSV')
            ,'estabelecimento' => array(new EstabelecimentoDAO($tpdo), 'ESTABELE')
            ,'socios'          => array(new SociosDAO($tpdo)         , 'SOCIOCSV')
            ,'empresa'         => array(new EmpresaDAO($tpdo)        , 'EMPRECSV')
            ,'simples'         => array(new SimplesDAO($tpdo)        , 'SIMPLES')
        );
    }
    public function getNomesTabelas()
    {
        return array_keys($this->tabelas);
    }
    /**
     * @param array|null $somenteTabelas - nomes das tabelas a carregar (null = todas)
     */
    public function executar(array $somenteTabelas = null){
        $tabelas = $this->filtrarTabelas($somenteTabelas);
        $this->quebraLinha();
        echo 'Esse procedimento vai apagar e carregar com os dados dos arquivos CSV as tabelas: '.implode(', ', array_keys($tabelas));
        $this->quebraLinha();
        $this->quebraLinha();
        $this->truncateDados($tabelas);
        $this->carregaDados($tabelas);
    }
    private function filtrarTabelas(array $somenteTabelas = null)
    {
        if ( empty($somenteTabelas) ) {
            return $this->tabelas;
        }
        $desconhecidas = array_diff($somenteTabelas, $this->getNomesTabelas());
        if ( !empty($desconhecidas) ) {
            throw new InvalidArgumentException('ERRO: tabela(s) desconhecida(s): '.implode(', ', $desconhecidas)
                                              .'. Use: '.implode(', ', $this->getNomesTabelas()));
        }
        return array_intersect_key($this->tabelas, array_flip($somenteTabelas));
    }
    public function quebraLinha(){
        $string = '<br>';
        if ( $this->is_cli ) {
            $string = PHP_EOL;
        }
        echo $string;
    }
    public function tempoFormatado($time_start){
        $time_end = microtime(true);
        $time = $time_end - $time_start; //calculate the difference between start and stop
        $time = number_format($time, 3, ',', '.');
        return $time;
    }
    public function truncateDados(array $tabelas){
        $time_start = microtime(true);
        foreach ($tabelas as $item) {
            $this->truncateTabela($item[0]);
        }
        echo 'Tempo total em segundos para todos os truncates: '.$this->tempoFormatado($time_start);
        $this->quebraLinha();
    }
    public function truncateTabela(Dao $classDao){
        $qtd = $classDao->selectCount();
        if( $qtd==0 ){
            echo 'TRUNCATE '.$classDao->getTabelaName().' tem ZERO registros, não rodou truncate';
        }else{
            echo 'TRUNCATE '.$classDao->getTabelaName().' com '.$qtd.' registros apagados';
            $classDao->truncate();
        }
        $this->quebraLinha();
    }
    public function carregaDados(array $tabelas){
        $this->quebraLinha();
        echo "---- Carga dos dados ----";
        $this->quebraLinha();
        $time_start = microtime(true);
        $totalDescartadas = 0;
        foreach ($tabelas as $item) {
            $resultado = $this->carregaDadosTabelaDoArquivo($item[0], $item[1]);
            $totalDescartadas = $totalDescartadas + $resultado[UploadCsv::DESCARTADAS];
        }
        echo 'Tempo total em segundos para toda as cargas: '.$this->tempoFormatado($time_start);
        $this->quebraLinha();
        if ( $totalDescartadas > 0 ) {
            echo 'ATENÇÃO: '.$totalDescartadas.' linha(s) descartada(s) por quantidade de colunas errada. Veja o error_log.';
            $this->quebraLinha();
        }
    }

    /**
     * Carrega dados de UM arquivos na classe DAO.
     *
     * @param Dao $classDao
     * @param string $arquivoCsv - caminho relativo à pasta de arquivos extraídos
     * @return array [lidas, gravadas, descartadas]
     */
    public function carregaDadosTabela(Dao $classDao, string $arquivoCsv){
        $time_start = microtime(true);
        $arquivoCsv = $this->pathExtractedFiles.DS.$arquivoCsv;
        if (!file_exists($arquivoCsv)){
            throw new InvalidArgumentException('ERRO: o arquivo '.$arquivoCsv.' não encontrado');
        }
        $uploadCsv = new UploadCsv($classDao,$arquivoCsv);
        $resultado = $uploadCsv->executar();
        echo $this->tempoFormatado($time_start).' segundos para a carga na tabela: '.$classDao->getTabelaName()
            .' arquivo: '.basename($arquivoCsv)
            .' lidas: '.$resultado[UploadCsv::LIDAS]
            .' gravadas: '.$resultado[UploadCsv::GRAVADAS]
            .' descartadas: '.$resultado[UploadCsv::DESCARTADAS];
        $this->quebraLinha();
        return $resultado;
    }

    /**
     * Lista, em ordem, os arquivos cujo nome contém a parte informada.
     * Arquivos .zip são ignorados.
     *
     * @param string $parteNomeArquivoCsv
     * @return array caminhos relativos à pasta de arquivos extraídos
     */
    public function listarArquivos(string $parteNomeArquivoCsv){
        $arquivos = array();
        $list = new RecursiveDirectoryIterator($this->pathExtractedFiles, FilesystemIterator::SKIP_DOTS);
        $it   = new RecursiveIteratorIterator($list);
        foreach ($it as $file) {
            if ( !$file->isFile() ) {
                continue;
            }
            $nome = strtoupper($file->getFilename());
            if ( strpos($nome, $parteNomeArquivoCsv) === false || substr($nome, -4) === '.ZIP' ) {
                continue;
            }
            $arquivos[] = $it->getSubPathName();
        }
        sort($arquivos);
        return $arquivos;
    }

    /**
     * Carrega dados de todos os arquivos que tenham parte do nome informado na classe DAO.
     *
     * @param Dao $classDao - 1: classe DAO para fazer o insert
     * @param string $parteNomeArquivoCsv - 2: nome da parte do arquivo
     * @return array [lidas, gravadas, descartadas] somando todos os arquivos
     */
    public function carregaDadosTabelaDoArquivo(Dao $classDao, string $parteNomeArquivoCsv){
        $time_start = microtime(true);
        $total = array(UploadCsv::LIDAS=>0, UploadCsv::GRAVADAS=>0, UploadCsv::DESCARTADAS=>0);
        $arquivos = $this->listarArquivos($parteNomeArquivoCsv);
        if ( empty($arquivos) ) {
            echo 'ATENÇÃO: nenhum arquivo com '.$parteNomeArquivoCsv.' no nome em '.$this->pathExtractedFiles;
            $this->quebraLinha();
        }
        foreach ($arquivos as $arquivo) {
            $resultado = $this->carregaDadosTabela($classDao, $arquivo);
            foreach ($total as $chave => $valor) {
                $total[$chave] = $valor + $resultado[$chave];
            }
        }
        echo $this->tempoFormatado($time_start).' para a carregar '.count($arquivos).' arquivo(s) na tabela '.$classDao->getTabelaName()
            .' gravadas: '.$total[UploadCsv::GRAVADAS]
            .' descartadas: '.$total[UploadCsv::DESCARTADAS];
        $this->quebraLinha();
        return $total;
    }
}
