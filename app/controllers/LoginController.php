<?php

class LoginController extends \Framework\AbstractController {

	public function beforeExecuteRoute(\Phalcon\Mvc\Dispatcher $dispatcher) {
		if ($this->session->get('auth')->isAuthenticated()) {
			$dispatcher->forward(array('controller' => 'home','action' => 'index'));
			return false;
		}
	}
	
	public function indexAction() {
		if (!$this->view->form) $this->view->setVar('form', new Signin\SigninForm());
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
			$form = new Signin\SigninForm();
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
				$this->response->redirect("index/index");
				return;
			}
		}

		if ($form) {
			$this->session->get('auth')->incrementRetryCount();
			$this->view->setVars(array('captcha' => $captcha, 'form' => $form, 'error' => true));
			$this->view->form->get('password')->clear();
			$this->view->messages->addError('Oops! The credentials you\'ve provided are wrong! Please, try again or contact with administration.');
		}

		return $this->dispatcher->forward(array(
				"controller" => "login",
				"action" => "index" ));

	}
}
