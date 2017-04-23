<?php

namespace core\errorhandler;

defined('SCRIPTOK') OR exit('No direct script access allowed');

/**
* class Errorhandler
*
* This class manage the ErrorHandler.
*
*/
class Errorhandler
{
	/**
	* errorHandler()
	*
	*
	*/
	public static function errorHandler($errno, $errstr, $errfile, $errline)
	{
		global $config;
		
		if ($config['dev']) {
			debugError("'$errstr', file:'$errfile' @ line : '$errline'");
			// echo "'$errstr', file:'$errfile' @ line : '$errline'";
		}
	}
} // class errorhandler