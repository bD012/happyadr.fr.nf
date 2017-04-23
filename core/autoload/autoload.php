<?php

defined('SCRIPTOK') OR exit('No direct script access allowed');

/**
* _autoload()
*
* class autoload function
*
* @param string $class  Class name
*/
function _autoload($class)
{
	$include = ROOT.'\\'.$class.'.php';
	$include = str_replace('\\', '/', $include);
	//var_dump($include);

	if (file_exists($include)) {
		require_once $include;
	} // 
} // _autoload()


/**
 *	 spl_autoload_register()
 *
 */
spl_autoload_register('_autoload');