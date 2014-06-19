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
				return $this->dispatcher->forward(
					array(
						'controller' => 'profileadmin',
						'action' => 'index') );
			} else {
				$this->view->messages->addError('Please, fix errors and try again!');
			}
		}
	}

}
