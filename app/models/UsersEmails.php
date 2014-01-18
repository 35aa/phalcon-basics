<?php

class UsersEmails extends \Phalcon\Mvc\Model {

	protected $verificationObject;

	public function createEmail($email) {
		$this->create(array('user_id' => $email->user_id, 'email' => $email->email, 'is_primary' => $email->is_primary));
	}

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

	public function getPrimaryEmailForUser($user_id) {
		return self::findFirst(array('user_id = :user_id: AND is_primary = 1', 'bind' => array('user_id' => $user_id)));
	}

	public static function isEmailRegistered($email) {
		return self::findFirst(array('email = :email: AND deleted IS NULL', 'bind' => array('email' => $email))) != null;
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
		return self::find(array('user_id = :user_id: AND deleted IS NULL', 'bind' => array('user_id' => $user_id)));
	}

	public function verify($code) {
		if (!$this->getVerificationObject()) return false;
		if ($this->getVerificationObject()->verifyCode($code)) {
			$this->setEmailVerified();
			return true;
		}
	}

	public function getEmailByIDandUserID($email) {
		return self::findFirst(array('id = :id: AND user_id = :user_id: AND deleted IS NULL', 'bind' => array('id' => $email->id, 'user_id' => $email->user_id)));
	}

	public function getUnverifiedEmailByEmail($email) {
		return self::findFirst(array('email = :email: AND verified IS NULL', 'bind' => array('email' => $email)));
	}

	public function getVerifiedPrimaryEmailsByEmail($email) {
		return self::find(array('email = :email: AND verified IS NOT NULL AND is_primary = 1', 'bind' => array('email' => $email)));
	}

	public function setEmailVerified() {
		$this->verified = time();
		$this->save();
	}

	public function setNewPrimaryEmail($primaryEmail) {
		$oldPrimaryEmail = $this->getPrimaryEmailForUser($primaryEmail->user_id);
		$newPrimaryEmail = $this->getEmailByIDandUserID($primaryEmail);
		if ($newPrimaryEmail && $newPrimaryEmail->verified) {
			$oldPrimaryEmail->resetPrimaryEmail();
			$newPrimaryEmail->setEmailPrimary();
			return true;
		}
		return false;
	}

	public function setEmailPrimary() {
		if (!$this->verified) return false;
		$this->is_primary = 1;
		return $this->save();
	}

	public function resetPrimaryEmail() {
		if (!$this->verified) return false;
		$this->is_primary = 0;
		return $this->save();
	}

	public function setEmailDeleted() {
		$this->deleted = time();
		return $this->save();
	}

}
