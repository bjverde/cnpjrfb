<?php

require_once 'filtroHref.class.php';

$url = 'https://www.gov.br/receitafederal/pt-br/assuntos/orientacao-tributaria/cadastros/consultas/dados-publicos-cnpj';
$conteudoTotal = file_get_contents($url);

//POG no PHP 8.1 filesize não funciona com Wrapper HTTP ou HTTPS
$tamanho = strlen($conteudoTotal);

$conteudo = fopen($url, "r");
stream_filter_register('alura.partes',filtroHref::class);
stream_filter_append($conteudo,'alura.partes');

$conteudoLimpo = fread($conteudo,$tamanho);

var_dump($conteudoLimpo);