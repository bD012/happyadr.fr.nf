<?php

namespace modules\user;

use core\router\router;
use libraries\validator\validator;

defined('SCRIPTOK') OR exit('No direct script access allowed');

/**
* class UserValidator
*
* This class define valdators for User.
*
*/
class UserValidator extends Validator
{

	/**
	 * initilialize()
	 *
	 * @param string $params  Define validator according State.
	 */
	public function initialize($param)
	{
		switch($param)
		{
			case 'account':
				$this->username();
				$this->email();
				$this->password();
				break;
			case 'register':
				$this->username();
				$this->email();
				$this->password();
				$this->confirm();
				break;
			case 'signin':
				$this->username();
				$this->password();
				break;
			case 'signout':
				break;
			default:
				debugWarning('UserValidator::initialize($param = '.$param.' not recognized !!...');
				break;
		}

		$this->addFeedback('switch_checked', Router::getVariable('switch'));
	} // initilialize()


	/**
	 * username()
	 *
	 * Set username validators.
	 */
	public function username()
	{
		$this->add('username', 'required', true, 'Username is required.');
		$this->add('username', 'regexp', '#^[a-zA-Z0-9_\-@]*$#', 'Bad username format.');
		$this->add('username', 'min', 4, 'Bad username length, at least 4 characters.');
		$this->add('username', 'max', 30, 'Bad username length, no more than 30 characters.');
		$this->add('username', 'function@SIGNIN', 'has', 'This username does not exist.');
		$this->add('username', 'function@REGISTER', '!has', 'This Username is already used.');

		$this->setInformation('username', 'Username desciption, 4 to 30 characters...');
	} // username()
	

	/**
	 * email()
	 *
	 * set email validators.
	 */
	public function email()
	{
		$this->add('email', 'required', true, 'Email is required.');
		$this->add('email', 'regexp', '#^[^\W][a-zA-Z0-9_]+(\.[a-zA-Z0-9_]+)*\@[a-zA-Z0-9_]+(\.[a-zA-Z0-9_]+)*\.[a-zA-Z]{2,4}$#', 'Bad Email format.');
		$this->add('email', 'max', 30, 'Bad Email length, no more than 30 characters.');

		$this->setInformation('email', 'Email format, no more than 30 characters...');
	} // email()


	/**
	 * password()
	 *
	 * set password validators.
	 */
	public function password()
	{
		$this->add('password', 'required', true, 'Password is required.');
		$this->add('password', 'regexp', '#^[a-zA-Z0-9_\-]*$#', 'Bad Password format.');
		$this->add('password', 'min', 4, 'Bad Password length, at least 4 characters.');
		$this->add('password', 'max', 30, 'Bad Password length, no more than 30 characters.');

		$this->setInformation('password', 'Password desciption, 4 to 30 characters...');
	} // password()
	
	/**
	 * confirm()
	 *
	 * set confirm validators.
	 */
	public function confirm()
	{
		$this->add('confirm', 'required', true, 'Password is required.');
		$this->add('confirm', 'regexp', '#^[a-zA-Z0-9_\-]*$#', 'Bad Password format.');
		$this->add('confirm', 'min', 4, 'Bad Password length, at least 4 characters.');
		$this->add('confirm', 'max', 30, 'Bad Password length, no more than 30 characters.');
		$this->add('confirm', 'isEqualToValidator', 'password', 'Password and Confirm Password have to be the same.');

		$this->setInformation('confirm', 'Confirm Password desciption, 4 to 30 characters...');
	} // confirm()
} // class UserValidator
