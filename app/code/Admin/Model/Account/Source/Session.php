<?php
	namespace Admin\Model\Account\Source;

	class Session extends \Lenkenith\Framework\Model\ResourceModel implements \Lenkenith\Framework\Model\Api\ResourceModelInterface {

		public function __construct(\Lenkenith\Framework\Model\Context $context){
			$this->setBasePrimaryKey('id');
			$this->setBaseTable( array('admin_account_session' => 'a') );
			parent::__construct($context);
		}

	}
?>