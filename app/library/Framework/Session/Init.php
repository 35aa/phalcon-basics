<?

namespace Framework\Session;

class Init {

	public static function session() {
		$cookies = new \Framework\Cookies();
		$session = new \Phalcon\Session\Adapter\Files();
		$session->start();
		if (!$session->get('auth')) {
			if ($cookies->has('remember-me') && $cookies->get('remember-me')->getValue()) {
				$userToRememberMeTable = new \UserToRememberMe();
				$rememberMe = $userToRememberMeTable->getCodeByCode($cookies->get('remember-me-code')->getValue());
				if ($rememberMe) {
					$usersTable = new \Users();
					$session->set('auth', new \Auth($usersTable->getUserById($rememberMe->user_id)));
				}
			}
			if (!$session->get('auth')) {
				$cookies->removeCookies();
			}
		}
		elseif ($session->get('auth')->isExpired()) {
			$session->destroy();
			$session->set('auth', new \Auth());
		}
		if (!$session->get('auth')) {
			$session->set('auth', new \Auth());
		}

		$session->get('auth')->resetTimeout();
		return $session;
	}

}
