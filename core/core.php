<?php defined('SCRIPTOK') OR exit('No direct script access allowed');

use core\ajax\ajax;
use core\csrf\csrf;
use core\router\router;
use core\user\user;

/**
* autoload
*
*
*/
require 'autoload/autoload.php';

/**
* debug
*
*
*/
require 'debug/debug.php';
debugTitle('CORE', WEBROOT);


/**
* set_error_handler()
*
*
*/
set_error_handler('core\errorhandler\errorhandler::errorHandler');

/**
* copy files
*
*
*/
foreach($config['copyfile'] as $source => $destination) {
  if (copy(ROOT.$source, ROOT.$destination)) {
    debugOK("copy('$source', '$destination')");
  } else {
    trigger_error("'$source', '$destination'");
  } // if
}
unset($source);
unset($destination);

/**
* css
*
*
*/
if (($stylecss = fopen(ROOT.'/public/css/style.css','w')) === false) {
  trigger_error("/public/css/style.css");
}

foreach($config['css'] as $source) {
  if (($css = file_get_contents(ROOT.$source)) === false) {
    trigger_error("$source");
  } else {
    debugOK("style.css : '".ROOT."$source'");
    if (fwrite($stylecss, $css.PHP_EOL.PHP_EOL) === false) {
        trigger_error("$source");
    } // if
  } // if
}

@fclose($stylecss);
unset($stylecss);
unset($css);
unset($source);


/**
*
* helpers
*
*/
require 'helpers/helpers.php';


/**
* Router management
*
*
*/
Router::initialize();


/**
* Ajax management
*
*
*/
Ajax::initialize();




if (Csrf::check()) {
  /**
  * user
  *
  *
  */
    User::initialize();


  /**
  * Controller/Call
  *
  *
  */
    eval(Router::callController());
} else {
  // end of Ajax & token update
  echo '
  [
    {"action":"setLoading","value":"off"},{"action":"endOfAjax"},
    {"action":"token","token":"'.Csrf::getToken().'"}
  ]';
}