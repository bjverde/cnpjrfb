<?php

function addArquivo($file,$conteudo=''){
    file_put_contents($file,$conteudo.PHP_EOL, FILE_APPEND);
}

//POG no PHP 8.1 filesize não funciona com Wrapper HTTP ou HTTPS. Então a solução é baixar o conteudo e trabalhar localmente
$url = 'https://www.gov.br/receitafederal/pt-br/assuntos/orientacao-tributaria/cadastros/consultas/dados-publicos-cnpj';
$conteudoRemoto = file_get_contents($url);

preg_match_all('/http:\/\/200.152.38.155\/((\w+\/?)+)(.zip)/i', $conteudoRemoto, $output_array);
$listArquivos = $output_array[0];

$file = 'download_arquivos.sh';
if( file_exists($file) ) {
    unlink($file);
}
addArquivo($file,'#!/bin/bash');
addArquivo($file);
addArquivo($file);
addArquivo($file,'#Cores');
addArquivo($file,'RED=\'\033[0;31m\'');
addArquivo($file,'LGREEN=\'\033[0;32m\'');
addArquivo($file,'YBLUE=\'\033[1;33;4;44m\'');
addArquivo($file,'NC=\'\033[0m\' # No Color');
addArquivo($file);
addArquivo($file);
addArquivo($file,'#Codigo');
addArquivo($file,'echo \'\'');
addArquivo($file,'echo -e "${YBLUE} Iniciando Download ${NC}"');
addArquivo($file,'echo \'\'');
addArquivo($file);
foreach ($listArquivos as $chave => $valor) {
    addArquivo($file, 'wget -c '.$valor);
}
echo exec('chmod +x download_arquivos.sh');

echo '<h2>Arquivo Gerado</h2>';
echo '<br>';
echo '<br>'.date("Y-m-d H:i:s");
echo '<br>Quantidade de arquivos: '.count($listArquivos);