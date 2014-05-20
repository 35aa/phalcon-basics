<?php

class UserController extends \Framework\AbstractController {

	public function beforeExecuteRoute(\Phalcon\Mvc\Dispatcher $dispatcher) {
		if ($this->session->get('auth')->isAuthenticated()) {
			$dispatcher->forward(array('controller' => 'home','action' => 'index'));
			return false;
		}
	}

	public function indexAction() {}

	public function resetpasswordAction() {
		if (!$this->session->get('reset-auth')) {
			return $this->dispatcher->forward(array(
					"controller" => "login",
					"action" => "index" ));
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
			return $this->dispatcher->forward(array(
					"controller" => "login",
					"action" => "index" ));
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

}
