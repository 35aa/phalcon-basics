<?

namespace ProfileForm;

class EmailForm extends AbstractForm {

	const ACTION = 'email';
	const ELEMENT_SUBMIT_VALUE = 'Add';

	public function initialize() {
		$this->setAction($this->_getFormAction().'/'.$this->getValue('id'));

		// hidden user_id
		$element = new \Framework\Forms\Element\HiddenUserID();
		$this->add($element);

		// email
		$email = new \Framework\Forms\Element\Email();
		$email->addValidator(new \Framework\Validation\Validator\EmailExists());
		$this->add($email);

		// submit
		$element = new \Framework\Forms\Element\Submit();
		$element->setDefault(self::ELEMENT_SUBMIT_VALUE);
		$this->add($element);
	}

	protected function _getAction() {
		return self::ACTION;
	}
}
