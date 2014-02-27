<?php

class UsersEmails extends \Phalcon\Mvc\Model {

	protected $verificationObject;
	protected $verificationCode;

	public function onConstruct() {
		$this->verificationObject = new UserVerificationCodes();
	}

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

	public function sendResetPasswordEmail($config) {
		//email validation
		$email = new Mail\ResetPassword();
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

	public function createVerificationCode($reason) {
		$this->verificationCode = $this->verificationObject->create(array('email_id' => $this->id, 'reason' => $reason));
		return $this->verificationCode;
	}

	public function getVerificationCode($reason) {
		if ($this->verificationCode) return $this->verificationCode;
		$this->verificationCode = $this->verificationObject->findVerificationCode($this, $reason);
		return $this->verificationCode;
	}

	public function getEmailsForUser($user_id) {
		return self::find(array('user_id = :user_id: AND deleted IS NULL', 'bind' => array('user_id' => $user_id)));
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
		$userEmails = $this->getEmailsForUser($primaryEmail->user_id);
		$newPrimary = null;
		foreach ($userEmails as $email) {
			if ($email->is_primary) {
				$oldPrimary = $email;
			} elseif ($email->id == $primaryEmail->id && $email->verified) {
				$newPrimary = $email;
			}
		}
		if ($oldPrimary && $newPrimary) {
			$oldPrimary->resetPrimaryEmail();
			$newPrimary->setEmailPrimary();
			return true;
		}
		return false;
	}

	public function setEmailPrimary() {
		$this->is_primary = 1;
		return $this->save();
	}

	public function resetPrimaryEmail() {
		$this->is_primary = 0;
		return $this->save();
	}

	public function setEmailDeleted() {
		$this->deleted = time();
		return $this->save();
	}

}
