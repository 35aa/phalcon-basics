<?php

class UserController extends \Framework\AbstractController {

	public function beforeExecuteRoute(\Phalcon\Mvc\Dispatcher $dispatcher) {
		if ($this->session->get('auth')->isAuthenticated()) {
			if ($dispatcher->getActionName() != 'signout') {
				$dispatcher->forward(array('controller' => 'home','action' => 'index'));
				return false;
			}
		}
	}

	public function indexAction() {}
	
	public function signupAction() {
		if (!$this->view->form) $this->view->setVar('form', new UserForm\SignupForm());
		if (!$this->view->captcha) $this->view->setVar('captcha', new Captcha\Captcha($this->getDI()->get('config')->recaptcha));
	}

	public function registerAction() {
		// check if sign_up_enable option is true
		if (!$this->view->getVar('sign_up_enable')) return $this->response->redirect('user/signup');
		$emailRegistered = false;
		$captcha = null;
		$form = null;
		if ($this->getDI()->getRequest()->isPost()) {
			$form = new UserForm\SignupForm();
			$captcha = new Captcha\Captcha($this->getDI()->get('config')->recaptcha);
			$newUser = new Users();

			if ($form->isValid($this->getDI()->getRequest()->getPost(), $newUser)
					&& $captcha->checkAnswer($this->getDI()->getRequest())
					// TODO: rename isEmailRegistered to getRegisteredEmail
					&& !($emailRegistered = UsersEmails::isEmailRegistered($newUser->email))) {
				// create new user
				$newUser->create();
				// send verification data to primary email
				$newUser->getPrimaryEmail()->sendVerifyEmail($this->getDI()->get('config'));

				// redirect to sing up confirmation page
				return $this->view->pick('user/signup_confirmation');
			}

			else if ($emailRegistered) {
				$this->view->setVar('emailRegistered', true);
			}
		}

		if ($captcha && $form) {
			$this->view->setVars(array('captcha' => $captcha, 'form' => $form));
			$this->view->form->get('password')->clear();
			$this->view->form->get('confirmPassword')->clear();
		}

		$this->dispatcher->forward(array(
				"controller" => "user",
				"action" => "signup" ));

	}

	public function signinAction() {
		if (!$this->view->form) $this->view->setVar('form', new UserForm\SigninForm());
		if (!$this->view->error) $this->view->setVar('error', false);
		if ($this->session->get('auth')->isMaxRetryCount()
				&& !$this->view->captcha) {
			$this->view->setVar('captcha', new Captcha\Captcha($this->getDI()->get('config')->recaptcha));
		}
	}

	public function checkCredentialsAction() {
		$captcha = null;
		$form = null;
		if ($this->getDI()->getRequest()->isPost()) {
			$form = new UserForm\SigninForm();
			$checkCaptcha = true;
			if ($this->session->get('auth')->isMaxRetryCount()) {
				$captcha = new Captcha\Captcha($this->getDI()->get('config')->recaptcha);
				$checkCaptcha = $captcha->checkAnswer($this->getDI()->getRequest());
			}
			$usersTable = new Users();
			$validatedData = (Object) Array();
			if ($form->isValid($this->getDI()->getRequest()->getPost(), $validatedData)
					&& $checkCaptcha
					&& $user = $usersTable->getUserByPrimaryEmailAndPass($validatedData->email, $validatedData->password)) {
				// remember me
				if (isset($validatedData->remember_me) && $validatedData->remember_me) {
					$userToRememberMeTable = new UserToRememberMe();
					if (!($rememberMe = $userToRememberMeTable->renewCodeByUserID($user->id))) {
						// create new
						$rememberMe = $userToRememberMeTable->create(array('user_id' => $user->id));
					}
					// set cookies
					$this->cookies->setCookies($rememberMe->code);
				}
				$this->session->set('auth', new \Auth($user));
				$this->response->redirect("home/index");
				return;
			}
		}

		if ($form) {
			$this->session->get('auth')->incrementRetryCount();
			$this->view->setVars(array('captcha' => $captcha, 'form' => $form, 'error' => true));
			$this->view->form->get('password')->clear();
		}

		$this->dispatcher->forward(array(
				"controller" => "user",
				"action" => "signin" ));

	}

	public function forgotpasswordAction() {
		if (!$this->view->form) $this->view->setVar('form', new UserForm\ForgotPassword());
		if (!$this->view->captcha) {
			$this->view->setVar('captcha', new Captcha\Captcha($this->getDI()->get('config')->recaptcha));
		}
	}

	public function sendresetpasswordAction() {
		$captcha = null;
		$form = null;
		if ($this->getDI()->getRequest()->isPost()) {
			$form = new UserForm\ForgotPassword();
			$captcha = new Captcha\Captcha($this->getDI()->get('config')->recaptcha);

			$usersTable = new Users();
			$validatedData = (Object) Array();
			//validate data and try to get user by email
			if ($form->isValid($this->getDI()->getRequest()->getPost(), $validatedData)
					&& $captcha->checkAnswer($this->getDI()->getRequest())
					&& $user = $usersTable->getUserByPrimaryEmail($validatedData->email)) {

				//send reset password email
				$user->getPrimaryEmail()->sendResetPasswordEmail($this->getDI()->get('config'));
				return $this->view->pick('user/reset_confirmation');
			}
		}

		if ($form) {
			$this->view->setVars(array('captcha' => $captcha, 'form' => $form));
		}

		$this->dispatcher->forward(array(
				"controller" => "user",
				"action" => "forgotpassword" ));
	}

	public function resetpasswordAction() {
		if (!$this->session->get('reset-auth')) {
			$this->dispatcher->forward(array(
					"controller" => "user",
					"action" => "signin" ));
		}
		if (!$this->view->form) $this->view->setVar('form', new UserForm\ResetPassword());
		if (!$this->view->captcha) {
			$this->view->setVar('captcha', new Captcha\Captcha($this->getDI()->get('config')->recaptcha));
		}
		$this->view->form->get('password')->clear();
		$this->view->form->get('confirmPassword')->clear();
	}

	public function setnewpasswordAction() {
		if (!$this->session->get('reset-auth')) {
			$this->dispatcher->forward(array(
					"controller" => "user",
					"action" => "signin" ));
		}
		$captcha = null;
		$form = null;
		$user = null;
		if ($this->getDI()->getRequest()->isPost()) {
			$form = new UserForm\ResetPassword();
			$captcha = new Captcha\Captcha($this->getDI()->get('config')->recaptcha);

			$usersTable = new Users();
			$validatedData = (Object) Array();
			// validate data and try to get user by user id
			// here we use auth object created in confirmemail controller
			if ($form->isValid($this->getDI()->getRequest()->getPost(), $validatedData)
					&& $captcha->checkAnswer($this->getDI()->getRequest())
					&& $user = $usersTable->getUserById($this->session->get('reset-auth')->getUserId())) {

				//seve new password
				$user->saveNewPassword($validatedData->password);
				//remove temporary session object used for reset password
				$this->session->remove('reset-auth');
				//now we could safely allow user to use this session object
				$this->session->set('auth', new \Auth($user));
				return $this->dispatcher->forward(array(
							"controller" => "home",
							"action" => "index" ));
				}
		}

		if ($form) {
			$this->view->setVars(array('captcha' => $captcha, 'form' => $form));
		}

		$this->dispatcher->forward(array(
				"controller" => "user",
				"action" => "resetpassword" ));

	}

	public function signoutAction() {
		//Destroy the whole session
		$this->session->destroy();
		// if remember-me cookies was set - kill them all!! boohaha
		if ($this->cookies->has('remember-me') && $this->cookies->has('remember-me-code')) {
			$this->cookies->removeCookies();
		}
		return $this->response->redirect('index/index');
	}

}
