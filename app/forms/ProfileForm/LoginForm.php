<?

namespace ProfileForm;

class LoginForm extends \Phalcon\Forms\Form {
	public function initialize() {
		$this->setAction('profile/login');

		$element = new \Phalcon\Forms\Element\Text('name', array('size' => '30', 'maxlength'=>70));
		$element->addValidators(array(
				new \Phalcon\Validation\Validator\PresenceOf(array(
						'message' => 'The username is required')),
				new \Phalcon\Validation\Validator\StringLength(array(
						'max' => 70,
						'min' => 3,
						'messageMaximum' => 'This name is too long. Please, select another one.',
						'messageMinimum' => 'This name is too short. Please, select another one.' )) ));
		$this->add($element);

		$element = new \Phalcon\Forms\Element\Submit('submit', array('value'=>'Change'));
		$this->add($element);

	}
}
