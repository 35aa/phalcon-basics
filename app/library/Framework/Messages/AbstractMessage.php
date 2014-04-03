<?

namespace Framework\Messages;

class AbstractMessage {

	const ERROR = 'Error';
	const SUCCESS = 'Success';
	const WARNING = 'Warning';
	const NOTIFICATION = 'Notification';

	protected $_type;
	protected $_message;

	public function getMessage() {
		return $this->_message;
	}

	public function getType() {
		return $this->_type;
	}
}
