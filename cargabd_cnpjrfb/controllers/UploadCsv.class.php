<?php

class UploadCsv {

    private $dao    = null;
    private $arquivoCsv    = null;

    public function __construct(Dao $classDao, string $arquivoCsv)
    {
       $this->dao = $classDao;
       $this->arquivoCsv = $arquivoCsv;
    }

	public function executar(){
        $separador = ';';
        $file = fopen($this->arquivoCsv, 'r');
        while ( ($line = fgets ($file)) !== false ){
            //Limpando a linha
            $line = StringHelper::str2utf8($line);
            $line = preg_replace('/["]/', '', $line);
            $line = strtr($line, chr(13), chr(32));// For CR
            $line = strtr($line, chr(10), chr(32));// For LF
            $line = explode($separador, $line);
            $this->dao->insert( $line );
        }
        fclose($file);
		return true;
	}
}