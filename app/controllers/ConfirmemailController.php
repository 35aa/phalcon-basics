<?php

class ConfirmemailController extends \Framework\AbstractController {

	public function indexAction() {
		//validate get params -> goto error page
		$form = new \UserForm\VerifyEmailForm();
		$validatedData = (Object) Array();
		if ($form->isValid($this->getDI()->getRequest()->getQuery(), $validatedData)) {
			$users = new \Users();
			$this->view->setVar('user', $users->verifyUserByIdAndCode($validatedData->id, $validatedData->code));
		}
	}

	public function initverifyAction() {
		$this->view->setVar('form', new \ConfirmEmailForm\InitverifyForm());
		$this->view->setVar('captcha', new \Captcha\Captcha($this->getDI()->get('config')->recaptcha));

		if ($this->getDI()->getRequest()->isPost()) {
			$initVerify = (Object) Array();
			$usersEmailsTable = new \UsersEmails();

			if ($this->view->form->isValid($this->getDI()->getRequest()->getPost(), $initVerify)
					&& $this->view->captcha->checkAnswer($this->getDI()->getRequest())
					&& ($userEmail = $usersEmailsTable->getUnverifiedEmailByEmail($initVerify->email)) ) {
				// send verification data
				$userEmail->sendVerifyEmail($this->getDI()->get('config'));
				return $this->view->pick('confirmemail/initverify_confirmation');
			}
		}
	}

	public function resetpasswordAction() {
		//validate get params -> goto error page
		$form = new \UserForm\VerifyEmailForm();
		$validatedData = (Object) Array();
		$users = new \Users();
		// validate data and try to ghet user by user id and validation code
		if ($form->isValid($this->getDI()->getRequest()->getQuery(), $validatedData)
				&& ($user = $users->findUserForResetPasswordByIdAndCode($validatedData->id, $validatedData->code))) {
			// Create new auth instance in session.
			// We could not use default auth location because in this case we could not use user controller.
			// Also we should not allow to use current data as default auth object without changing password.
			// I do not want to place change password functionlity here.
			// It should belong to user controller.
			$this->session->set('reset-auth', new \Auth($user));
			return $this->dispatcher->forward(array(
				"controller" => "user",
				"action" => "resetpassword" ));
		}
		if (!$this->view->getVar('form')) {
			$this->view->setVar('form', new \ConfirmEmailForm\ResetPassword());
			$this->view->setVar('captcha', new \Captcha\Captcha($this->getDI()->get('config')->recaptcha));
		}
		return $this->view->pick('confirmemail/reset_password');
	}

	// use rredirected to this action when he was arrived to resetpassword action,
	// but for some reason previous action failed to achieve success.
	public function resendresetpasswordAction() {
		$this->view->setVar('form', new \ConfirmEmailForm\ResetPassword());
		$this->view->setVar('captcha', new \Captcha\Captcha($this->getDI()->get('config')->recaptcha));

		if ($this->getDI()->getRequest()->isPost()) {
			$resetPassword = (Object) Array();
			$usersTable = new \Users();

			// validate data and try to find user by email
			if ($this->view->form->isValid($this->getDI()->getRequest()->getPost(), $resetPassword)
					&& $this->view->captcha->checkAnswer($this->getDI()->getRequest())
					&& ($user = $usersTable->getUserByPrimaryEmail($resetPassword->email)) ) {
				// send reset password email
				$user->getPrimaryEmail()->sendResetPasswordEmail($this->getDI()->get('config'));
				return $this->view->pick('confirmemail/reset_confirmation');
			}
		}
		return $this->dispatcher->forward(array(
				"controller" => "confirmemail",
				"action" => "resetpassword" ));
	}
}
