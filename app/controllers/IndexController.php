<?php

class IndexController extends \Framework\AbstractController {

	public function beforeExecuteRoute(\Phalcon\Mvc\Dispatcher $dispatcher) {}

	public function indexAction() {
		echo "<h1>Hello!</h1>";
	}

}
