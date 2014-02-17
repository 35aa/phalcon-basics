<?php

class ConfirmemailController extends \Framework\AbstractController {

	public function beforeExecuteRoute(\Phalcon\Mvc\Dispatcher $dispatcher) {
		if ($this->session->get('auth')->isAuthenticated()) {
			$dispatcher->forward(array('controller' => 'home','action' => 'index'));
			return false;
		}
	}

	public function indexAction() {
		//validate get params -> goto error page
		$form = new UserForm\VerifyEmailForm();
		$validatedData = (Object) Array();
		if ($form->isValid($this->getDI()->getRequest()->getQuery(), $validatedData)) {
			$users = new Users();
			$this->view->setVar('user', $users->verifyUserByIdAndCode($validatedData->id, $validatedData->code));
		}
	}

	public function initverifyAction() {
		$this->view->setVar('form', new ConfirmEmailForm\InitverifyForm());
		$this->view->setVar('captcha', new Captcha\Captcha($this->getDI()->get('config')->recaptcha));

		if ($this->getDI()->getRequest()->isPost()) {
			$initVerify = (Object) Array();
			$usersEmailsTable = new UsersEmails();

			if ($this->view->form->isValid($this->getDI()->getRequest()->getPost(), $initVerify)
					&& $this->view->captcha->checkAnswer($this->getDI()->getRequest())
					&& ($userEmail = $usersEmailsTable->getUnverifiedEmailByEmail($initVerify->email)) ) {
				// send verification data
				$userEmail->sendVerifyEmail($this->getDI()->get('config'));
				return $this->view->pick('confirmemail/initverify_confirmation');
			}
		}
	}
}
