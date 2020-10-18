<?php
	namespace Admin\Model\Account\Api;

	class Factory extends \Lenkenith\Framework\Model\Api\Factory implements \Lenkenith\Framework\Model\Api\FactoryInterface {
		private $_account;
		public function __construct(\Admin\Model\Account\Source\Account $accountResourceModel,
			\Admin\Model\Account\Data\Collection $accountCollection){
			$this->_account 		= $accountResourceModel;
			$this->resultCollection = $accountCollection;
			$this->opts 			= array();
			parent::__construct($accountResourceModel);
		}
		public function load($return_collection=true){
			if(is_numeric($return_collection)){
				$this->opts['filter'] = array('account_id' => $return_collection);
			}
			$this->resultCollection = new \Admin\Model\Account\Data\Collection($this->_account->fetch($this->opts));
			$this->opts 			= array();
			return ($return_collection === true) ? $this->resultCollection : $this;
		}
	}
?>