<?php

class Auth {

	const SESSION_TIMEOUT = 3600; //one hour
	const MAX_RETRY_COUNT = 3;

	protected $user;
	protected $started;
	protected $retryCount;

	public function __construct($user = null) {
		$this->user = array();
		if ($user) {
			$this->saveUser($user);
		}
		$this->resetTimeout();
		$this->resetRetryCount();
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
						&& !$this->isExpired();
	}

	public function isExpired() {
		return $this->started + self::SESSION_TIMEOUT < time();
	}

	public function resetTimeout() {
		$this->started = time();
	}

	public function incrementRetryCount() {
		$this->retryCount++;
	}

	public function resetRetryCount() {
		$this->retryCount = 0;
	}

	public function isMaxRetryCount() {
		return $this->retryCount >= self::MAX_RETRY_COUNT;
	}

	public function getUserCredentials() {
		return array('id' => $this->getUserId());
	}

	public function getUserId() {
		return $this->user['id'];
	}
}

