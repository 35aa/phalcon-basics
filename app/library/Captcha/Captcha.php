<?php

namespace Captcha;

include_once(APP_PATH.'/lib/recaptcha-php-1.11/recaptchalib.php');

class Captcha {
	protected $publicKey = '';
	protected $privateKey = '';
	protected $error;
	protected $response;

	public function __construct($options) {
		$this->publicKey = $options->publicKye;
		$this->privateKey = $options->privateKey;
	}
	
	public function getHtml() {
		return recaptcha_get_html($this->publicKey, ($this->error ? $this->error : null));
	}

	public function checkAnswer(\Phalcon\Http\Request $request) {
		if ($request->getPost('recaptcha_challenge_field')
				&& $request->getPost('recaptcha_response_field')) {
			$this->response = recaptcha_check_answer(
					$this->privateKey,
					$request->getClientAddress(),
					$request->getPost('recaptcha_challenge_field'),
					$request->getPost('recaptcha_response_field') );
		}
		else {
			$this->error = 'This field could not be empty';
		}

		return $this->isValid();
	}

	public function isValid() {
		if (!$this->response) return false;
		$this->error = $this->response->error;
		return $this->response->is_valid;
	}

	public function getError() {
		return $this->error;
	}
}
