<?php

class ProfileController extends \Framework\AbstractController {

	public function beforeExecuteRoute(\Phalcon\Mvc\Dispatcher $dispatcher) {
		if (!$this->session->get('auth')->isAuthenticated()) {
			$dispatcher->forward(array('controller' => 'login','action' => 'index'));
			return false;
		}
	}

	public function indexAction() {
		$session = $this->session->get('auth');
		$user_id = $session->getUserCredentials()['id'];
		$usersTable = new Users();
		// Get user
		$user = $usersTable->getUserByID($user_id);
		$this->view->setVar('user', $user);
	}

	public function usernameAction() {
		// In this action implemented change username function
		if (!$this->view->form) $this->view->setVar('form', new \ProfileForm\UsernameForm());
		if ($this->getDI()->getRequest()->isPost()) {
			$login = (Object) Array();
			if ($this->view->form->isValid($this->getDI()->getRequest()->getPost(), $login)) {
				$session = $this->session->get('auth');
				$usersTable = new Users();
				$user_id = $session->getUserCredentials()['id'];
				$usersTable->getUserByID($user_id)->setNewUsername($login);
				return $this->response->redirect('profile/index');
			} else {
				// output error message
				$this->view->setVar('errors', array_merge(array('name' => 'Can\'t save this username'),$this->view->form->getMessages()));
			}
		}
	}

	public function passwordAction() {
		if (!$this->view->form) $this->view->setVar('form', new \ProfileForm\PasswordForm());
		if ($this->getDI()->getRequest()->isPost()) {
			$password = (Object) Array();
			if ($this->view->form->isValid($this->getDI()->getRequest()->getPost(), $password)) {
				$session = $this->session->get('auth');
				$user_id = $session->getUserCredentials()['id'];
				$usersTable = new Users();
				$passwordChanged = $usersTable->getUserByID($user_id)->changeUserPassword($password);
				// redirect to sing up confirmation page
				if ($passwordChanged) return $this->view->pick('profile/password_confirmation');
			} else {
				// output error message
				$this->view->setVar('errors', array_merge(array('name' => 'Can\'t save this password'),$this->view->form->getMessages()));
			}
		}
		$this->view->form->get('old_password')->clear();
		$this->view->form->get('new_password')->clear();
		$this->view->form->get('confirmPassword')->clear();
	}

	public function emailAction() {
		if (!$this->view->form) $this->view->setVar('form', new \ProfileForm\EmailForm());
		if ($this->getDI()->getRequest()->isPost()) {
			$email = (Object) Array('user_id' =>'','is_primary'=>''); // pre-populate required data
			if ($this->view->form->isValid($this->getDI()->getRequest()->getPost(), $email)) {
				$session = $this->session->get('auth');
				$email->user_id = $session->getUserCredentials()['id'];
				$email->is_primary = 0; // not primary
				$usersEmails = new UsersEmails();
				$config = $this->getDI()->get('config');
				$usersEmails->createEmail($email);
				$usersEmails->sendVerifyEmail($config);
				// redirect to profile/index
				if ($usersEmails) return $this->response->redirect('profile/index');
			} else {
				// output error message
				$this->view->setVar('errors', array_merge(array('name' => 'Can\'t save this email'),$this->view->form->getMessages()));
			}
		}
	}

	public function deleteemailAction() {
		if (!$this->view->form) $this->view->setVar('form', new \ProfileForm\DeleteEmailForm());
		$deleteEmail = (Object) Array();
		if ($this->view->form->isValid($this->getDI()->getRequest()->getQuery(), $deleteEmail)) {
			$session = $this->session->get('auth');
			$deleteEmail->user_id = $session->getUserCredentials()['id'];  // pre-populate required data
			$usersEmails = new UsersEmails();
			$userEmail = $usersEmails->getEmailByIDandUserID($deleteEmail);
			// Email should exist and it should not be primary. Without primary email user could not login to the system!
			if ($userEmail && !$userEmail->is_primary) $userEmail->setEmailDeleted();
			return $this->response->redirect('profile/index');
		}
	}

	public function setprimaryemailAction() {
		if (!$this->view->form) $this->view->setVar('form', new \ProfileForm\SetEmailAsPrimaryForm());
		$primaryEmail = (Object) Array();
		if ($this->view->form->isValid($this->getDI()->getRequest()->getQuery(), $primaryEmail)) {
			$session = $this->session->get('auth');
			$primaryEmail->user_id = $session->getUserCredentials()['id']; // pre-populate required data
			$usersEmails = new UsersEmails();
			$usersEmails->setNewPrimaryEmail($primaryEmail);
			// update session object
			$usersTable = new Users();
			$user = $usersTable->getUserByID($primaryEmail->user_id);
			$this->session->set('auth', new \Auth($user));
			// redirect to profile/index
			if ($usersEmails) return $this->response->redirect('profile/index');
		}
	}

}
