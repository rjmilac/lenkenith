<?php
	namespace Admin\Model\Account;
	class Auth {
		private $_accountFactory, $_accountSession, $_hashManager;
		const AUTH_CODE_SUCCESS			= 'LOGIN_SUCCESS';
		const AUTH_CODE_INVALID_PASS	= 'INVALID_PASSWORD';
		const AUTH_CODE_INVALID_USER	= 'INVALID_USER';
		public function __construct(\Admin\Model\Account\Api\Factory $accountFactory,
			\Admin\Model\Account\Session $accountSession,
			\Lenkenith\Core\Encryption\HashManager $hashManager){
			$this->_accountFactory 	= $accountFactory;
			$this->_accountSession 	= $accountSession;
			$this->_hashManager 	= $hashManager;
		}
		public function login($user,$password){
			$authentication = $this->authenticate($user,$password);
			if($authentication['success'] === true){
				
			}
			return $authentication;
		}
		public function authenticate($user,$password){
			$account = $this->_accountFactory->create()
				->addAttributeToFilter('username',$user)
				->setPageSize(1)
				->setCurrPage(1)
				->load()->getFirstItem();
			if($account->getId()){
				if($this->_hashManager->verifyPasswordHash($password,$account->getPasswordHash())){
					return array(
						'success' => true, 
						'code' => self::AUTH_CODE_SUCCESS
					);	
				}
				return array(
					'success' => false, 
					'code' => self::AUTH_CODE_INVALID_PASS
				);				
			}		
			return array(
				'success' => false, 
				'code' => self::AUTH_CODE_INVALID_USER
			);
		}
	}
?>