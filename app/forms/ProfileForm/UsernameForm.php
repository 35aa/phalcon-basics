<?

namespace ProfileForm;

class UsernameForm extends \Phalcon\Forms\Form {

	public function initialize() {
		$this->setAction('profile/username');

		// name
		$this->add(new \Framework\Forms\Element\Name());

		// submit
		$element = new \Framework\Forms\Element\Submit();
		$element->setDefault('Change');
		$this->add($element);
	}

}
