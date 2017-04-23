<?php

namespace core\View;

use core\ajax\ajax;
use core\csrf\csrf;
use core\router\router;
use core\http\http;

defined('SCRIPTOK') OR exit('No direct script access allowed');

/**
* class View
*
* This class manage Views (render of html files)
*
*/

class View
{
	public $id;
	public $file;
	public $variables;	
} // class View

class ViewController
{
	protected static $_saved;
	protected $ajax;
	
	/**
	* setView()
	*
	* @param string $id  Id of the View.
	* @param string $file  File of the View.
	* @param mixed $variables  Variables for the View.
	*
	* @return View $view
	*/
	public function setView($id, $file, $variables)
	{
		$view = new View;
		$view->id = $id;
		$view->file = $file;
		$view->variables = $variables;

		self::$_saved[$id] = $view;

		return self::$_saved[$id];
	} // setView()


	/**
	* getView()
	*
	* $variables === null => return savedView[$view]
	* else return (view)$view{id=null, file=$view, variables=$variables}
	*
	* @param string $view  Saved View.
	* @param array $variables.
	*
	* @return View $view
	*/
	public function getView($view, $variables = null)
	{
		if ($variables === null) {
			return (isset(self::$_saved[$view])) ? (self::$_saved[$view]) : (null);
		} else {
			$return = new View;

			$return->id = null;
			$return->file = $view;
			$return->variables = $variables;

			return $return;
		}
	} // getView()


	/**
	* isView()
	*
	* @return boolean $isView(id)
	*/
	public function isView($id)
	{
		return isset(self::$_saved[$id]);
	} // isView()


	/**
	* render
	*
	* Render View($view), if $view='ajax', render only Ajax view.
	*
	* @param View $view  View to be rendrered, if 'ajax' => render (ajax view)
	* @param array $variables  Variables to be used by the view.
	*/
	public function render($view, $variables=array()) {
		global $config;

		debug('$view', $view);
		
		// rendering ajax only...
		if ($view !== 'ajax') {
			if (gettype($view) == 'string') {
				if (isset(self::$_saved[$view])) {
					$view = self::$_saved[$view];
				} // if
			} // if

			$html = $this->renderHtml($view->file, $view->variables);
		} // if

		if (Http::post('ajax') == 'ajax') {
			// add $html
			if (isset($html)) {
				Ajax::add(array(
					'action' => 'innerHtml',
					'id' => $view->id,
					'innerHtml' =>  $html
				));
			}

			// add token
			Ajax::add(array(
				'action' => 'token',
				'token' => Csrf::getToken()
			));

			// add debug to Ajax
			if ($config['dev']) {
				Ajax::add(array(
					'action' => 'innerHtml',
					'id' => 'debug',
					'innerHtml' => debugHtml()
				));
			}

		// add debug to Ajax
		// 	Ajax::add(array(
		// 		'action' => 'endOfAjax'
		// 	));

			// add Ajax
			while(($ajax = Ajax::getNext()) !== false) {
				$array[] = $ajax;
			}

			echo json_encode($array);
			$return = null;
		} else {
			// html
			$return = $html;
		}

		return $return;
	} // render()


	/**
	* renderHtml
	*
	* html rendering. File or Views are managed.
	*
	* @param string $file  File/View to be rendered.
	* @param array $variables  Variables to be used by the view.
	*
	* @return string $view  Rendered view in html format.
	*/
	public function renderHtml($file, $variables=array())
	{
		foreach($variables as $variable => $value) { 
			if (gettype($value) == 'object') {
				eval('$'.$variable.' = $this->renderHtml($value->file, $value->variables);');
				unset($variables[$variable]);
			} else
			if (gettype($value) == 'array') {
				$imax = count($value);
				eval('$'.$variable.' = \'\';');
				for($i=0; $i<$imax; $i++) {
					eval('$'.$variable.' .= $this->renderHtml($value['.$i.']->file, $value['.$i.']->variables);');
				} // for
				unset($variables[$variable]);
			} // if
		} // foreach
		extract($variables);

		debugInfo("render($file)");
		debug('$variables', $variables);

		ob_start();
		include ROOT.'/modules'.$file;
		$renderedView = ob_get_clean();
		//debug('$renderedView', $renderedView);
		return $renderedView;
	} // renderHtml()


	/**
	* innerHtmln
	*
	* @param string $file  File to be rendered.
	* @param array $variables  Variables to be used by the view.
	*
	* @return string $view  Rendered view in html format.
	*/
	public function innerHtml($file, $variables=array())
	{
		extract($variables);

		debugInfo("innnerHtml($file)");
		debug('$variables', $variables);

		ob_start();
		include ROOT.'/modules'.$file;
		$renderedView = ob_get_clean();
		return $renderedView;
	} // innerHtml()
} // class View