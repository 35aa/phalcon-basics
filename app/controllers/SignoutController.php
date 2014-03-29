<?php

class SignoutController extends \Framework\AbstractController {

	public function beforeExecuteRoute(\Phalcon\Mvc\Dispatcher $dispatcher) {
		if (!$this->session->get('auth')->isAuthenticated()) {
			$dispatcher->forward(array('controller' => 'index','action' => 'index'));
			return false;
		}
	}

	public function indexAction() {
		//Destroy the whole session
		$this->session->destroy();
		// if remember-me cookies was set - kill them all!! boohaha
		if ($this->cookies->has('remember-me') && $this->cookies->has('remember-me-code')) {
			$this->cookies->removeCookies();
		}
		return $this->response->redirect('index/index');
	}
}
