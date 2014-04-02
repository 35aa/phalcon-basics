<?php

namespace Signup;

class SignupForm extends \Framework\Forms\Form {

	public function initialize() {
		$this->setAction('user/register');

		// name
		$this->add(new \Framework\Forms\Element\Name());

		// email
		$this->add(new \Framework\Forms\Element\Email());

		// password
		$element = new \Framework\Forms\Element\Password();
		$element->addValidators(array(
				new \Phalcon\Validation\Validator\Confirmation(array(
						'message' => 'Password doesn\'t match confirmation',
						'with' => 'confirmPassword'
				))
		));
		$this->add($element);

		// confirmPassword
		$element = new \Framework\Forms\Element\Password();
		$element->setName('confirmPassword');
		$this->add($element);

		// submit
		$element = new \Framework\Forms\Element\Submit();
		$element->setDefault('Create Account');
		$this->add($element);
	}

}
