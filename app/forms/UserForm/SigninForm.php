<?php

namespace UserForm;

class SigninForm extends \Phalcon\Forms\Form {
	public function initialize() {
		$this->setAction('user/checkCredentials');

		$element = new \Phalcon\Forms\Element\Text('email', array('class' => 'form-control', 'size' => '30', 'maxlength'=>70));
		$element->addValidators(array(
				new \Phalcon\Validation\Validator\PresenceOf(array(
						'message' => 'The email is required')),
				new \Phalcon\Validation\Validator\Email(array(
						'message' => 'The email is not valid' ))
		));
		$this->add($element);

		$element = new \Phalcon\Forms\Element\Password('password', array('class' => 'form-control', 'size' => '15', 'maxlength'=>30));
		$element->addValidators(array(
				new \Phalcon\Validation\Validator\PresenceOf(array(
						'message' => 'The password is required'
				)),
				new \Phalcon\Validation\Validator\StringLength(array(
						'min' => 8,
						'messageMinimum' => 'Password is too short. Minimum 8 characters required.'
				)),
		));
		$this->add($element);

		$element = new \Phalcon\Forms\Element\Check('remember_me', array('value' => true));
		$this->add($element);

		$element = new \Phalcon\Forms\Element\Submit('submit', array('class'=>'btn btn-default', 'value'=>'Login'));
		$this->add($element);
	}
}
