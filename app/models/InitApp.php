<?

class InitApp {

	public static function initView() {
		$config = \Phalcon\DI::getDefault()->get('config');
		$view = new \Phalcon\Mvc\View();
		$view->setTemplateAfter('main');
		$view->setVar('messages', new \Framework\Messages());
		$view->setVar('bootstrap_enable', $config->view->bootstrap);
		$view->setVar('sign_up_enable', $config->application->signUp);
		// for escaping in views
		$view->setVar('escaper', new \Phalcon\Escaper());
		$view->setViewsDir($config->view->dir);
		return $view;
	}

	public static function initDb() {
		$config = \Phalcon\DI::getDefault()->get('config');
		$connection = new \Phalcon\Db\Adapter\Pdo\Mysql($config->database->toArray());
		if (getenv('APPLICATION_ENV') == 'devel') {
			$eventsManager = new \Phalcon\Events\Manager();
			$eventsManager->attach('db', function($event, $connection) {
				if ($event->getType() == 'beforeQuery') {
					//Start a profile with the active connection
					error_log($connection->getSQLStatement()."\n".json_encode($connection->getSQLVariables()));
				}
			});
			$connection->setEventsManager($eventsManager);
		}
		return  $connection;
	}

	public static function initCrypt() {
		$config = \Phalcon\DI::getDefault()->get('config');
		$crypt = new \Phalcon\Crypt();
		$crypt->setKey($config->application->cryptKey);
		return $crypt;
	}

	public static function initCookies() {
		$cookies = new \Framework\Cookies();
		$userToRememberMeTable = new \UserToRememberMe();
		// remember-me
		$code = null;
		if ($cookies->has('remember-me') && $cookies->has('remember-me-code')) {
			if ($code = $userToRememberMeTable->getCodeByCode($cookies->get('remember-me-code')->getValue())) {
				// set new cookie
				$cookies->updateCookies($code->renewCode()->code);
			}
		}
		if (!$code) {
			// if we lose one of our cookies - kill the rest cookie
			$cookies->removeCookies();
		}
		return $cookies;
	}

	public static function initDispatcher() {
		//Create an EventsManager
		$eventsManager = new \Phalcon\Events\Manager();
		//Attach a listener

		//Listen for events produced in the dispatcher using the Security plugin
		$eventsManager->attach('dispatch', new \Framework\Mvc\User\Security(\Phalcon\DI::getDefault()));

		$dispatcher = new \Phalcon\Mvc\Dispatcher();
		//Bind the EventsManager to the dispatcher
		$dispatcher->setEventsManager($eventsManager);
		return $dispatcher;
	}

	/**
	 * see for more info ./app/library/Framework/Mvc/User/Security.php
	 */
	public static function initAcl() {
		//Create the ACL
		$acl = new \Phalcon\Acl\Adapter\Memory();

		//The default action is DENY access
		$acl->setDefaultAction(\Phalcon\Acl::DENY);

		//get user role
		$role = new \Phalcon\Acl\Role(\Phalcon\DI::getDefault()->get('session')->get('auth')->getUserRole());

		$acl->addRole($role);

		// all resources available for administrator
		if ($role->getName() == UsersRoles::ROLE_ADMINISTRATOR) {
			$acl->allow($role->getName(), '*', '*');
		}
		else {
			//Private area resources
			$userResources = array(
				'profile' => array('index', 'username', 'password', 'email', 'deleteemail', 'setprimaryemail', 'deactivate'),
				'home' => array('index') );

			//Public area resources
			$publicResources = array(
				'confirmemail' => array('index', 'initverify', 'resetpassword', 'resendresetpassword'),
				'error' => array('notfound', 'serviceunavailable'),
				'forgotpassword' => array('index', 'sendresetpassword'),
				'index' => array('index'),
				'login' => array('index', 'checkCredentials'),
				'user' => array('index', 'setnewpassword', 'resetpassword'),
				'signout' => array('index'),
				'register' => array('index', 'register') );

			//select resources for the role for the guest
			if ($role->getName() == UsersRoles::ROLE_GUEST) {
				$resources = $publicResources;
			}
			//select resources for the role for the user
			else {
				$resources = array_merge($userResources, $publicResources);
			}

			//register resources
			foreach ($resources as $resource => $actions) {
				$acl->addResource(new Phalcon\Acl\Resource($resource), $actions);
			}

			//Grant access to the resources
			foreach ($resources as $resource => $actions) {
				foreach ($actions as $action) {
					$acl->allow($role->getName(), $resource, $action);
				}
			}
		}
		return $acl;
	}

	public static function initBreadcrumbs() {
		$breadcrumbs = new \Framework\Breadcrumbs(\Phalcon\DI::getDefault()->get('dispatcher'));
		$view = \Phalcon\DI::getDefault()->get('view');
		$view->setVar('breadcrumbs', $breadcrumbs);
	}

}
