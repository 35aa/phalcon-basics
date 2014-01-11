<?php

class UsersEmails extends \Phalcon\Mvc\Model {

	protected $verificationObject;

	public function create($data = array(), $whiteList = array()) {
		if (count($data)) $this->assign($data);
		$this->created = time();
		parent::create($data, $whiteList);
	}

	public function sendVerifyEmail($config) {
		//email validation
		$email = new Mail\Registration();
		$email->send($this, $config->application->fromEmail, $config->application->baseUri);
	}

	public function getEmailByEmailID($email_id) {
		return self::findFirst(array('id = :email_id:', 'bind' => array('email_id' => $email_id)));
	}

	public static function isEmailRegistered($email) {
		return self::findFirst(array('email = :email:', 'bind' => array('email' => $email))) != null;
	}

	public function createVerificationObject() {
		$this->verificationObject = new UserEmailVerifications();
		$this->verificationObject->create(array('email_id' => $this->id));
	}

	public function getVerificationObject() {
		// create verification data for new user
		if ($this->verificationObject) return $this->verificationObject;
		$emailVerificationTable = new UserEmailVerifications();
		foreach ($emailVerificationTable->loadVerificationObjectForEmail($this) as $verificationObject) {
			if ($verificationObject->created + UserEmailVerifications::VERIFICATION_CODE_LIFESPAN > time()) {
				$this->verificationObject = $verificationObject;
				break;
			}
		}
		return $this->verificationObject;
	}

	public function getEmailsForUser($user_id) {
		return self::find(array('user_id = :user_id:', 'bind' => array('user_id' => $user_id)));
	}

	public function verify($code) {
		if (!$this->getVerificationObject()) return false;
		if ($this->getVerificationObject()->verifyCode($code)) {
			$this->setEmailVerified();
			return true;
		}
	}

	public function setEmailVerified() {
		$this->verified = time();
		$this->save();
	}

	public function getUnverifiedEmailByEmail($email) {
		return self::findFirst(array('email = :email: AND verified IS NULL', 'bind' => array('email' => $email)));
	}

	public function getVerifiedPrimaryEmailsByEmail($email) {
		return self::find(array('email = :email: AND verified IS NOT NULL AND is_primary = 1', 'bind' => array('email' => $email)));
	}

}
