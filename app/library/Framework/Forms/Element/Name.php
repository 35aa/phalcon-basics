<?

namespace Framework\Forms\Element;

Class Name extends \Phalcon\Forms\Element\Text {

	const ELEMENT_NAME = 'name';
	const ELEMENT_CLASS = 'form-control';
	const ELEMENT_SIZE = 30;
	const ELEMENT_MAXLENGTH = 70;
	const ELEMENT_LABEL = 'Name';
	const ELEMENT_VALIDATOR_PRESENCEOF_MESSAGE = 'The username is required';
	const ELEMENT_VALIDATOR_STRINGLENGTH_MAX = 70;
	const ELEMENT_VALIDATOR_STRINGLENGTH_MIN = 3;
	const ELEMENT_VALIDATOR_STRINGLENGTH_MAX_MESSAGE = 'This name is too long. Please, select another one.';
	const ELEMENT_VALIDATOR_STRINGLENGTH_MIN_MESSAGE = 'This name is too short. Please, select another one.';

	public function __construct() {
		parent::__construct(self::ELEMENT_NAME, array(
			'class' => self::ELEMENT_CLASS, 
			'size' => self::ELEMENT_SIZE, 
			'maxlength'=> self::ELEMENT_MAXLENGTH) );
		$this->setLabel(self::ELEMENT_LABEL);
		$this->addValidators(array(
				new \Phalcon\Validation\Validator\PresenceOf(array(
						'message' => self::ELEMENT_VALIDATOR_PRESENCEOF_MESSAGE)),
				new \Phalcon\Validation\Validator\StringLength(array(
						'max' => self::ELEMENT_VALIDATOR_STRINGLENGTH_MAX,
						'min' => self::ELEMENT_VALIDATOR_STRINGLENGTH_MIN,
						'messageMaximum' => self::ELEMENT_VALIDATOR_STRINGLENGTH_MAX_MESSAGE,
						'messageMinimum' => self::ELEMENT_VALIDATOR_STRINGLENGTH_MIN_MESSAGE ))
		));
	}

}
