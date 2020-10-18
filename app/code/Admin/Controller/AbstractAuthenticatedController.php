<?php
	namespace Admin\Controller;
	abstract class AbstractAuthenticatedController extends \Lenkenith\Framework\Controller\BaseController{
		private $_accountSession;
		public function __construct(\Admin\Model\Account\Session $accountSession,
			\Lenkenith\Framework\Controller\Context $context){
			$this->_accountSession = $accountSession;
			$this->verifyCredentials();
			parent::__construct($context);
		}
		protected function verifyCredentials(){
			$status = $this->_accountSession->getStatus();
			$session_status = $this->_accountSession->getStatus();
			if($session_status === \Admin\Model\Account\Session::SESS_CODE_ACTIVE){
				$this->_accountSession->regenerate();
			}
			else{
				$this->redirect('admin/account/logout/index/'.strtolower(urlencode($session_status)));
			}
		}
	}

?>