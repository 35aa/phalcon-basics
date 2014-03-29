<?php

class HomeController extends \Framework\AbstractController {

	public function beforeExecuteRoute(\Phalcon\Mvc\Dispatcher $dispatcher) {
		if (!$this->session->get('auth')->isAuthenticated()) {
			$dispatcher->forward(array('controller' => 'login','action' => 'index'));
			return false;
		}
	}

	public function indexAction() {}
}
