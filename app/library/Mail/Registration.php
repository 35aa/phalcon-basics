<?

namespace Mail;

class Registration extends Mail {

	const SUBJECT = 'Реєстрація на ресурсі ....';

	public function send($user, $from, $server) {
		// get setTo email
		$this->setTo(array($user->getPrimaryEmail()->email));
		$this->setFrom($from);
		$this->setReplyTo($from);
		$this->setSubject(self::SUBJECT);

		$this->addBody($server, $user->getPrimaryEmail());
		$this->sendEmail();
	}

	public function addBody($server, $primaryEmail) {
		// Passing variables to the views, these will be created as local variables
		$this->view->setVar('server', $server);
		$primaryEmail->createVerificationObject();
		$this->view->setVar('primaryEmail', $primaryEmail);

		$this->setContent($this->view->render('emailtemplates/registration', array()));
	}
}
