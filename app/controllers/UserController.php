<?php

class UserController extends \Phalcon\Mvc\Controller {

	public function initialize() {
		if (false/*check if user already logged in*/) {}//redirect to index/index page
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
					&& !($emailRegistered = UsersEmails::isEmailRegistered($newUser->email))) {
				// create new user
				$newUser->create();
				// send verification data to primary email
				$newUser->getPrimaryEmail()->sendVerifyEmail($this->getDI()->get('config'));

				// phone validation
				$this->dispatcher->forward(array(
						"controller" => "user",
						"action" => "signin" ));
				return;
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

	public function initverifyAction() {
		$form = new UserForm\SignupForm();
		$captcha = new Captcha\Captcha($this->getDI()->get('config')->recaptcha);
		if ($this->getDI()->getRequest()->isPost()) {
			
		}
		else {
			$this->view->setVars(array('captcha' => $captcha, 'form' => $form, 'isError' => false, 'success' => false));
		}
	}

	public function confirmemailAction() {
		//validate get params -> goto error page
		$form = new UserForm\VerifyEmailForm();
		$validatedData = (Object) Array();
		if ($form->isValid($this->getDI()->getRequest()->getQuery(), $validatedData)) {
			// http://alex.mydev.org.ua/user/confirmemail?id=76f879cc7a38ff7c9165fa7019113347&code=%242a%2410%24xAe0R8BsNl6ul0qBztquzeKPQM9ItQGhjFxj8%2Fkwm48lhqeecY4zC
			$users = new Users();
			$this->view->setVar('user', $users->verifyUserByIdAndCode($validatedData->id, $validatedData->code));
		}
	}

	public function signinAction() {
		// TODO: basic sign in
	}

	public function checkCredentialsAction() {}

}
