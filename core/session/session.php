<?php

namespace core\session;

defined('SCRIPTOK') OR exit('No direct script access allowed');

/**
* class Session
*
* This final class manage Session datas.
*
*/
final class Session
{

	/**
	* set()
	*
	* SESSION are like :
	*  - SESSION['USER'] = array('username' =>'', 'role' => 0);
	* @param string $name  Name of the Session
	* @param array $values  Values for the Session[$name]
	*/
	public static function set($name, $values)
	{
		foreach($values as $key => $value) {
			$_SESSION[$name][$key] = $value;
		} // foreach

		debug('Session::set("'.$name.'")', $_SESSION[$name]);
	} // set()


	/**
	* get()
	*
	* @param string $name  Name of the Session
	* @param string $subname  SubName of the Session
	*
	* @return array $SESSION[$name] if $subname === null else
	*   mixed $SESSION[$name][$subname]
	*/
	public static function get($name, $subname = null)
	{
		if ($subname === null) {
		 return isset($_SESSION[$name]) ? ($_SESSION[$name]) : (false);
		} else {
		 return isset($_SESSION[$name][$subname]) ? ($_SESSION[$name][$subname]) : (false);
		}
	} // get()


	/**
	* forget()
	*
	* unset the SESSION[$name] or SESSION[$name][$subname]
	*
	* @param string $name  Name of the Session
	* @param string $subname  SubName of the Session
	*/
	public static function forget($name, $subname = null)
	{
		if ($subname === null) {
		 unset($_SESSION[$name]);
		} else {
		 unset($_SESSION[$name][$subname]);
		}
	} // forget()
} // class Session