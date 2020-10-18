<?php
	namespace Admin\Model\Account;
	class Manager {
		private $_accountFactory, $_accountSessionFactory, $_account;
		private $_account = null;
		public function __construct(\Admin\Model\Account\Api\Factory $accountFactory, 
			\Admin\Model\Account\Api\SessionFactory $accountSessionFactory){
			$this->_accountFactory 			= $accountFactory->create();
			$this->_accountSessionFactory 	= $accountSessionFactory->create();
		}
		public function loadAccountById($account_id){
			$this->_account = $this->_accountFactory->load($account_id)->getFirstItem();
			return $this->_account;
		}
		public function getAccount($account_id = null){
			if($account_id != null && is_numeric($account_id)){
				return $this->loadAccountById($account_id);
			}
			return $this->_account;
		}
	}
?>