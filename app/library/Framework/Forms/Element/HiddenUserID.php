<?

namespace Framework\Forms\Element;

Class HiddenUserID extends \Phalcon\Forms\Element\Hidden {

	const ELEMENT_NAME = 'id';
	const ELEMENT_SIZE = 32;
	const ELEMENT_MAXLENGTH = 32;
	// regexp for empty string or md5 hash
	const ELEMENT_VALIDATOR_REGEX_PATTERN = '/(^$)|(^[a-fA-F\d]{32,32}$)/';

	public function __construct() {
		parent::__construct(self::ELEMENT_NAME, array(
			'size' => self::ELEMENT_SIZE, 
			'maxlength'=> self::ELEMENT_MAXLENGTH) );
		$this->addValidators(array(
				new \Phalcon\Validation\Validator\Regex(array(
						'pattern' => self::ELEMENT_VALIDATOR_REGEX_PATTERN ))
		));
	}

}
