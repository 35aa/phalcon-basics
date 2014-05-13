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
		$crypt = new \Phalcon\Crypt();
		$crypt->setKey('+N+~j!Oc%>{#^h@8.K');
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
		$userId = \Phalcon\DI::getDefault()->get('session')->get('auth')->getUserId();

		//Create the ACL
		$acl = new \Phalcon\Acl\Adapter\Memory();

		//The default action is DENY access
		$acl->setDefaultAction(\Phalcon\Acl::DENY);

		//Register two roles, Users is registered users
		//and guests are users without a defined identity
		$role = new \Phalcon\Acl\Role(UsersRoles::ROLE_GUEST);
		if ($userId) {
			$usersTable = new \Users();
			if ($userRole = $usersTable->getUserById($userId)->getUsersRoles()) {
				$role = new \Phalcon\Acl\Role($userRole->role);
			}
		}

		$acl->addRole($role);

		if ($role->getName() == UsersRoles::ROLE_ADMINISTRATOR) {
			$acl->allow($role->getName(), '*', '*');
		}
		else {

			//Private area resources (backend)
			$userResources = array(
				'profile' => array('index', 'username', 'password', 'email', 'deleteemail', 'setprimaryemail'),
				'signout' => array('index'),
				'home' => array('index') );

			//Public area resources (frontend)
			$publicResources = array(
				'confirmemail' => array('index', 'initverify', 'resetpassword', 'resendresetpassword'),
				'error' => array('notfound', 'serviceunavailable'),
				'forgotpassword' => array('index', 'sendresetpassword'),
				'index' => array('index'),
				'login' => array('index', 'checkCredentials'),
				'user' => array('index', 'setnewpassword', 'resetpassword'),
				'register' => array('index', 'register') );

			foreach (array_merge($userResources, $publicResources) as $resource => $actions) {
				$acl->addResource(new Phalcon\Acl\Resource($resource), $actions);
			}

			//Grant access to public areas to both administrator and guests
			foreach ($publicResources as $resource => $actions) {
				foreach ($actions as $action) {
					$acl->allow($role->getName(), $resource, $action);
				}
			}

			if ($role->getName() != UsersRoles::ROLE_GUEST) {
				//Grant access to private area only to role Users
				foreach ($userResources as $resource => $actions) {
					foreach ($actions as $action) {
						$acl->allow($role->getName(), $resource, $action);
					}
				}
			}
		}

		return $acl;
	}
}
