<?php

class Users extends \Phalcon\Mvc\Model {

	protected $verificationCode;

	public function create($data = array(), $whiteList = array()) {
		if (count($data)) $this->assign($data);
		$this->id = md5(microtime(true));
		$this->hashPassword($this->password);
		$this->getVerificationCode();
		$this->created = time();
		parent::create($data, $whiteList);
	}

	public function hashPassword($password) {
		$security = new Phalcon\Security();
		$this->salt = $security->getSaltBytes();
		$this->password = $security->hash($password.$this->salt, 10);
	}
	
	public function getVerificationCode() {
		if (!$this->verificationCode) {
			$security = new Phalcon\Security();
			$this->verificationCode = $security->hash($this->email.$this->name, 10);
		}
		return $this->verificationCode;
	}

	public function checkPassword($password) {
		$security = new Phalcon\Security();
		return $security->checkHash($password.$this->salt, $this->password);
	}

	public static function isEmailRegistered($email) {
		return self::findFirst(array('email = :email:', 'bind' => array('email' => $email))) != null;
	}

	public function verifyUserByIdAndCode($id, $code) {
		$user = self::findFirst(array(
				'id=:id: and created + 18000 > unix_timestamp() and verified is null',
				'bind'=>array('id'=>$id) ));
		if (!$user) return;
		$security = new Phalcon\Security();
		if (!$security->checkHash($user->email.$user->name, $code)) return;
		$user->verified = time();
		$user->save();
		return $user;
	}
}
