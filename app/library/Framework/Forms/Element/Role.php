<?

namespace Framework\Forms\Element;

Class Role extends \Phalcon\Forms\Element\Select {

	const ELEMENT_NAME = 'role_id';
	const ELEMENT_CLASS = 'form-control';
	const ELEMENT_SIZE = 2;
	const ELEMENT_MAXLENGTH = 70;
	const ELEMENT_LABEL = 'Role';
	const ELEMENT_VALIDATOR_PRESENCEOF_MESSAGE = 'Role is required';
	const ELEMENT_VALIDATOR_INCLUSIONIN_MESSAGE = 'This role not exist. Please try againg';

	public function __construct() {
		parent::__construct(self::ELEMENT_NAME, \UsersRoles::find(), array(
			'class' => self::ELEMENT_CLASS, 
			'size' => self::ELEMENT_SIZE, 
			'maxlength'=> self::ELEMENT_MAXLENGTH, 
			'using' => array('id', 'role')) );
		$this->setLabel(self::ELEMENT_LABEL);
		$this->addValidators(array(
				new \Phalcon\Validation\Validator\PresenceOf(array(
						'message' => self::ELEMENT_VALIDATOR_PRESENCEOF_MESSAGE)),
				new \Phalcon\Validation\Validator\InclusionIn(array(
					'message' => self::ELEMENT_VALIDATOR_INCLUSIONIN_MESSAGE,
					'domain' => array(
						\UsersRoles::ROLE_GUEST_ID, 
						\UsersRoles::ROLE_USER_ID, 
						\UsersRoles::ROLE_ADMINISTRATOR_ID)) )
		));
	}

}
