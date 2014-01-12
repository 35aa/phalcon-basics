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

	public function initverifyAction() {
		if (!$this->view->form) $this->view->setVar('form', new UserForm\InitverifyForm());
		if (!$this->view->captcha) $this->view->setVar('captcha', new Captcha\Captcha($this->getDI()->get('config')->recaptcha));

		if ($this->getDI()->getRequest()->isPost()) {
			$initVerify = (Object) Array();
			$usersEmailsTable = new UsersEmails();

			if ($this->view->form->isValid($this->getDI()->getRequest()->getPost(), $initVerify)
					&& $this->view->captcha->checkAnswer($this->getDI()->getRequest())
					&& ($userEmail = $usersEmailsTable->getUnverifiedEmailByEmail($initVerify->email)) ) {
				// send verification data
				$userEmail->sendVerifyEmail($this->getDI()->get('config'));
			}
		}
	}

	public function confirmemailAction() {
		//validate get params -> goto error page
		$form = new UserForm\VerifyEmailForm();
		$validatedData = (Object) Array();
		if ($form->isValid($this->getDI()->getRequest()->getQuery(), $validatedData)) {
			$users = new Users();
			$this->view->setVar('user', $users->verifyUserByIdAndCode($validatedData->id, $validatedData->code));
		}
	}

	public function signinAction() {
		if (!$this->view->form) $this->view->setVar('form', new UserForm\SigninForm());
		//TODO: after 3 times should appear captcha!!!
//		if (!$this->view->captcha) $this->view->setVar('captcha', new Captcha\Captcha($this->getDI()->get('config')->recaptcha));
	}

	public function checkCredentialsAction() {
		$captcha = null;
		$form = null;
		if ($this->getDI()->getRequest()->isPost()) {
			$form = new UserForm\SigninForm();
//TODO: add captcha after 3 times
//			$captcha = new Captcha\Captcha($this->getDI()->get('config')->recaptcha);
			$usersTable = new Users();
			$validatedData = (Object) Array();
			if ($form->isValid($this->getDI()->getRequest()->getPost(), $validatedData)
//					&& $captcha->checkAnswer($this->getDI()->getRequest())
					&& $user = $usersTable->getUserByPrimaryEmailAndPass($validatedData->email, $validatedData->password)) {

				$this->session->set('auth', new \Auth($user));
				$this->response->redirect("home/index");
				return;
			}
		}

		if ($form) {
			$this->view->setVars(array('captcha' => $captcha, 'form' => $form));
			$this->view->form->get('password')->clear();
		}

		$this->dispatcher->forward(array(
				"controller" => "user",
				"action" => "signin" ));

	}

	public function signoutAction() {
		//Destroy the whole session
		return $this->session->destroy();
	}

}
