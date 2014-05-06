<?

namespace UserForm;

class VerifyEmailForm extends \Framework\Forms\Form {

	const ELEMENT_ID = 'id';
	const ELEMENT_ID_VALIDATOR = '/^[a-fA-F\d]{32,32}$/';
	const ELEMENT_CODE_ID = 'code';
	const ELEMENT_CODE_SIZE = '30';
	const ELEMENT_CODE_MAXLENGTH = 70;
	const ELEMENT_CODE_VALIDATOR_MAX = 60;
	const ELEMENT_CODE_VALIDATOR_MIN = 59;

	public function initialize() {
		$element = new \Phalcon\Forms\Element\Text(self::ELEMENT_ID);
		$element->addValidators(array(
				new \Phalcon\Validation\Validator\PresenceOf(),
				new \Phalcon\Validation\Validator\Regex(array(
						'pattern' => self::ELEMENT_ID_VALIDATOR )) ));
		$this->add($element);

		$element = new \Phalcon\Forms\Element\Text(self::ELEMENT_CODE_ID, array('size' => self::ELEMENT_CODE_SIZE, 'maxlength' => self::ELEMENT_CODE_MAXLENGTH));
		$element->addValidators(array(
				new \Phalcon\Validation\Validator\PresenceOf(),
				new \Phalcon\Validation\Validator\StringLength(array(
						'max' => self::ELEMENT_CODE_VALIDATOR_MAX,
						'min' => self::ELEMENT_CODE_VALIDATOR_MIN )) ));
		$this->add($element);
	}

}
