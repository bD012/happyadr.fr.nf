<?php

defined('SCRIPTOK') OR exit('No direct script access allowed');

/**
* Helpers function for debug
*
* 
* 
*
*/

if ($config['dev'])
{
	$_debug = array();
}

/**
 * real_var_dump()
 *
 * @param mixed $variable
 *
 * @return string modified var_dump() display
 */
function _real_var_dump($variable)
{
	// Parsing
	ob_start();

	var_dump($variable);

	$content = ob_get_contents();
	ob_end_clean();

	return preg_replace('#<small>.+debug\.php.+</small>#','',$content);
} // debug_var_dump()


/**
 * _var_dump()
 *
 * @param mixed $variable
 *
 * @return string my var_dump
 */
function _var_dump($variable, $tab=0)
{
	// Initialisation
	$return ='';


	switch(gettype($variable))
	{
		case 'array':

			$return .= '<span class="debug-array-txt">';
			$return .= 'array<span class="debug-type-txt">(length = '.count($variable).')</span></span>';
			$return .= str_repeat('&nbsp', 2*($tab+1));


			foreach($variable as $key => $value) {

				$return .= "<p>";
				$return .= str_repeat('&nbsp', 2*($tab+1));

				$keytype = gettype($key);

				// Start
				switch($keytype)
				{	
					case 'integer':
						$return .= '<span class="debug-array-key">['.$key.']</span> = ';
						break;
					case 'string':
						$return .= '<span class="debug-array-key">\''.$key.'\'</span> => ';
						break;
				} // switch

				// Value
				$return .= _var_dump($value, $tab+1);

				// End
				switch($keytype)
				{	
					case 'integer':
						$return .= '</p>';
						break;
					case 'string':
						$return .= '</p>';
						break;
				} // switch
			} // foreach
			break;

		case 'boolean':
			$return .= '<span class="debug-type-txt">(boolean)</span>';
			$return .= '<span> = </span>';
			$return .= '<span class="debug-boolean-vardump">';
			$return .= ($variable) ? ('true') : ('false');
			$return .= '</span">';
			break;

		case 'double':
			$return .= '<span class="debug-type-txt">(float)</span>';
			$return .= '<span> = </span>';
			$return .= '<span class="debug-double-vardump">';
			$return .= $variable;
			$return .= '</span">';
			break;

		case 'integer':
			$return .= '<span class="debug-type-txt">(integer)</span>';
			$return .= '<span> = </span>';
			$return .= '<span class="debug-integer-vardump">';
			$return .= $variable;
			$return .= '</span">';
			break;

		case 'NULL':
			$return .= '<span class="debug-type-txt">(null)</span>';
			$return .= '<span> = </span>';
			$return .= '<span class="debug-null-vardump">';
			$return .= 'null';
			$return .= '</span">';
			break;

		case 'object':
		case 'resource':
			$return .= '<span class="debug-object-txt">';
			$return .= str_repeat('&nbsp', 2*($tab+1));
			$return .= _real_var_dump($variable);
			$return .= '</span>';
			break;

		case 'string':
			$return .= '<span class="debug-type-txt">(string)</span>';
			$return .= '<span> = </span>';
			$return .= '<span class="debug-string-vardump">';
			$return .= "'".$variable."'";
			$return .= '<span class="debug-type-txt">(length = '.strlen($variable).')</span>';
			$return .= '</span">';
			break;

		default:
			$return .= '<p>UNKNOWN TYPE !!!</p>';
			break;
	} // switch
	return $return;
} // _var_dump()

/**
*	debug()
*
* Prepare debug variable
*
* @param string $message  Message to be displayed for debug information
* @param mixed $variable  Variable to be debugged
*/
	function debug($message, $variable)
{
	global $config;

	// debug only for developement
	if ($config['dev']) {
	
		$content = _var_dump($variable);

		global $_debug;
		$_debug[] = array(
			'type' => 'variable',
			'name' => $message,
			'dump' => $content
		);
	}
} // debug()


/**
*	debugTitle()
*
* Prepare debug Title
*
* @param string $message  Message to be displayed for debug information
* @param string $comment  Complementary comment
*/
	function debugTitle($message, $comment = '')
{
	global $config;
	
	// debug only for developement
	if ($config['dev']) {
	
		global $_debug;
		$_debug[] = array(
			'type' => 'title',
			'message' => $message,
			'comment' => $comment
		);
	}
} // debugTitle()

/**
*	debugInfo()
*
* Prepare debug Information
*
* @param string $message  Message to be displayed for debug information
* @param string $comment  Complementary comment
*/
	function debugInfo($message, $comment = '') {
	global $config;
	
	// debug only for developement
	if ($config['dev']) {
	
		global $_debug;
		$_debug[] = array(
			'type' => 'info',
			'message' => $message,
			'comment' => $comment
			);
	}
} // debugInfo()


/**
*	debugOK()
*
* Prepare debug OK
*
* @param string $message  Message to be displayed for debug information
* @param string $comment  Complementary comment
*/
	function debugOK($message, $comment = '') {
	global $config;
	
	// debug only for developement
	if ($config['dev']) {
	
		global $_debug;
		$_debug[] = array(
			'type' => 'ok',
			'message' => $message,
			'comment' => $comment
			);
	}
}// debugOK()


/**
*	debugWarning()
*
* Prepare debug Warning
*
* @param string $message  Message to be displayed for debug information
* @param string $comment  Complementary comment
*/
	function debugWarning($message, $comment = '') {
	global $config;
	
	// debug only for developement
	if ($config['dev']) {
	
		global $_debug;
		$_debug[] = array(
			'type' => 'warning',
			'message' => $message,
			'comment' => $comment
			);
	}
} // debugWarinig()


/**
*	debugError()
*
* Prepare debug Error
*
* @param string $message  Message to be displayed for debug information
* @param string $comment  Complementary comment
*/
	function debugError($message, $comment = '') {
	global $config;
	
	// debug only for developement
	if ($config['dev']) {
	
		global $_debug;
		$_debug[] = array(
			'type' => 'error',
			'message' => $message,
			'comment' => $comment
			);
	}
} // debugError()


/**
*	debugDisplay()
*
* @return html of all debug informations.
*/
function debugDisplay()
{
	global $_debug;

	$return = '';
	// $return = '<input id="debug-switch" type="checkbox">';	

	// $return .= '<div id="debug">';
	$return .= '<div id ="debug-flex"class="flexContainer">';

	$imax = count($_debug);
	for($i=0; $i<$imax; $i++) {

		$debug = $_debug[$i];

		$counter = sprintf('%04d', $i + 1);

		$type = $debug['type'];

		$return .= '<div class="flexItemContainer flexS12  flexIStretch debug-'.$type.'">';
		// Counter
			$return .= '<div class="flexItemContainer flexS12 flexM01">';
				$return .= '<div class="flexItem debug-counter">['.$counter.']</div>';
			$return .= '</div>';

		if ($type == 'variable') {

			// Message & Comment
			$return .= '<div class="flexItemContainer flexS12 flexM11 flexIMiddle">';

				$return .= '<div class="flexItem flexS12 debug-var-name">';
					$return .= 	$debug['name'].' : ';
					$return .= '<span class="debug-var-dump">'.$debug['dump'].'</span>';
				$return .= '</div>';

			$return .= '</div>';
		} else {

			$debug['message'] = htmlspecialchars($debug['message']);

			// Message & Comment
			$return .= '<div class="flexItemContainer flexS12 flexM11 flexIMiddle">';

				$return .= '<div class="flexItem flexS12 debug-message">'.$debug['message'].'</div>';
			if ($debug['comment'] != '') {
				$return .= '<div class=" flexItemContainer flexS12 flexIBottom debug-comment">';
					$return .= '<div class="flexItem">('.$debug['comment'].')</div>';
				$return .= '</div>';
			}
			$return .= '</div>';
		}
		$return .= '</div>';
	} // for

	$return .= '</div>';
	// $return .= '</div>';

	return $return;
} // debugDisplay()


/**
*	debugHtml()
*
* add SUPER GLOBALS to debug then return html debug view (debugDisplay())
*
* @return html of all debug informations.
*/
function debugHtml()
{
	global $config;

	// debug only for developement
	if ($config['dev']) {

		// SUPER GLOBAL
		debugTitle('SUPER GLOBAL');
		debug('$_SESSION', $_SESSION);
		debug('$_GET', $_GET);
		debug('$_POST', $_POST);
		debug('$_SERVER', $_SERVER);

		// Display
		return debugDisplay();
	} // if
} // debugHtml()