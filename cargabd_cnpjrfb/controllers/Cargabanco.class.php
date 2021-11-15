<?php

class Cargabanco
{    
    private $cnaeDAO = null;

    public function __construct()
    {
        $tpdo = New TPDOConnection();
        $tpdo::connect();
        $this->cnaeDAO = new CnaeDAO($tpdo);
    }

    public function executar(){
        try{
            echo 'Esse procedimento vai apagar todo o banco e carregar com os dados dos arquivos CSV';
            echo '<br>';
            $this->truncateDados();
        }
        catch (Exception $e) {
            print($e->getMessage());
        }
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
    }
}