<?php

namespace core\csrf;

use core\http\http;
use core\session\session;
use core\token\token;

defined('SCRIPTOK') OR exit('No direct script access allowed');

/**
* class Csrf
*
* This final static class manage the Csrf protection, based on tokens
* comparisons in session and post.
*
*/
final class Csrf
{
  static protected $_token = null;

	/**
	* __construct()
	*
	* private => instanciation not possible.
	*/
  private function __construct()
  {
  } // __construct


  /*
  * check()
  *
  * return false if:
  *  - POST without token
  *  - POST(token) != SESSION(token)
  * Set the Token if not defined (call setToken())
  *
  * @return boolean true if no Csrf issue.
  */
  public static function check()
  {
    global $config;

    if ($config['csrf'] === false) return true;
    
    // Init
    $return = true;
    $post = Http::post('token', null);

    // POST without token.
    if (Http::isPost() && ($post === null)) {
      $return = false;
    }

    // token is defined in POST
    if ($post !== null) {
      if ((($session = Session::get('TOKEN', 'token')) === false) || ($post !== $session)) {
        // No or Bad Token
        $return = false;
      } // if
    } // if post

    self::setToken();
    
    return $return;
  } // check()


  /**
  * getToken()
  *
  * @return Actual or set new token if not defined
  */
  public static function getToken()
  {
    return (self::$_token !== null) ? (self::$_token) :(self::setToken());
  }


  /**
  * setToken()
  *
  * Set the Token only if not defined.
  *
  * @return string token
  */
  public static function setToken()
  {
    if (self::$_token === null) {
      self::$_token = password(mt_rand());
    }

    Session::set('TOKEN', array('token' =>self::$_token));

    return self::$_token;
  }

  /**
  * getTokenView()
  *
  * @return string input tag with type="hidden"
  */
  public static function getTokenView()
  {
    return '<input type="hidden" name="token" value="'.self::$_token.'">';
  }
} // class Csrf