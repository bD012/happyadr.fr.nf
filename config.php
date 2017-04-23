<?php

/* *****************************************************************
 *  $config
 *
 *	 
 */
//define('ROOT', '.');
define("ROOT",dirname(__FILE__));

if ($_SERVER['SERVER_NAME'] != "localhost") {
  define('WEBROOT', 'http://www.happyadr.fr.nf');
} else {
  define('WEBROOT', '/happyadr.fr.nf');
}

// password
define('PASSWORD_ALGO', 'sha256');
define('PASSWORD_KEY', 'T-REX*12!');

// Token
$config['token-key'] = '!kfIuy88%:Kio,qw155!';

/* *****************************************************************
 * Developement or Production
 *
 * dev = true (development) or false (production)
 */
$config['dev'] = true;
$config['csrf'] = false;

/* *****************************************************************
 * Meta title
 *
 */
$config['<title>'] = '{happy}adr';


/* *****************************************************************
 * Contact
 *
 */
$config['contact']['to'] = 'bdelansay@gmail.com';


/* *****************************************************************
 * User
 *
 */
$config['user']['controller'] = 'user';

// project path
//$config['path'] = '.';

$config['path_modules'] = '/modules';

// copy files definition (access only on public path)
$config['copyfile']['/core/ajax/ajax.js'] = '/public/js/ajax.js'; 
$config['copyfile']['/modules/common/js.js'] = '/public/js/js.js'; 

// $config['copyfile']['/libraries/icomoon/icomoon.css'] = '/public/css/icomoon.css'; 
$config['copyfile']['/libraries/icomoon/icomoon.eot'] = '/public/fonts/icomoon.eot'; 
$config['copyfile']['/libraries/icomoon/icomoon.svg'] = '/public/fonts/icomoon.svg'; 
$config['copyfile']['/libraries/icomoon/icomoon.ttf'] = '/public/fonts/icomoon.ttf'; 
$config['copyfile']['/libraries/icomoon/icomoon.woff'] = '/public/fonts/icomoon.woff'; 

// css files
$config['css'][] = '/libraries/flexbox/resetcss.css';
$config['css'][] = '/libraries/flexbox/flexbox.css';
$config['css'][] = '/libraries/flexbox/mediaqueries.css';
$config['css'][] = '/core/debug/debug.css';
$config['css'][] = '/core/ajax/ajax.css';

$config['css'][] = '/libraries/icomoon/icomoon.css';

$config['css'][] = '/themes/happy/generic.css';
$config['css'][] = '/themes/happy/header_nav.css';
$config['css'][] = '/themes/happy/input.css';
$config['css'][] = '/themes/happy/utils.css';
$config['css'][] = '/modules/logo/logo.css';
$config['css'][] = '/modules/common/style.css';

$config['css'][] = '/modules/user/user.css';
$config['css'][] = '/modules/backoffice/backoffice.css';
$config['css'][] = '/modules/developer/developer.css';
$config['css'][] = '/modules/common/z-index.css';

// Model
$config['model'] = array('_status' => 0, '_comment' => null);

// database : pdo
$config['pdo']['dsn'] = 'mysql:host=localhost;dbname=happyadr';
$config['pdo']['username'] = 'root';
$config['pdo']['password'] = '';


/**
*  $routes['']
*
*	 /{controller}/{call}/variables...
*/
// helpers (/!\ order)
$config['4routes']['#all'] = '.*';                   // all characters, must ending a definition
$config['4routes']['#char'] = '[a-zA-Z]';            // lowercase or uppercase characters
$config['4routes']['#num'] = '[0-9]';                // numbers
$config['4routes']['#mixed'] = '[a-zA-Z0-9-]';       // lower/Uppercase num '-' 
$config['4routes']['#postcode'] = '[0-9]{5}';        // postcode
$config['4routes']['#city'] = '[\+\$\"_a-zA-Z-]{1,30}';     // city
$config['4routes']['#street'] = "[\+\$\"_a-zA-Z0-9']";    // street 
$config['4routes']['#address'] = "[a-zA-Z0-9,_]";    // address 

// http_error
//$routes['#all'] = '/httperror/error404';
$config['routes']['error'] =    '/httperror@error404'; 
?>