<?

namespace Framework;

class Breadcrumbs {

	const TEMPLATE = 'helpers/breadcrumbs';

	protected $_crumbs = array();
	protected $_dispatcher;
	protected $_dispatcherParams = array();

	public function __construct(\Phalcon\Mvc\Dispatcher $dispatcher) {
		$this->_init($dispatcher);
	}

	public function render() {
		$this->_getCrumbs();
		$this->view = new \Phalcon\Mvc\View\Simple();
		$this->view->setViewsDir(TEMPLATE_DIR);
		$this->view->setVar('breadcrumbs', $this->_prepareCrumbs());
		return $this->view->render(self::TEMPLATE, array());
	}

	protected function _init(\Phalcon\Mvc\Dispatcher $dispatcher) {
		$this->_dispatcher = $dispatcher;
		$this->_dispatcherParams = $dispatcher->getParams();
		
	}

	protected function _prepareCrumbs() {
		return $this->_crumbs[$this->_dispatcher->getControllerName()][$this->_dispatcher->getActionName()];
	}

	protected function _getCrumbs() {
		$this->_crumbs = array(
			'profile' => array(
				'index' => array(
					$this->_getHomeCrumb(), 
					array(
						'name' => 'Profile',
					)
				),
				'username' => array(
					$this->_getHomeCrumb(), 
					$this->_getProfileIndexCrumb(),
					array(
						'name' => 'Change username'
					)
				),
				'password' => array(
					$this->_getHomeCrumb(), 
					$this->_getProfileIndexCrumb(),
					array(
						'name' => 'Change password'
					)
				),
				'email' => array(
					$this->_getHomeCrumb(), 
					$this->_getProfileIndexCrumb(),
					array(
						'name' => 'Add new email'
					)
				)
			), // end profile
			'useradmin' => array(
				'index' => array(
					$this->_getHomeCrumb(), 
					array(
						'name' => 'Users',
					)
				)
			), // end useradmin
			'profileadmin' => array(
				'index' => array(
					$this->_getHomeCrumb(), 
					$this->_getUseradminIndexCrumb(),
					array(
						'name' => $this->_getUserNameById()
					)
				),
				'username' => array(
					$this->_getHomeCrumb(), 
					$this->_getUseradminIndexCrumb(),
					$this->_getUseradminUsernameCrumb(),
					array(
						'name' => 'Change username'
					)
				),
				'password' => array(
					$this->_getHomeCrumb(), 
					$this->_getUseradminIndexCrumb(),
					$this->_getUseradminUsernameCrumb(),
					array(
						'name' => 'Change password'
					)
				),
				'email' => array(
					$this->_getHomeCrumb(), 
					$this->_getUseradminIndexCrumb(),
					$this->_getUseradminUsernameCrumb(),
					array(
						'name' => 'Add new email'
					)
				),
				'role' => array(
					$this->_getHomeCrumb(), 
					$this->_getUseradminIndexCrumb(),
					$this->_getUseradminUsernameCrumb(),
					array(
						'name' => 'Change role'
					)
				)
			), // end profileadmin
			'forgotpassword' => array(
				'index' => array(
					$this->_getHomeCrumb(), 
					array(
						'name' => 'Forgot password',
					)
				),
				'sendresetpassword' => array(
					$this->_getHomeCrumb(), 
					array(
						'name' => 'Forgot password',
						'controller' => 'forgotpassword',
						'action' => 'index'
					),
					array(
						'name' => 'Email confirmation'
					)
				)
			),  // end forgotpassword
			'register' => array(
				'index' => array(
					$this->_getHomeCrumb(), 
					array(
						'name' => 'Sign Up',
					)
				),
				'register' => array(
					$this->_getHomeCrumb(), 
					array(
						'name' => 'Sign Up',
						'controller' => 'register',
						'action' => 'index'
					),
					array(
						'name' => 'Email confirmation'
					)
				)
			), // end register
			'confirmemail' => array(
				'index' => array(
					$this->_getHomeCrumb(), 
					array(
						'name' => 'Email verification',
					)
				),
				'initverify' => array(
					$this->_getHomeCrumb(), 
					array(
						'name' => 'Manual email activation',
					)
				)
			)
		);
	}

	protected function _getHomeCrumb() {
		return array(
			'name' => 'Home', 
			'controller' => 'index', 
			'action' => 'index'
		);
	}

	protected function _getProfileIndexCrumb() {
		return array(
			'name' => 'Profile',
			'controller' => 'profile', 
			'action' => 'index'
		);
	}

	protected function _getUseradminIndexCrumb() {
		return array(
			'name' => 'Users',
			'controller' => 'useradmin',
			'action' => 'index'
		);
	}

	protected function _getUseradminUsernameCrumb() {
		return array(
			'name' => $this->_getUserNameById(),
			'controller' => 'profileadmin',
			'action' => 'index/'.array_shift($this->_dispatcher->getParams())
		);
	}

	protected function _getUserNameById() {
		$usersTable = new \Users();
		if (count($this->_dispatcher->getParams()) 
			&& $this->_dispatcher->getParams()) {
			return $usersTable->getUserByID(array_shift($this->_dispatcher->getParams()))->name;
		}
	}

}
