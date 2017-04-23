<?php

namespace modules\contact;

use core\router\router;
use libraries\validator\validator;

defined('SCRIPTOK') OR exit('No direct script access allowed');

/**
* class ContactValidator
*
* This class define validators for contact form.
*
*/
class ContactValidator extends Validator
{
	use \core\properties\properties;

	/**
	 * initilialize()
	 *
	 *
	 */
	public function initialize()
	{
		$this->email();
		$this->message();
	} // initilialize()


	/**
	 * email()
	 *
	 * email validator definition.
	 */
	public function email()
	{
		$this->add('email', 'required', true, 'Email is required.');
		$this->add('email', 'regexp', '#^[^\W][a-zA-Z0-9_]+(\.[a-zA-Z0-9_]+)*\@[a-zA-Z0-9_]+(\.[a-zA-Z0-9_]+)*\.[a-zA-Z]{2,4}$#', 'Bad Email format.');
		$this->add('email', 'max', 30, 'Bad Email length, no more than 30 characters.');

		$this->setInformation('email', 'Email format, no more than 30 characters...');
	} // email()


	/**
	 * message()
	 *
	 * message validator definition.
	 */
	public function message()
	{
		$this->add('message', 'required', true, 'Message is required.');
		$this->add('message', 'regexp', '#^[a-zA-Z0-9\s_\- ,\.!]*$#', 'Bad message format.');
		$this->add('message', 'max', 250, 'Bad message length, no more than 250 characters.');

		$this->setInformation('message', 'Message desciption, no specials characters, no accent, no more than 250 characters...');
	} // message()
} // class UserValidator