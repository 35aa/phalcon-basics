<?php

class Auth {

	const SESSION_TIMEOUT = 3600; //one hour
	const MAX_RETRY_COUNT = 3;

	protected $user;
	protected $started;
	protected $retryCount;
	protected $role;

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
		if ($this->isAuthenticated()) {
			return $this->user['id'];
		}
		else {
			return null;
		}
	}

	public function getUserRole() {
		// if role is not defined - define it
		if (!$this->role) {
			//by default guest
			$this->role = UsersRoles::ROLE_GUEST;
			//if user authorized - get his own role
			if ($this->isAuthenticated()) {
				$usersTable = new \Users();
				$this->role = $usersTable->getUserById($this->getUserId())->getUsersRoles()->role;
			}
		}
		//return role
		return $this->role;
	}

	//magic method called when session is storing in the file as string
	//should return array of properties' names which should be serialized
	//WARNING: all properties which does not appear here will not be saved in session
	//WARNING: do not add property 'role' here. It should be populated per each request.
	public function __sleep() {
		return array('user', 'started', 'retryCount');
	}
}

