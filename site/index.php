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

	//Start the session the first time when some component request the session service
	$di->setShared('session', function() {return \Framework\Session\Init::session(); });

	$di->set('cookies', function() {
		$cookies = new \Phalcon\Http\Response\Cookies();
		$userToRememberMeTable = new \UserToRememberMe();
		// remember-me
		if ($cookies->has('remember-me') && $cookies->has('remember-me-code')) {
			if ($code = $userToRememberMeTable->getCodeByCode($cookies->get('remember-me-code')->getValue())) {
				$newCode = $code->renewCode();
				// set new cookie
				$cookies->get('remember-me-code')->setValue($newCode->code);
				$cookies->get('remember-me-code')->setExpiration(time() + 15 * 86400);
				$cookies->get('remember-me-code')->send();
				$cookies->get('remember-me')->setValue(1);
				$cookies->get('remember-me')->setExpiration($cookies->get('remember-me-code')->getExpiration());
				$cookies->get('remember-me')->send();
			} else {
				$cookies->get('remember-me')->delete();
				$cookies->get('remember-me-code')->delete();
			}
		} else {
			// if we lose one of our cookies - kill the rest cookie
			$cookies->get('remember-me')->delete();
			$cookies->get('remember-me-code')->delete();
		}
		$cookies->useEncryption(false);
		return $cookies;
	});

	$di->set('dispatcher', function() use ($di) {
		//Create an event manager
		$eventsManager = new \Phalcon\Events\Manager();
		//Attach a listener for type "dispatch"
		$eventsManager->attach("dispatch:beforeDispatchLoop", function($event, $dispatcher) use ($di) {
			// remember-me
			$di->get('session')->get('auth');
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
