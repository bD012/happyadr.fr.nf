<?php

namespace modules\contact;

use libraries\model\model;

defined('SCRIPTOK') OR exit('No direct script access allowed');

/**
* class ContactModel
*
* This class define models for contact form.
*
*/
class ContactModel extends Model
{
	/**
	* initilialize()
	*
	*
	*/
	public function initialize()
	{
		// Not used for Database
		$this->setTable(null);
		
		$this->addModels(array('email '=> '', 'message' => ''));

		debug('ContactModel::initialize(): $this', $this);
	} // initilialize()
} // class Model