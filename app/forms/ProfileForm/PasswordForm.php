<?php

namespace ProfileForm;

class PasswordForm extends \Phalcon\Forms\Form {

	public function initialize() {
		$this->setAction('profile/password');

		// old password
		$element = new \Framework\Forms\Element\Password();
		$element->setName('old_password');
		$this->add($element);

		// new password
		$element = new \Framework\Forms\Element\Password();
		$element->setName('new_password');
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
		$element->setDefault('Change');
		$this->add($element);
	}

}
