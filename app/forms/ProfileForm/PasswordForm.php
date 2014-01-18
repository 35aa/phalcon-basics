<?php

namespace ProfileForm;

class PasswordForm extends \Phalcon\Forms\Form {
	public function initialize() {
		$this->setAction('profile/password');

		$element = new \Phalcon\Forms\Element\Password('old_password', array('size' => '22', 'maxlength'=>30));
		$element->addValidators(array(
				new \Phalcon\Validation\Validator\PresenceOf(array(
						'message' => 'The password is required'
				)),
				new \Phalcon\Validation\Validator\StringLength(array(
						'max' => 30,
						'min' => 8,
						'messageMaximum' => 'This name is too long. Please, select another one.',
						'messageMinimum' => 'Password is too short. Minimum 8 characters required.'
				))
		));
		$this->add($element);

		$element = new \Phalcon\Forms\Element\Password('new_password', array('size' => '22', 'maxlength'=>30));
		$element->addValidators(array(
				new \Phalcon\Validation\Validator\PresenceOf(array(
						'message' => 'The password is required'
				)),
				new \Phalcon\Validation\Validator\StringLength(array(
						'max' => 30,
						'min' => 8,
						'messageMaximum' => 'This name is too long. Please, select another one.',
						'messageMinimum' => 'Password is too short. Minimum 8 characters required.'
				)),
				new \Phalcon\Validation\Validator\Confirmation(array(
						'message' => 'Password doesn\'t match confirmation',
						'with' => 'confirmPassword'
				))
		));
		$this->add($element);

		$element = new \Phalcon\Forms\Element\Password('confirmPassword', array('size' => '22', 'maxlength'=>30));
		$this->add($element);
		
		$element = new \Phalcon\Forms\Element\Submit('submit', array('value'=>'Change'));
		$this->add($element);
	}
}
