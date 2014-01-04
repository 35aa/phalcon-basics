<?

namespace Mail;

class Registration extends Mail {

	const SUBJECT = 'Реєстрація на ресурсі ....';

	public function send($user, $from, $server) {
		// get setTo email
		$usersEmails = new \UsersEmails();
		$userEmail = $usersEmails->getEmailByEmailID($user->email_id);
		$this->setTo(array($userEmail->email));
		$this->setFrom($from);
		$this->setReplyTo($from);
		$this->setSubject(self::SUBJECT);
		
		$this->addBody($server, $user);
		$this->sendEmail();
	}

	public function addBody($server, $verification) {
		// Passing variables to the views, these will be created as local variables
		$this->view->setVar('server', $server);
		$this->view->setVar('verification', $verification);

		$this->setContent($this->view->render('emailtemplates/registration', array()));
	}
}
