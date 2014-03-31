<?php

namespace Framework\Validation\Validator;

class UserExists extends \Phalcon\Validation\Validator {
	public function validate ($validator, $attribute) {
		$userTable = new \Users();
		if ($userTable->isUserNameStored($validator->getValue($attribute))) {
			$validator->appendMessage(new \Phalcon\Validation\Message('Username has already registered in the system', $attribute, 'User'));
			return false;
		}
		return true;
	}
}
