<?

namespace ConfirmEmailForm;

class ResetPassword extends \Framework\Forms\Form {
	public function initialize() {
		$this->setAction('confirmemail/resendresetpassword');

		$element = new \Phalcon\Forms\Element\Text('email', array('size' => '30', 'maxlength'=>70));
		$element->addValidators(array(
				new \Phalcon\Validation\Validator\PresenceOf(array(
						'message' => 'The email is required')),
				new \Phalcon\Validation\Validator\Email(array(
						'message' => 'The email is not valid' ))
		));
		$this->add($element);

		$element = new \Phalcon\Forms\Element\Submit('submit', array('value'=>'Resend'));
		$this->add($element);
	}
}
