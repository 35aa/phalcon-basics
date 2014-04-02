<?php

class ForgotpasswordController extends \Framework\AbstractController {

	public function beforeExecuteRoute(\Phalcon\Mvc\Dispatcher $dispatcher) {
		if ($this->session->get('auth')->isAuthenticated()) {
			$dispatcher->forward(array('controller' => 'home','action' => 'index'));
			return false;
		}
	}

	public function indexAction() {
		if (!$this->view->form) $this->view->setVar('form', new \ForgotPassword\ForgotPassword());
		if (!$this->view->captcha) {
			$this->view->setVar('captcha', new \Captcha\Captcha($this->getDI()->get('config')->recaptcha));
		}
	}

	public function sendresetpasswordAction() {
		$captcha = null;
		$form = null;
		if ($this->getDI()->getRequest()->isPost()) {
			$form = new \ForgotPassword\ForgotPassword();
			$captcha = new \Captcha\Captcha($this->getDI()->get('config')->recaptcha);

			$usersTable = new \Users();
			$validatedData = (Object) Array();
			//validate data and try to get user by email
			if ($form->isValid($this->getDI()->getRequest()->getPost(), $validatedData)
					&& $captcha->checkAnswer($this->getDI()->getRequest())
					&& $user = $usersTable->getUserByPrimaryEmail($validatedData->email)) {

				//send reset password email
				$user->getPrimaryEmail()->sendResetPasswordEmail($this->getDI()->get('config'));
				return $this->view->pick('forgotpassword/reset_confirmation');
			} else {
				// output error message
				$this->view->setVar('errors', $form->getMessages());
			}
		}

		if ($form) {
			$this->view->setVars(array('captcha' => $captcha, 'form' => $form));
		}

		$this->dispatcher->forward(array(
				"controller" => "forgotpassword",
				"action" => "index" ));
	}
}
