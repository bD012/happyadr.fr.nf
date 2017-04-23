<?php

namespace modules\user;

use core\controller\controller;
use core\ajax\ajax;
use core\csrf\csrf;
use core\http\http;
use core\router\router;
use core\session\session;
use core\token\token;
use core\user\user;

use libraries\captcha\captcha;

defined('SCRIPTOK') OR exit('No direct script access allowed');

/**
* class UserController
*
* This class manage the User rendering view.
*
*/
class UserController extends Controller
{
	protected $controller = 'user';

	protected $feedback;

	/**
	 * initialize()
	 *
	 * called by Controller::__construct
	 */
	public function initialize()
	{
		// A FAIRE AVEC _POST['ajax']...
	} // initialize()


	/**
	 * switcher()
	 *
	 * Switch to correct page according logged status.
	 */
	public function switcher()
	{
		if (User::isLogged()) {
			$this->account();
		} else {
			$this->signin();
		}
	} // switcher()


	/**
	* signin()
	*
	* Render signin page.
	*/
	public function signin()
	{
		debugTitle('userController::signin()');

		// if already logged, go to 'signout' view
		if (User::isLogged()) {
			return $this->account(); 
		}
		// User validator for feedback
		$validator = new userValidator('signin'); // bd ??

		if ((Router::getCall($this->controller) == 'signin') && (Http::post() !== null)) {
			// call from form => checked to log or not

			// by default
			$logged = false;
			$validator->setFeedback('switch_checked', 'checked');

			// Acces to DataBase
 			$db = new userModel('signin');

			// Set state to 
			$validator->setState('SIGNIN');

			// Link to DataBase for FUNCTION@ calls 
			$validator->addDependancy($db);

			// check _POST according Validator (and DataBase)
			if ($validator->check(Http::post())) {
				
				// check passwords
				if (checkPassword($db->read($db->id)['password'], Http::post('password'))) {

					// The user is logged
					$logged = true;

				} else {
					$validator->setFeedback('password_value', '');
					$validator->addWarning('password', 'Bad password.');
				} // if
			} // if

			if ($logged) {

				// Set User
				User::set($db->id, $db->username, $db->role);

				// return view
				return $this->account();
			} // if logged
		} // if


		// Return view
		// **********************************************************
		$validator->addFeedback('user_view', $this->setView('user_view', '/user/signin.html', $validator->getFeedback()));
		$feedback = $validator->getFeedback();

		$this->setView('user-nav', '/user/navsignin.html', array());
		$this->setView('user', '/user/user.html', $validator->getFeedback());

		Ajax::add(array(
			'action' => 'setLoading',
			'value' => 'off'
		));

		Ajax::add(array(
			'action' => 'innerHtml',
			'id' => 'user_nav',
			'innerHtml' => $this->innerHtml('/user/navsignin.html', array())
		));

		if (Router::getController() == $this->controller) $this->render('user_view');
	} // signin()


	/**
	* signout()
	*
	* Render signout page.
	*/
	public function signout()
	{
		debugTitle('userController::signout()');

		// if not logged, go to  'signin' view
		if (!User::isLogged()) {
			return $this->signin(); 
		}

		// called from form => user has be logged out
		if (Router::getCall($this->controller) == 'signout') {

			User::reset();

			// return 'signin' view
			return $this->signin();
		}

		// not called from form => only render view
		$feedback = array(
			'switch_checked' => '',
			'username' => User::getUsername()
		);
		$feedback['user_view'] = $this->setView('user_view', '/user/signout.html', $feedback);

		// return view
		$this->setView('user-nav', '/user/navaccount.html', array());
		$this->setView('user','/user/user.html', $feedback);

		Ajax::add(array(
			'action' => 'setLoading',
			'value' => 'off'
		));

		if (Router::getController() == $this->controller) $this->render('user_view');
	} // signout()


	/**
	* account()
	*
	* Render account page.
	*/
	public function account()
	{
		debugTitle('userController::account()');

		// if not logged, go to 'signin' view
		if (!User::isLogged()) {
			return $this->signin(); 
		}

		// User validator for feedback
		$validator = new userValidator('account');

		// Acces to DataBase
		$db = new userModel('account');

		// Link to DataBase for FUNCTION@ calls 
		$validator->addDependancy($db);
		$validator->setState('ACCOUNT');

		if ((Router::getCall($this->controller) == 'account') && (Http::post() !== null)) {

			$validator->addFeedback('switch_checked', 'checked');

			// check _POST according Validator (and DataBase)
			if ($validator->check(Http::post())) {

				// read in DataBase
				$db->where('username', '=', User::getUsername())
					->read();

				// check passwords
				if (checkPassword($db->read($db->id)['password'], Http::post('password'))) {

					// The user is logged
					$db->set('email', Http::post('email'))
						->update($db->id) ;

				} else {
					$validator->addWarning('password', 'Bad password.');
				} // if

				$validator->setFeedback('password_value', '');
			} // if
		} else {
			$result = $db->read(User::getId());
			$validator->addFeedback('username_value', $result['username']);
			$validator->addFeedback('email_value', $result['email']);
		} // if


		// 
		// **********************************************************

		// Key Access
		Token::encode(array(
			'username' => User::getUsername(),
			'date' => date('Y-m-d')
		));
		$validator->addFeedback('keyaccess', Token::get());

		$validator->addFeedback('isadmin', (User::getRole() == USER_ROLE_ADMIN));
		$validator->addFeedback('tobackoffice', $this->setView('tobackoffice', '/user/tobackoffice.html', 
		$validator->getFeedback()));

		$validator->addFeedback('username_disabled', 'disabled');
		$validator->addFeedback('signout', $this->setView('signout', '/user/signout.html', $validator->getFeedback()));
		$validator->addFeedback('user_view', $this->setView('user_view', '/user/account.html', $validator->getFeedback()));
		
		$this->setView('user-nav', '/user/navaccount.html', array());
		$this->setView('user', '/user/user.html', $validator->getFeedback());

		Ajax::add(array(
			'action' => 'setLoading',
			'value' => 'off'
		));

		Ajax::add(array(
			'action' => 'innerHtml',
			'id' => 'user_nav',
			'innerHtml' => $this->innerHtml('/user/navaccount.html', array())
		));

		if (Router::getController() == $this->controller) $this->render('user_view');
	} // account()


	/**
	* register()
	*
	* Render register page.
	*/
	public function register()
	{
		debugTitle('userController::register()');

		// if logged, go to 'signout' view
		if (User::isLogged()) {
			return $this->account(); 
		}

		// User validator for feedback
		$validator = new userValidator('register');

		//Captcha initialisation
		$captcha = new Captcha(ROOT.'/public/captcha', WEBROOT.'/public/captcha');

		$register = false;

		if ((Router::getCall($this->controller) == 'register') && (Http::post() !== null)) {
			// by default
			$validator->addFeedback('switch_checked', 'checked');

			// call from form => checked to log or not

			// Set state to 
			$validator->setState('REGISTER');

			// Acces to DataBase
			$db = new userModel('register');

			// Link to DataBase for FUNCTION@ calls 
			$validator->addDependancy($db);


			// check _POST according Validator (and DataBase)
			if ($validator->check(Http::post()) && ($captcha->check())) {
				// The user is registered
				$register = true;

			} // if
		} // if

		if ($register) {
			$db->set('username', Http::post('username'))
				->set('email', Http::post('email'))
				->set('password', password(Http::post('password')))
				->create() ;

			// return view
			return $this->signin();
		} // if registered

		// User is not registered
		// **********************************************************
		$validator->addFeedback('captcha', $captcha->getImg());
		$validator->addFeedback('captcha_reload', $captcha->getReload());


		$validator->addFeedback('username_disabled', '');
		$validator->addFeedback('user_view', $this->setView('user_view', '/user/register.html', $validator->getFeedback()));
		
		$this->setView('user-nav', '/user/navsignin.html', array());
		$this->setView('user', '/user/user.html', $validator->getFeedback());

		Ajax::add(array(
			'action' => 'setLoading',
			'value' => 'off'
		));

		if (Router::getController() == $this->controller) $this->render('user_view');
	} // register()
} // class User