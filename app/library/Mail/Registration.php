<?

namespace Mail;

class Registration extends Mail {

	const SUBJECT = 'Реєстрація на ресурсі ....';

	public function send($user, $from, $server) {
		$this->setTo(array($user->email));
		$this->setFrom($from);
		$this->setReplyTo($from);
		$this->setSubject(self::SUBJECT);
		
		$this->addBody($server, $user);
		$this->sendEmail();
	}

	public function addBody($server, $user) {
		// Passing variables to the views, these will be created as local variables
		$this->view->setVar('server', $server);
		$this->view->setVar('user', $user);

		$this->setContent($this->view->render('emailtemplates/registration', array()));
	}
}
