<?

namespace Mail;

class ResetPassword extends Registration {

	const SUBJECT = 'Відновлення паролю на ресурсі ....';
	const TEMPLATE = 'emailtemplates/ResetPassword';

	public function addBody($server, $primaryEmail) {
		// Passing variables to the views, these will be created as local variables
		$this->view->setVar('server', $server);
		$this->view->setVar('primaryEmail', $primaryEmail);

		$this->setContent($this->view->render(self::TEMPLATE, array()));
	}
}
