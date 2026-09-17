<?php
class TFormDinGenericDAO
{
    private string|null $database = null;
    private string|null $repository = null;
    private TFormDinPdoConnection|null $tpdo = null;

    /**
     * Seta os elmentos basicos para conectar no banco
     *
     * @param string $database   Nome da conexão ou novo do arquivo em /app/config
     * @param string $repository Nome da Classe do tipo Active Record no diretorio /app/model/maindatabase
     * @param object $tpdo objeto do tipo TFormDinPdoConnection
     */
    public function __construct($database = null, $repository = null, $tpdo = null)
    {
        if (empty($database) && empty($tpdo)) {
            throw new InvalidArgumentException('É necessário informar $database ou $tpdo');
        }
        $this->setRepository($repository);
        if (!empty($tpdo)) {
            $this->setTPDOConnection($tpdo);
            $this->setDatabase($tpdo->getDatabase());
        } else {
            $this->setDatabase($database);
            if (!empty($repository)) {
                $this->setTPDOConnection(new TFormDinPdoConnection($database));
            }
        }
    }
    public function getTPDOConnection()
    {
        return $this->tpdo;
    }
    private function initTPDOConnection()
    {
        if ($this->tpdo === null && $this->database !== null) {
            $this->setTPDOConnection(new TFormDinPdoConnection($this->database));
        }
    }
    public function setTPDOConnection(TFormDinPdoConnection $tpdo)
    {
        //FormDinHelper::validateObjTypeTPDOConnectionObj($tpdo,__METHOD__,__LINE__);
        $this->tpdo = $tpdo;
        if (!empty($tpdo->getDatabase())) {
            $this->setDatabase($tpdo->getDatabase());
        }
    }

    /**
     * Busca o nome do banco de dados
     *
     * @return string|null
     */
    public function getDatabase()
    {
        return $this->database;
    }

    /**
     * Seta o nome do banco de dados
     *
     * @param string|null $database
     * @return void
     */
    public function setDatabase(string|null $database)
    {
        $this->database = $database;
        if ($this->tpdo !== null && $database !== null) {
            $this->tpdo->setDatabase($database);
        }
    }

    /**
     * Busca o nome do repository
     *
     * @return string|null
     */
    public function getRepository()
    {
        return $this->repository;
    }
    public function setRepository(string|null $repository)
    {
        $this->repository = $repository;
    }

    /**
     * Busca informações do banco de dados
     *
     * @return array
     */
    public function getDatabaseInfo()
    {
        $this->initTPDOConnection();
        $this->getTPDOConnection()->getDatabaseInfo();
    }
    
    /**
     * Executa comandos SQL e retorna os registros
     *
     * @param string $sql
     * @return mixed|null
     */
    public function executeSelect(string $sql)
    {
        try {
            $this->initTPDOConnection();
            $tpdo = clone $this->getTPDOConnection();
            $tpdo->setFech(PDO::FETCH_ASSOC);
            $tpdo->setOutputFormat(ArrayHelper::TYPE_PDO);
            $tpdo->setCase(PDO::CASE_NATURAL);
            return $tpdo->executeSql($sql);
        } catch (Exception $e) {
            throw new Exception($e->getMessage());
        }
    }

    /**
     * Executa comandos SQL e retorna a quantidade de registros
     *
     * @param string $sql
     * @return mixed|null
     */
    public function executeSelectCount(string $sql)
    {
        try {
            $result = $this->executeSelect($sql);
            return ArrayHelper::get($result, 0);
        } catch (Exception $e) {
            throw new Exception($e->getMessage());
        }
    }

    /**
     * Executa comandos SQL
     *
     * @param string $sql
     * @param array $values
     * @return mixed|null
     */
    public function execute(string $sql, array $values)
    {
        try {
            $this->initTPDOConnection();
            return $this->getTPDOConnection()->executeSql($sql, $values);
        } catch (Exception $e) {
            throw new Exception($e->getMessage());
        }
    }

    /**
     * Busca arrays baseado em uma criteria
     *
     * @param TCriteria $criteria
     * @param bool $showDumpLogTela
     * @return mixed|null
     */
    public function getArrayByCriteria(TCriteria $criteria, bool $showDumpLogTela = false)
    {
        try {
            $this->initTPDOConnection();
            $tpdo = $this->getTPDOConnection();
            return $tpdo->selectByTCriteria($criteria, $this->getRepository(), $showDumpLogTela);
        } catch (Exception $e) {
            throw new Exception($e->getMessage());
        }
    }

    /**
     * Busca objetos baseados em uma criteria
     *
     * @param TCriteria $criteria
     * @param bool $showDumpLogTela
     * @return mixed|null
     */
    public function getListObjByCriteria(TCriteria $criteria, bool $showDumpLogTela = false)
    {
        try {
            $this->initTPDOConnection();
            $tpdo = $this->getTPDOConnection();
            return $tpdo->selectByTCriteria($criteria, $this->getRepository(), $showDumpLogTela);
        } catch (Exception $e) {
            throw new Exception($e->getMessage());
        }
    }

    /**
     * Conta registros baseado em uma criteria
     *
     * @param TCriteria $criteria
     * @param bool $showDumpLogTela
     * @return mixed|null
     */
    public function getCountByCriteria(TCriteria $criteria, bool $showDumpLogTela = false)
    {
        try {
            TTransaction::open($this->getDatabase());

            //Mostra SQL na tela
            if ($showDumpLogTela == true) {
                TTransaction::dump( /* '/tmp/log.txt' */);
                TTransaction::setLoggerFunction(function ($message) {
                    echo $message . '<br>';
                });
            }

            //load using repository
            $repository = new TRepository($this->getRepository());
            $count = $repository->count($criteria); 
            TTransaction::close();
            return $count;
        } catch (Exception $e) {
            throw new Exception($e->getMessage());
        }
    }    
}//fim classe