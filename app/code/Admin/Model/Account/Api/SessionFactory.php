<?php
	namespace Admin\Model\Account\Api;

	class SessionFactory extends \Lenkenith\Framework\Model\Api\Factory implements \Lenkenith\Framework\Model\Api\FactoryInterface {

		private $_accountSession;

		public function __construct(\Admin\Model\Account\Source\Session $accountSessionResourceModel,
			\Admin\Model\Account\Data\Collection $accountCollection){
			$this->_accountSession 	= $accountSessionResourceModel;
			$this->resultCollection = $accountCollection;
			$this->opts 			= array();
			parent::__construct($accountSessionResourceModel);
		}

	}
?>