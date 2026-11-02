<?php

class ValidarHelper
{

    public static function validarExtensaoCarregada($extensao=null)
    {
        if (!extension_loaded($extensao)) {
            throw new InvalidArgumentException('ERRO: extensão do PHP '.$extensao.' não está instalada!');
        }
    }
    /**
     * Confere a configuração antes da carga. Lança exceção se algo estiver errado,
     * para a carga não começar (e apagar as tabelas) com a configuração quebrada.
     */
    public static function InformacoesIniciais()
    {
        self::validarExtensaoCarregada('mbstring');
        self::validarExtensaoCarregada('pdo');
        TPDOConnection::validarDrive();
        TPDOConnection::validarDBMS();
        ConfigHelper::getExtractedFilesPath();
    }
    /**
     * Recebe a data no formato AAAAMMDD do arquivo da RFB e devolve AAAA-MM-DD.
     * Data vazia, zerada (0 ou 00000000) ou inválida vira NULL.
     *
     * @param string|null $data
     * @return string|null
     */
    public static function validarData($data)
    {
        $data = is_null($data) ? '' : trim($data);
        if ( !preg_match('/^(\d{4})(\d{2})(\d{2})$/', $data, $partes) ) {
            return null;
        }
        if ( !checkdate((int) $partes[2], (int) $partes[3], (int) $partes[1]) ) {
            return null;
        }
        return $partes[1].'-'.$partes[2].'-'.$partes[3];
    }
    /**
     * Códigos de dois dígitos do layout (situacao_cadastral, porte_empresa).
     * O arquivo pode trazer '2' ou '02'; o banco grava sempre '02'.
     *
     * @param string|null $codigo
     * @return string|null
     */
    public static function codigoDoisDigitos($codigo)
    {
        if ( is_null($codigo) || $codigo === '' ) {
            return $codigo;
        }
        if ( ctype_digit($codigo) && strlen($codigo) < 2 ) {
            $codigo = str_pad($codigo, 2, '0', STR_PAD_LEFT);
        }
        return $codigo;
    }
}
