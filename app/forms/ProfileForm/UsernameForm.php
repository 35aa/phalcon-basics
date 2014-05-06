<?

namespace ProfileForm;

class UsernameForm extends \Framework\Forms\Form {

	const FORM_ACTION = 'profile/username';
	const ELEMENT_SUBMIT_VALUE = 'Change';

	public function initialize() {
		$this->setAction(self::FORM_ACTION);

		// name
		$username = new \Framework\Forms\Element\Name();
		$username->addValidator(new \Framework\Validation\Validator\UserExists());
		$this->add($username);

		// submit
		$element = new \Framework\Forms\Element\Submit();
		$element->setDefault(self::ELEMENT_SUBMIT_VALUE);
		$this->add($element);
	}

}
