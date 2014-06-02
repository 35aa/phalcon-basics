<?

namespace ProfileForm;

class UsernameForm extends AbstractForm {

	const ACTION = 'username';
	const ELEMENT_SUBMIT_VALUE = 'Update';

	public function initialize() {
		// e.g. profile/username/6540a8e347ed821ddf602b3bf6388359
		$this->setAction($this->_getFormAction().'/'.$this->getValue('id'));

		// hidden user_id
		$element = new \Framework\Forms\Element\HiddenUserID();
		$this->add($element);

		// name
		$username = new \Framework\Forms\Element\Name();
		$username->addValidator(new \Framework\Validation\Validator\UserExists());
		$this->add($username);

		// submit
		$element = new \Framework\Forms\Element\Submit();
		$element->setDefault(self::ELEMENT_SUBMIT_VALUE);
		$this->add($element);
	}

	protected function _getAction() {
		return self::ACTION;
	}
}
