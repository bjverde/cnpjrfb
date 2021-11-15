<?php

class Cargabanco
{    
    private $is_cli  = false;
    private $path    = null;
    
    private $cnaeDAO  = null;
    private $qualsDAO = null;

    public function __construct()
    {
        if (php_sapi_name() == "cli") {
            $this->is_cli = true;
        }
        $this->path=ConfigHelper::getExtractedFilesPath();
        $tpdo = New TPDOConnection();
        $tpdo::connect();

        $this->cnaeDAO = new CnaeDAO($tpdo);
        $this->cnaeDAO->setNomeArquivoCsv('F.K03200$Z.D11009.CNAECSV');

        $this->qualsDAO = new QualsDAO($tpdo);
        $this->qualsDAO->setNomeArquivoCsv('F.K03200$Z.D11009.QUALSCSV');
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
    public function truncateDados(){
        $this->truncateTabela($this->cnaeDAO);
        $this->truncateTabela($this->qualsDAO);
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
        $this->carregaDadosTabela($this->cnaeDAO);
        $this->carregaDadosTabela($this->qualsDAO);
    }
    public function carregaDadosTabela(Dao $classDao){
        $arquivoCsv = $this->path.DS.$classDao->getNomeArquivoCsv();
        if (!file_exists($arquivoCsv)){
            throw new InvalidArgumentException('ERRO: o arquivo '.$arquivoCsv.' não encontrado');
        }
        $uploadCsv = new UploadCsv($classDao,$arquivoCsv);
        $uploadCsv->executar();
    }    
}