<?php
/**
 * FormDin or Dynamic Form is a php Framework for creating web system quickly and easily.
 *
 * @author  Bjverde <bjverde@yahoo.com.br>
 * @license https://github.com/bjverde/formDin/blob/master/LICENSE GPL-3.0
 * @link    https://github.com/bjverde/formDin
 *
 * PHP Version 5.6
 */
class UploadException extends Exception {
    public function __construct($code) {
        $message = self::codeToMessage($code);
        parent::__construct($message, $code);
    }
    
    public static function codeToMessage($code) {
        switch ($code) {
            case UPLOAD_ERR_INI_SIZE:
                $message = "The uploaded file exceeds the upload_max_filesize directive in php.ini";
                break;
            case UPLOAD_ERR_FORM_SIZE:
                $message = "The uploaded file exceeds the MAX_FILE_SIZE directive that was specified in the HTML form";
                break;
            case UPLOAD_ERR_PARTIAL:
                $message = "The uploaded file was only partially uploaded";
                break;
            case UPLOAD_ERR_NO_FILE:
                $message = "No file was uploaded";
                break;
            case UPLOAD_ERR_NO_TMP_DIR:
                $message = "Missing a temporary folder";
                break;
            case UPLOAD_ERR_CANT_WRITE:
                $message = "Failed to write file to disk";
                break;
            case UPLOAD_ERR_EXTENSION:
                $message = "File upload stopped by extension";
                break;
                
            default:
                $message = "Unknown upload error";
                break;
        }
        return $message;
    }
} 

?>