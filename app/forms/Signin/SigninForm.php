<?php

namespace Signin;

class SigninForm extends \Framework\Forms\Form {

	const FORM_ACTION = 'login/checkCredentials';
	const ELEMENT_REMEMBER_ME_ID = 'remember_me';
	const ELEMENT_REMEMBER_ME_VALUE = true;
	const ELEMENT_SUBMIT_VALUE = 'Sign In';

	public function initialize() {
		$this->setAction(self::FORM_ACTION);

		// email
		$this->add(new \Framework\Forms\Element\Email());

		// password
		$this->add(new \Framework\Forms\Element\Password());

		// remember me
		$this->add(new \Phalcon\Forms\Element\Check(self::ELEMENT_REMEMBER_ME_ID, array('value' => self::ELEMENT_REMEMBER_ME_VALUE)));

		// submit
		$element = new \Framework\Forms\Element\Submit();
		$element->setDefault(self::ELEMENT_SUBMIT_VALUE);
		$this->add($element);
	}

}
