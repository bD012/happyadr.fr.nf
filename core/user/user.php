<?php

namespace core\user;

use core\router\router;
use core\session\session;

// cf model: _status
define('USER_ROLE_USER', 0);
define('USER_ROLE_MODERATOR', 1);
define('USER_ROLE_ADMIN', 2);

define('ANONYMOUS', 'ANONYMOUS');

/**
 * class User
 */
final class User
{
	/*
	* 'user' : 'id', 'username', 'role'
	* 'islogged' : true, false
	* 'data' : other informations like model
	*/
  private static $_user = null;


	/**
	* __construct()
	*
	* private => instanciation not possible.
	*/
	private function __construct()
	{
	} // __construct()


	/**
	* initialize()
	*
	*  - From Session if defined
	*  - Else init to ANONYMOUS
	*  - Launch userController defined in config.php
	*/
	public static function initialize()
  {
		global $config;
		
		if (Session::get('USER','islogged') === true) {

			// Initialize from Session
			self::$_user = Session::get('USER');
			self::$_user['islogged'] = true;
		} else {

			// Initialize to ANONYMOUS
			self::$_user['user'] = array(
				'id' => null,
				'username' => ANONYMOUS,
				'role' => USER_ROLE_USER
			);
			self::$_user['islogged'] = false;
			self::$_user['data'] = null;

			Session::set('USER', self::$_user);
		} // if

		// Initialize UserController
		if (Router::getController() != $config['user']['controller']) {
			eval('(new \\modules\\'.$config['user']['controller'].'\\'.$config['user']['controller'].'Controller())->switcher();');
		}

		debugTitle('USER');

		debug('User::$_user', self::$_user);
  } // initialize


	/**
	* set()
	*
	* Set static variables and session
	*
	* @param integer $id  Set the Id of the User
	* @param integer $username  Set the Username of the User
	* @param integer $role  Set the Role of the User
	*/
	public static function set($id, $username, $role = USER_ROLE_USER, $data=null)
  { 
		self::setId($id);
		self::setUsername($username);
		self::setRole($role);
		self::setData($data);

		self::$_user['islogged'] = true;

		Session::set('USER', self::$_user);
  } // set()


	/**
	* reset()
	*
	* Reset static variables and session
	*/
	public static function reset()
  {
		Session::forget('USER');

		self::$_user['user'] = array(
			'id' => null,
			'username' => ANONYMOUS,
			'role' => USER_ROLE_USER
		);
		self::$_user['islogged'] = false;
		self::$_user['data'] = null;

		Session::set('USER', self::$_user);
  } // reset()


	/**
	* setId()
	*
	* Set the User Id
	*
	* @param integer $id  Set the Id of the User
	*/
	public static function setId($id)
  {
		self::$_user['user']['id'] = $id;
  } // setId()
  
	/**
	* getId()
	*
	* @return integer $Id  Id of the User
	*/
	public static function getId()
  {
		return self::$_user['user']['id'];
  } // getId()


	/**
	* setUsername()
	*
	* Set the User username
	*
	* @param string $username  Set the Username of the User
	*/
	public static function setUsername($username)
  {
		self::$_user['user']['username'] = $username;
  } // setUsername()
  
	/**
	* getUsername()
	*
	* @return string $Username  Username of the User
	*/
	public static function getUsername()
  {
		return self::$_user['user']['username'];
  } // getUsername()


	/**
	* setRole()
	*
	* Set the User Role
	*
	* @param string $role  Set the Role of the User
	*/
	public static function setRole($role = USER_ROLE_USER)
  {
		self::$_user['user']['role'] = $role;
  } // setRole()
  
	/**
	* getRole()
	*
	* @return integer $Role  Role of the User
	*/
	public static function getRole()
  {
		return self::$_user['user']['role'];
  } // getRole()


	/**
	* setData()
	*
	* Set the User Data
	*
	* @param mixed $data  Set Data for the User
	*/
	public static function setData($data)
  {
		self::$_user['data'] = $data;
  } // setData()
  
	/**
	* getData()
	*
	* @return mixed $data  Get data from the User.
	*/
	public static function getData()
  {
		return self::$_user['data'];
  } // getData()


	/**
	* isLogged()
	*
	* @return boolean $isLogged
	*/
	public static function isLogged()
  {
		debug('self::$_user[islogged]',self::$_user['islogged']);
		return self::$_user['islogged'];
  } // isLogged()
} // class User