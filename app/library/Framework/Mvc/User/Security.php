<?php

namespace Framework\Mvc\User;

class Security extends \Phalcon\Mvc\User\Plugin {

	public function beforeExecuteRoute(\Phalcon\Events\Event $event, \Phalcon\Mvc\Dispatcher $dispatcher, $exception) {
		if ($exception) {
			//Handle 404 exceptions
			if ($exception instanceof \Phalcon\Mvc\Dispatcher\Exception) {
				$dispatcher->forward(array('controller' => 'error', 'action' => 'notfound'));
				return false;
			}
			//Handle other exceptions
			$dispatcher->forward(array('controller' => 'error', 'action' => 'serviceunavailable'));
			error_log($exception->__toString());
			return false;
		}

		//Check whether the "auth" variable exists in session to define the active role
		$auth = $this->session->get('auth');
		$role = $auth->isAuthenticated() ? 'Administrator' : 'Guest';

		//Take the active controller/action from the dispatcher
		$controller = $dispatcher->getControllerName();
		$action = $dispatcher->getActionName();

		//Obtain the ACL list
		$acl = \Phalcon\DI::getDefault()->get('acl');

		//Check if the Role have access to the controller (resource)
		if ($acl->isAllowed($role, $controller, $action) != \Phalcon\Acl::ALLOW) {

			//If he doesn't have access forward him to the index controller
			$dispatcher->forward(array('controller' => 'index', 'action' => 'index'));

			//Returning "false" we tell to the dispatcher to stop the current operation
			return false;
		}
	}

	public function beforeDispatchLoop(\Phalcon\Events\Event $event, \Phalcon\Mvc\Dispatcher $dispatcher) {
		// Initialize session
		\Phalcon\DI::getDefault()->get('session')->get('auth');
		// Initialize cookies and renew it on client side
		\Phalcon\DI::getDefault()->get('cookies')->has('remember-me');
	}
}
