<?php

class UseradminController extends \Framework\AbstractController {

	public function beforeExecuteRoute(\Phalcon\Mvc\Dispatcher $dispatcher) {
		if (!$this->session->get('auth')->isAuthenticated()) {
			$dispatcher->forward(array('controller' => 'login','action' => 'index'));
			return false;
		}
	}

	public function indexAction() {
		$pageNumber = intval($this->getDI()->getRequest()->get('pageNumber'));
		$paginator = new \Framework\Paginator\Adapter\ModelTable(array('pageNumber' => $pageNumber, 'dataProvider' => new \Users(), 'itemsPerPage' => 5));
		$this->view->setVar('users', $paginator);
	}

}
