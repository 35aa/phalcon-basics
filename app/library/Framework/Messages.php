<?

namespace Framework;

class Messages extends Messages\AbstractMessage {

	protected $_messages = array();

	public function __construct() {
		$this->_messages = array(
				self::ERROR => array(),
				self::WARNING => array(),
				self::NOTIFICATION => array(),
				self::SUCCESS => array() );
	}

	public function add($type, $message) {
		$class = "Framework\Messages\\".$type;
		$this->_messages[$type][] = new $class($message);
	}

	public function addError($message) {
		$this->add(self::ERROR, $message);
	}

	public function addSuccess($message) {
		$this->add(self::SUCCESS, $message);
	}

	public function addWarning($message) {
		$this->add(self::WARNING, $message);
	}

	public function addNotification($message) {
		$this->add(self::NOTIFICATION, $message);
	}

	public function getGroup($type) {
		return $this->_messages[$type];
	}

	public function getErrors() {
		return $this->getGroup(self::ERROR);
	}

	public function getSuccess() {
		return $this->getGroup(self::SUCCESS);
	}

	public function getWarnings() {
		return $this->getGroup(self::WARNING);
	}

	public function getNotifications() {
		return $this->getGroup(self::NOTIFICATION);
	}

}
