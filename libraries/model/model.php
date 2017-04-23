<?php

namespace libraries\model;

use \PDO;

defined('SCRIPTOK') OR exit('No direct script access allowed');

/**
* class Ajax
*
* This class manage initilisation and access to database.
*
*/
class Model
{
	/**
	* class properties
	*/

	private $_db;
	private $_table = '';
	private $_models = null;

	private $_variables;

	private $_query;
	private $_set;
	private $_where;
	private $_limit = null;

	private $_result;

	/**
	* Constructor of the class Model
	*
	* Add models that are defined in $config['model'].
	* Set $this->_db to nex PDO instance.
	* Call child::initilize()
	*
	* @param array $params  Parameters for child method initialize()
	*/
	public function __construct($param = null)
	{
		global $config;

		// add 'id'
		$this->addModels(array('id' => null));
		// add from $config
		$this->addModels($config['model']);
		
		// open PDO
		try {
			$this->_db = new PDO(
				$config['pdo']['dsn'],
				$config['pdo']['username'],
				$config['pdo']['password'], 
				array(
					PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES latin1'
				));

			$this->_db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_WARNING);
			$this->_db->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_OBJ);
		} catch (\PDOException $e) {
			trigger_error("Cannot connect to database");
		}

		// call initialize() if exists
			if ($param === null) {
				call_user_func(array($this, 'initialize'));
			} else {
				call_user_func(array($this, 'initialize'), $param);
			} // if
	} // function __construct


	/* ***************************************************************
	**  Table name management
	** ************************************************************ */

	/**
	* set Table name
	*
	* @param string $table Name of the table in DataBase
	*/
	public function setTable($table)
	{
		debugInfo(get_class($this)."->setTable($table)");

		$this->_table = $table;
	} // function setTable


	/* ***************************************************************
	**  Models management
	** ************************************************************ */

	/**
	* Add models definition to $this->_models
	*
	* @param array $models array($key=>$value) with $key=column name, $value=column value
	*/
	public function addModels($models)
	{
		// add from parameters:$models
		foreach($models as $key => $value) {
			$this->_models[$key] = $value;
		} // foreach
	} // function addModels


	/* ***************************************************************
	**  CRUD : Create Read Update Delete
	** ************************************************************ */

	/**
	* Create a new row in Data Base
	*
	* INSERT INTO table (col1, col2) VALUES ('val1', 'val2');
	*
	* @return integer Value of the 'id' or false.
	*/
	public function create()
	{
		debugInfo(get_class($this)."->create()");
		global $config;

		// Add set from $config['model'] {'_status', '_comment'}
		$this->set($config['model']);

		// INSERT
		$this->_query = $this->_insert();

		// execute SQL request without Result (false)
		if ($this->_execute(false) === false) {
			$return = false;
		} else {
			$return = $this->_db->lastInsertId();
		}

		// clear private variables
		$this->_clear();

		debug('->create(), $return', $return);
		return $return;
	} // create()


	/**
	* Read datas in Data Base
	*
	*  SELECT columns FROM table WHERE condition
	*
	* @param mixed $colmuns Array : read columns, Integer: read WHERE id
	*
	* @return mixed false in case of problem, 
	* $this->_result if columns type is array
	* if type of columns is integer return 
	*/
	public function read($columns = array('*'))
	{
		debugInfo(get_class($this).'->read($colmuns)');
		debug('$columns', $columns);

		if (gettype($columns) == 'array') {
			// add where _status not DELETED
			$this->where('_status', '<', 255);

			// SELECT
			$this->_query = $this->_select($columns);

			// execute SQL request
			$return = $this->_execute();

		} else {
			if (($this->_isCleared()) && ($this->_result !== null)) {

				// search for 'id' in _result
				$imax = count($this->_result);
				for($i=0; $i<$imax; $i++) {
					if ($this->_result[$i]['id'] == $columns) {
						// return the record...
						return $this->_result[$i];
					} // if
				} // for
			} else {
				$this->_clear();
				$this->where('id', '=', $columns)->limit(1);
				// SELECT
				$this->_query = $this->_select(array('*'));

				// execute SQL request
				$return = $this->_execute()[0];
			} // if
		} // if

		debug('->read(), $return', $return);
		return $return;
	} // read()


	/**
	* Update datas in Data Base
	*
	* UPDATE table SET col1='val1', col2='val2' WHERE condition
	*
	* @param integer $id
	*
	* @return mixed _execute() return
	*/
	public function update($id)
	{
		debugInfo(get_class($this)."->read($id)");

		// add where _status not DELETED
		$this->where('id', '=', $id);

		// SELECT
		$this->_query = $this->_update();

		$return = $this->_execute(false);

		debug('->update(), $return', $return);
		return $return;
	} // update()


	/**
	* Delete data in Data Base
	*
	* Delete set the _status to 255
	*
	* @param $id
	*
	* @return mixed update() return
	*/
	public function delete($id)
	{
		debugInfo(get_class($this)."->delete($id)");

		// clear private variables
		$this->_clear();

		$return = $this->set('_status', 255)->update($id);

		debug('->delete(), $return', $return);
		return $return;
	} // delete()


	/**
	* Clear all datas in Data Base
	*
	*
	* @return mixed update() return
	*/
	public function truncate()
	{
		debugInfo(get_class($this)."->truncate");

		// clear private variables
		$this->_clear();

		$this->_query = "TRUNCATE TABLE $this->_table;";

		$return = $this->_execute(false);

		debug('->truncate(), $return', $return);
		return $return;
	} // delete()


	/**
	* Raw Query
	*
	*
	* @return result
	*/
	public function raw($query, $withResult = true)
	{
		debugInfo(get_class($this)."->raw($query);");

		// clear private variables
		$this->_clear();

		$this->_query = $query;

		$return = $this->_execute($withResult);

		// debug('->raw(), $return', $return);
		return $return;
	} // raw()


	/* ***************************************************************
	**  Tools (has)
	** ************************************************************ */

	/**
	* has()
	*
	* Read where 'id' or array(columns => values) in Data Base, return true or false.
	*
	* @param mixed $idorarray
	*
	* @return boolean $has
	*/
	public function has($idorarray)
	{ 
		if (gettype($idorarray) == 'array') {
			foreach($idorarray as $key => $value) {
				$this->where($key, '=', $value);
			} // foreach
		} // if
		$return = $this->limit(1)->read();

		// ... !=0) null: no result, false: issue
		if ($return != 0) return true;
		return false;
	} // has()
	

	/**
	* id()
	*
	* @param integer $index  Index in query result
	*
	* @return mixed $result  Id of result[$index]
	*         boolean false if no result
	*/
	public function id($index = 0)
	{
		if (isset($this->_result[$index]['id'])) {
			return $this->_result[$index]['id'];
		} else {
			debugWarning('$this->id() is not defined.');
			return false;
		}
	} // getId()


	/**
	* __get()
	*
	* Generic getter
	*/
	public function __get($name)
	{ 
		if (isset($this->_result[0][$name])) {
			return $this->_result[0][$name];
		} else 
		if (($name == 'role') && (isset($this->_result[0]['_status']))) {
			return $this->_result[0]['_status'];
		} else {
			debugWarning('$this->_result[0][\''.$name.'\'] is not defined.');
			return false;
		}
	} // __get()

	/* ***************************************************************
	**  set(), where(), limit()
	** ************************************************************ */

	/**
	* Define attributes for Read()
	*
	* SQL : LIMIT Offset, Depth
	*
	* @param integer $depth
	* @param integer $offset 
	*
	* @return object $this 
	*/
	public function limit($depth, $offset=0)
	{
		$this->_limit['depth'] = $depth;
		$this->_limit['offset'] = $offset;

		return $this;
	} // limit()


	/**
	* Define attributes for Create( (columns,) VALUES ('Values',)) and Update (SET)
	*
	* SQL : (col1, col2) VALUES ('val1', 'val2')
	* SQL : SET col1='val1', col2='val2'
	*
	* @param array or string $column 
	*  if type array : array($key=>$value), add to $this->_set all $key=>$val, $value is not used.
	*  if type string : $this->_set[$key] = $value;
	*
	* @return object $this 
	*/
	public function set($column, $value = null)
	{
		if (gettype($column) == 'array') {
			foreach($column as $key => $value) {
				$this->_set[$key] = addslashes($value);
			} // foreach
		} else {
			$this->_set[$column] = addslashes($value);
		} // if

		return $this;
	} // set()




	/**
	* Define attributes for all
	* 
	* SQL : WHERE col1 = 'val1' AND col2 = 'val2'
	*
	* @param $column string
	* @param $condition string, '=', '<', '>'...
	* @param $value string
	*
	* @return object $this 
	*/
	public function where($column, $condition, $value)
	{
		$this->_where[] = array('row' => $column, 'condition' => $condition, 'value' => $value);
		return $this;
	} // where()


	/* ***************************************************************
	* Protected functions
	*************************************************************** */
	/**
	* _clear()
	* 
	* Clear private variables 
	*/
	protected function _clear()
	{
		$this->_query = null;
		$this->_set = null;
		$this->_where = null;
		$this->_limit = null;
	} // clear()


	/**
	* _isCleared()
	* 
	* Test if all private variables are cleared
	*
	* @return boolean true if cleared else false 
	*/
	protected function _isCleared()
	{
		// Initialisation
		$return = true;

		// Tests
		if ($this->_query !== null) $return = false;
		if ($this->_set !== null) $return = false;
		if ($this->_where !== null) $return = false;
		if ($this->_limit !== null) $return = false;

		return $return;
	} // _isCleared()


	/**
	* _execute
	*
	* Prepare and execute the SQL request, private variables are clear.
	*
	* @param boolean $withResult, flag if expected result from DataBase
	*
	* @return if result is not expected else Result of SQL Request
	*/
	protected function _execute($withResult = true)
	{
		debugInfo(get_class($this).'->_execute()');
		debug('SQL Query', $this->_query);

		// Initialisation
		$this->_result = null;

		if ($this->_db === null) {
			trigger_error("Not connected to database");
			return false;
		}

		// query
		if (($prepare = $this->_db->prepare($this->_query)) === false) {
			trigger_error("Not connected to database");
			return false;
		}
		
		if (($return = $prepare->execute()) !== false) {
			if ($withResult) {
					$return = null;
					while($row = $prepare->fetch(PDO::FETCH_ASSOC)) {
						$return[] = $row;
					} // while
					$this->_result = $return;
			} // if
		} else {
			trigger_error("Not connected to database");
			return false;
		} // if

		// clear private variables
		$this->_clear();

		debug('->_execute(), $return', $return);
		return $return;
	} // _execute()


	/**
	* Prepare INSERT request
	*
	* @return string SQL Request
	*/
	protected function _insert()
	{
		// INSERT INTO 'table'
		$this->_query = 'INSERT INTO '.$this->_table.' (';

		//  * / row1, row2
		$first = true;
		$vals = '';
		foreach($this->_set as $key => $value) {
			$this->_query .= ($first) ? ('') : (', '); 
			$vals .= ($first) ? ('') : (', '); 
			$first = false;

			$this->_query .= $key;
			$vals .= "'$value'";
		} // foreach

		$this->_query .= ") VALUES ($vals);";

		return $this->_query;
	} // _insert()


	/**
	* Prepare SELECT request
	*
	* @return string SQL Request
	*/
	protected function _select($columns)
	{
		// SELECT
		$this->_query = 'SELECT ';

		//  * / row1, row2
		$first = true;
		foreach($columns as $column) {
			$this->_query .= ($first) ? ('') : (' , '); 
			$first = false;

			$this->_query .= $column; 
		} // foreach

		// FROM 'table'
		$this->_query .= ' FROM '.$this->_table;

		// WHERE foo = 'bar'
		$this->_query .= ' WHERE ';
		$imax = count($this->_where);
		$first = true;
		for($i=0; $i<$imax; $i++) {
			$this->_query .= ($first) ? ('') : (' AND '); 
			$first = false;

			$this->_query .= $this->_where[$i]['row'].' ';
			$this->_query .= $this->_where[$i]['condition'].' \'';
			$this->_query .= $this->_where[$i]['value'].'\'';
		} // foreach

		// LIMIT Offset, Depth
		if ($this->_limit !== null) {
			$this->_query .= ' LIMIT '.$this->_limit['offset'].', '.$this->_limit['depth'];
		} // if
		
		// end ;
		$this->_query .= ';';

		return $this->_query;
	} // _select()


	/**
	* Prepare UPDATE request
	*
	* @return string SQL Request
	*/
	protected function _update()
	{
		// UPDATE 'table' SET
		$this->_query = 'UPDATE '.$this->_table.' SET ';

		//  * / row1, row2
		$first = true;
		$vals = '';
		foreach($this->_set as $key => $value) {
			$this->_query .= ($first) ? ('') : (', '); 
			$this->_query .= "$key = '$value'";

			$first = false;
		} // foreach

		// WHERE foo = 'bar'
		$this->_query .= ' WHERE ';
		$imax = count($this->_where);
		$first = true;
		for($i=0; $i<$imax; $i++) {
			$this->_query .= ($first) ? ('') : (' AND '); 
			$first = false;

			$this->_query .= $this->_where[$i]['row'].' ';
			$this->_query .= $this->_where[$i]['condition'].' \'';
			$this->_query .= $this->_where[$i]['value'].'\'';
		} // foreach

		// end ;
		$this->_query .= ';';

		return $this->_query;
	} // _update()
} // class Model
