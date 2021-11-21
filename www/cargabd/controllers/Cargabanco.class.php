<?php

class Cargabanco
{    
    private $is_cli  = false;
    private $pathExtractedFiles  = null;
    
    private $cnaeDAO  = null;
    private $qualsDAO = null;

    public function __construct()
    {
        if (php_sapi_name() == "cli") {
            $this->is_cli = true;
        }
        $this->pathExtractedFiles=ConfigHelper::getExtractedFilesPath();
        $tpdo = New TPDOConnection();
        $tpdo::connect();

        $this->cnaeDAO  = new CnaeDAO($tpdo);
        $this->motiDAO  = new MotiDAO($tpdo);
        $this->municDAO = new MunicDAO($tpdo);
        $this->natjuDAO = new NatjuDAO($tpdo);
        $this->paisDAO  = new PaisDAO($tpdo);
        $this->qualsDAO = new QualsDAO($tpdo);
        $this->estabelecimentoDAO = new EstabelecimentoDAO($tpdo);
        $this->simplesDAO = new SimplesDAO($tpdo);
        $this->sociosDAO = new SociosDAO($tpdo);
        $this->empresaDAO = new EmpresaDAO($tpdo);
    }
    public function executar(){
        try{
            $this->quebraLinha();
            $this->quebraLinha();
            echo 'Esse procedimento vai apagar todo o banco e carregar com os dados dos arquivos CSV';
            $this->quebraLinha();
            $this->quebraLinha();
            $this->truncateDados();
            $this->carregaDados();
        }
        catch (Exception $e) {
            print($e->getMessage());
        }
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
    public function truncateDados(){
        $time_start = microtime(true);
        $this->truncateTabela($this->empresaDAO);
        $this->truncateTabela($this->sociosDAO);
        $this->truncateTabela($this->simplesDAO);
        $this->truncateTabela($this->estabelecimentoDAO);
        $this->truncateTabela($this->cnaeDAO);
        $this->truncateTabela($this->motiDAO);
        $this->truncateTabela($this->municDAO);
        $this->truncateTabela($this->natjuDAO);
        $this->truncateTabela($this->paisDAO);
        $this->truncateTabela($this->qualsDAO);
        $time_end = microtime(true);
        $time = $time_end - $time_start; //calculate the difference between start and stop
        $time = number_format($time, 3, ',', '.');
        echo "Tempo total em segundos para todos os truncates: $time";
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
    public function carregaDados(){
        $this->quebraLinha();
        echo "---- Carga dos dados ----";
        $this->quebraLinha();
        $time_start = microtime(true);
        $this->carregaDadosTabelaDoArquivo($this->cnaeDAO,'CNAECSV');
        $this->carregaDadosTabelaDoArquivo($this->motiDAO,'MOTICSV');
        $this->carregaDadosTabelaDoArquivo($this->municDAO,'MUNICCSV');
        $this->carregaDadosTabelaDoArquivo($this->natjuDAO,'NATJUCSV');
        $this->carregaDadosTabelaDoArquivo($this->paisDAO,'PAISCSV');
        $this->carregaDadosTabelaDoArquivo($this->qualsDAO,'QUALSCSV');
        $this->carregaDadosTabelaDoArquivo($this->estabelecimentoDAO,'ESTABELE');
        $this->carregaDadosTabelaDoArquivo($this->sociosDAO,'SOCIOCSV');
        $this->carregaDadosTabelaDoArquivo($this->empresaDAO,'EMPRECSV');
        $this->carregaDadosTabelaDoArquivo($this->simplesDAO,'SIMPLES');
        $time_end = microtime(true);
        $time = $time_end - $time_start; //calculate the difference between start and stop
        $time = number_format($time, 3, ',', '.');
        echo "Tempo total em segundos para toda as cargas: $time";
        $this->quebraLinha();
    }

    /**
     * Carrega dados de UM arquivos na classe DAO.
     *
     * @param Dao $classDao
     * @param string $arquivoCsv
     * @return void
     */
    public function carregaDadosTabela(Dao $classDao, string $arquivoCsv){
        $time_start = microtime(true);
        $arquivoCsv = $this->pathExtractedFiles.DS.$arquivoCsv;
        if (!file_exists($arquivoCsv)){
            throw new InvalidArgumentException('ERRO: o arquivo '.$arquivoCsv.' não encontrado');
        }
        $uploadCsv = new UploadCsv($classDao,$arquivoCsv);
        $numRegistros = $uploadCsv->executar();
        $time_end = microtime(true);
        $time = $time_end - $time_start; //calculate the difference between start and stop
        $time = number_format($time, 3, ',', '.');
        echo $time.' segundos para a carga na tabela: '.$classDao->getTabelaName().' quantidade de registros: '.$numRegistros;
        $this->quebraLinha();
        return $numRegistros;
    }

    /**
     * Carrega dados de todos os arquivos que tenham parte do nome informado na classe DAO.
     *
     * @param Dao $classDao - 1: classe DAO para fazer o insert
     * @param string $parteNomeArquivoCsv - 2: nome da parte do arquivo
     * @return void
     */
    public function carregaDadosTabelaDoArquivo(Dao $classDao, string $parteNomeArquivoCsv){
        $time_start = microtime(true);
        $qtdArquivos = 0;
        $numRegistrosTotal = 0;
        $numRegistros = 0;

        $list = new RecursiveDirectoryIterator($this->pathExtractedFiles);
        $it   = new RecursiveIteratorIterator($list);
        foreach ($it as $file) {
            if ($it->isFile()) {
                $temParteArquivo = str_contains($it->getSubPathName(),$parteNomeArquivoCsv);
                if($temParteArquivo){
                    $qtdArquivos = $qtdArquivos + 1;
                    $numRegistros = $this->carregaDadosTabela($classDao,$it->getSubPathName());
                    $numRegistrosTotal = $numRegistrosTotal +  $numRegistros;
                }
            }
        }//FIM foreach
        $tempoFormatado = $this->tempoFormatado($time_start);
        echo $tempoFormatado.' para a carregar '.$qtdArquivos.' arquivo(s) na tabela '.$parteNomeArquivoCsv.' quantidade de registros: '.$numRegistrosTotal;
        $this->quebraLinha();
    }
}