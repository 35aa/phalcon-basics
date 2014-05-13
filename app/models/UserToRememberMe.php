<?php

class UserToRememberMe extends \Phalcon\Mvc\Model {

	public function create($data = array(), $whiteList = array()) {
		if (count($data)) $this->assign($data);
		return $this->renewCode();
	}

	public function getCodeByUserID($user_id) {
		return self::findFirst(array('user_id = :user_id:', 'bind' => array('user_id' => $user_id)));
	}

	public function getCodeByCode($code) {
		return self::findFirst(array('code = :code:', 'bind' => array('code' => $code)));
	}

	public function renewCode() {
		$this->generateToken();
		$this->last_updated = time();
		$this->save();
		return $this;
	}

	public function renewCodeByUserID($user_id) {
		if ($code = $this->getCodeByUserID($user_id)) {
			$code->renewCode();
		}
		return $code;
	}

	public function generateToken() {
		return $this->code = md5(microtime(true));
	}

}
