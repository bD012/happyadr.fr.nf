<?php

namespace core\properties;

defined('SCRIPTOK') OR exit('No direct script access allowed');

/**
* trait Properties
*
* This trait defines natural setters and getters for properties.
*
*/
trait Properties
{

	protected $_properties;

	/**
	* __get()
	*
	* @param string $name  Name of the property.
	*
	* @return mixed $value of the property, false if not defined.
	*/
	public function __get($name)
	{
		if (isset($this->_properties[$name])) {
			return $this->_properties[$name];
		} else {
			debugWarning(get_class($this)."::__get($name), this name does not exist.");
			return false;
		} // if
	}	// __get()


	/**
	* _getProperties()
	*
	* @return mixed $properties  All properties.
	*/
	public function _getProperties()
	{
		return $this->_properties;
	}	// _getProperties()


	/**
	* __set
	*
	* @param string $name  Define $name property.
	* @param mixed $value  Define $value of $name property
	*/
	public function __set($name, $value)
	{
		$this->_properties[$name] = $value;
	} // __set()

	/**
	* _push()
	*
	* Push (like array management) of Property.
	*
	* @param string $name  Name of the Property.
	* @param string $subname  Subname of the Property. 
	* @param mixed $value Value of the Property.
	*/
	public function _push($name, $subname, $value)
	{
		$this->_properties[$name][$subname] = $value;
	} // _push()
} // trait Properties