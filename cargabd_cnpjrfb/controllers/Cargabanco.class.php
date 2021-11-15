<?php

class Cargabanco
{    
    private $cnaeDAO = null;
    private $is_cli  = false;

    public function __construct()
    {
        $tpdo = New TPDOConnection();
        $tpdo::connect();
        $this->cnaeDAO = new CnaeDAO($tpdo);
        
        if (php_sapi_name() == "cli") {
            $this->is_cli = true;
        }
    }
    public function executar(){
        try{
            $this->quebraLinha();
            $this->quebraLinha();
            echo 'Esse procedimento vai apagar todo o banco e carregar com os dados dos arquivos CSV';
            $this->quebraLinha();
            $this->quebraLinha();
            $this->truncateDados();
            
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
    }
    public function truncateTabela(Dao $classDao){
        $qtd = $classDao->selectCount();
        if( $qtd==0 ){
            echo 'Tabela: '.$classDao->getTabelaName().' tem ZERO registros, não rodou truncate';
        }else{
            echo 'Tabela: '.$classDao->getTabelaName().' com '.$qtd.' registros apagados';
        }
        $this->quebraLinha();
    }
}