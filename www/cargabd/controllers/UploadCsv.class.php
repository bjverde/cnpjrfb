<?php
/*
 * CargaBd_CnpjRFB
 * https://github.com/bjverde/cnpjrfb
 *
 * Lê um arquivo CSV da RFB (separador ';', campos entre aspas, ISO-8859-1)
 * e grava as linhas usando o DAO, em transações de N linhas.
 * ----------------------------------------------------------------------------
 */
class UploadCsv {

    const LIDAS = 'lidas';
    const GRAVADAS = 'gravadas';
    const DESCARTADAS = 'descartadas';

    private $dao    = null;
    private $arquivoCsv    = null;
    private $linhasPorTransacao = null;

    public function __construct(Dao $classDao, string $arquivoCsv, $linhasPorTransacao = null)
    {
       $this->dao = $classDao;
       $this->arquivoCsv = $arquivoCsv;
       $this->linhasPorTransacao = empty($linhasPorTransacao) ? ConfigHelper::getLinhasPorTransacao() : (int) $linhasPorTransacao;
    }

    /**
     * Converte o campo para UTF-8. Os arquivos da RFB vêm em ISO-8859-1;
     * se o campo já for UTF-8 válido (ou só ASCII) fica como está.
     */
    public static function campoParaUtf8($campo)
    {
        if ( is_null($campo) || mb_check_encoding($campo, 'UTF-8') ) {
            return $campo;
        }
        return mb_convert_encoding($campo, 'UTF-8', 'ISO-8859-1');
    }

    /**
     * Grava o arquivo no banco.
     * Linhas com quantidade de colunas diferente do layout são descartadas e
     * registradas no error_log. Erro do banco interrompe a carga com a
     * linha do arquivo e desfaz a transação em andamento.
     *
     * @return array [lidas, gravadas, descartadas]
     */
	public function executar(){
        $resultado = array(self::LIDAS=>0, self::GRAVADAS=>0, self::DESCARTADAS=>0);
        $qtdColunas = $this->dao->getQtdColunas();
        $tpdo = $this->dao->getTPDOConnection();

        $file = fopen($this->arquivoCsv, 'r');
        if ( $file === false ) {
            throw new InvalidArgumentException('ERRO: não foi possível abrir '.$this->arquivoCsv);
        }
        $numLinha = 0;
        $naTransacao = 0;
        try {
            $tpdo->beginTransaction();
            //escape '' : o arquivo da RFB não usa barra invertida como escape
            while ( ($campos = fgetcsv($file, 0, ';', '"', '')) !== false ){
                $numLinha++;
                if ( $campos === array(null) ) {
                    continue; //linha em branco
                }
                $resultado[self::LIDAS]++;
                if ( count($campos) != $qtdColunas ) {
                    $resultado[self::DESCARTADAS]++;
                    error_log('Linha descartada ('.count($campos).' colunas, esperado '.$qtdColunas.'): '
                             .$this->arquivoCsv.' linha '.$numLinha);
                    continue;
                }
                $campos = array_map(array('UploadCsv','campoParaUtf8'), $campos);
                try {
                    $this->dao->insert( $campos );
                } catch (PDOException $e) {
                    throw new RuntimeException('ERRO ao gravar '.$this->dao->getTabelaName()
                                              .' arquivo '.$this->arquivoCsv.' linha '.$numLinha
                                              .': '.$e->getMessage(), 0, $e);
                }
                $resultado[self::GRAVADAS]++;
                $naTransacao++;
                if ( $naTransacao >= $this->linhasPorTransacao ) {
                    $tpdo->commit();
                    $tpdo->beginTransaction();
                    $naTransacao = 0;
                }
            }
            $tpdo->commit();
        } catch (Exception $e) {
            $tpdo->rollBack();
            fclose($file);
            throw $e;
        }
        fclose($file);
		return $resultado;
	}
}
