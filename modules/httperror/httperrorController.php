<?php

namespace modules\httperror;

use core\controller\controller;
use modules\user\userController;

defined('SCRIPTOK') OR exit('No direct script access allowed');

/**
* class HttperrorController
*
* This class manage http errors rendering.
*
*/
class HttperrorController extends Controller
{

	/**
  * error404()
  *
  * Render 404 page.
  */
 public function error404()
  {
    global $config;

    debugTitle('HttpError::error404()');

    $sections[] = $this->getView('/httperror/404.html', array());

		$this->htmlRender(array('sections' => $sections));
  } // error404()
} // class HttpError
