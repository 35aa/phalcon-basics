<?

namespace ProfileForm;

class UsernameForm extends \Framework\Forms\Form {

	public function initialize() {
		$this->setAction('profile/username');

		// name
		$username = new \Framework\Forms\Element\Name();
		$username->addValidator(new \Framework\Validation\Validator\UserExists());
		$this->add($username);

		// submit
		$element = new \Framework\Forms\Element\Submit();
		$element->setDefault('Change');
		$this->add($element);
	}

}
