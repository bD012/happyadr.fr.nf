<?php

namespace libraries\validator;

use core\csrf\csrf;

defined('SCRIPTOK') OR exit('No direct script access allowed');

/**
* class Validator
*
* This class manage all form validator definition and chechs.
*
*/
class Validator
{
	use \core\properties\properties;

	protected $state;

	const HAS_WARNING = 'has-warning';
	const AUTOFOCUS = 'autofocus="autofocus" ';
	protected $_feedback = null;
	protected $_dependancies = null;


	/**
	* Constructor of the class Model
	*
	* Add token feedback.
	* Call child::initilize()
	*
	* @param array $params  Parameters for child method initialize()
	*/
	public function __construct($param = null)
	{
		$this->addFeedback('token', Csrf::getTokenView());
		
		// call initialize() if exists
			if ($param === null) {
				call_user_func(array($this, 'initialize'));
			} else {
				call_user_func(array($this, 'initialize'), $param);
			} // if

			$this->_autofocus();
	} // __construct()


	/**
	* add()
	*
	* Add feedback.
	*
	* @param string $name  Feedback name.
	* @param string $type  Feedback type.
	* @param string $value  Feedback value.
	* @param string $message  Feedback message.
	*/
	public function add($name, $type, $value, $message)
	{ 
		// not function
		$not = false;
		if ($value[0] == '!') {
			$not = true;
			$value = substr($value, 1);
		}

		$this->_feedback[$name.'_autofocus'] = '';
		$this->_feedback[$name.'_feedback'] = '';
		$this->_feedback[$name.'_hasfeedback'] = '';
		$this->_feedback[$name.'_value'] = null;

		// function@CREATE
		$switch = explode('@', $type, 2)[0];
		switch($switch)
		{
			case 'equal':
			case 'isEqualToValidator':
			case 'function':
			case 'max':
			case 'min':
			case 'regexp':
			case 'required':
				$this->_push($name, $type, array('value' => $value, 'message' => $message, 'not' => $not));
				break;
			default:
				trigger_error("Validator->add");
				break;
		} // switch
	} // add()


	/**
	* setInformation()
	*
	* Set information feedback (for input description).
	*
	* @param string $name  Feedback name.
	* @param string $message  Feedback message.
	*/
	public function setInformation($name, $message)
	{
		$this->_feedback[$name.'_information'] = $message;
	} // setInformation()


	/**
	* setState
	*
	* @param string $state  Set the state for next check.
	*/
	public function setState($state)
	{
		$this->state = strtoupper($state);
	} // function setTable


	/**
	* getFeedback
	*
	* @return mixed $feedback
	*/
	public function getFeedback()
	{
		return $this->_feedback;
	} // getFeedback()


	/**
	* addFeedback
	*
	* Add key=>value feedback.
	*
	* @param string $key
	* @param string $value
	*/
	public function addFeedback($key, $value)
	{
		$this->_feedback[$key] = $value;
	} // addFeedback()


	/**
	* setFeedback
	*
	* Alias for addFeedback.
	*
	* @param string $key
	* @param string $value
	*/
	public function setFeedback($key, $value)
	{
		$this->_feedback[$key] = $value;
	} // setFeedback()


	/**
	* addWarning
	*
	* Add a Warning feedback. set flag for _hasfeedback, and _feedback
	*
	* @param string $name  Feedback name.
	* @param string $message  Feedback message.
	*/
	public function addWarning($name, $message)
	{
		$this->_feedback[$name.'_hasfeedback'] = self::HAS_WARNING;
		$this->_feedback[$name.'_feedback'] .= $message;
	} // addWarning()


	/**
	* addDependancy()
	*
	* Add dependency for checking Functions, generally, dependancy is for Model.
	* 
	* @param mixed $dependancy  Dependancy to add in dependancies array.
	*/
	public function addDependancy($dependancy)
	{
		$this->_dependancies[] = $dependancy;
	} // addDependancy()


	/**
	* _autofocus()
	*
	* Manage the autofocus feedback, _autofocus.
	*/
	protected function _autofocus()
	{
		// Init
		$first = null;
		$autofocused = false;

		foreach($this->_getProperties() as $key => $value) {

			// Reset autofocus
			$this->_feedback[$key.'_autofocus'] ='';

			if ($first === null) {
				$first = $key;
			} // if

			if ($this->_feedback[$key.'_hasfeedback'] != '') {
				$this->_feedback[$key.'_autofocus'] = self::AUTOFOCUS;
				$autofocused = true;
				break;
			} // if
		} // foreach

		if (!$autofocused) {
			$this->_feedback[$first.'_autofocus'] = self::AUTOFOCUS;
		}
	} // _autofocus()


	/**
	* check()
	*
	* Check all validators definition for all defined validator.
	*
	* @param mixed $params  Generally $_POST
	*
	* @return boolean $reult  Check result.
	*/
	public function check($params)
	{
		debugInfo(get_class($this).'->check($params)');

		$return = true;

		foreach($this->_getProperties() as $key => $value) {
			if (isset($params[$key])) {
				debugInfo("->check('$key', '$params[$key]')");
				$return &= $this->_validator($key, $params);
			} else {
				//$return = false;
			} // if
		} // foreach

		$this->_autofocus();

		debug(get_class($this).'->check() return', (boolean)$return);
		debug(get_class($this).'->_feedback', $this->_feedback);
		return $return;
	} // check()


	/**
	* _validator()
	*
	* Validator parsing function.
	*
	* @param string $nameName  Name of the validator.
	* @param array $nameParams  Parameters ($_POST[$each])
	*
	* @return boolean $reult  Check result.
	*/
	protected function _validator($nameName, $nameParams=false)
	{
		$break = false;
		$return = true;

		$this->_feedback[$nameName.'_feedback'] = '';
		$this->_feedback[$nameName.'_hasfeedback'] = '';
		$this->_feedback[$nameName.'_value'] = $nameParams[$nameName];

		foreach($this->$nameName as $key=>$value) {
			$ok = true;
			$message = '';

			switch($key)
			{
				/*
				* equal
				*/
				case 'equal':
					if ($this->_feedback[$value['value'].'_value'] != $nameParams[$nameName]) {
						$ok = false;
						$message = $value['message'];
					} // if
					break;

				/*
				* function
				*/
				case 'function@'.$this->state: 
					if ($return === false) break;

					$params = array( $nameName => $nameParams[$nameName]);
					
					$function = 'function@'.$this->state;

					$test = null;
					$imax = count($this->_dependancies);
					for($i=0; $i<$imax; $i++) {
						if (method_exists($this->_dependancies[$i], $value['value'])) {
							$test = call_user_func(array($this->_dependancies[$i], $value['value']), $params);
						} // if
					} // for
					
					if ($value['not']) { $test = !$test; }
					if ($test === false) {
						$ok = false;
						$message = $value['message'];
						$break = true;	 
					} // if
					break;

				/*
				* isEqualToValidator
				*/
				case 'isEqualToValidator':
					if ($this->_feedback[$nameName.'_value'] != $this->_feedback[$value['value'].'_value']  ) {
						$ok = false;
						$message = $value['message'];
					} // if
					break;

				/*
				* max
				*/
				case 'max':
					$tmp = (gettype($nameParams[$nameName]) == 'string') ? strlen($nameParams[$nameName]) : $nameParams[$nameName];
					if ($tmp > $value['value']) {
						$ok = false;
						$message = $value['message'];
					}
					break;
				/*
				* min
				*/
				case 'min':
					$tmp = (gettype($nameParams[$nameName]) == 'string') ? strlen($nameParams[$nameName]) : $nameParams[$nameName];
					if ($tmp < $value['value']) {
						$ok = false;
						$message = $value['message'];
					}
					break;

				/*
				* regexp
				*/
				case 'regexp':
					if (preg_match($value['value'], $nameParams[$nameName]) == 0) {
						$ok = false;
						$message = $value['message'];
					} // if
					break;

				/*
				* required
				*/
				case 'required':
					if (($value['value'] === true) && ($nameParams[$nameName] == '')) {
						$ok = false;
						$message = $value['message'];
						$break = true; // exit foreach
					}
					break;

				/*
				* default
				*/
				default:
					//trigger_error(__FILE__.__LINE__);
					break;
			} // switch

			debug("...$key, $value[value] => ",$ok);

			if ($ok === false) {
				$return = false;
				$this->_feedback[$nameName.'_feedback'] .= $message.' ';
				$this->_feedback[$nameName.'_hasfeedback'] = self::HAS_WARNING.' ';
			} // if

			if ($break) {
				break; 
			} // if break
		} // foreach

		return $return;
	} // _validator()
} // class Validator