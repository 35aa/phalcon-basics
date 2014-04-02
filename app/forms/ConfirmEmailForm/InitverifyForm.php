<?

namespace ConfirmEmailForm;

class InitverifyForm extends \Framework\Forms\Form {

	public function initialize() {
		$this->setAction('confirmemail/initverify');

		// email
		$this->add(new \Framework\Forms\Element\Email());

		// submit
		$element = new \Framework\Forms\Element\Submit();
		$element->setDefault('Resend');
		$this->add($element);
	}

}
