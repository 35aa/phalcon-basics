<?php

class UserEmailVerifications extends \Phalcon\Mvc\Model {

	protected $_secure_verification_code;

	public function create($data = array(), $whiteList = array()) {
// error_log(print_r(debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS),true));
		if (count($data)) $this->assign($data);
		$this->email_id = $this->email_id;
		$this->getVerificationCode();
		$this->created = time();
		parent::create($data, $whiteList);
	}

	public function getVerificationCode() {
		$security = new Phalcon\Security();
		if (!isset($this->verification_code)) {
			$this->salt = $security->getSaltBytes();
			$this->verification_code = md5(microtime(true));
		}
		if (!$this->_secure_verification_code) {
			$this->_secure_verification_code = $security->hash($this->verification_code.$this->salt, 10);
		}
		return $this->_secure_verification_code;
	}

	public function verifyCode($verification_code) {
		if ($this->created - time() > 18000) return false;
		$security = new Phalcon\Security();
		return $security->checkHash($this->verification_code.$this->salt, $verification_code);
	}

	public function loadVerificationObjectForEmail($email) {
		return self::find(array('email_id=:email_id:','bind'=>array('email_id'=>$email->id) ));
	}
}
