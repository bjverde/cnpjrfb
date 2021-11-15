<?php

class Cargabanco
{    
    private $tpdo = null;

    public function __construct()
    {
        $tpdo = New TPDOConnection();
        $tpdo::connect();
        $this->tpdo = $tpdo;
    }

    public function executar(){
        echo 'Esse procedimento vai apagar todo o banco e carregar com os dados dos arquivos CSV';        
    }

    public function dropDados(){
        
    }
}