<?php

namespace modules\database;

use libraries\model\model;

/**
* class StreetsModel
*
* This class define models for streets database.
*
*/
class StreetsModel extends Model
{
	/**
	* initilialize()
	*
	*
	*/
	public function initialize()
	{
		$this->setTable('streets');
		
		$this->addModels(array('search' => '', 'street'=> '', 'id_city' => ''));

		debug('UCitiesModel::initialize(): $this', $this);
	} // initilialize()
} // class StreetsModel
