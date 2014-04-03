<?

namespace Framework\Forms\Element;

Class Password extends \Phalcon\Forms\Element\Password {

	const ELEMENT_NAME = 'password';
	const ELEMENT_CLASS = 'form-control';
	const ELEMENT_SIZE = 15;
	const ELEMENT_MAXLENGTH = 30;
	const ELEMENT_VALIDATOR_PRESENCEOF_MESSAGE = 'The password is required';
	const ELEMENT_VALIDATOR_STRINGLENGTH_MAX = 30;
	const ELEMENT_VALIDATOR_STRINGLENGTH_MIN = 8;
	const ELEMENT_VALIDATOR_STRINGLENGTH_MAX_MESSAGE = 'This password is too long. Please, select another one.';
	const ELEMENT_VALIDATOR_STRINGLENGTH_MIN_MESSAGE = 'Password is too short. Minimum 8 characters required.';

	public function __construct() {
		parent::__construct(self::ELEMENT_NAME, array(
			'class' => self::ELEMENT_CLASS, 
			'size' => self::ELEMENT_SIZE, 
			'maxlength'=> self::ELEMENT_MAXLENGTH) );
		$this->addValidators(array(
				new \Phalcon\Validation\Validator\PresenceOf(array(
						'message' => self::ELEMENT_VALIDATOR_PRESENCEOF_MESSAGE
				)),
				new \Phalcon\Validation\Validator\StringLength(array(
						'max' => self::ELEMENT_VALIDATOR_STRINGLENGTH_MAX,
						'min' => self::ELEMENT_VALIDATOR_STRINGLENGTH_MIN,
						'messageMaximum' => self::ELEMENT_VALIDATOR_STRINGLENGTH_MAX_MESSAGE,
						'messageMinimum' => self::ELEMENT_VALIDATOR_STRINGLENGTH_MIN_MESSAGE
				)),
		));
	}

}
