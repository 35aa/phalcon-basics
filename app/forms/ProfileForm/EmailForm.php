<?

namespace ProfileForm;

class EmailForm extends \Phalcon\Forms\Form {

	public function initialize() {
		$this->setAction('profile/email');

		// email
		$this->add(new \Framework\Forms\Element\Email());

		// submit
		$element = new \Framework\Forms\Element\Submit();
		$element->setDefault('Add');
		$this->add($element);
	}

}
