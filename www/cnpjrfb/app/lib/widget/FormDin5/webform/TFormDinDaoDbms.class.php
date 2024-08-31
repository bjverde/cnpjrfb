<?php
/*
 * ----------------------------------------------------------------------------
 * Formdin 5 Framework
 * SourceCode https://github.com/bjverde/formDin5
 * @author Reinaldo A. Barrêto Junior
 * 
 * É uma reconstrução do FormDin 4 Sobre o Adianti 7.X
 * @author Luís Eugênio Barbosa do FormDin 4
 * 
 * Adianti Framework é uma criação Adianti Solutions Ltd
 * @author Pablo Dall'Oglio
 * ----------------------------------------------------------------------------
 * This file is part of Formdin Framework.
 *
 * Formdin Framework is free software; you can redistribute it and/or
 * modify it under the terms of the GNU Lesser General Public License version 3
 * as published by the Free Software Foundation.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU Lesser General Public License version 3
 * along with this program; if not,  see <http://www.gnu.org/licenses/>
 * or write to the Free Software Foundation, Inc., 51 Franklin Street,
 * Fifth Floor, Boston, MA  02110-1301, USA.
 * ----------------------------------------------------------------------------
 * Este arquivo é parte do Framework Formdin.
 *
 * O Framework Formdin é um software livre; você pode redistribuí-lo e/ou
 * modificá-lo dentro dos termos da GNU LGPL versão 3 como publicada pela Fundação
 * do Software Livre (FSF).
 *
 * Este programa é distribuído na esperança que possa ser útil, mas SEM NENHUMA
 * GARANTIA; sem uma garantia implícita de ADEQUAÇÃO a qualquer MERCADO ou
 * APLICAÇÃO EM PARTICULAR. Veja a Licença Pública Geral GNU/LGPL em português
 * para maiores detalhes.
 *
 * Você deve ter recebido uma cópia da GNU LGPL versão 3, sob o título
 * "LICENCA.txt", junto com esse programa. Se não, acesse <http://www.gnu.org/licenses/>
 * ou escreva para a Fundação do Software Livre (FSF) Inc.,
 * 51 Franklin St, Fifth Floor, Boston, MA 02111-1301, USA.
 */

class TFormDinDaoDbms
{
    private $connection = null;
	private $schema      = null;
	private $utf8        = null;
	private $error		= null;
	private $conn		= null;
	private $tableName	= null;
	private $fields		= null;
	private $specialChars = array();
	private $charset = null;
	private $metadataDir = null;
	private $primaryKeys = null;
	private $autoincFieldName = null;
	private $lastId      = null;
	private $autoCommit  = null;
	private $hasActiveTransaction = false;
	private $sqlCmd		= null;
	private $sqlParams	= null;
	private $cursor		= null;
	private $eof		= null;
	private $type		= null;

	/**
	* Classe para pegar metadatos sobre diversos banco de dados.
	*
	* @param [type] $tableName - 1: nome da tabela
	* @param [type] $type      - 2: Tipo de banco de dados conforme TFormDinPdoConnection
	* @param [type] $user      - 3: usuario do banco
	* @param [type] $pass      - 4: senha do usuario no SGBD
	* @param [type] $name      - 5: nome da bando de dados ou arquivo
	* @param [type] $host      - 6: nome ou ip do servidor
	* @param [type] $port      - 7: porta padrão
	* @param [type] $strSchema
	*/
	public function __construct(  $tableName = null
								, $type = null
								, $user = null
								, $pass = null
								, $name = null
								, $host = null
								, $port = null
								, $strSchema = null)
	{
		$this->setTableName( $tableName );
		$this->setType($type);
		$this->setSchema( $strSchema );

		$connection = new TFormDinPdoConnection();
		$connection->setType($type);
		$connection->setUser($user);
		$connection->setPass($pass);
		$connection->setName($name);
		$connection->setHost($host);
		$connection->setPort($port);
		$this->setConnection($connection);
	}

	public function getType()
    {
        return $this->type;
	}
	/**
	 * Define o tipo do banco de dados que será acessado.
	 * Os tipos de banco de dados suportados atualmente estão em
	 * TFormDinPdoConnection::getListDBMS()
	 * 
	 * @param string $type
	 * @return void
	 */
    public function setType($type)
    {
        $listType = TFormDinPdoConnection::getListDBMS();
        $inArray = ArrayHelper::has($type,$listType);
        if (!$inArray) {
            throw new InvalidArgumentException('Type DBMS is not value valid');
        }
        $this->type = $type;
    }

    public function getConnection()
    {
        return $this->connection;
	}
    public function setConnection($connection)
	{
		if(!($connection instanceof TFormDinPdoConnection)){
			throw new InvalidArgumentException(TFormDinMessage::ERROR_OBJ_TYPE_WRONG);
		}
        $this->connection = $connection;
    }

	/**
	* Define o nome do esquema dentro do banco de dados que deverã ser utilizado.
	* Este método aplica somente ao banco de dados postgres
	* Quando informado será adicionado ao path do banco de dados
	*
	* @param string $strNewValue
	*/
	public function setSchema( $strNewValue = null )
	{
		$this->schema=$strNewValue;
	}

	/**
	* Retorna o nome do esquema do banco de dados que será utilizado. Aplica-se somente
	* ao banco de dados postgres.
	*
	* Quando informado será adicionado ao path do banco de dados
	*
	* @return string
	*/
	public function getSchema()
	{
		if( $this->conn )
		{
			return $this->getConnSchema();
		}
		return $this->schema;
	}

	/**
	 * @codeCoverageIgnore
	 * Retorna um array com os dados
     * @param const $outputFormat - 2: Type OutPut Format. Default = ArrayHelper::TYPE_ADIANTI, ArrayHelper::TYPE_PDO, ArrayHelper::TYPE_FORMDIN
     * @param const $typeCase     - 3: Type Case. Default = PDO::CASE_NATURAL, PDO::CASE_UPPER, PDO::CASE_LOWER
	 * @return void
	 */
	public function executeSql($sql,$outputFormat = ArrayHelper::TYPE_PDO,$case = PDO::CASE_UPPER)
	{
		//O result vem no padrão PDO
		$this->getConnection()->setCase($case);
		$this->getConnection()->setOutputFormat($outputFormat);
		$result  = $this->getConnection()->executeSql($sql);
		return $result;
	}

	/**
	* Adiciona campos da tabela ao array de campos que serão utilizados
	* nos binds e nos métodos save, insert e delete da classe
	*
	* @param string $strFieldName
	* @param string $strFieldType
	* @param integer $intSize
	* @param integer $intPrecision
	* @param string $strDefaultValue
	* @param boolean $boolNullable
	* @param boolean $boolAutoincrement
	* @param boolean $boolPrimaryKey
	*/
	public function addField( $strFieldName, $strFieldType = null, $intSize = null, $intPrecision = null, $strDefaultValue = null, $boolNullable = null, $boolAutoincrement = null, $boolPrimaryKey = null )
	{
		$strFieldType 		=( is_null( $strFieldType ) ? 'varchar':$strFieldType);
		$boolAutoincrement	=( is_null( $boolAutoincrement ) ? 0 : $boolAutoincrement );
		$boolNullable       =( is_null( $boolNullable ) ? 1 : $boolNullable );
		$boolPrimaryKey     =( is_null( $boolPrimaryKey ) ? 0 : $boolPrimaryKey );
		$this->fields[ strtoupper( $strFieldName )]=(object)array
			(
			'fieldName'     => $strFieldName,
			'fieldType'     => $strFieldType,
			'size'          => $intSize,
			'precision'     => $intPrecision,
			'defaultValue'  => $strDefaultValue,
			'nullable'      => $boolNullable,
			'autoincrement' => $boolAutoincrement,
			'primaryKey'    => $boolPrimaryKey,
			'value'			=> null
			);
	}

	/**
	* Retorna o objeto do campo solictado
	* Se o campo não existier retorna null
	*
	* @param string $strFieldName
	*/
	public function getField( $strFieldName )
	{
		$strFieldName=strtoupper( $strFieldName );

		if ( isset( $this->fields[ $strFieldName ] ) )
		{
			return $this->fields[ $strFieldName ];
		}

		return null;
	}

	/**
	* Retorna o array de objetos dos campos da tabela
	*
	*/
	public function getFields()
	{
		return $this->fields;
	}
	/**
	* Defina a mensagem de erro
	*
	* @param string $strError
	*/
	public function setError( $strError = null )
	{
		$this->error=$strError;
	}
	/**
	* Retorna a mensagem de erro atual
	*
	*/
	public function getError()
	{
		return $this->error;
	}

	/**
	* Define o nome da tabela do banco de dados que será utizizada nos
	* comando insert, save, delete ...
	*
	* @param string $strNewValue
	*/
	public function setTableName( $strNewValue = null )
	{
		$this->tableName=$strNewValue;
	}
    /**
    * Retorna o nome da tabela que está sendo utilizada nos comandos
    * insert, delete, save ...
    *
    */
	public function getTableName()
	{
		return $this->tableName;
	}

	/**
	* Retorna o diretório/pasta onde será armazenada as informações dos campos
	* extraídos das tabela
	*
	* @param string $strNewValue
	*/
	public function setMetadataDir( $strNewValue = null )
	{
		$this->metadataDir=trim( $strNewValue ) . '/';
		$this->metadataDir=preg_replace( '/\/\//', '', $this->metadataDir ) . '/';

		if ( !is_null( $strNewValue ) && !file_exists( $strNewValue ) )
		{
			$oldumask=umask( 0 );
			@mkdir( $strNewValue, 0755, true );
			umask( $oldumask );
		}
	}
    /**
    * Retorna o nome do diretório/pasta onde serão armazendas as informações dos campos
    * das tabelas
    *
    * @return string;
    */
	public function getMetadataDir()
	{
		if ( !is_null( $this->metadataDir ) && file_exists( $this->metadataDir ) )
		{
			return preg_replace( '/\/\//', '/', $this->metadataDir . '/' );
		}

		return null;
	}

	/**
	* Serialize e salva os campos no diretório/pasta de metadados
	*
	* @return null
	*/
	public function serializeFields()
	{
		if ( $this->getMetadataDir() && $this->getTableName() )
		{
		    $filename = $this->getMetadataDir() . $this->getConnDbType() . '-' . $this->getTableName() . '.ser';
		    $data = serialize( $this->getFields() );
		    file_put_contents( $filename, $data );
		}
	}
	
	public function loadTablesFromDatabaseSqLite() {
		$sql = 'SELECT 
				\'\' as TABLE_SCHEMA
				,name as TABLE_NAME 
				,\'\' as COLUMN_QTD
				,upper(type) as TABLE_TYPE
			FROM sqlite_master where type in (\'table\', \'view\')';
		return $sql;
	}

	public function loadTablesFromDatabaseMySql() {
		$sql = "select vg.TABLE_SCHEMA
						,vg.TABLE_NAME
						,vg.COLUMN_QTD
						,vg.TABLE_TYPE
				from
				(
					select vt.TABLE_SCHEMA
							,vt.TABLE_NAME
							,count(*) as COLUMN_QTD
							,vt.TABLE_TYPE
					from
					(
						SELECT t.TABLE_SCHEMA
								,t.TABLE_NAME
								,case when upper(t.TABLE_TYPE) = 'BASE TABLE' then 'TABLE' else upper(t.TABLE_TYPE) end  as TABLE_TYPE
						FROM INFORMATION_SCHEMA.TABLES as t
							,INFORMATION_SCHEMA.COLUMNS as c
						WHERE t.TABLE_NAME = c.TABLE_NAME 
						and  t.TABLE_SCHEMA = c.TABLE_SCHEMA
						and (t.TABLE_TYPE = 'BASE TABLE' OR t.TABLE_TYPE = 'VIEW')
						and t.TABLE_SCHEMA not in ('sys','phpmyadmin','performance_schema','mysql','information_schema')
					) as vt
					group by vt.TABLE_SCHEMA
							,vt.TABLE_NAME
							,vt.TABLE_TYPE
							
					union
				
					select vp.TABLE_SCHEMA
							,vp.TABLE_NAME
							,count(*) as COLUMN_QTD
							,'PROCEDURE' as TABLE_TYPE
					from
					(
						select p.SPECIFIC_SCHEMA as TABLE_SCHEMA
								,p.SPECIFIC_NAME as TABLE_NAME
								,p.routine_type as TABLE_TYPE
						from information_schema.routines as r
						left join information_schema.parameters as p
									on p.specific_schema = r.routine_schema
									and p.specific_name = r.specific_name
						where r.routine_schema not in ('sys','phpmyadmin','information_schema','mysql', 'performance_schema')
						and p.routine_type = 'PROCEDURE'
					) as vp
					group by vp.TABLE_SCHEMA
							,vp.TABLE_NAME
							,vp.TABLE_TYPE
				) as vg
				order by 
						vg.TABLE_SCHEMA
						,vg.TABLE_TYPE
						,vg.TABLE_NAME";
		return $sql;
	}

	public function loadTablesFromDatabaseSqlServer() {
		$sql = "select 
				TABLE_SCHEMA
				,TABLE_NAME
				,COLUMN_QTD
				,TABLE_TYPE
				from (
				SELECT qtd.TABLE_SCHEMA
						,qtd.TABLE_NAME
						,qtd.COLUMN_QTD
						,case ty.TABLE_TYPE WHEN 'BASE TABLE' THEN 'TABLE' ELSE ty.TABLE_TYPE end as TABLE_TYPE
				FROM
					(SELECT TABLE_SCHEMA
							,TABLE_NAME
							,COUNT(TABLE_NAME) COLUMN_QTD
					FROM INFORMATION_SCHEMA.COLUMNS c
					where c.TABLE_SCHEMA <> 'METADADOS'
					group by TABLE_SCHEMA, TABLE_NAME
					) as qtd
					,(SELECT TABLE_SCHEMA
							, TABLE_NAME
							, TABLE_TYPE
					FROM INFORMATION_SCHEMA.TABLES i
					where I.TABLE_SCHEMA <> 'METADADOS'
					) as ty
				where qtd.TABLE_SCHEMA = ty.TABLE_SCHEMA
				and qtd.TABLE_NAME = ty.TABLE_NAME
				
				UNION
				
				SELECT Schema_name(schema_id)   AS TABLE_SCHEMA,
					SO.NAME                   AS TABLE_NAME,       
					count(*)                  AS COLUMN_QTD,
					CASE SO.type_desc 
					WHEN  'SQL_STORED_PROCEDURE' THEN 'PROCEDURE'
					ELSE 'FUNCTION' 
					END AS TABLE_TYPE	   
				FROM   sys.objects AS SO
					INNER JOIN sys.parameters AS P
							ON SO.object_id = P.object_id
				WHERE  SO.object_id IN (SELECT object_id
										FROM   sys.objects
										WHERE  type IN ( 'P', 'FN' ))
				group by schema_id, SO.NAME, SO.type_desc
				) as res
				order by res.TABLE_SCHEMA
					, res.TABLE_TYPE
					, res.TABLE_NAME";		
		return $sql;
	}

	public function loadTablesFromDatabasePostGres() {
		$sql = "SELECT qtd.TABLE_SCHEMA
						,qtd.TABLE_NAME
						,qtd.COLUMN_QTD
						,ty.TABLE_TYPE
						,case ty.TABLE_TYPE WHEN 'BASE TABLE' THEN 'TABLE' ELSE ty.TABLE_TYPE end as TABLE_TYPE
				FROM
					(SELECT TABLE_SCHEMA
							,TABLE_NAME
							,COUNT(TABLE_NAME) COLUMN_QTD
					FROM INFORMATION_SCHEMA.COLUMNS c
					where c.TABLE_SCHEMA <> 'pg_catalog' and c.TABLE_SCHEMA <> 'information_schema'
					group by TABLE_SCHEMA, TABLE_NAME
					) as qtd
					,(SELECT TABLE_SCHEMA
							, TABLE_NAME
							, TABLE_TYPE
					FROM INFORMATION_SCHEMA.TABLES i
					where I.TABLE_SCHEMA <> 'pg_catalog' and I.TABLE_SCHEMA <> 'information_schema'
					) as ty
				where qtd.TABLE_SCHEMA = ty.TABLE_SCHEMA
				and qtd.TABLE_NAME = ty.TABLE_NAME
				order by qtd.TABLE_SCHEMA, qtd.TABLE_NAME";
		return $sql;
	}

	/**
	 * Retorna a string do SQL com a lista de tabela de banco de dados
	 *
	 * @return string
	 */
	public function loadSqlTablesFromDatabase() {
		$DbType = $this->getType();
		$sql = null;
		switch( $DbType ) {
			case TFormDinPdoConnection::DBMS_SQLITE:
				$sql = $this->loadTablesFromDatabaseSqLite();
			break;
			//--------------------------------------------------------------------------------
			case TFormDinPdoConnection::DBMS_MYSQL:
				$sql = $this->loadTablesFromDatabaseMySql();
			break;
			//--------------------------------------------------------------------------------
			case TFormDinPdoConnection::DBMS_SQLSERVER:
				$sql = $this->loadTablesFromDatabaseSqlServer();
			break;
			//--------------------------------------------------------------------------------
			case TFormDinPdoConnection::DBMS_POSTGRES:
				$sql = $this->loadTablesFromDatabasePostGres();
			break;
			//--------------------------------------------------------------------------------
			default:
				throw new DomainException('Database '.$DbType.' not implemented ! TDAO->loadTablesFromDatabase. Contribute to the project https://github.com/bjverde/sysgen !');
		}
		return $sql;
	}

	/**
	 * @codeCoverageIgnore
	 * Retorna array com a lista de tabelas do banco de dados
	 * @return array
	 */
	public function loadTablesFromDatabase() {
		$sql    = $this->loadSqlTablesFromDatabase();
		$result = $this->executeSql($sql,ArrayHelper::TYPE_FORMDIN);
		return $result;
	}
	
	public function getMsSqlShema() {
	    $result = '';
	    if($this->getSchema()){
	        $result = " AND upper(c.TABLE_SCHEMA) = upper('".$this->getSchema()."') ";
	    }
	    return $result;
	}
	
	public function getSqlToFieldsFromOneStoredProcedureMySQL() {
	    $sql="select 
                	 p.parameter_name as COLUMN_NAME
                	,'FALSE' as REQUIRED
                	,r.routine_type AS DATA_TYPE
                	,p.character_maximum_length as CHAR_MAX
                	,p.numeric_precision as NUM_LENGTH
                	,p.numeric_scale as NUM_SCALE
                	,r.ROUTINE_COMMENT as COLUMN_COMMENT
                	,r.specific_name as TABLE_NAME
                	,r.routine_schema as TABLE_SCHEMA
                	,p.ordinal_position
                	,case when p.parameter_mode is null and p.data_type is not null
                				then 'RETURN'
                				else parameter_mode end as parameter_mode
                from information_schema.routines r
                left join information_schema.parameters p
                          on p.specific_schema = r.routine_schema
                          and p.specific_name = r.specific_name
                where r.routine_schema not in ('sys', 'information_schema','mysql', 'performance_schema')
                and upper(r.specific_name)  = upper('".$this->getTableName()."')
                and upper(r.routine_schema) = upper('".$this->getSchema()."')
                order by r.routine_schema,
                         r.specific_name,
                         p.ordinal_position";
	    return $sql;
	}
	
	public function getSqlToFieldsFromOneStoredProcedureSqlServer() {
	    $name = $this->getTableName();
	    $shema = $this->getSchema();
	    $sql="SELECT REPLACE(P.NAME,'@','')   AS COLUMN_NAME
                   ,'FALSE'                   AS REQUIRED
            	   ,Type_name(P.user_type_id) AS DATA_TYPE
                   ,P.max_length              AS CHAR_MAX
                   ,null                      AS NUM_LENGTH
                   ,null                      AS NUM_SCALE
                   ,null                      AS COLUMN_COMMENT
            	   ,null                      AS COLUMN_COMMENT
            	   ,null                      AS KEY_TYPE
            	   ,null                      AS REFERENCED_TABLE_NAME
            	   ,null                      AS REFERENCED_COLUMN_NAME
                   ,Schema_name(schema_id)    AS TABLE_SCHEMA
                   ,SO.NAME                   AS table_name
            FROM   sys.objects AS SO
                   INNER JOIN sys.parameters AS P
                           ON SO.object_id = P.object_id
            WHERE  SO.object_id IN (SELECT object_id
                                    FROM   sys.objects
                                    WHERE  type IN ( 'P'))
                  AND upper(SO.NAME) = upper('".$name."')
                  AND upper(Schema_name(schema_id)) = upper('".$shema."')
                  ";
	    return $sql;
	}
	
	public function getSqlToFieldsOneStoredProcedureFromDatabase() {
	    //$DbType = $this->getConnDbType();
	    $DbType = $this->getType();
	    $sql    = null;
	    $params = null;
	    $data   = null;
	    
	    // ler os campos do banco de dados
	    if ( $DbType == TFormDinPdoConnection::DBMS_MYSQL ){
	        $sql   = $this->getSqlToFieldsFromOneStoredProcedureMySQL();
	    }
	    else if( $DbType == TFormDinPdoConnection::DBMS_SQLSERVER ) {
	        $sql   = $this->getSqlToFieldsFromOneStoredProcedureSqlServer();
	        $params=array($this->getTableName());
	    }
	    $result = array();
	    $result['sql']    = $sql;
	    $result['params'] = $params;
	    $result['data']   = $data;	    
	    return $result;
	}
	
	/**
	 * Recupera as informações dos parametros de uma Storage Procedeure diretamente do banco de dados
	 * @return null
	 */
	public function loadFieldsOneStoredProcedureFromDatabase() {
	    $DbType = $this->getType();
	    if ( !$this->getTableName() ) {
	        throw new InvalidArgumentException(TFormDinMessage::ERROR_OBJ_STORED_PROC);
	    }
	    $result = $this->getSqlToFieldsOneStoredProcedureFromDatabase();
	    $sql    = $result['sql'];
	    switch( $DbType ) {
	        case TFormDinPdoConnection::DBMS_MYSQL:
	        case TFormDinPdoConnection::DBMS_SQLSERVER:
	            $result = $this->executeSql($sql,ArrayHelper::TYPE_FORMDIN);
	        break;
	        //--------------------------------------------------------------------------------
	        default:
	            throw new DomainException('Database '.$DbType.' not implemented ! '.TFormDinMessage::MSG_CONTRIB_PROJECT);
	    }
	    return $result;
	}
	
	public function getSqlToFieldsFromDatabaseMySQL() {
	    // http://dev.mysql.com/doc/refman/5.0/en/tables-table.html
	    $sql="SELECT c.column_name COLUMN_NAME
						, case when upper(c.IS_NULLABLE) = 'NO' then 'TRUE' else 'FALSE' end REQUIRED
						, c.data_type DATA_TYPE
						, c.character_maximum_length CHAR_MAX
						, c.numeric_precision NUM_LENGTH
						, c.numeric_scale NUM_SCALE
						, c.COLUMN_COMMENT
						, case when upper(c.COLUMN_KEY) = 'PRI' then 'PK' when ( upper(c.COLUMN_KEY) = 'MUL' AND k.REFERENCED_TABLE_NAME is not null ) then 'FOREIGN KEY' else 0 end  KEY_TYPE
						, case when lower(c.EXTRA) = 'auto_increment' then 1 else 0 end  AUTOINCREMENT
						, c.COLUMN_DEFAULT
						, k.REFERENCED_TABLE_NAME
						, k.REFERENCED_COLUMN_NAME
						, c.TABLE_SCHEMA
						, c.table_name
						, c.TABLE_CATALOG
				   from information_schema.columns as c
				   left join information_schema.KEY_COLUMN_USAGE as k
				   on c.TABLE_SCHEMA = k.TABLE_SCHEMA
				   and c.table_name = k.table_name
				   and c.column_name = k.column_name
				   WHERE upper(c.table_name) = upper('".$this->getTableName()."')
						 order by c.table_name
						 ,c.ordinal_position";
	    return $sql;
	}
	
	public function getSqlToFieldsFromDatabaseSqlServer() {
	    $sql="SELECT c.column_name as COLUMN_NAME
                          ,case c.IS_NULLABLE WHEN 'YES' THEN 'FALSE' ELSE 'TRUE' end as REQUIRED
                          ,c.DATA_TYPE
                          ,c.CHARACTER_MAXIMUM_LENGTH as CHAR_MAX
                          ,c.NUMERIC_PRECISION as NUM_LENGTH
                          ,c.NUMERIC_SCALE as NUM_SCALE
                    	  ,prop.value AS COLUMN_COMMENT
                    	  ,fk2.CONSTRAINT_TYPE as KEY_TYPE
                    	  ,fk2.REFERENCED_TABLE_NAME
                    	  ,fk2.REFERENCED_COLUMN_NAME
                          ,c.TABLE_SCHEMA
                          ,c.table_name
                    	  ,c.TABLE_CATALOG
                    from INFORMATION_SCHEMA.COLUMNS c
                        join sys.columns AS sc on sc.object_id = object_id(c.TABLE_SCHEMA + '.' + c.TABLE_NAME) AND sc.NAME = c.COLUMN_NAME
                        LEFT JOIN sys.extended_properties prop ON prop.major_id = sc.object_id AND prop.minor_id = sc.column_id AND prop.NAME = 'MS_Description'
                    	LEFT JOIN (
                    		SELECT CT.TABLE_CATALOG
                    			 , CT.TABLE_SCHEMA
                    			 , CT.TABLE_NAME
                    			 , CT.COLUMN_NAME
                    			 , CT.CONSTRAINT_TYPE
                    			 , FK.REFERENCED_TABLE_NAME
                    			 , FK.REFERENCED_COLUMN_NAME
                    		FROM (
                    				SELECT kcu.TABLE_CATALOG
                    					 , kcu.TABLE_SCHEMA
                    					 , kcu.TABLE_NAME
                    					 , kcu.COLUMN_NAME
                    					 , tc.CONSTRAINT_TYPE
                    					 , kcu.CONSTRAINT_NAME
                    				FROM INFORMATION_SCHEMA.KEY_COLUMN_USAGE kcu
                    					,INFORMATION_SCHEMA.TABLE_CONSTRAINTS  tc
                    				where kcu.CONSTRAINT_NAME = tc.CONSTRAINT_NAME
                    				) as CT
	        
                    			LEFT JOIN (
                    				SELECT
                    					 KCU1.CONSTRAINT_NAME AS FK_CONSTRAINT_NAME
                    					,KCU1.TABLE_NAME AS FK_TABLE_NAME
                    					,KCU1.COLUMN_NAME AS FK_COLUMN_NAME
                    					,KCU2.TABLE_NAME AS REFERENCED_TABLE_NAME
                    					,KCU2.COLUMN_NAME AS REFERENCED_COLUMN_NAME
                    				FROM INFORMATION_SCHEMA.REFERENTIAL_CONSTRAINTS AS RC
	        
                    				INNER JOIN INFORMATION_SCHEMA.KEY_COLUMN_USAGE AS KCU1
                    					ON KCU1.CONSTRAINT_CATALOG = RC.CONSTRAINT_CATALOG
                    					AND KCU1.CONSTRAINT_SCHEMA = RC.CONSTRAINT_SCHEMA
                    					AND KCU1.CONSTRAINT_NAME = RC.CONSTRAINT_NAME
	        
                    				INNER JOIN INFORMATION_SCHEMA.KEY_COLUMN_USAGE AS KCU2
                    					ON KCU2.CONSTRAINT_CATALOG = RC.UNIQUE_CONSTRAINT_CATALOG
                    					AND KCU2.CONSTRAINT_SCHEMA = RC.UNIQUE_CONSTRAINT_SCHEMA
                    					AND KCU2.CONSTRAINT_NAME = RC.UNIQUE_CONSTRAINT_NAME
                    					AND KCU2.ORDINAL_POSITION = KCU1.ORDINAL_POSITION
                    				) as FK
                    		ON CT.CONSTRAINT_NAME = FK.FK_CONSTRAINT_NAME
                    		AND CT.TABLE_NAME = FK.FK_TABLE_NAME
                    		AND CT.COLUMN_NAME = FK.FK_COLUMN_NAME
                    	) as FK2
                    	on c.TABLE_SCHEMA = FK2.TABLE_SCHEMA
                    	and c.TABLE_NAME = Fk2.TABLE_NAME
                    	and c.COLUMN_NAME = fk2.COLUMN_NAME
                    WHERE upper(c.table_name) = upper('".$this->getTableName()."')".$this->getMsSqlShema()."
                    ORDER by   c.TABLE_SCHEMA
                              ,c.TABLE_NAME
                              ,c.ORDINAL_POSITION";
	    return $sql;
	}
	
	public function getSqlToFieldsFromDatabasePostGres() {
		$sql   ="SELECT c.column_name as COLUMN_NAME
					  , case c.IS_NULLABLE WHEN 'YES' THEN 'FALSE' ELSE 'TRUE' end as REQUIRED
					  , data_type as DATA_TYPE
					  , character_maximum_length CHAR_MAX
					  , coalesce(numeric_precision, datetime_precision) as NUM_LENGTH
					  , numeric_scale as NUM_SCALE
					  , des.description COLUMN_COMMENT
					  , refe.KEY_TYPE
					  , column_default COLUMN_DEFAULT
					  , refe.REFERENCED_TABLE_NAME
					  , refe.REFERENCED_COLUMN_NAME					  
					  , position('nextval(' in column_default)=1 as AUTOINCREMENT
					  , c.TABLE_SCHEMA
					  , c.table_name
					  , c.TABLE_CATALOG
					FROM information_schema.columns as c
						 left join (SELECT  st.schemaname as table_schema
							              , st.relname as table_name
							              , pgd.objsubid
							              , pgd.description
							         FROM pg_catalog.pg_statio_all_tables as st
							         inner join pg_catalog.pg_description pgd on (pgd.objoid=st.relid)
							       ) as des
						 on (des.objsubid=c.ordinal_position and  des.table_schema = c.table_schema and des.table_name = c.table_name)
						 left join (SELECT
										  tc.table_schema
										, tc.table_name
										, kcu.column_name
										, tc.constraint_name	
										, tc.constraint_type
										, case when upper(tc.constraint_type) = 'PRIMARY KEY' THEN  'PK' 
											   when upper(tc.constraint_type) = 'FOREIGN KEY' THEN 'FOREIGN KEY' 
											   ELSE tc.constraint_type
											   END  as KEY_TYPE	
										, ccu.table_schema AS REFERENCED_TABLE_SCHEMA
										, ccu.table_name AS REFERENCED_TABLE_NAME
										, ccu.column_name AS REFERENCED_COLUMN_NAME 
									FROM 
										information_schema.table_constraints AS tc 
										JOIN information_schema.key_column_usage AS kcu
										  ON tc.constraint_name = kcu.constraint_name
										JOIN information_schema.constraint_column_usage AS ccu
										  ON ccu.constraint_name = tc.constraint_name
									WHERE constraint_type in ('FOREIGN KEY' ,'PRIMARY KEY')
							       ) as refe
						 on (refe.table_schema = c.table_schema and refe.table_name = c.table_name and refe.column_name = c.column_name)						 
					WHERE upper(c.table_name) =upper('".$this->getTableName()."')".$this->getMsSqlShema()." 
					ORDER BY c.TABLE_SCHEMA
                            ,c.table_name
                            ,c.ordinal_position";
		return $sql;
	}
	
	public function getSqlToFieldsFromDatabase() {
		//$DbType = $this->getConnDbType();
		$DbType = $this->getType();
		$sql    = null;
		$params = null;
		$data   = null;
		
		// ler os campos do banco de dados
		if ( $DbType == TFormDinPdoConnection::DBMS_MYSQL ){
		    $sql   = $this->getSqlToFieldsFromDatabaseMySQL();
			$params=null;
		}
		else if( $DbType == TFormDinPdoConnection::DBMS_SQLSERVER ) {
		    $sql   = $this->getSqlToFieldsFromDatabaseSqlServer();
		    $params=array($this->getTableName());
		}
		else if( $DbType == TFormDinPdoConnection::DBMS_ORACLE ) {
			$sql="select a.column_name COLUMN_NAME
					, a.data_type DATA_TYPE
					, data_default as COLUMN_DEFAULT
					, 0 AUTOINCREMENT
					, decode(nullable,'Y',1,0) as REQUIRED
					, a.data_length CHAR_MAX
					, a.data_precision NUM_LENGTH
					, a.data_scale NUM_SCALE
    				from all_tab_columns a
    				where upper(a.table_name) = upper(:0)";
			
			$params=array($this->getTableName());
		}
		else if( $DbType == TFormDinPdoConnection::DBMS_POSTGRES ) {
		    $schema=( is_null( $this->getSchema() ) ? 'public' : $this->getSchema());
		    $sql   = $this->getSqlToFieldsFromDatabasePostGres();			
			$params=array( $schema ,$this->getTableName() );
		}
		else if( $DbType == TFormDinPdoConnection::DBMS_FIREBIRD ) {
			$sql='SELECT
					RDB$RELATION_FIELDS.RDB$FIELD_NAME COLUMN_NAME,
					\'\' as COLUMN_DEFAULT,
					0 AUTOINCREMENT,
					0 REQUIRED,
					RDB$TYPES.RDB$TYPE_NAME DATA_TYPE,
					RDB$FIELDS.RDB$CHARACTER_LENGTH CHAR_MAX,
					RDB$FIELDS.RDB$FIELD_PRECISION NUM_LENGTH,
					RDB$FIELDS.RDB$FIELD_SCALE NUM_SCALE
					FROM RDB$RELATIONS
					INNER JOIN RDB$RELATION_FIELDS ON RDB$RELATIONS.RDB$RELATION_NAME = RDB$RELATION_FIELDS.RDB$RELATION_NAME
					LEFT JOIN RDB$FIELDS ON RDB$RELATION_FIELDS.RDB$FIELD_SOURCE = RDB$FIELDS.RDB$FIELD_NAME
					LEFT JOIN RDB$TYPES ON RDB$FIELDS.RDB$FIELD_TYPE = RDB$TYPES.RDB$TYPE
					LEFT JOIN RDB$FIELD_DIMENSIONS  on RDB$FIELD_DIMENSIONS.RDB$FIELD_NAME = RDB$FIELDS.RDB$FIELD_NAME
					WHERE UPPER(RDB$RELATIONS.RDB$RELATION_NAME) = upper(?)
					AND RDB$RELATIONS.RDB$SYSTEM_FLAG = 0
					AND RDB$TYPES.RDB$FIELD_NAME=\'RDB$FIELD_TYPE\'
					ORDER BY RDB$RELATION_FIELDS.RDB$FIELD_POSITION';
			
			$params=array($this->getTableName());
		}
		else if( $DbType == TFormDinPdoConnection::DBMS_SQLITE) {
			$sql  = "PRAGMA table_info(".$this->getTableName().")";
			$res  = $this->executeSql($sql);
			$data = null;
			$sql  = null;
			foreach($res as $rownum => $row)
			{
				$data[$rownum]['COLUMN_NAME'] 	= $row['NAME'];
				$data[$rownum]['COLUMN_DEFAULT']= $row['DFLT_VALUE'];
				$data[$rownum]['AUTOINCREMENT'] = $row['PK'];
				$data[$rownum]['REQUIRED'] 		= ( $row['NOTNULL'] == 0 ? 'FALSE' : 'TRUE' );
				$data[$rownum]['DATA_TYPE'] 	= strtoupper($row['TYPE']);
				$data[$rownum]['CHAR_MAX'] 	= null;
				$data[$rownum]['NUM_LENGTH']= 0;
				$data[$rownum]['NUM_SCALE']	= 0;
				$data[$rownum]['PRIMARYKEY']	= $row['PK'];
				if( preg_match('/\(/',$row['TYPE']) == 1 )
				{
					$aTemp = explode('(',$row['TYPE']);
					$data[$rownum]['DATA_TYPE'] = $aTemp[0];
					$type= substr($row['TYPE'],strpos($row['TYPE'],'('));
					$type = preg_replace('/(\(|\))/','',$type);
					@list($length,$precision) = explode(',',$type);
					
					if( preg_match('/varchar/i',$aTemp[0]==1) ) {
						$data[$rownum]['DATA_LENGTH'] = $length;
					}
					else {
						$data[$rownum]['CHAR_MAX'] 	  = 0;
						$data[$rownum]['NUM_LENGTH']  = $length;
						$data[$rownum]['NUM_SCALE']   = $precision;
					}
				}
			}
		}
		$result = array();
		$result['sql']    = $sql;
		$result['params'] = $params;
		$result['data']   = $data;
		
		return $result;
	}
	
	/**
	 * Recupera as informações dos campos da tabela defida na classe diretamente do banco de dados
	 * @return null
	 */
	public function loadFieldsOneTableFromDatabase() {
		$DbType = $this->getType();
		if ( !$this->getTableName() ) {
			throw new InvalidArgumentException(TFormDinMessage::ERROR_OBJ_TABLE);
		}
		$result = $this->getSqlToFieldsFromDatabase();
		$sql    = $result['sql'];
		$data   = $result['data'];
		switch( $DbType ) {
			case TFormDinPdoConnection::DBMS_SQLITE:
				$result = ArrayHelper::convertArrayPdo2FormDin($data);
			break;
			//--------------------------------------------------------------------------------
			case TFormDinPdoConnection::DBMS_MYSQL:
			case TFormDinPdoConnection::DBMS_SQLSERVER:
			case TFormDinPdoConnection::DBMS_POSTGRES:
				$result = $this->executeSql($sql,ArrayHelper::TYPE_FORMDIN);
		    break;
			//--------------------------------------------------------------------------------
			default:
			throw new DomainException('Database '.$DbType.' not implemented ! '.TFormDinMessage::MSG_CONTRIB_PROJECT);
		}		
		return $result;
	}

	/**
	* Recupera as informações dos campos da tabela defida na classe diretamente do banco de dados
	* @return null
	*/
	public function loadFieldsFromDatabase() {		
		if ( !$this->getTableName() ) {
			return null;
		}
		$result = $this->getSqlToFieldsFromDatabase();
		$sql    = $result['sql'];
		$params = $result['params'];
		$data   = $result['data'];
		
		if ( !is_null( $sql ) ) {
			$data =  $this->query( $sql, $params );
		}
		
		if ( is_array( $data ) ){
			foreach( $data as $k => $row ) {
				$boolPrimaryKey = ArrayHelper::get($row,'PRIMARYKEY');
				$this->addField( trim( $row[ 'COLUMN_NAME' ] )
				               , trim( strtolower($row[ 'DATA_TYPE' ]) )
				               , ( (int) $row[ 'NUM_LENGTH' ] > 0 ? $row[ 'NUM_LENGTH' ] : $row[ 'CHAR_MAX' ] )
				               , $row[ 'NUM_SCALE' ]
				               , $row[ 'COLUMN_DEFAULT' ]
				               , $row[ 'REQUIRED' ]
				               , $row[ 'AUTOINCREMENT' ]
				               , $boolPrimaryKey);
			}
			if ( is_array( $this->getfields() ) ) {
				$this->serializeFields();
			}
		}
	}

}
?>
