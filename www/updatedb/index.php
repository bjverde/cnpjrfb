<?php

require_once 'filtroHref.class.php';

//POG no PHP 8.1 filesize não funciona com Wrapper HTTP ou HTTPS. Então a solução é baixar o conteudo e trabalhar localmente
$url = 'https://www.gov.br/receitafederal/pt-br/assuntos/orientacao-tributaria/cadastros/consultas/dados-publicos-cnpj';
$conteudoRemoto = file_get_contents($url);

preg_match_all('/http:\/\/200.152.38.155\/((\w+\/?)+)(.zip)/i', $conteudoRemoto, $output_array);
$listArquivos = $output_array[0];

$file = 'listArquivo.txt';
if( file_exists($file) ) {
    unlink($file);
}
foreach ($listArquivos as $chave => $valor) {
    file_put_contents($file, 'wget -c '.$valor.PHP_EOL, FILE_APPEND);
}

echo '<pre>';
var_dump($listArquivos);
echo '</pre>';