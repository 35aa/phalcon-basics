<?php

class RegisterController extends \Framework\AbstractController {

	public function beforeExecuteRoute(\Phalcon\Mvc\Dispatcher $dispatcher) {
		if ($this->session->get('auth')->isAuthenticated()) {
			$dispatcher->forward(array('controller' => 'home','action' => 'index'));
			return false;
		}
	}
	
	public function indexAction() {
		if (!$this->view->form) $this->view->setVar('form', new Signup\SignupForm());
		if (!$this->view->captcha) $this->view->setVar('captcha', new Captcha\Captcha($this->getDI()->get('config')->recaptcha));
	}

	public function registerAction() {
		// check if sign_up_enable option is true
		if (!$this->view->getVar('sign_up_enable')) return $this->response->redirect('register/index');
		$emailRegistered = false;
		$captcha = null;
		$form = null;
		if ($this->getDI()->getRequest()->isPost()) {
			$form = new Signup\SignupForm();
			$captcha = new Captcha\Captcha($this->getDI()->get('config')->recaptcha);
			$newUser = new Users();

			if ($form->isValid($this->getDI()->getRequest()->getPost(), $newUser)
					&& $captcha->checkAnswer($this->getDI()->getRequest())
					&& !($emailRegistered = \UsersEmails::getRegisteredEmail($newUser->email))) {
				// create new user
				$newUser->create();
				// send verification data to primary email
				$newUser->getPrimaryEmail()->sendVerifyEmail($this->getDI()->get('config'));

				// redirect to sing up confirmation page
				return $this->view->pick('register/signup_confirmation');
			}

			else if ($emailRegistered) {
				$this->view->setVar('emailRegistered', true);
			}
		}

		if ($captcha && $form) {
			$this->view->setVars(array('captcha' => $captcha, 'form' => $form));
			$this->view->form->get('password')->clear();
			$this->view->form->get('confirmPassword')->clear();
			$this->view->messages->addError('Please, fix errors and try again!');
		}

		$this->dispatcher->forward(array(
				"controller" => "register",
				"action" => "index" ));

	}

}
