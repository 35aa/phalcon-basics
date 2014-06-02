<?

namespace ProfileForm;

class DeleteEmailForm extends AbstractForm {

	const ACTION = 'deleteemail';
	const ELEMENT_ID = 'id';

	public function initialize() {
		$this->setAction($this->_getFormAction());

		$element = new \Phalcon\Forms\Element\Text(self::ELEMENT_ID);
		$element->addValidators(array(
				new \Phalcon\Validation\Validator\PresenceOf(),
				new \Phalcon\Validation\Validator\Regex(array(
						'pattern' => '/([0-9]+)/' )) ));
		$this->add($element);

	}

	protected function _getAction() {
		return self::ACTION;
	}
}
