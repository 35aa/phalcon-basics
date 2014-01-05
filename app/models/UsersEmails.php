<?php

class UsersEmails extends \Phalcon\Mvc\Model {

	public function create($data = array(), $whiteList = array()) {
		if (count($data)) $this->assign($data);
		$this->created = time();
		parent::create($data, $whiteList);
	}

	public function sendVerifyEmail($config) {
		// create verification data for new user
		$emailVerification = new UserEmailVerifications();
		$emailVerification->create(array('email_id' => $this->id));
		//email validation
		$email = new Mail\Registration();
		$email->send($emailVerification, $config->application->fromEmail, $config->application->baseUri);
	}

	public function getEmailByEmailID($email_id) {
		return self::findFirst(array('id = :email_id:', 'bind' => array('email_id' => $email_id)));
	}

	public static function isEmailRegistered($email) {
		return self::findFirst(array('email = :email:', 'bind' => array('email' => $email))) != null;
	}

}
