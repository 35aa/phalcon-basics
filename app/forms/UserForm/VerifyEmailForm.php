<?

namespace UserForm;

class VerifyEmailForm extends \Framework\Forms\Form {

	public function initialize() {
		$element = new \Phalcon\Forms\Element\Text('id');
		$element->addValidators(array(
				new \Phalcon\Validation\Validator\PresenceOf(),
				new \Phalcon\Validation\Validator\Regex(array(
						'pattern' => '/^[a-fA-F\d]{32,32}$/' )) ));
		$this->add($element);

		$element = new \Phalcon\Forms\Element\Text('code', array('size' => '30', 'maxlength'=>70));
		$element->addValidators(array(
				new \Phalcon\Validation\Validator\PresenceOf(),
				new \Phalcon\Validation\Validator\StringLength(array(
						'max' => 60,
						'min' => 59 )) ));
		$this->add($element);
	}

}
