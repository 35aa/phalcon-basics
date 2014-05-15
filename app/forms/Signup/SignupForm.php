<?php

namespace Signup;

class SignupForm extends \Framework\Forms\Form {

	const FORM_ACTION = 'register/register';
	const ELEMENT_PASSWORD_LABEL = 'Password';
	const ELEMENT_PASSWORD_MESSAGE = 'Password doesn\'t match confirmation';
	const ELEMENT_PASSWORD_WITH_ID = 'confirmPassword';
	const ELEMENT_CONFIRM_PASSWORD_LABEL = 'Сonfirm password';
	const ELEMENT_CONFIRM_PASSWORD_NAME = 'confirmPassword';
	const ELEMENT_SUBMIT_VALUE = 'Create Account';

	public function initialize() {
		$this->setAction(self::FORM_ACTION);

		// name
		$this->add(new \Framework\Forms\Element\Name());

		// email
		$this->add(new \Framework\Forms\Element\Email());

		// password
		$element = new \Framework\Forms\Element\Password();
		$element->setLabel(self::ELEMENT_PASSWORD_LABEL);
		$element->addValidators(array(
				new \Phalcon\Validation\Validator\Confirmation(array(
						'message' => self::ELEMENT_PASSWORD_MESSAGE,
						'with' => self::ELEMENT_PASSWORD_WITH_ID
				))
		));
		$this->add($element);

		// confirmPassword
		$element = new \Framework\Forms\Element\Password();
		$element->setLabel(self::ELEMENT_CONFIRM_PASSWORD_LABEL);
		$element->setName(self::ELEMENT_CONFIRM_PASSWORD_NAME);
		$this->add($element);

		// submit
		$element = new \Framework\Forms\Element\Submit();
		$element->setDefault(self::ELEMENT_SUBMIT_VALUE);
		$this->add($element);
	}

}
