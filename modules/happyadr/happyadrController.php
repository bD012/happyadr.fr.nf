<?php

namespace modules\happyadr;

use core\controller\controller;
use core\ajax\ajax;

/**
* class HappyadrController
*
* This class manage happyadr page.
*
*/
class HappyadrController extends Controller
{
	protected $controller = 'happyadr';

	/**
	* index()
	*
	* Render index view.
	*/
  public function index()
  {

    global $config;

    debugTitle('Happyadr::index()');

    $sections[] = $this->getView('/happyadr/introduction.html', array());
    $sections[] = $this->getView('/happyadr/description.html', array());
    $sections[] = $this->getView('/happyadr/documentation.html', array());

		$this->htmlRender(array(
      'meta_description' => "happy{adr} is a secured site based on INSEE files for adress in france and provide also a web API for search adress results with different search filters.",
      'sections' => $sections
      ));
  } // index
} // class HappyadrController
