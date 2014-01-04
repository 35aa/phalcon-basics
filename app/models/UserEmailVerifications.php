<?php

class UserEmailVerifications extends \Phalcon\Mvc\Model {

	protected $verification_code;

	public function create($data = array(), $whiteList = array()) {
		if (count($data)) $this->assign($data);
		$security = new Phalcon\Security();
		$this->getVerificationCode();
		$this->salt = $security->getSaltBytes();
		$this->created = time();
		parent::create($data, $whiteList);
	}

	public function getVerificationCode() {
		if (!$this->verification_code) {
			$security = new Phalcon\Security();
			$this->verification_code = $security->hash(md5(microtime(true)), 10);
		}
		return $this->verification_code;
	}

}
