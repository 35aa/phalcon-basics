<?php

namespace UserForm;

class SignupForm extends \Phalcon\Forms\Form {
	public function initialize() {
		$this->setAction('user/register');

		$element = new \Phalcon\Forms\Element\Text('name', array('class' => 'form-control', 'size' => '30', 'maxlength'=>70));
		$element->addValidators(array(
				new \Phalcon\Validation\Validator\PresenceOf(array(
						'message' => 'The username is required')),
				new \Phalcon\Validation\Validator\StringLength(array(
						'max' => 70,
						'min' => 3,
						'messageMaximum' => 'This name is too long. Please, select another one.',
						'messageMinimum' => 'This name is too short. Please, select another one.' )) ));
		$this->add($element);

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
				new \Phalcon\Validation\Validator\Confirmation(array(
						'message' => 'Password doesn\'t match confirmation',
						'with' => 'confirmPassword'
				))
		));
		$this->add($element);

		$element = new \Phalcon\Forms\Element\Password('confirmPassword', array('class' => 'form-control', 'size' => '15', 'maxlength'=>30));
		$this->add($element);
		
		$element = new \Phalcon\Forms\Element\Submit('submit', array('class'=>'btn btn-default', 'value'=>'Register'));
		$this->add($element);
	}
}
