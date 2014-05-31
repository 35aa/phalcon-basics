<?

namespace ProfileForm;

abstract class AbstractForm extends \Framework\Forms\Form {

	protected function _getFormAction() {
		return \Phalcon\DI::getDefault()->get('dispatcher')->getControllerName().'/'.$this->_getAction();
	}

	abstract protected function _getAction();

}
