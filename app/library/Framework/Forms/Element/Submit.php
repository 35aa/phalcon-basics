<?

namespace Framework\Forms\Element;

Class Submit extends \Phalcon\Forms\Element\Submit {

	const ELEMENT_NAME = 'submit';

	public function __construct() {
		parent::__construct(self::ELEMENT_NAME, array('class' => 'btn btn-default', 'value'=>'Submit'));
	}

}
