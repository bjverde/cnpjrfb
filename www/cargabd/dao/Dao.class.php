<?php
/*
 * CargaBd_CnpjRFB
 * Criado por Reinaldo A. Barrêto Jr
 * https://github.com/bjverde/cnpjrfb
 *
 * Classe base dos DAOs da carga. Cada DAO informa a tabela, as colunas na
 * mesma ordem do arquivo CSV da RFB e quais colunas são datas. O INSERT é
 * preparado uma única vez e reaproveitado em todas as linhas.
 * ----------------------------------------------------------------------------
 */
class Dao
{
    private $tpdo = null;
    private $tabela = null;
    private $colunas = array();
    private $colunasData = array();
    private $colunaContagem = null;
    private $stmtInsert = null;

    /**
     * Colunas de texto NOT NULL que podem vir vazias no arquivo e devem ser
     * gravadas como '' (as demais colunas vazias são gravadas como NULL).
     */
    private $colunasManterVazio = array('descricao');

    /**
     * @param TPDOConnection $tpdo
     * @param string $tabela         - nome da tabela sem o schema
     * @param array  $colunas        - colunas na ordem do arquivo CSV
     * @param array  $colunasData    - colunas que vêm no formato AAAAMMDD
     * @param string $colunaContagem - coluna usada no count(...)
     */
    public function __construct($tpdo=null, $tabela=null, array $colunas=array(), array $colunasData=array(), $colunaContagem='codigo')
    {
        $this->setTPDOConnection($tpdo);
        $this->tabela = $tabela;
        $this->colunas = $colunas;
        $this->colunasData = $colunasData;
        $this->colunaContagem = $colunaContagem;
    }
    public function getTPDOConnection()
    {
        return $this->tpdo;
    }
    public function setTPDOConnection($tpdo)
    {
        $this->tpdo = $tpdo;
    }
    /**
     * Nome da tabela com o schema configurado em config.php (db_schema)
     */
    public function getTabelaName()
    {
        return ConfigHelper::getDbSchemaPrefix().$this->tabela;
    }
    public function getColunas()
    {
        return $this->colunas;
    }
    public function getQtdColunas()
    {
        return count($this->colunas);
    }
    //--------------------------------------------------------------------------------
    public function selectCount()
    {
        $sql = 'select count('.$this->colunaContagem.') as qtd from '.$this->getTabelaName();
        $result = $this->tpdo->executeSql($sql);
        $result = ArrayHelper::getArray($result,0);
        $result = ArrayHelper::get($result,'qtd');
        return $result;
    }
    //--------------------------------------------------------------------------------
    public function truncate()
    {
        $tabela = $this->getTabelaName();
        switch( $this->tpdo->getDBMS() ) {
            case TPDOConnection::DBMS_POSTGRES:
                //Sem CASCADE de propósito: com as FKs de modelo_postgres_fk.sql criadas, o CASCADE
                //apagaria também as tabelas filhas. As FKs devem ser criadas depois da carga.
                $sql = 'TRUNCATE TABLE '.$tabela;
            break;
            case TPDOConnection::DBMS_SQLITE:
            case TPDOConnection::DBMS_SQLSERVER:
                //SQLite não tem TRUNCATE. No SQL Server o TRUNCATE falha se houver FK.
                $sql = 'DELETE FROM '.$tabela;
            break;
            default:
                //MySQL/MariaDB: as FKs são desligadas na conexão (TPDOConnection::configurarSessao)
                //Oracle: como no PostgreSQL, crie as FKs (modelo_oracle_fk.sql) depois da carga
                $sql = 'TRUNCATE TABLE '.$tabela;
            break;
        }
        return $this->tpdo->executeSql($sql);
    }
    //--------------------------------------------------------------------------------
    /**
     * Converte uma linha do CSV nos valores que vão para o banco:
     * campo vazio vira NULL e datas AAAAMMDD inválidas ou zeradas viram NULL.
     * Os DAOs podem sobrescrever para tratar campos específicos.
     *
     * @param array $linhaArquivoCsv
     * @return array
     */
    public function transformar(array $linhaArquivoCsv)
    {
        $values = array();
        foreach ($this->colunas as $indice => $coluna) {
            $valor = isset($linhaArquivoCsv[$indice]) ? $linhaArquivoCsv[$indice] : null;
            if ( in_array($coluna, $this->colunasData, true) ) {
                $valor = ValidarHelper::validarData($valor);
            } elseif ( $valor === '' && !in_array($coluna, $this->colunasManterVazio, true) ) {
                $valor = null;
            }
            $values[] = $valor;
        }
        return $values;
    }
    //--------------------------------------------------------------------------------
    public function getSqlInsert()
    {
        $placeholders = implode(',', array_fill(0, $this->getQtdColunas(), '?'));
        return 'INSERT INTO '.$this->getTabelaName()
              .' ('.implode(',', $this->colunas).') VALUES ('.$placeholders.')';
    }
    /**
     * Grava uma linha do CSV. Lança exceção (PDOException) se o banco recusar.
     *
     * @param array $linhaArquivoCsv
     * @return boolean
     */
    public function insert( array $linhaArquivoCsv )
    {
        if ( is_null($this->stmtInsert) ) {
            $this->stmtInsert = $this->tpdo->getInstance()->prepare( $this->getSqlInsert() );
        }
        $values = $this->transformar($linhaArquivoCsv);
        $this->stmtInsert->execute($values);
        return true;
    }
}
