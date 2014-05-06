<?

namespace ProfileForm;

class EmailForm extends \Framework\Forms\Form {

	const FORM_ACTION = 'profile/email';
	const ELEMENT_SUBMIT_VALUE = 'Add';

	public function initialize() {
		$this->setAction(self::FORM_ACTION);

		// email
		$email = new \Framework\Forms\Element\Email();
		$email->addValidator(new \Framework\Validation\Validator\EmailExists());
		$this->add($email);

		// submit
		$element = new \Framework\Forms\Element\Submit();
		$element->setDefault(self::ELEMENT_SUBMIT_VALUE);
		$this->add($element);
	}

}
