<?php

class UserVerificationCodes extends \Phalcon\Mvc\Model {

	const VERIFICATION_CODE_LIFESPAN = 18000; // 5 hours

	const REASON_EMAIL_VERIFICATION = 'emailVerification';
	const REASON_RESET_PASSWORD = 'resetPassword';

	protected $_secure_verification_code;

	public function create($data = array(), $whiteList = array()) {
		if (count($data)) $this->assign($data);
		$this->getVerificationCode();
		$this->created = time();
		parent::create($data, $whiteList);
		return $this;
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
		if ($this->created - time() > self::VERIFICATION_CODE_LIFESPAN) return false;
		$security = new Phalcon\Security();
		return $security->checkHash($this->verification_code.$this->salt, $verification_code);
	}

	public function findVerificationCode($email, $reason){
		foreach ($this->loadVerificationObjectForEmail($email, $reason) as $verificationCode) {
			if ($verificationCode->created + UserVerificationCodes::VERIFICATION_CODE_LIFESPAN > time()) {
				return $verificationCode;
			}
		}
	}

	public function loadVerificationObjectForEmail($email, $reason) {
		return self::find(array(
				'email_id=:email_id: AND reason=:reason:',
				'bind'=>array('email_id'=>$email->id, 'reason'=>$reason),
				'order' => 'created desc' ));
	}
}
