<?php

DEFINE('APP_PATH', __DIR__.'/..');

date_default_timezone_set('Europe/Kiev');

try {

	//Read the configuration
	$config = new \Phalcon\Config\Adapter\Ini(__DIR__.'/../app/config/config.ini');

	//Register an autoloader
	$loader = new \Phalcon\Loader();
	//Register some namespaces
	$loader->registerNamespaces($config->library->toArray());
	$loader->registerDirs($config->path->toArray())->register();

	//Create a DI
	$di = new \Phalcon\DI\FactoryDefault();

	$di->set('config', $config);

	//Setting up the view component
	$di->set('view', function() use ($config) {
		$view = new \Phalcon\Mvc\View();
		$view->setViewsDir($config->view->dir);
		$view->setTemplateAfter('main');
		$view->setVar('bootstrap_enable', $config->view->bootstrap);
		return $view;
	});

	//Start the session the first time when some component request the session service
	$di->set('db', function() use ($config) {
		$connection = new \Phalcon\Db\Adapter\Pdo\Mysql($config->database->toArray());
		if (getenv('APPLICATION_ENV') == 'devel') {
			$eventsManager = new \Phalcon\Events\Manager();
			$eventsManager->attach('db', function($event, $connection) {
				if ($event->getType() == 'beforeQuery') {
					//Start a profile with the active connection
					// error_log($connection->getSQLStatement()."\n".json_encode($connection->getSQLVariables()));
				}
			});
			$connection->setEventsManager($eventsManager);
		}
		return  $connection; });

	$di->set('crypt', function() {
		$crypt = new \Phalcon\Crypt();
		$crypt->setKey('+N+~j!Oc%>{#^h@8.K');
		return $crypt;
	});

	//Start the session the first time when some component request the session service
	$di->setShared('session', function() {return \Framework\Session\Init::session(); });

	$di->set('cookies', function() {
		$cookies = new \Framework\Cookies();
		$userToRememberMeTable = new \UserToRememberMe();
		// remember-me
		if ($cookies->has('remember-me') && $cookies->has('remember-me-code')) {
			$newCode = null;
			if ($code = $userToRememberMeTable->getCodeByCode($cookies->get('remember-me-code')->getValue())) {
				$newCode = $code->renewCode();
				// set new cookie
				$cookies->updateCookies($newCode->code);
			}
		}
		if (!$newCode) {
			// if we lose one of our cookies - kill the rest cookie
			$cookies->removeCookies();
		}
		return $cookies;
	});

	// This method is required for remember-me function
	$di->set('dispatcher', function() use ($di) {
		//Create an event manager
		$eventsManager = new \Phalcon\Events\Manager();
		//Attach a listener for type "dispatch"
		$eventsManager->attach("dispatch:beforeDispatchLoop", function($event, $dispatcher) use ($di) {
			// Initialize session
			$di->get('session')->get('auth');
			// Initialize cookies and renew it on client side
			$di->get('cookies')->has('remember-me');
		});
		$dispatcher = new \Phalcon\Mvc\Dispatcher;
		//Bind the eventsManager to the view component
		$dispatcher->setEventsManager($eventsManager);
		return $dispatcher;
	}, true);

	//Handle the request
	$application = new \Phalcon\Mvc\Application($di);

	\Phalcon\Tag::setDoctype(\Phalcon\Tag::HTML401_STRICT);

	$application->getDI()->getResponse()->setContentType('text/html', 'UTF-8');

	echo $application->handle()->getContent();

}
catch(\Phalcon\Exception $e) {
	error_log("PhalconException: ". $e->getMessage());
}
catch(Exception $e) {
	error_log("Exception: ". $e->getMessage());
}
