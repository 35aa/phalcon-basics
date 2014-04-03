<?

namespace Framework\Messages;

class Warning  extends AbstractMessage {

	public function __construct($message) {
		$this->_type = self::WARNING;
		$this->_message = $message;
	}
}
