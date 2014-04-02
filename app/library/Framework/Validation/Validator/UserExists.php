<?php

namespace Framework\Validation\Validator;

class UserExists extends \Phalcon\Validation\Validator {

	const VALIDATION_MESSAGE = 'Username has already registered in the system';

	public function validate ($validator, $attribute) {
		$userTable = new \Users();
		if ($userTable->isUserNameStored($validator->getValue($attribute))) {
			$validator->appendMessage(new \Phalcon\Validation\Message(self::VALIDATION_MESSAGE, $attribute, 'User'));
			return false;
		}
		return true;
	}

}
