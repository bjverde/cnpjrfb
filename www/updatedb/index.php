<?php

$url = 'https://www.gov.br/receitafederal/pt-br/assuntos/orientacao-tributaria/cadastros/consultas/dados-publicos-cnpj';
$conteudo = file_get_contents($url);

echo $conteudo;