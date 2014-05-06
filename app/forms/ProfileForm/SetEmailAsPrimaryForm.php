<?

namespace ProfileForm;

class SetEmailAsPrimaryForm extends DeleteEmailForm {

	const FORM_ACTION = 'profile/setprimaryemail';

	public function initialize() {
		// This form used only for email_id validation and is not visible to user.
		parent::initialize();
		$this->setAction(self::FORM_ACTION);
	}

}

