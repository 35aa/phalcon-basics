<?php

class ProfileadminController extends ProfileController {

	protected function _getUserID() {
		return $this->dispatcher->getParams()[0];
	}

	protected function _isUserDataRequired() {
		return true;
	}

	public function activateAction() {
		if ($user = $this->_getUserByID()) $user->setUserActive();
		return $this->dispatcher->forward(array('controller' => $this->dispatcher->getControllerName(),'action' => 'index' ));
	}

	public function roleAction() {
		// validate whether user_id is md5 value and get user
		if (!$user = $this->_getUserByID()) return false;
		if (!$this->view->form) $this->view->setVar('form', new \ProfileForm\RoleForm($this->_isUserDataRequired() ? $user : null));
		if ($this->getDI()->getRequest()->isPost()) {
			$role = (Object) Array();
			if ($this->view->form->isValid($this->getDI()->getRequest()->getPost(), $role)) {
				$user->setNewRole($role);
				$this->view->messages->addSuccess('Role was changed successfully!');
			} else {
				$this->view->messages->addError('Please, fix errors and try again!');
			}
		}
	}

	public function passwordAction() {
		// validate whether user_id is md5 value and get user
		if (!$user = $this->_getUserByID()) return false;
		if (!$this->view->form) $this->view->setVar('form', new \ProfileForm\PasswordAdminForm($this->_isUserDataRequired() ? $user : null));
		if ($this->getDI()->getRequest()->isPost()) {
			$password = (Object) Array();
			if ($this->view->form->isValid($this->getDI()->getRequest()->getPost(), $password)) {
				$user->saveNewPassword($password->new_password);
				$this->view->messages->addSuccess('Password was changed successfully!');
			}
			else {
				$this->view->messages->addError('Please, fix errors and try again!');
			}
			$this->view->form->get('new_password')->clear();
			$this->view->form->get('confirmPassword')->clear();
		}
	}

}
