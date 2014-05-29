<?php

class ProfileController extends \Framework\AbstractController {

	public function beforeExecuteRoute(\Phalcon\Mvc\Dispatcher $dispatcher) {
		if (!$this->session->get('auth')->isAuthenticated()) {
			$dispatcher->forward(array('controller' => 'login','action' => 'index'));
			return false;
		}
	}

	public function indexAction($user_id = null) {
		$session = $this->session->get('auth');
		if (!$user_id) $user_id = $session->getUserCredentials()['id'];
		$usersTable = new Users();
		// Get user
		$user = $usersTable->getUserByID($user_id);
		$this->view->setVar('user', $user);
	}

	public function usernameAction($user_id = null) {
		$session = $this->session->get('auth');
		$usersTable = new Users();
		if (!$user_id) $user_id = $session->getUserCredentials()['id'];
		// In this action implemented change username function
		if (!$this->view->form) $this->view->setVar('form', new \ProfileForm\UsernameForm($user = $usersTable->getUserByID($user_id)));
		if ($this->getDI()->getRequest()->isPost()) {
			$login = (Object) Array();
			if ($this->view->form->isValid($this->getDI()->getRequest()->getPost(), $login)) {
				$user->setNewUsername($login);
				return $this->dispatcher->forward(array('controller' => 'profile','action' => 'index' ));
			}
			else {
				$this->view->messages->addError('Please, fix errors and try again!');
			}
		}
	}

	public function passwordAction($user_id = null) {
		$session = $this->session->get('auth');
		$usersTable = new Users();
		if (!$user_id) $user_id = $session->getUserCredentials()['id'];
		if (!$this->view->form) $this->view->setVar('form', new \ProfileForm\PasswordForm($user = $usersTable->getUserByID($user_id)));
		if ($this->getDI()->getRequest()->isPost()) {
			$password = (Object) Array();
			if ($this->view->form->isValid($this->getDI()->getRequest()->getPost(), $password)) {
				$passwordChanged = $user->changeUserPassword($password);
				// redirect to sing up confirmation page
				if ($passwordChanged) return $this->view->pick('profile/password_confirmation');
			}
			else {
				$this->view->messages->addError('Please, fix errors and try again!');
				$this->view->form->get('old_password')->clear();
				$this->view->form->get('new_password')->clear();
				$this->view->form->get('confirmPassword')->clear();
			}
		}
	}

	public function emailAction($user_id = null) {
		$session = $this->session->get('auth');
		$usersTable = new Users();
		if (!$user_id) $user_id = $session->getUserCredentials()['id'];
		if (!$this->view->form) $this->view->setVar('form', new \ProfileForm\EmailForm($user = $usersTable->getUserByID($user_id)));
		if ($this->getDI()->getRequest()->isPost()) {
			// pre-populate required data
			$email = (Object) Array(
				'user_id' => $user_id, 'is_primary' => ''
			);
			if ($this->view->form->isValid($this->getDI()->getRequest()->getPost(), $email)) {
				$email->is_primary = 0; // not primary
				$usersEmails = new UsersEmails();
				$config = $this->getDI()->get('config');
				$usersEmails->createEmail($email);
				$usersEmails->sendVerifyEmail($config);
				// redirect to profile/index
				if ($usersEmails) return $this->dispatcher->forward(array('controller' => 'profile','action' => 'index' ));
			} else {
				// output error message
				$this->view->messages->addError('Please, fix errors and try again!');
			}
		}
	}

	public function deleteemailAction($user_id = null) {
		$session = $this->session->get('auth');
		$usersTable = new Users();
		if (!$user_id) $user_id = $session->getUserCredentials()['id'];
		if (!$this->view->form) $this->view->setVar('form', new \ProfileForm\DeleteEmailForm());
		// pre-populate required data
		$deleteEmail = (Object) Array('user_id' => $user_id);
		if ($emailDeleted = $this->view->form->isValid($this->getDI()->getRequest()->getQuery(), $deleteEmail)) {
			$usersEmails = new UsersEmails();
			$userEmail = $usersEmails->getEmailByIDandUserID($deleteEmail);
			// Email should exist and it should not be primary. Without primary email user could not login to the system!
			if ($emailDeleted = $userEmail && !$userEmail->is_primary) $userEmail->setEmailDeleted();
		} 
		if (!$emailDeleted) {
			// output error message
			$this->view->messages->addError('Oops, selected email couldn\'t be deleted');
		}
		return $this->dispatcher->forward(array(
				"controller" => "profile",
				"action" => "index" ));
	}

	public function setprimaryemailAction($user_id = null) {
		$session = $this->session->get('auth');
		$usersTable = new Users();
		if (!$user_id) $user_id = $session->getUserCredentials()['id'];
		if (!$this->view->form) $this->view->setVar('form', new \ProfileForm\SetEmailAsPrimaryForm());
		// pre-populate required data
		$primaryEmail = (Object) Array('user_id' => $user_id);
		if ($this->view->form->isValid($this->getDI()->getRequest()->getQuery(), $primaryEmail)) {
			$usersEmails = new UsersEmails();
			$usersEmails->setNewPrimaryEmail($primaryEmail);
			// update session object
			$usersTable = new Users();
			$user = $usersTable->getUserByID($primaryEmail->user_id);
			$this->session->set('auth', new \Auth($user));
			// redirect to profile/index
			if ($usersEmails) return $this->dispatcher->forward(array('controller' => 'profile','action' => 'index' ));
		}
	}

}
