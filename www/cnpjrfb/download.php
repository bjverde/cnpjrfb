<?php
if (isset($_GET['file']))
{
    $file = $_GET['file'];
    
    // must be inside the application
    if (strpos(realpath($file), __DIR__) === false)
    {
        return;
    }
    
    if (!file_exists($file))
    {
        return;
    }
    
    // reserved path
    if (substr($file,0,12) === 'files/system')
    {
        return;
    }
    
    $info      = pathinfo($file);
    $extension = $info['extension'];
    
    $content_type_list = array();
    $content_type_list['txt']  = 'text/plain';
    $content_type_list['html'] = 'text/html';
    $content_type_list['csv']  = 'text/csv';
    $content_type_list['pdf']  = 'application/pdf';
    $content_type_list['rtf']  = 'application/rtf';
    $content_type_list['doc']  = 'application/msword';
    $content_type_list['docx'] = 'application/vnd.openxmlformats-officedocument.wordprocessingml.document';
    $content_type_list['xls']  = 'application/vnd.ms-excel';
    $content_type_list['xlsx'] = 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet';
    $content_type_list['ppt']  = 'application/vnd.ms-powerpoint';
    $content_type_list['pptx'] = 'application/vnd.openxmlformats-officedocument.presentationml.presentation';
    $content_type_list['odt']  = 'application/vnd.oasis.opendocument.text';
    $content_type_list['ods']  = 'application/vnd.oasis.opendocument.spreadsheet';
    $content_type_list['jpeg'] = 'image/jpeg';
    $content_type_list['jpg']  = 'image/jpeg';
    $content_type_list['png']  = 'image/png';
    $content_type_list['gif']  = 'image/gif';
    $content_type_list['svg']  = 'image/svg+xml';
    $content_type_list['xml']  = 'application/xml';
    $content_type_list['zip']  = 'application/zip';
    $content_type_list['rar']  = 'application/x-rar-compressed';
    $content_type_list['bz']   = 'application/x-bzip';
    $content_type_list['bz2']  = 'application/x-bzip2';
    $content_type_list['tar']  = 'application/x-tar';
    
    if (file_exists($file) AND in_array(strtolower($extension), array_keys($content_type_list)))
    {
        $basename = !empty($_GET['basename']) ? $_GET['basename'] : basename($file);
        $filesize = filesize($file); // get the filesize
        
        header("Pragma: public");
        header("Expires: 0"); // set expiration time
        header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
        header("Content-type: " . $content_type_list[strtolower($extension)] );
        header("Content-Length: {$filesize}");
        header("Content-disposition: inline; filename=\"{$basename}\"");
        header("Content-Transfer-Encoding: binary");
        
        // a readfile da problemas no internet explorer
        // melhor jogar direto o conteudo do arquivo na tela
        echo file_get_contents($file);
    }
}
