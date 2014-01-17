<?php

class ProfileController extends \Phalcon\Mvc\Controller {

	public function initialize() {
		if (!$this->session->get('auth')->isAuthenticated()) return $this->response->redirect("user/signin");//redirect to index/index page
	}

	public function indexAction() {
		$session = $this->session->get('auth');
		if (!$session->isAuthenticated()) return $this->response->redirect("user/signin");
		$user_id = $session->getUserCredentials()['id'];
		$usersTable = new Users();
		// Get user
		$user = $usersTable->getUserByID($user_id);
		$this->view->setVar('user', $user);
		// Get emails
		$user_emails = $user->getEmails();
		$this->view->setVar('user_emails', $user_emails);
	}

	public function loginAction() {
		if (!$this->view->form) $this->view->setVar('form', new ProfileForm\LoginForm());
		if ($this->getDI()->getRequest()->isPost()) {
			$login = (Object) Array();
			if ($this->view->form->isValid($this->getDI()->getRequest()->getPost(), $login)) {
				$session = $this->session->get('auth');
				$usersTable = new Users();
				$user_id = $session->getUserCredentials()['id'];
				$usersTable->getUserByID($user_id)->setNewUsername($login);
				return $this->response->redirect('profile/index');
			}
		}
	}

	public function passwordAction() {
		if (!$this->view->form) $this->view->setVar('form', new ProfileForm\PasswordForm());
		if ($this->getDI()->getRequest()->isPost()) {
			$password = (Object) Array();
			if ($this->view->form->isValid($this->getDI()->getRequest()->getPost(), $password)) {
				$session = $this->session->get('auth');
				$user_id = $session->getUserCredentials()['id'];
				$usersTable = new Users();
				$user = $usersTable->getUserByID($user_id)->changeUserPassword($password);
				// redirect to sing up confirmation page
				if ($user) return $this->view->pick('profile/password_confirmation');
			}
		}
		if ($this->view->form) {
			$this->view->form->get('old_password')->clear();
			$this->view->form->get('new_password')->clear();
			$this->view->form->get('confirmPassword')->clear();
		}
	}

	public function emailAction() {
		if (!$this->view->form) $this->view->setVar('form', new ProfileForm\EmailForm());
		if ($this->getDI()->getRequest()->isPost()) {
			$email = (Object) Array('user_id' =>'','is_primary'=>''); // pre-populate required data
			if ($this->view->form->isValid($this->getDI()->getRequest()->getPost(), $email) 
					&& !(UsersEmails::isEmailRegistered($email->email))) {
				$session = $this->session->get('auth');
				$email->user_id = $session->getUserCredentials()['id'];
				$email->is_primary = 0; // not primary
				$usersEmails = new UsersEmails();
				$config = $this->getDI()->get('config');
				$usersEmails->create(array('user_id' => $email->user_id, 'email' => $email->email, 'is_primary' => $email->is_primary));
				$usersEmails->sendVerifyEmail($config);
				// redirect to profile/index
				if ($usersEmails) return $this->response->redirect('profile/index');
			}
		}
	}

	public function deleteemailAction() {
		if (!$this->view->form) $this->view->setVar('form', new ProfileForm\DeleteEmailForm());
		$deleteEmail = (Object) Array();
		if ($this->view->form->isValid($this->getDI()->getRequest()->getQuery(), $deleteEmail)) {
			$session = $this->session->get('auth');
			$deleteEmail->user_id = $session->getUserCredentials()['id'];  // pre-populate required data
			$usersEmails = new UsersEmails();
			$userEmail = $usersEmails->getEmailByIDandUserID($deleteEmail->id, $deleteEmail->user_id);
			// check if email exist and is not primary
			if ($userEmail && $userEmail->is_primary != 1) $userEmail->setEmailDeleted();
			return $this->response->redirect('profile/index');
		}
	}

	public function setprimaryemailAction() {
		if (!$this->view->form) $this->view->setVar('form', new ProfileForm\SetEmailAsPrimaryForm());
		$primaryEmail = (Object) Array();
		if ($this->view->form->isValid($this->getDI()->getRequest()->getQuery(), $primaryEmail)) {
			$session = $this->session->get('auth');
			$primaryEmail->user_id = $session->getUserCredentials()['id']; // pre-populate required data
			$usersEmails = new UsersEmails();
			$usersEmails->setNewPrimaryEmail($primaryEmail);
			// redirect to profile/index
			if ($usersEmails) return $this->response->redirect('profile/index');
		}
	}

}
