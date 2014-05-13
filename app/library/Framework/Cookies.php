<?

namespace Framework;

class Cookies extends \Phalcon\Http\Response\Cookies {

	const COOKIE_EXPIRATION_TIME = 1296000; // 15 days

	public function __construct() {
		$this->useEncryption(true);
	}

	public function setCookies($code) {
		$this->set('remember-me', '1', time() + self::COOKIE_EXPIRATION_TIME);
		$this->set('remember-me-code', $code, time() + self::COOKIE_EXPIRATION_TIME);
	}

	public function updateCookies($code) {
		$this->get('remember-me-code')->setValue($code);
		$this->get('remember-me-code')->setExpiration(time() + self::COOKIE_EXPIRATION_TIME);
		$this->get('remember-me-code')->send();
		$this->get('remember-me')->setValue(1);
		$this->get('remember-me')->setExpiration($this->get('remember-me-code')->getExpiration());
		$this->get('remember-me')->send();
	}

	public function removeCookies() {
		$this->get('remember-me')->delete();
		$this->get('remember-me-code')->delete();
	}


}
