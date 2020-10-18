<?php

	namespace Admin\Controller\Account;

	class Login extends \Lenkenith\Framework\Controller\BaseController {

		private $_setupValidator, $_accountSession;

		public function __construct(\Lenkenith\Framework\Controller\Context $context,
			\Admin\Model\Account\Api\SetupValidator $setupValidator,
			\Admin\Model\Account\Session $accountSession){
			$this->_setupValidator = $setupValidator;
			$this->_accountSession = $accountSession;
			parent::__construct($context);
		}

		public function index(){
			if($this->_setupValidator->isSetupNeeded(1)){
				$this->loadInitialSetupWizard();
			}
			else{
				$session_status = $this->_accountSession->getStatus();
				// session is present and in active state with authenticated token, redirect to dashboard...
				if($session_status === \Admin\Model\Account\Session::SESS_CODE_ACTIVE){
					$this->_accountSession->regenerate();
					$this->redirect('admin/dashboard/index');
				}
				// session is present but it is expired or invalid, so let's clear everything up...
				else if($session_status != \Admin\Model\Account\Session::SESS_CODE_OFFLINE){
					$this->redirect('admin/account/logout/index/'.strtolower(urlencode($session_status)));
				}
				// no session is present or active
				else{
					$this->getLayout()->loadTemplateFile('Admin::Account/login.phtml','Admin\Block\Account\Login');
				}
			}
			return true;
		}

		private function loadInitialSetupWizard(){
			$this->getLayout()->loadTemplateFile('Admin::setup.phtml','Admin\Block\Setup');
		}

	}

?>