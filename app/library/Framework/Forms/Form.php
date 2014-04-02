<?

namespace Framework\Forms;

class Form extends \Phalcon\Forms\Form {

	public function getMessages($byItemName = false) {
		$messages = array();
		foreach (parent::getMessages(true) as $key => $message) {
			$messages[$key] = $message[0]->getMessage();
		}
		return $messages;
	}

}