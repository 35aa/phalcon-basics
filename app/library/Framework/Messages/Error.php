<?

namespace Framework\Messages;

class Error extends AbstractMessage {

	public function __construct($message) {
		$this->_type = self::ERROR;
		$this->_message = $message;
	}
}
