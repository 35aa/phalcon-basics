<?

namespace Framework\Forms\Element;

Class Submit extends \Phalcon\Forms\Element\Submit {

	const ELEMENT_NAME = 'submit';
	const ELEMENT_CLASS = 'btn btn-default';
	const ELEMENT_VALUE = 'Submit';

	public function __construct() {
		parent::__construct(self::ELEMENT_NAME, array(
			'class' => self::ELEMENT_CLASS, 
			'value'=> self::ELEMENT_VALUE) );
	}

}
