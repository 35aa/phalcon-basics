<?php

namespace ProfileForm;

class PasswordForm extends \Framework\Forms\Form {

	public function initialize() {
		$this->setAction('profile/password');

		// old password
		$element = new \Framework\Forms\Element\Password();
		$element->setName('old_password');
		$element->setLabel('Old password');
		$this->add($element);

		// new password
		$element = new \Framework\Forms\Element\Password();
		$element->setName('new_password');
		$element->setLabel('New password');
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
		$element->setLabel('Repeat new password');
		$this->add($element);

		// submit
		$element = new \Framework\Forms\Element\Submit();
		$element->setDefault('Change');
		$this->add($element);
	}

}
