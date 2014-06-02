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

}
