<?php

namespace Core\Router;

use core\session\session;
use core\http\http;

defined('SCRIPTOK') OR exit('No direct script access allowed');

/**
* class Router
*
* This final static class is the Router manager.
*
*/

class Route
{
	public $route;										// /controller/function/12
	public $controller;								// controller
	public $call;								      // controller
	public $main;								      // main Controller/Call
	public $variables;								// /controller/function/{id}
}

final class Router
{
	protected static $REGEXP_DELIMITER = '#';

	protected static $_router = null;
	

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
	* Parse routes and initialize the Router.
	*
	* init :
	*  - $controller & $call
	*  - $main[$controller, $call] : main controller to be called after
	*
	* @param string $path  To extend Router management.
	*/
	public static function initialize($path = null)
	{
		global $config;

		debugTitle('ROUTER');

		if (self::$_router === null) {
			if ($_SERVER['HTTP_HOST'] == 'localhost') { // local
				if (($path === null) && (isset($_SERVER['PATH_INFO']))) {
						$path = $_SERVER['PATH_INFO'];
				}
			} else { // Production
				$path = $_SERVER['REQUEST_URI'];
			}

			self::$_router = new Route();

			// Get main Controller/Call from session
			if ($main = Session::get('ROUTER','main')) {
				self::$_router->main = $main;
			}

			// parse global $routes to self::$route with regexp definition
			$vars = self::_parseRoutes($path);
			debug('Router::initialize() : SelectedRoute', $vars);
			// at least controller and function
			$key = explode('/', $vars);
			$value = explode('/', self::$_router->route);

      $main = false;
			$imax = count($key);
      for($i=0;$i<$imax;$i++) {

				if ($key[$i] != '') {

					// * => main
					if ($key[$i][0] == "*") {
						$main = true;
					} else 

					// #controller
					if ($key[$i][0] == "#") {
						self::$_router->controller = substr($key[$i], 1);
					} else 

					// @call
					if ($key[$i][0] == "@") {
						self::$_router->call =  substr($key[$i], 1);
					} else

					// { variable }
					if (preg_match('#^{(.+)}$#',$key[$i],$goodKey) == 1) {
						self::$_router->variables[$goodKey[1]] = $value[$i];
					} // if
				} // if
      } // for
			
			if ($main) {
				self::$_router->main['controller'] = self::$_router->controller;
				self::$_router->main['call'] = self::$_router->call;

				Session::set('ROUTER', array('main' =>self::$_router->main));
			}
			debug('Router::$_router', self::$_router);
		} // if
	} // initialize()


	/**
	* redirect()
	*
	* @param string $controller  Controller to be redirected.
	* @param string $call  Method call to be redirected.
	*/
	public static function redirect($controller, $call)
	{
    eval(Router::callController($controller, $call));
  } // redirect()


	/**
	* _replace()
	*
	* From '#controller@call' to '/#controller/@call'
	*
	* @param string $value From string
	*
	* @return string $To
	*/
	protected static function _replace($value)
	{
		$cut = explode('/', $value);
		$value='';
		$imax = count($cut);
		for($i=0; $i<$imax;$i++) {
			if (preg_match('#([^@]+)@([^@]+)#', $cut[$i], $matches) === 1) {
				// controller = #, call = @
				$value .= "/#$matches[1]/@$matches[2]";
			} else {
				$value .= ($i==0) ? ("$cut[$i]") : ("/$cut[$i]");
			} // if
		} // for

		return $value;
	} // _replace


	/**
	* _parseRoutes()
	*
	* Parsing all routes, set to Home or http_error if necessary.
	*
	* @param string $path Path to be parsed.
	*
	* @return string $Route  Route like '* /#controller/@call/{variable}'
	*/
	private static function _parseRoutes($path)
	{
		global $config, $routes;
		$cpyRoutes = array();

		$search = array_keys($config['4routes']);
		$replace = array_values($config['4routes']);

		foreach($routes as $key => $value) {
				
			$cpyKey = self::$REGEXP_DELIMITER.'^'
				.str_replace($search, $replace, $key)
				.'$'.self::$REGEXP_DELIMITER;

			$value = self::_replace($value);

			$cpyRoutes[$cpyKey] = $value;
		} // foreach
		debug('Router::_parseRoutes() : Path', $path);
		debug('Router::_parseRoutes() : CopyRoutes', $cpyRoutes);

		if ($path !== null) {
			foreach($cpyRoutes as $key => $value) {
				if (preg_match($key, $path) === 1) {

					// routes found
					self::$_router->route = $path;
					return $value;
				}
			} // foreach

			// http_error 404
			self::$_router->route =  $path;
			return self::_replace($config['routes']['error']);
		} else {

			// route : '/'
			self::$_router->route = '/';

			return $cpyRoutes['#^/$#'];
		} // if
	} // _parseRoutes()


	/**
	* callController()
	*
	* Specific management in case of Ajax request on main.
	*
	* @return string $for_eval  For eval function like :
	*   (new Controller())->Call();(new MainController())->Call();
	*/
	public static function callController($controller = null, $call = null)
	{
    if ($controller === null) {

			// Controller from Uri
      //$controller = ucfirst(self::getController());
			$controller = self::getController();
      // $_CONTROLLER = new Controller(self::Router);
      //$return = '(new \\Modules\\'.$controller.'\\'.$controller.'Controller())';
      $return = '(new \\modules\\'.$controller.'\\'.$controller.'Controller())';

      // $_CONTROLLER->Function()
      $return .= '->'.self::getCall().'();';

			if (Http::post('ajax') != 'ajax') {
				// Controller from main
				//if ($controller != ucfirst(self::$_router->main['controller'])) {
				if ($controller != self::$_router->main['controller']) {
					$controller = self::$_router->main['controller'];
					//$return .= '(new \\Modules\\'.$controller.'\\'.$controller.'Controller())';
					$return .= '(new \\modules\\'.$controller.'\\'.$controller.'Controller())';

					$return .= '->'.self::$_router->main['call'].'();';
				}
			} // if ajax
    } else {
      //$controller = ucfirst($controller);
      //$return = '(new \\Modules\\'.$controller.'\\'.$controller.'Controller())';
      $return = '(new \\modules\\'.$controller.'\\'.$controller.'Controller())';
      $return .= '->'.$call.'();';
    }
		
		debug('Router::callController()', $return);

		return $return;
	}


	/**
	* getController()
	*
	* @return string $controller  Get the Controller to be instancied.
	*/
	public static function getController()
	{
    if (($controller = self::$_router->controller) !== null) {
			return $controller;
    } else {
      debugError('Router::self->controller = null (bad $route[] definition in routes.php.');
      trigger_error();
      die();
    } // if
	} // getController()


	/**
	* getCall()
	*
	* @return string $call  Get the Calling method of the Controller.
	*/
	public static function getCall()
	{
    if (($call = self::$_router->call) !== null) {
			return $call;
    } else {
      debugError('Router::self->call = null (bad $route[] definition in routes.php.');
      trigger_error();
      die();
    } // if
	} // getCall()


	/**
	* getMainController()
	*
	* @return string $mainController  Get the Controller to be instancied.
	*/
	public static function getMainController()
	{
		if (($controller = self::$_router->main['controller']) !== null) {
			return $controller;
    } else {
      debugError('Router::self->controller = null (bad $route[] definition in routes.php.');
      trigger_error();
      die();
    } // if
	} // getMainController()


	/**
	* getMainCall()
	*
	* @return string $mainCall  Get the Calling method of the Controller.
	*/
	public static function getMainCall()
	{
		if (($call = self::$_router->main['call']) !== null) {
			return $call;
    } else {
      debugError('Router::self->call = null (bad $route[] definition in routes.php.');
      trigger_error();
      die();
    } // if
	} // getMainCall()


	/**
	* getVariable()
	*
	* @return string $variable  Get the Variables.
	*/
	public static function getVariable($name=null, $default=null)
	{
		if ($name === null) {
			$return = self::$_router->variables;
		} else {
			$return = (isset(self::$_router->variables[$name])) ? (self::$_router->variables[$name]) : ($default);
		} // if

		return $return;
	} // getVariable()

} // class Router
