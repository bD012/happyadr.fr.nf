<?php

namespace modules\database;

use libraries\model\model;

defined('SCRIPTOK') OR exit('No direct script access allowed');

/**
* class CitiesModel
*
* This class define models for cities database.
*
*/
class CitiesModel extends Model
{
	/**
	* initilialize()
	*
	*
	*/
	public function initialize()
	{
		$this->setTable('cities');
		
		$this->addModels(array('search_city' => '', 'search_department' => '', 'city'=> '', 'postcode' => '', 'department' => ''));

		debug('CitiesModel::initialize(): $this', $this);
	} // initilialize()
} // class Model
