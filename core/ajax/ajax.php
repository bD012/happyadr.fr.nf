<?php

namespace core\ajax;

use core\http\http;

defined('SCRIPTOK') OR exit('No direct script access allowed');

/**
* class Ajax
*
* This static final class manage Ajax datas to be returnes to Ajax request.
*
*/
final class Ajax
{
	protected static $_response;
	protected static $_index;
	protected static $_max;

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
  * Initialize all static properties.
  */
	public static function initialize()
	{
    self::clear();
	} // initialize()


	/**
	* isAjax()
	*
	* Return true if $_POST['ajax'] is defined.
  *
  * @return boolean
	*/
   public static function isAjax()
   {
     return isPost('ajax');
   } // isAjax()


	/**
	* clear()
	*
	* Clear all old Ajax responses.
	*/
   public static function clear()
   {
     self::$_response = null;
     self::$_index = null;
     self::$_max = 0;
   } // clear()


	/**
	* add()
	*
	* Add an Ajax response.
  *
  * @param array $params  Parameters for responses.
	*/
   public static function add($params)
   {
     // witch case on strtolower of 'action'
     switch(strtolower($params['action']))
     {
      // click
      case 'click':
        self::$_response[] = array(
          'action' => 'click',
          'id' => $params['id'],
          'delay' => $params['delay']
        );
        break;

      // endOfAjax
      case 'endofajax':
        self::$_response[] = array(
          'action' => 'endOfAjax'
        );
        break;

      // httpRequest
      case 'httprequest':
        self::$_response[] = array(
          'action' => 'httpRequest',
          'uri' => $params['uri'],
          'delay' => $params['delay']
        );
        break;

      // innerHtml
      case 'innerhtml':
        self::$_response[] = array(
          'action' => 'innerHtml',
          'id' => $params['id'],
          'innerHtml' => $params['innerHtml']
        );
        break;

      // setAttr
      case 'setattr':
        self::$_response[] = array(
          'action' => 'setAttr',
          'attr' => $params['attr'],
          'id' => $params['id'],
          'value' => $params['value']
        );
        break;

      // setLoading (On or Off)
      case 'setloading':
        self::$_response[] = array(
          'action' => 'setLoading',
          'value' => strtolower($params['value'])
        );

        if (strtolower($params['value']) == 'off') {
          self::$_response[] = array(
            'action' => 'endOfAjax'
          );
        } // if
        break;

      // Token
      case 'token':
        self::$_response[] = array(
          'action' => 'token',
          'token' => $params['token']
        );
        break;

      // default
      default:
        trigger_error("Ajax::add($param[action])");
      break;
     } // end switch

     self::$_max++;
   } // add()


	/**
	* getNext()
	*
  * Get the next ajax response
	*
  * @return next ajax (array)response / false for end of responses.
	*/
   public static function getNext()
   {
     // first call
     if (self::$_index === null) {
       self::$_index = 0;
     }

     if (self::$_index < self::$_max) {
       return self::$_response[self::$_index++];
     } else {
       self::clear();
       return false;
     }
   } // get()
} // class Ajax