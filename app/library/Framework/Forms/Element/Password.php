<?

namespace Framework\Forms\Element;

Class Password extends \Phalcon\Forms\Element\Password {

	const ELEMENT_NAME = 'password';

	public function __construct() {
		parent::__construct(self::ELEMENT_NAME, array('class' => 'form-control', 'size' => '15', 'maxlength'=>30));
		$this->addValidators(array(
				new \Phalcon\Validation\Validator\PresenceOf(array(
						'message' => 'The password is required'
				)),
				new \Phalcon\Validation\Validator\StringLength(array(
						'max' => 30,
						'min' => 8,
						'messageMaximum' => 'This password is too long. Please, select another one.',
						'messageMinimum' => 'Password is too short. Minimum 8 characters required.'
				)),
		));
	}

}
