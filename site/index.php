<?php

DEFINE('APP_PATH', __DIR__.'/..');

date_default_timezone_set('Europe/Kiev');

try {

	//Read the configuration
	$config = new Phalcon\Config\Adapter\Ini(__DIR__.'/../app/config/config.ini');

	//Register an autoloader
	$loader = new \Phalcon\Loader();
	//Register some namespaces
	$loader->registerNamespaces($config->library->toArray());
	$loader->registerDirs($config->path->toArray())->register();

	//Create a DI
	$di = new Phalcon\DI\FactoryDefault();

	$di->set('config', $config);

	//Setting up the view component
	$di->set('view', function() use ($config) {
		$view = new \Phalcon\Mvc\View();
		$view->setViewsDir($config->view->dir);
		return $view; });

	//Start the session the first time when some component request the session service
	$di->set('db', function() use ($config) {
		$connection = new \Phalcon\Db\Adapter\Pdo\Mysql($config->database->toArray());
		if (getenv('APPLICATION_ENV') == 'devel') {
			$eventsManager = new Phalcon\Events\Manager();
			$eventsManager->attach('db', function($event, $connection) {
				if ($event->getType() == 'beforeQuery') {
				  //Start a profile with the active connection
				  error_log($connection->getSQLStatement()."\n".json_encode($connection->getSQLVariables()));
				}
			});
			$connection->setEventsManager($eventsManager);
		}
		return  $connection; });

	//Start the session the first time when some component request the session service
	$di->setShared('session', function() {
		$session = new Phalcon\Session\Adapter\Files();
		$session->start();
		if (!$session->get('auth')) $session->set('auth', new \Auth());
		else if (!$session->get('auth')->isAuthenticated()) {
			$session->destroy();
			$session->set('auth', new \Auth());
		}
		$session->get('auth')->resetTimeout();
		return $session; });

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
