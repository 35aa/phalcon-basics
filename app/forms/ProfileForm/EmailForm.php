<?

namespace ProfileForm;

class EmailForm extends \Framework\Forms\Form {

	public function initialize() {
		$this->setAction('profile/email');

		// email
		$email = new \Framework\Forms\Element\Email();
		$email->addValidator(new \Framework\Validation\Validator\EmailExists());
		$this->add($email);

		// submit
		$element = new \Framework\Forms\Element\Submit();
		$element->setDefault('Add');
		$this->add($element);
	}

}
