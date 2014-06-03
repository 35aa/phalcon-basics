<?php

class ProfileController extends \Framework\AbstractController {

	public function beforeExecuteRoute(\Phalcon\Mvc\Dispatcher $dispatcher) {
		if (!$this->session->get('auth')->isAuthenticated()) {
			$dispatcher->forward(array('controller' => 'login','action' => 'index'));
			return false;
		}
	}

	public function indexAction() {
		if ($user = $this->_getUserByID()) {
			return $this->view->setVar('user', $user);
		}
	}

	public function usernameAction() {
		// validate whether user_id is md5 value and get user
		if (!$user = $this->_getUserByID()) return false;
		// In this action implemented change username function
		if (!$this->view->form) $this->view->setVar('form', new \ProfileForm\UsernameForm($this->_isUserDataRequired() ? $user : null));
		if ($this->getDI()->getRequest()->isPost()) {
			$login = (Object) Array();
			if ($this->view->form->isValid($this->getDI()->getRequest()->getPost(), $login)) {
				$user->setNewUsername($login);
				return $this->dispatcher->forward(
					array(
						'controller' => $this->dispatcher->getControllerName(),
						'action' => 'index' ) );
			}
			else {
				$this->view->messages->addError('Please, fix errors and try again!');
			}
		}
	}

	public function passwordAction() {
		// validate whether user_id is md5 value and get user
		if (!$user = $this->_getUserByID()) return false;
		if (!$this->view->form) $this->view->setVar('form', new \ProfileForm\PasswordForm($this->_isUserDataRequired() ? $user : null));
		if ($this->getDI()->getRequest()->isPost()) {
			$password = (Object) Array();
			if ($this->view->form->isValid($this->getDI()->getRequest()->getPost(), $password)) {
				$passwordChanged = $user->changeUserPassword($password);
				$this->view->messages->addSuccess('Password was changed successfully!');
			}
			else {
				$this->view->messages->addError('Please, fix errors and try again!');
			}
			$this->view->form->get('old_password')->clear();
			$this->view->form->get('new_password')->clear();
			$this->view->form->get('confirmPassword')->clear();
		}
	}

	public function emailAction() {
		// validate whether user_id is md5 value and get user
		if (!$user = $this->_getUserByID()) return false;
		if (!$this->view->form) $this->view->setVar('form', new \ProfileForm\EmailForm($this->_isUserDataRequired() ? $user : null));
		if ($this->getDI()->getRequest()->isPost()) {
			// pre-populate required data
			$email = (Object) Array(
				'user_id' => $user->id, 'is_primary' => ''
			);
			if ($this->view->form->isValid($this->getDI()->getRequest()->getPost(), $email)) {
				$email->is_primary = 0; // not primary
				$usersEmails = new UsersEmails();
				$config = $this->getDI()->get('config');
				$usersEmails->createEmail($email);
				$usersEmails->sendVerifyEmail($config);
				// redirect to profile/index
				if ($usersEmails) return $this->dispatcher->forward(array('controller' => $this->dispatcher->getControllerName(),'action' => 'index' ));
			} else {
				// output error message
				$this->view->messages->addError('Please, fix errors and try again!');
			}
		}
	}

	public function deleteemailAction() {
		// validate whether user_id is md5 value and get user
		if (!$user = $this->_getUserByID()) return false;
		if (!$this->view->form) $this->view->setVar('form', new \ProfileForm\DeleteEmailForm());
		// pre-populate required data
		$deleteEmail = (Object) Array('user_id' => $user->id);
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
				"controller" => $this->dispatcher->getControllerName(),
				"action" => "index" ));
	}

	public function setprimaryemailAction() {
		// validate whether user_id is md5 value and get user
		if (!$user = $this->_getUserByID()) return false;
		if (!$this->view->form) $this->view->setVar('form', new \ProfileForm\SetEmailAsPrimaryForm());
		// pre-populate required data
		$primaryEmail = (Object) Array('user_id' => $user->id);
		if ($this->view->form->isValid($this->getDI()->getRequest()->getQuery(), $primaryEmail)) {
			$usersEmails = new UsersEmails();
			$usersEmails->setNewPrimaryEmail($primaryEmail);
			// update session object
			$session = $this->session->get('auth');
			if ($user->id == $session->getUserId()) {
				$this->session->set('auth', new \Auth($user));
			}
			// redirect to profile/index
			if ($usersEmails) return $this->dispatcher->forward(array('controller' => $this->dispatcher->getControllerName(),'action' => 'index' ));
		}
	}

	public function deactivateAction() {
		if ($user = $this->_getUserByID()) {
			$user->setUserDeleted();
			return $this->dispatcher->forward(array('controller' => $this->dispatcher->getControllerName(),'action' => 'index' ));
		}
	}

	protected function _getUserByID() {
		$user_id = $this->_getUserID();
		$usersTable = new \Users();
		if (\Framework\Validation\Validator\Md5::isValid($user_id) 
			&& $user = $usersTable->getUserByID($user_id)) {
			return $user;
		}
		$this->dispatcher->forward(
			array('controller' => 'index','action' => 'index' )
		);
		return null;
	}

	protected function _getUserID() {
		$session = $this->session->get('auth');
		return $session->getUserId();
	}

	protected function _isUserDataRequired() {
		return false;
	}

}
