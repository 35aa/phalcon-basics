<?php

namespace Framework\Validation\Validator;

class EmailExists extends \Phalcon\Validation\Validator {

	const VALIDATION_MESSAGE = 'Email has already registered in the system';

	public function validate ($validator, $attribute) {
		$emailTable = new \UsersEmails();
		if ($emailTable->isEmailRegistered($validator->getValue($attribute))) {
			$validator->appendMessage(new \Phalcon\Validation\Message(self::VALIDATION_MESSAGE, $attribute, 'Email'));
			return false;
		}
		return true;
	}

}
