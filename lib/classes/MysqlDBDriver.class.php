<?php
/**
  * User: Eric Gorbikov
 * Date: 8/26/13
 * Time: 6:37 AM
 *
 * MySQL db wrapper class
  */

class MysqlDBDriver extends Singleton {

	/**
	 * @var mysqli
	 */
	protected $link;
	protected $isConnected = false;

	protected function __construct(){/**/}

	/**
	 * Get an instance of me
	 *
	 * @return MysqlDBDriver
	 */
	public static function me()
	{
		return self::getInstance(__CLASS__);
	}


	public function query($query)
	{
		return mysqli_query($this->link, $query);
	}


	public function queryArray($query)
	{
		$queryResult = $this->query($query);
		if(is_bool($queryResult)) {
			return array();
		}
		
		$result = array();
		while($rowData = $queryResult->fetch_array(MYSQL_ASSOC)) {
			$result[] = $rowData;
		}
		
		return $result;
	}

	
	public function getSingleRowBy($table, $fieldName, $fieldValue, $silent = true)
	{
		$table = $this->escapeFieldOrTable($table);
		$fieldName = $this->escapeFieldOrTable($fieldName);
		$fieldValue = $this->escapeAndQuoteValue($fieldValue);
		
		$resultArray = $this->queryArray("SELECT * FROM {$table} WHERE {$fieldName} = {$fieldValue}");
		
		if(count($resultArray) == 0) {
			return null;
		}
		
		if(count($resultArray) > 1 && !$silent) {
			throw new Exception('More than one row has been returned!');
		}
		
		return $resultArray[0];
	}
	
	
	public function checkExistenceBy($table, $fieldName, $fieldValue)
	{
		$table = $this->escapeFieldOrTable($table);
		$fieldName = $this->escapeFieldOrTable($fieldName);
		$fieldValue = $this->escapeAndQuoteValue($fieldValue);
		
		$resultArray = $this->queryArray("SELECT COUNT(*) AS COUNT FROM {$table} WHERE {$fieldName} = {$fieldValue}");
		
		if($resultArray[0]['COUNT'] == 0) {
			return false;
		}
		
		return true;
	}
	
	
	public function getIdBy($table, $fieldName, $fieldValue, $silent = true)
	{
		$table = $this->escapeFieldOrTable($table);
		$fieldName = $this->escapeFieldOrTable($fieldName);
		$fieldValue = $this->escapeAndQuoteValue($fieldValue);
		
		$resultArray = $this->queryArray("SELECT id FROM {$table} WHERE {$fieldName} = {$fieldValue}");

		if(count($resultArray) == 0) {
			return null;
		}

		if(count($resultArray) > 1 && !$silent) {
			throw new Exception('More than one row has been returned!');
		}
		
		return $resultArray[0]['id'];
	}


	public function insertRow($table, array $data)
	{
		if(count($data) == 0) {
			return false;
		}

		$table = $this->escapeFieldOrTable($table);
		
		$fieldNames = array_keys($data);
		$fieldValues = array_values($data);
		
		$fieldNamesString = implode(', ', array_map(array($this, 'escapeFieldOrTable'), $fieldNames)); 
		$fieldValuesString = implode(', ', array_map(array($this, 'escapeAndQuoteValue'), $fieldValues)); 
			
		$this->query("INSERT INTO {$table} ($fieldNamesString) VALUES ($fieldValuesString)");

		if($this->link->errno) {
			print('Error while inserting a row: ' . $this->link->errno . ': ' . $this->link->error . "\n\n");
			exit;
		}
		
		return $this->link->insert_id;
	}
	

	/**
	 * Connect to db
	 *
	 * @param string $host
	 * @param string $dbName
	 * @param string $user
	 * @param string $password
	 * @throws Exception
	 */
	public function connect($host, $dbName, $user, $password = '')
	{

		$this->link = new mysqli($host, $user, $password, $dbName);
		
		$this->query("SET NAMES 'utf8'");

		if(mysqli_connect_errno()) {
			throw new Exception('Couldn\'t connect to DB. '.mysqli_connect_errno(). ': '.mysqli_connect_error());
		}

		$this->isConnected = true;
	}

	public function begin()
	{
		$this->link->autocommit(false);
	}
	
	public function commit()
	{
		$this->link->commit();
	}


	/**
	 * Disconnect from db
	 */
	public function disconnect(){
		if($this->isConnected()) {
			$this->link->close();
		}
	}


	/**
	 * Check if connected to db
	 * @return bool
	 */
	public function isConnected()
	{
		return $this->isConnected;
	}


	/**
	 * @return MysqlDBDriver
	 */
	public static function getDefaultInstance()
	{
		$dbDriver = static::me();
		$dbDriver->connect(DB_HOST, DB_NAME, DB_USER, DB_PASSWORD);
		return $dbDriver;
	}

	/**
	 * @return string
	 */
	public function escapeFieldOrTable($name)
	{
		return '`' . $name . '`';
	}


	/**
	 * @return string
	 */
	private function escapeAndQuoteValue($value)
	{
		$escapedValue = $this->escapeValue($value);
		return "'" . $escapedValue . "'";
	}

	/**
	 * @return string
	 */
	private function escapeValue($value)
	{
		return $this->link->real_escape_string($value);
	}
}