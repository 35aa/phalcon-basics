<?

class InitApp {

	public static function initView() {
		$config = \Phalcon\DI::getDefault()->get('config');
		$view = new \Phalcon\Mvc\View();
		$view->setViewsDir($config->view->dir);
		return $view;
	}

	public static function initDb() {
		$config = \Phalcon\DI::getDefault()->get('config');
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
		return  $connection;
	}

	public static function initSession() {
		$session = new Phalcon\Session\Adapter\Files();
		$session->start();
		if (!$session->get('auth')) $session->set('auth', new \Auth());
		elseif ($session->get('auth')->isExpired()) {
			$session->destroy();
			$session->set('auth', new \Auth());
		}
		$session->get('auth')->resetTimeout();
		return $session;
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

		$dispatcher = new \Phalcon\Mvc\Dispatcher();
		//Bind the EventsManager to the dispatcher
		$dispatcher->setEventsManager($eventsManager);
		return $dispatcher;
	}
}
