<?php

namespace UserForm;

class ResetPassword extends \Framework\Forms\Form {
	public function initialize() {
		$this->setAction('user/setnewpassword');

		// password
		$element = new \Framework\Forms\Element\Password();
		$element->setName('password');
		$element->setLabel('New Password');
		$this->add($element);

		// confirmPassword
		$element = new \Framework\Forms\Element\Password();
		$element->setName('confirmPassword');
		$element->setLabel('Repeat new password');
		$this->add($element);

		// submit
		$element = new \Framework\Forms\Element\Submit();
		$element->setDefault('Change');
		$this->add($element);
	}
}
