<?php

class filtroHref extends php_user_filter
{
    public $stream;
    public function onCreate(): bool
    {
        $this->stream = fopen( 'php://temp','w+');
        return $this->stream !== false;
    }
    public function filter($in, $out, &$consumed, bool $closing): int
    {
        $saida = '';
        while($bucket = stream_bucket_make_writeable($in)){
            $linhas = explode(PHP_EOL, $bucket->data); 
            foreach ($linhas as $linha) {
                if (stripos($linha, '.zip') !== false){
                    $saida .= "$linha" . PHP_EOL;
                }
            }
        }
        $bucketSaida = stream_bucket_new($this->stream, $saida);
        stream_bucket_append($out, $bucketSaida);
        return PSFS_PASS_ON;
    }
}