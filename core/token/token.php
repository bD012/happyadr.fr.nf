<?php

namespace core\token;
use libraries\jwt\src\JWT;

defined('SCRIPTOK') OR exit('No direct script access allowed');

/**
* class Token
*
* This static final class manage Token based on JWT library.
*
*/
final class Token
{
  const INFINITE = null;

  protected static $_token;


  /**
  * __construct()
  *
  *
  */
  private function __construct()
  {
  } // __construct


  /**
  * encode()
  *
  * @param array $payload  Data to be inserted in body of Token.
  * @param integer $_time  Time validity of the Token
  *
  * @return string $token  Encoded Token
  */
  public static function encode($payload, $_time=self::INFINITE)
  {
    global $config;

		debugInfo('Token:encode($payload, '.$_time.')');
		debug('$payload', $payload);

    if ($_time === self::INFINITE) {
      $payload['_time'] = $_time;
    } else {
      $payload['_time'] = time() + (60 * $_time);
    }

    self::$_token = JWT::encode($payload, $config['token-key']);

    debug('Token::encode: $payload', $payload);
    debug('Token::encode: self::$_token', self::$_token);

    return self::$_token;
  } // encode


  /**
  * decode()
  *
  * @param string $token  Encoded Token
  *
  * @return array $payload  Decoded Token or null is bad Token.
  */
  public static function decode($token=null)
  {
    global $config;

    try{
      self::$_token = (array)JWT::decode($token, $config['token-key'], array('HS256'));
    } catch (\Exception $e) {
      self::$_token = null;
    }

    unset(self::$_token['_time']);

    return self::$_token;
  } // decode()


  /**
  * check
  *
  * @param string $token  Encoded Token
  *
  * @return boolean  false if token != savedToken || timeOut
  */
  public static function check($token)
  {
    if ($token != self::$_token) return false;

    $decoded = self::$_decode($token);

    if (($decoded['_time'] !== self::INFINITE) and (($decoded['_time'] - time()) < 0)) {
      return false;
    }

    return true;
  } // check()


  /**
  * getter
  *
  * @param string $name  Name of the Token payload value
  * @param mixed $default  Default Value
  *
  * @return mixed $token  All Token if $name === null
  *   mixed $value  Value of Token[$name]
  */
  public static function get($name = null, $default = null)
  {
    if ($name === null) return self::$_token;

    return (isset(self::$_token[$name])) ? (self::$_token[$name]) : ($default);
  } // get()


  /**
  * htmlToken()
  *
  * @return string $view  View of input tag, type="hidden, value=Token
  */
  public static function htmlToken()
  {
     return '<input class"flexItem" type="hidden" name="token" value="'.self::$_token.'"';
  } // htmlToken()
} // class Token