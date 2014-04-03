<?

namespace Framework\Messages;

class Success  extends AbstractMessage {

	public function __construct($message) {
		$this->_type = self::SUCCESS;
		$this->_message = $message;
	}
}
