<?

namespace Framework\Forms\Element;

Class Email extends \Phalcon\Forms\Element\Text {

	const ELEMENT_NAME = 'email';

	public function __construct() {
		parent::__construct(self::ELEMENT_NAME, array('class' => 'form-control', 'size' => '30', 'maxlength'=>70));
		$this->addValidators(array(
				new \Phalcon\Validation\Validator\PresenceOf(array(
						'message' => 'The email is required')),
				new \Phalcon\Validation\Validator\Email(array(
						'message' => 'The email is not valid' ))
		));
	}

}
