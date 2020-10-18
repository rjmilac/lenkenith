<?php
	namespace Admin\Model\Account\Api;
	class SetupValidator {
		private $_accountFactory, $_tableChecker;
		const REQUIRED_TABLES = array('admin_accounts','admin_account_status','admin_account_roles','admin_account_activity','admin_account_session','admin_account_mailbox','admin_system_messages','admin_message_levels');	
		public function __construct(
			\Admin\Model\Account\Api\Factory $accountFactory,
			\Lenkenith\Framework\Model\Api\TableChecker $tableChecker
		){
			$this->_accountFactory = $accountFactory;
			$this->_tableChecker = $tableChecker;
		}
		private function hasMasterAccount($master_role_id = 1){
			$collection = $this->_accountFactory->create()
				->addAttributeToSelect('id')
				->addAttributeToFilter('role_id', $master_role_id)
				->setCurrPage(1)
				->setPageSize(1)
				->load();
			return ($collection->count() < 1) ? false : true;
		}
		public function isSetupNeeded($master_role_id = 1){
			$table_installation = $this->_tableChecker->checkTables(self::REQUIRED_TABLES);
			return (!$this->hasMasterAccount($master_role_id));
		}
	}
?>