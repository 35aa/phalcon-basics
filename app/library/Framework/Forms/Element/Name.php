<?

namespace Framework\Forms\Element;

Class Name extends \Phalcon\Forms\Element\Text {

	const ELEMENT_NAME = 'name';
	const ELEMENT_LABEL = 'Name';

	public function __construct() {
		parent::__construct(self::ELEMENT_NAME, array('class' => 'form-control', 'size' => '30', 'maxlength'=>70));
		$this->setLabel(self::ELEMENT_LABEL);
		$this->addValidators(array(
				new \Phalcon\Validation\Validator\PresenceOf(array(
						'message' => 'The username is required')),
				new \Phalcon\Validation\Validator\StringLength(array(
						'max' => 70,
						'min' => 3,
						'messageMaximum' => 'This name is too long. Please, select another one.',
						'messageMinimum' => 'This name is too short. Please, select another one.' ))
		));
	}

}
