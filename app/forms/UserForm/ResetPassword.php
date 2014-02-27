<?php

namespace UserForm;

class ResetPassword extends \Phalcon\Forms\Form {
	public function initialize() {
		$this->setAction('user/setnewpassword');

		$element = new \Phalcon\Forms\Element\Password('password', array('size' => '15', 'maxlength'=>30));
		$element->addValidators(array(
				new \Phalcon\Validation\Validator\PresenceOf(array(
						'message' => 'The password is required'
				)),
				new \Phalcon\Validation\Validator\StringLength(array(
						'min' => 8,
						'messageMinimum' => 'Password is too short. Minimum 8 characters required.'
				)),
				new \Phalcon\Validation\Validator\Confirmation(array(
						'message' => 'Password doesn\'t match confirmation',
						'with' => 'confirmPassword'
				))
		));
		$this->add($element);

		$element = new \Phalcon\Forms\Element\Password('confirmPassword', array('size' => '15', 'maxlength'=>30));
		$this->add($element);

		$element = new \Phalcon\Forms\Element\Submit('submit', array('value'=>'Save'));
		$this->add($element);
	}
}
