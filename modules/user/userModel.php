<?php

namespace modules\user;

use libraries\model\model;

defined('SCRIPTOK') OR exit('No direct script access allowed');

/**
* class UserModel
*
* This class define models for User.
*
*/
class UserModel extends Model
{
	/**
	* initilialize()
	*
	* @param string $param  Several model definition according State.
	*/
	public function initialize($param)
	{
		$this->setTable('users');
		
		switch($param)
		{
			case 'signin':
				$this->addModels(array('username '=> '', 'password' => ''));
				break;
			case 'account':
			case 'register':
				$this->addModels(array('username '=> '', 'password' => '', 'email' => ''));
				break;
			case 'signout':
				break;
			default:
				// ???
				break;
		} // switch (variable) {

		debug('UserModel::initialize(): $this', $this);
	} // initilialize()
} // class Model
