<?php

namespace modules\developer;

use core\controller\controller;
use core\ajax\ajax;

/**
* class DeveloperController
*
* This class manage developer's side page.
*
*/
class DeveloperController extends Controller
{
	protected $controller = 'developer';

	/**
	* index()
	*
	* Render index view.
	*/
  public function index()
  {

    global $config;

    debugTitle('Happyadr::index()');

    $sections[] = $this->getView('/developer/summary.html', array());
    $sections[] = $this->getView('/developer/developer.html', array());

		$this->htmlRender(array('sections' => $sections));
  } // index
} // class DeveloperController
