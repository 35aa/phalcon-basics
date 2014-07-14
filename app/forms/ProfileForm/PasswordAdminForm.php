<?php

namespace ProfileForm;

class PasswordAdminForm extends AbstractForm {

	const ACTION = 'password';
	const ELEMENT_NEW_PASSWORD_NAME = 'new_password';
	const ELEMENT_NEW_PASSWORD_LABEL = 'New password';
	const ELEMENT_NEW_PASSWORD_MESSAGE = 'Password doesn\'t match confirmation';
	const ELEMENT_CONFIRM_PASSWORD_NAME = 'confirmPassword';
	const ELEMENT_CONFIRM_PASSWORD_LABEL = 'Repeat new password';
	const ELEMENT_SUBMIT_VALUE = 'Change';

	public function initialize() {
		$this->setAction($this->_getFormAction().'/'.$this->getValue('id'));

		// hidden user_id
		$element = new \Framework\Forms\Element\HiddenUserID();
		$this->add($element);

		// new password
		$element = new \Framework\Forms\Element\Password();
		$element->setName(self::ELEMENT_NEW_PASSWORD_NAME);
		$element->setLabel(self::ELEMENT_NEW_PASSWORD_LABEL);
		$element->addValidators(array(
				new \Phalcon\Validation\Validator\Confirmation(array(
						'message' => self::ELEMENT_NEW_PASSWORD_MESSAGE,
						'with' => self::ELEMENT_CONFIRM_PASSWORD_NAME
				))
		));
		$this->add($element);

		// confirmPassword
		$element = new \Framework\Forms\Element\Password();
		$element->setName(self::ELEMENT_CONFIRM_PASSWORD_NAME);
		$element->setLabel(self::ELEMENT_CONFIRM_PASSWORD_LABEL);
		$this->add($element);

		// submit
		$element = new \Framework\Forms\Element\Submit();
		$element->setDefault(self::ELEMENT_SUBMIT_VALUE);
		$this->add($element);
	}

	protected function _getAction() {
		return self::ACTION;
	}
}
