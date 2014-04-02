<?php

namespace Signin;

class SigninForm extends \Framework\Forms\Form {

	public function initialize() {
		$this->setAction('login/checkCredentials');

		// email
		$this->add(new \Framework\Forms\Element\Email());

		// password
		$this->add(new \Framework\Forms\Element\Password());

		// remember me
		$this->add(new \Phalcon\Forms\Element\Check('remember_me', array('value' => true)));

		// submit
		$element = new \Framework\Forms\Element\Submit();
		$element->setDefault('Sign In');
		$this->add($element);
	}

}
