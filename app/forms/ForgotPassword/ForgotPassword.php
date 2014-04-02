<?php

namespace ForgotPassword;

class ForgotPassword extends \Framework\Forms\Form {
	public function initialize() {
		$this->setAction('forgotpassword/sendresetpassword');

		// email
		$this->add(new \Framework\Forms\Element\Email());

		// submit
		$element = new \Framework\Forms\Element\Submit();
		$element->setDefault('Reset');
		$this->add($element);
	}
}
