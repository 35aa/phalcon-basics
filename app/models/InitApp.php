<?

class InitApp {

	public static function initView() {
		$config = \Phalcon\DI::getDefault()->get('config');
		$view = new \Phalcon\Mvc\View();
		$view->setTemplateAfter('main');
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
		$eventsManager->attach("dispatch:beforeException", function($event, $dispatcher, $exception) {
			//Handle 404 exceptions
			if ($exception instanceof \Phalcon\Mvc\Dispatcher\Exception) {
				$dispatcher->forward(array('controller' => 'error', 'action' => 'notfound'));
				return false;
			}
			//Handle other exceptions
			$dispatcher->forward(array('controller' => 'error', 'action' => 'serviceunavailable'));
			error_log($exception->__toString());
			return false;
		});

		$eventsManager->attach("dispatch:beforeDispatchLoop", function($event, $dispatcher) {
			// Initialize session
			\Phalcon\DI::getDefault()->get('session')->get('auth');
			// Initialize cookies and renew it on client side
			\Phalcon\DI::getDefault()->get('cookies')->has('remember-me');
		});

		$dispatcher = new \Phalcon\Mvc\Dispatcher();
		//Bind the EventsManager to the dispatcher
		$dispatcher->setEventsManager($eventsManager);
		return $dispatcher;
	}
}
