<?php

namespace Framework\Validation\Validator;

class EmailExists extends \Phalcon\Validation\Validator {
	public function validate ($validator, $attribute) {
		$emailTable = new \UsersEmails();
		if ($emailTable->isEmailRegistered($validator->getValue($attribute))) {
			$validator->appendMessage(new \Phalcon\Validation\Message('Email has already registered in the system', $attribute, 'Email'));
			return false;
		}
		return true;
	}
}
