<?php

class UserController extends \Phalcon\Mvc\Controller {

	public function initialize() {
		if ($this->session->get('auth')->isAuthenticated()) {$this->response->redirect("home/index");}//redirect to index/index page
	}

	public function indexAction() {}
	
	public function signupAction() {
		if (!$this->view->form) $this->view->setVar('form', new UserForm\SignupForm());
		if (!$this->view->captcha) $this->view->setVar('captcha', new Captcha\Captcha($this->getDI()->get('config')->recaptcha));
	}

	public function registerAction() {
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
					if ($existed = $userToRememberMeTable->getCodeByUserID($user->id)) {
						// update existed record
						$rememberMe = $existed->renewCode();
					} else {
						// create new
						$rememberMe = $userToRememberMeTable->create(array('user_id' => $user->id));
					}
					// set cookies
					$this->cookies->set('remember-me', '1', time() + 15 * 86400);
					$this->cookies->set('remember-me-code', $rememberMe->code, time() + 15 * 86400);
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

	public function signoutAction() {
		//Destroy the whole session
		$this->session->destroy();
		// if remember-me cookies was set - kill them all!! boohaha
		if ($this->cookies->has('remember-me') && $this->cookies->has('remember-me-code')) {
			$this->cookies->get('remember-me')->delete();
			$this->cookies->get('remember-me-code')->delete();
		}
		return $this->response->redirect('index/index');
	}

}
