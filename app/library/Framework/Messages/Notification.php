<?

namespace Framework\Messages;

class Notification  extends AbstractMessage {

	public function __construct($message) {
		$this->_type = self::NOTIFICATION;
		$this->_message = $message;
	}
}
