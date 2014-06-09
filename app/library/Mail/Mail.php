<?

namespace Mail;

class Mail {

	protected $to = array();
	protected $cc = array();
	protected $bcc = array();
	protected $from = array();
	protected $replyTo = array();
	protected $returnTo = array();
	protected $subject = '';
	protected $text = '';
	protected $html = '';
	protected $attachments = array();
	protected $emailValidator;
	protected $view;

	public function __construct($options = array()) {
		$this->emailValidator = new \Phalcon\Validation();
		$this->emailValidator->add('email', new \Phalcon\Validation\Validator\Email());
		$this->view = new \Phalcon\Mvc\View\Simple();
		$this->view->setViewsDir(TEMPLATE_DIR);
		if (isset($options['to']) && $options['to']) $this->setTo($options['to']);
		if (isset($options['cc']) && $options['cc']) $this->setTo($options['cc']);
		if (isset($options['bcc']) && $options['bcc']) $this->setTo($options['bcc']);
		if (isset($options['returnTo']) && $options['returnTo']) $this->setReturnTo($options['returnTo']);
		if (isset($options['subject']) && $options['subject']) $this->setSubject($options['subject']);
		if (isset($options['text']) && $options['text']) $this->setText($options['text']);
		if (isset($options['html']) && $options['html']) $this->setHtml($options['html']);
		if (isset($options['content']) && $options['content']) $this->setHtml($options['content']);
		if (isset($options['attachments'])) $this->setHtml($options['content']);
	}

	public function addTo($email) {
		$this->_addEmail('to', $email);
	}

	public function setTo($emails) {
		$this->_setEmails('to', $emails);
	}

	public function addCc($email) {
		$this->_addEmail('cc', $email);
	}

	public function setCc($emails) {
		$this->_setEmails('cc', $emails);
	}

	public function addBcc($email) {
		$this->_addEmail('bcc', $email);
	}

	public function setBcc($emails) {
		$this->_setEmails('bcc', $emails);
	}

	public function setFrom($email) {
		$this->_addEmail('from', $email);
	}

	public function setReturnTo($email) {
		$this->_addEmail('returnTo', $email);
	}

	public function setReplyTo($email) {
		$this->_addEmail('replyTo', $email);
	}

	public function setSubject($subject) {
		$this->subject = $subject;
	}

	public function setContent($content) {
		if (is_object(@\DOMDocument::loadHTML($content))) {
			$this->setHtml(str_replace("\n=", "\n=3D", $content));
			$this->setText(strip_tags($content));
		}
		else {
			$this->setHtml(nl2br(str_replace("\n=", "\n=3D", $content)));
			$this->setText($content);
		}
	}

	public function setText($content) {
		$this->text = $content;
	}

	public function setHtml($content) {
		$this->html = $content;
	}

	public function setAttachments($attachments) {
		if (is_array($options['attachments']) && count($options['attachments'])) {
			$this->attachments = $attachments;
		}
	}

	public function sendEmail() {
		$this->_checkRequiredFields();
		$boundary = md5(microtime(true));
		$boundary_content = md5(microtime(true).time());
		$rn = "\r\n";
		
		//headers
		$headers = array();
		$headers[] = 'From:'.implode(', ', $this->from);
		$headers[] = 'Cc:'.implode(', ', $this->cc);
		$headers[] = 'Bcc:'.implode(', ', $this->bcc);
		$headers[] = 'Reply-To:'.implode(', ', $this->replyTo);
		$headers[] = 'Mime-Version: 1.0';
		$headers[] = 'Content-Type: multipart/related;boundary=' . $boundary;

		// prepare body (including attachments and base64 encoding)
		$body = '--' . $boundary . $rn;
		$body.= 'Content-Type: multipart/alternative;' . $rn;
		$body.= ' boundary="' . $boundary_content . '"' . $rn . $rn;
		//text
		$body.= '--' . $boundary_content . $rn;
		$body.= 'Content-Type: text/plain; charset=ISO-8859-1' . $rn;
		$body .= strip_tags($this->text) . $rn . $rn;
		//html
		$body.= '--' . $boundary_content . $rn;
		$body .= 'Content-Type: text/html; charset=UTF8' . $rn;
		$body .= 'Content-Transfer-Encoding: base64' . $rn . $rn;
		$body .= chunk_split(base64_encode('<div>' . $this->html . '</div>')) . $rn;
		$body .= '--' . $boundary_content . '--' . $rn . $rn;
		
		foreach ($this->attachments as $attachment) {
			if (!$attachment['content']) {error_log(__METHOD__.': missed attachment.'); continue;}
			if (!$attachment['filename']) {error_log(__METHOD__.': missed file name.'); continue;}
			if (!$attachment['filetype']) {error_log(__METHOD__.': missed file type.'); continue;}
			$body.= '--' . $boundary . $rn;
			$body.= 'Content-Type: "' . $attachment['filetype'] . '"; name="' . $attachment['filename'] . '"' . $rn;
			$body .= "Content-Transfer-Encoding: base64" . $rn;
			$body .= 'Content-ID: <' . $attachment['filename'] . '>' . $rn . $rn;
			$body .= chunk_split(base64_encode($attachment['content'])) . $rn . $rn;
		}
		$body .= '--' . $boundary . '--' . $rn;
		// send email
		mail(implode(', ', $this->to), $this->subject, $body, implode($rn, $headers), '-fwebmaster@mydev.org.ua');
	}

	protected function _addEmail($where, $email) {
		if ($this->emailValidator->validate(array('email' => $email))->count() == 0) {
			$this->{$where}[count($this->$where)] = $email;
		}		
	}

	protected function _setEmails($where, $emails) {
		if (is_string($emails)) $emails = array($emails);
		if (is_array($emails)) {
			foreach ($emails as $email) {
				$this->_addEmail($where, $email);
			}
		}
	}

	protected function _checkRequiredFields() {
		if (!count($this->to)) throw new Exception(__METHOD__.': Recipient does not set.');
		if (!count($this->from)) throw new Exception(__METHOD__.': FROM field is missed.');
		if (!$this->subject) throw new Exception(__METHOD__.': SUBJECT does not set.');
	}
}
