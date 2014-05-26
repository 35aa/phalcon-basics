<?php

namespace UserForm;

class ResetPassword extends \Framework\Forms\Form {

	const FORM_ACTION = 'user/setnewpassword';
	const ELEMENT_PASSWORD_NAME = 'password';
	const ELEMENT_PASSWORD_LABEL = 'New Password';
	const ELEMENT_CONFIRM_PASSWORD_NAME = 'confirmPassword';
	const ELEMENT_CONFIRM_PASSWORD_LABEL = 'Repeat new password';
	const ELEMENT_SUBMIT_VALUE = 'Change';
	const ELEMENT_PASSWORD_MESSAGE = 'Password doesn\'t match confirmation';
	const ELEMENT_PASSWORD_WITH_ELEMENT_ID = 'confirmPassword';

	public function initialize() {
		$this->setAction(self::FORM_ACTION);

		// password
		$element = new \Framework\Forms\Element\Password();
		$element->setName(self::ELEMENT_PASSWORD_NAME);
		$element->setLabel(self::ELEMENT_PASSWORD_LABEL);
		$element->addValidators(array(
				new \Phalcon\Validation\Validator\Confirmation(array(
						'message' => self::ELEMENT_PASSWORD_MESSAGE,
						'with' => self::ELEMENT_PASSWORD_WITH_ELEMENT_ID
				))
		));
		$this->add($element);

		// confirmPassword
		$element = new \Framework\Forms\Element\Password();
		$element->setName(self::ELEMENT_CONFIRM_PASSWORD_NAME);
		$element->setLabel(self::ELEMENT_CONFIRM_PASSWORD_LABEL);
		$this->add($element);

		// submit
		$element = new \Framework\Forms\Element\Submit();
		$element->setDefault(self::ELEMENT_SUBMIT_VALUE);
		$this->add($element);
	}
}
