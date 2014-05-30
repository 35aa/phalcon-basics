<?php

namespace Framework\Validation\Validator;

class Md5 extends \Phalcon\Validation\Validator {

	const VALIDATION_MESSAGE = 'Provided value for attribute is not a valid md5 string.';

	public static function isValid($value) {
		$validation = new \Phalcon\Validation();
		$validation->add('md5', new self());
		$messages = $validation->validate(array('md5'=>$value));
		return count($messages) == 0;
	}
	
	public function validate ($validator, $attribute) {
		$userTable = new \Users();
		if (preg_match('/^[a-f0-9]{32}$/', $validator->getValue($attribute))) {
						return true;
		}
		else {
			$validator->appendMessage(new \Phalcon\Validation\Message(self::VALIDATION_MESSAGE, $attribute, 'User'));
			return false;
		}
		return true;
	}

}
