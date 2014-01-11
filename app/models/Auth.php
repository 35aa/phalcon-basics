<?php

class Auth {

	const SESSION_TIMEOUT = 3600; //one hour

	protected $user;
	protected $started;

	public function __construct($user = null) {
		$this->user = array();
		if ($user) {
			$this->saveUser($user);
		}
		$this->started = time();
	}

	public function saveUser($user) {
		$this->user = array(
			'id' => $user->id,
			'email' => $user->getPrimaryEmail()->email,
			'name' => $user->name,
			'created' => $user->created
		);
	}

	public function isAuthenticated() {
		return isset($this->user['id']) && $this->user['id']
						&& $this->started + self::SESSION_TIMEOUT < time();
	}
}

