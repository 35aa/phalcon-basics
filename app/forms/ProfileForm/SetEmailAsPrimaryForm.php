<?

namespace ProfileForm;

class SetEmailAsPrimaryForm extends DeleteEmailForm {

	public function initialize() {
		// This form used only for email_id validation and is not visible to user.
		parent::initialize();
		$this->setAction('profile/setprimaryemail');
	}

}

