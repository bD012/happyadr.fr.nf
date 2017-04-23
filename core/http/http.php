<?php

namespace core\http;

defined('SCRIPTOK') OR exit('No direct script access allowed');

/**
* class Ajax
*
* This static class manage Http (get and post) variables and implement protection.
*
*/
class Http
{
  protected static $_get = null;
  protected static $_post = null;


	/**
	* get()
	*
  * @return string protected($_GET['name']) or default if not defined
	*/
  public static function get($name = null, $default = '')
  {
    // Parse _GET if not
    if (self::$_get === null) {
      if (!empty($_GET)) {
        foreach($_GET as $key=>$value) {
          self::$_get[$key] = htmlspecialchars($value);
        } // foreach
      } else {
        
      } // if
    } // if

    // _GET all
    if ($name === null) {
      return self::$_get;
    } else {
      if (isset(self::$_get[$name]) && (self::$_get[$name] !== '')) {
        return self::$_get[$name];
      } else {
        return $default;
      } // if
    } // if
  } // get()


	/**
	* post()
	*
  * @return string protected($_POST['name']) or default if not defined
	*/
  public static function post($name = null, $default = '')
  {
    // Parse _POST if not
    if (self::$_post === null) {
      if (!empty($_POST)) {
        foreach($_POST as $key=>$value) {
          self::$_post[$key] = htmlspecialchars($value);
        } // foreach
      } // if
    } // if

    // _POST all
    if ($name === null) {
      return self::$_post;
    } else {
      if (isset(self::$_post[$name]) && (self::$_post[$name] !== '')) {
        return self::$_post[$name];
      } else {
        return $default;
      } // if
    } // if
  } // post()


	/**
	* isPost()
	*
  * @return string protected($_GET['name']) or default if not defined
	*/
  public static function isPost($name = null)
  {
    if ($name === null) {
      return (!empty($_POST));
    } else {
      return isset($_POST[$name]);
    }
  } // isPost()
} // class Http