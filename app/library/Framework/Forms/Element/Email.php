<?

namespace Framework\Forms\Element;

Class Email extends \Phalcon\Forms\Element\Text {

	const ELEMENT_NAME = 'email';
	const ELEMENT_CLASS = 'form-control';
	const ELEMENT_SIZE = 30;
	const ELEMENT_MAXLENGTH = 70;
	const ELEMENT_LABEL = 'Email';
	const ELEMENT_VALIDATOR_PRESENCEOF_MESSAGE = 'The email is required';
	const ELEMENT_VALIDATOR_EMAIL_MESSAGE = 'The email is not valid';

	public function __construct() {
		parent::__construct(self::ELEMENT_NAME, array(
			'class' => self::ELEMENT_CLASS, 
			'size' => self::ELEMENT_SIZE, 
			'maxlength'=> self::ELEMENT_MAXLENGTH) );
		$this->setLabel(self::ELEMENT_LABEL);
		$this->addValidators(array(
				new \Phalcon\Validation\Validator\PresenceOf(array(
						'message' => self::ELEMENT_VALIDATOR_PRESENCEOF_MESSAGE)),
				new \Phalcon\Validation\Validator\Email(array(
						'message' => self::ELEMENT_VALIDATOR_EMAIL_MESSAGE ))
		));
	}

}
