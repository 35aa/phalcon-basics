<?

namespace ConfirmEmailForm;

class InitverifyForm extends \Framework\Forms\Form {

	const FORM_ACTION = 'confirmemail/initverify';
	const ELEMENT_SUBMIT_VALUE = 'Resend';

	public function initialize() {
		$this->setAction(self::FORM_ACTION);

		// email
		$this->add(new \Framework\Forms\Element\Email());

		// submit
		$element = new \Framework\Forms\Element\Submit();
		$element->setDefault(self::ELEMENT_SUBMIT_VALUE);
		$this->add($element);
	}

}
