<?php

class Users extends \Phalcon\Mvc\Model {

	protected $_emails = array();

	public function create($data = array(), $whiteList = array()) {
		if (count($data)) $this->assign($data);
		$this->id = md5(microtime(true));
		$this->hashPassword($this->password);
		$this->created = time();
		parent::create($data, $whiteList);
		// create new user email
		$newUsersEmails = new UsersEmails();
		$newUsersEmails->create(array('user_id' => $this->id, 'email' => $this->email, 'is_primary' => 1));
		$this->_emails[] = $newUsersEmails;
	}

	public function hashPassword($password) {
		$security = new Phalcon\Security();
		$this->salt = $security->getSaltBytes();
		$this->password = $security->hash($password.$this->salt, 10);
	}
	

	public function checkPassword($password) {
		$security = new Phalcon\Security();
		return $security->checkHash($password.$this->salt, $this->password);
	}

	public function getUserByID($user_id) {
		return self::findFirst(array('id = :user_id:', 'bind' => array('user_id' => $user_id)));
	}

	public function getPrimaryEmail() {
		foreach ($this->getEmails() as $email) {
			if($email->is_primary == 1) {
				return $email;
			}
		}
	}

	public function getEmails() {
		return $this->_emails;
	}

	// public function verifyUserByIdAndCode($id, $code) {
	// 	$user = self::findFirst(array(
	// 			'id=:id: and created + 18000 > unix_timestamp() and verified is null',
	// 			'bind'=>array('id'=>$id) ));
	// 	if (!$user) return;
	// 	$security = new Phalcon\Security();
	// 	if (!$security->checkHash($user->email.$user->name, $code)) return;
	// 	$user->verified = time();
	// 	$user->save();
	// 	return $user;
	// }

}
