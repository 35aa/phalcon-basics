<?php

class HomeController extends \Phalcon\Mvc\Controller {

	public function initialize() {
		if (!$this->session->get('auth')->isAuthenticated()) {$this->response->redirect("user/signin");}//redirect to index/index page
	}

	public function indexAction() {}
}
