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

		//Obtain the ACL list
		$acl = \Phalcon\DI::getDefault()->get('acl');

		//Check whether the "auth" variable exists in session to define the active role
		$role = array_pop($acl->getRoles());

		//Take the active controller/action from the dispatcher
		$controller = $dispatcher->getControllerName();
		$action = $dispatcher->getActionName();

		//Check if the Role have access to the controller (resource)
		if ($acl->isAllowed($role->getName(), $controller, $action) != \Phalcon\Acl::ALLOW) {

			//If he doesn't have access forward him to the index controller
			$dispatcher->forward(array('controller' => 'index', 'action' => 'index'));

			//Returning "false" we tell to the dispatcher to stop the current operation
			return false;
		}
	}

	public function beforeDispatchLoop(\Phalcon\Events\Event $event, \Phalcon\Mvc\Dispatcher $dispatcher) {
		// I do not think we need this more!!!
		// This is needed by anonymous functions: leaving this for future use!!!
		// Initialize session
		//\Phalcon\DI::getDefault()->get('session')->get('auth');
		// Initialize cookies and renew it on client side
		//\Phalcon\DI::getDefault()->get('cookies')->has('remember-me');
	}
}
