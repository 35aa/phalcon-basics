<?php

class UseradminController extends \Framework\AbstractController {

	public function beforeExecuteRoute(\Phalcon\Mvc\Dispatcher $dispatcher) {
		if (!$this->session->get('auth')->isAuthenticated()) {
			$dispatcher->forward(array('controller' => 'login','action' => 'index'));
			return false;
		}
	}

	public function indexAction() {
		$users = new \Users();
		$this->view->setVar('users', $users->getAllUsers());
	}

}
