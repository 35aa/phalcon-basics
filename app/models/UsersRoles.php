<?php

class UsersRoles extends \Phalcon\Mvc\Model {

	/**
	 * WARNING: The following constants should be identical to the role records in DB
	 */
	const ROLE_GUEST = 'Guest';
	const ROLE_USER = 'User';
	const ROLE_ADMINISTRATOR = 'Administrator';
	const ROLE_GUEST_ID = 1;
	const ROLE_USER_ID = 2;
	const ROLE_ADMINISTRATOR_ID = 3;

	protected $_emails = array();

	public function initialize() {
		$this->hasOne('id', 'Users', 'role_id');
	}

}
