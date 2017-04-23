<?php

namespace core\controller;

use core\ajax\ajax;
use core\router\router;
use core\view\viewController;


defined('SCRIPTOK') OR exit('No direct script access allowed');

/**
* class Controller to be extended
*
* This  class has to be extended for Controller modules.
*
*/
class Controller
{
	use \modules\common\common;

	protected $_view;
	
	/**
	* __construct()
	*
	* this method is final, call child method initialize()
	*/
	final public function __construct($param = null)
	{
		$this->_view = new ViewController();

		// Clear Ajax datas
		Ajax::clear();

		// call initialize() if exists
		if (method_exists($this, 'initialize')) {
			if ($param === null) {
				call_user_func(array($this, 'initialize'));
			} else {
				call_user_func(array($this, 'initialize'), $param);
			} // if
		} // if
	} // _construct()


	/**
	* setView()
	*
	* call the $this->_view method
	*/
	public function setView($id, $file, $variables)
	{ 
		return $this->_view->setView($id, $file, $variables);
	} // setView()


	/**
	* getView()
	*
	* call the $this->_view method
	*/
	public function getView($view, $variables = null)
	{
		return $this->_view->getView($view, $variables);
	} // getView()


	/**
	* isView()
	*
	* call the $this->_view method
	*/
	public function isView($id)
	{
		return $this->_view->isView($id);
	} // isView()


	/**
	* render()
	*
	* call the $this->_view method
	*/
	public function render($file = null, $variables = array())
	{ 
		return $this->_view->render($file, $variables);
	} // render()


	/**
	* innerHtml()
	*
	* call the $this->_view method
	*/
	public function innerHtml($file, $variables=array())
	{ 
			return $this->_view->innerHtml($file, $variables);
	} // innerHtml()
} // class Controller