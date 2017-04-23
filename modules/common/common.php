<?php

namespace modules\common;

use core\controller\controller;
use core\user\user;
use core\csrf\csrf;
use modules\user\userController;

defined('SCRIPTOK') OR exit('No direct script access allowed');

/**
* trait Common
*
* This trait Commmon add htmlRender to render final html document.
*
*/
trait Common
{
	/**
	* htmlRender()
	*
	* @param mixed $parameters  All views for final rendering.
	*/
  public function htmlRender($parameters)
  {
    global $config;
    
		$title = (isset($parameters['title'])) ? ($parameters['title']) : 
			($config['<title>']);

		$nav = (isset($parameters['nav'])) ? ($parameters['nav']) : 
			($this->getView('/common/nav.html', array(
				'usernav' => $this->getView('user-nav'))));

		$header = (isset($parameters['header'])) ? ($parameters['header']) : 
			($this->getView('/common/header.html', array('nav' => $nav)));

		$user = (isset($parameters['user'])) ? ($parameters['user']) : 
			($this->getView('user'));

		$main = (isset($parameters['main'])) ? ($parameters['main']) : 
			($this->getView('/common/main.html', array()));

		$sections = (isset($parameters['sections'])) ? ($parameters['sections']) : 
			($this->getView('/common/main.html', array()));

		$footer = (isset($parameters['footer'])) ? ($parameters['footer']) : 
			($this->getView('/common/footer.html', array()));


    $html = $this->getView('/common/html.html', array(
			'title' => $title,
			'meta_description' => $parameters['meta_description'],
			'nav' => $nav,
			'header' => $header,
			'user' => $user,
			'main' => $main,
			'sections' => $sections,
			'footer' => $footer,
			'token' => Csrf::getToken()
		));


    echo $this->render($html);
  } // htmlRender()
} // trait Common