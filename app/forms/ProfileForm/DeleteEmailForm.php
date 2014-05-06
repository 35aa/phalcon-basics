<?

namespace ProfileForm;

class DeleteEmailForm extends \Framework\Forms\Form {

	const FORM_ACTION = 'profile/deleteemail';
	const ELEMENT_ID = 'id';

	public function initialize() {
		$this->setAction(self::FORM_ACTION);

		$element = new \Phalcon\Forms\Element\Text(self::ELEMENT_ID);
		$element->addValidators(array(
				new \Phalcon\Validation\Validator\PresenceOf(),
				new \Phalcon\Validation\Validator\Regex(array(
						'pattern' => '/([0-9]+)/' )) ));
		$this->add($element);

	}

}
