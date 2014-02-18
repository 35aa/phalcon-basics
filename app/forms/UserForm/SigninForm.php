<?php

namespace UserForm;

class SigninForm extends \Phalcon\Forms\Form {

	public function initialize() {
		$this->setAction('user/checkCredentials');

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
