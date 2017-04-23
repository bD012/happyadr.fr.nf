<?php

namespace modules\contact;

use core\controller\controller;
use core\http\http;
use core\router\router;

use libraries\captcha\captcha;

defined('SCRIPTOK') OR exit('No direct script access allowed');

/**
* class ContactController
*
* This class manage Contact page.
*
*/
class ContactController extends Controller
{
	// Auth User
	protected $controller = 'contact';

	use \modules\common\common;

	/**
	* contact()
	*
	* Render contact page.
	*/
  public function contact()
  {
		debugTitle('ContactController::contact()');

    global $config;

		//Captcha initialisation
		$captcha = new Captcha(ROOT.'/public/captcha', WEBROOT.'/public/captcha');

		$validator = new contactValidator();

		if ((Router::getCall($this->controller) == 'contact') && (Http::post() !== null)) {
			// call from form

			// Acces to DataBase
 			$db = new contactModel();

			// check _POST according Validator (and DataBase)
			if ($validator->check(Http::post()) && ($captcha->check())) {
				if (($ok = $this->email())) {
					$this->emailsent();
				} // if
			} // if
		} // if

		// Captcha
		$validator->addFeedback('captcha', $captcha->getImg());
		$validator->addFeedback('captcha_reload', $captcha->getReload());

    $sections[] = $this->getView('/contact/contact.html', $validator->getFeedback());

		$this->htmlRender(array(
			  'meta_description' => "happy{adr} is secured site based on INSEE files for adress in france, here is the contact form with captcha validation.",
				'sections' => $sections
			));
  } // contact()


	/**
	* email()
	*
	* render emailSent page.
	*/
  public function email()
  {
		global $config;

		$to = $config['contact']['to'];
		$subject = 'happyadr : '.date('Y-m-d G:i:s');
		$message = 'Message from : '.Http::post('email')."\n\r\n\r";
		$message .= "Message body : \n\r".Http::post('message');

		$return = mail($to, $subject, $message);

		debug('$return : ',$return, 'contact->email');

		return $return;
  } // email()


	/**
	* emailsent()
	*
	* render emailSent page.
	*/
  public function emailsent()
  {
		debugTitle('ContactController::emailsent()');

    $sections[] = $this->getView('/contact/emailsent.html', array());

		$this->htmlRender(array(
		  'meta_description' => "happy{adr} is secured site based on INSEE files for adress in france, here is the contact form with captcha validation.",
			'sections' => $sections
			));
  } // emailsent()

} // class ContactController
