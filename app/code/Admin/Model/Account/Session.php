<?php
	namespace Admin\Model\Account;	
	class Session {
		private $_hashManager, $_session, $_accountModel, $_sessKeys, $accountFactory;
		const SESS_KEY_USER 		= 'adm_ses_euid';
		const SESS_KEY_ACCESS_TOKEN = 'adm_ses_token';
		const SESS_KEY_IV 			= 'adm_ses_iv'; 
		const SESS_KEY_AUTH_KEY		= 'adm_ses_ak';
		const SESS_KEY_ENC_KEY		= 'adm_ses_ek';
		const SESS_KEY_TIMESTAMP	= 'adm_ses_timestamp';
		const SESS_KEY_LAST_ACTIVITY= 'adm_ses_lastactivity';
		const SESS_CODE_ACTIVE		= 'SESS_ACTIVE';
		const SESS_CODE_EXPIRED 	= 'SESS_EXPIRED';
		const SESS_CODE_MISMATCH 	= 'SESS_TOKEN_MISMATCH';
		const SESS_CODE_OFFLINE 	= 'SESS_OFFLINE';
		public function __construct(\Lenkenith\Core\Data\Session $session,
			\Lenkenith\Core\Encryption\HashManager $hashManager,
			\Lenkenith\Core\Encryption\Crypt $crypt,
			\Admin\Model\Account\Api\Factory $accountFactory, 
			\Admin\Model\Account\Api\SessionFactory $accountSessionFactory){
			$this->_session 				= $session;
			$this->_hashManager 			= $hashManager;
			$this->_crypt 					= $crypt;
			$this->accountFactory 			= $accountFactory;
			$this->accountSessionFactory 	= $accountSessionFactory;
		}
		private function decryptUsername($encoded_username, $iv = null, $auth_key = null, $enc_key = null){
			$encrypted_data = ($encoded_username instanceof \Lenkenith\Core\Encryption\Api\AuthenticatedEncryptionDataInterface) ? 
				$encoded_username : 
				new \Lenkenith\Core\Encryption\Api\AuthenticatedEncryptionResult(
						array(
							'hash' => $encoded_username, 
							'iv' => $iv, 
							'auth' => $auth_key,
							'encryption_key' => $enc_key
						)
					);
			return $this->_crypt->decryptData($encrypted_data);
		}
		public function isLoggedIn($return_status=false){
			if($this->_session->has(self::SESS_KEY_USER) 
				&& $this->_session->has(self::SESS_KEY_ACCESS_TOKEN)
				&& $this->_session->has(self::SESS_KEY_LAST_ACTIVITY)
				&& $this->_session->has(self::SESS_KEY_IV)
				&& $this->_session->has(self::SESS_KEY_AUTH_KEY) 
				&& $this->_session->has(self::SESS_KEY_ENC_KEY) 
			){
				if($this->_session->isExpired($this->_session->get(self::SESS_KEY_LAST_ACTIVITY))){
					return ($return_status) ? self::SESS_CODE_EXPIRED : false;
				}
				$active_tokens = $this->getActiveAccessTokens();
				if(in_array($this->_session->get(self::SESS_KEY_ACCESS_TOKEN), $active_tokens)){
					return ($return_status) ? self::SESS_CODE_ACTIVE : true;
				}
				return ($return_status) ? self::SESS_CODE_MISMATCH : false;
			}
			$this->clearSession();
			return ($return_status) ? self::SESS_CODE_OFFLINE : false;
		}
		public function getStatus(){
			return $this->isLoggedIn(true);
		}
		public function startSession(Array $account_session_data){
			$a = $this->_session->store(self::SESS_KEY_USER, $account_session_data['user']);
			$b = $this->_session->store(self::SESS_KEY_ACCESS_TOKEN, $account_session_data['access_token']);
			$c = $this->_session->store(self::SESS_KEY_IV, $account_session_data['iv']);
			$d = $this->_session->store(self::SESS_KEY_AUTH_KEY, $account_session_data['auth_key']);
			$e = $this->_session->store(self::SESS_KEY_ENC_KEY, $account_session_data['enc_key']);
			$f = $this->_session->store(self::SESS_KEY_TIMESTAMP, $account_session_data['timestamp']);
			$g = $this->_session->store(self::SESS_KEY_LAST_ACTIVITY, $account_session_data['last_activity']);
			return ( $a && $b && $c && $d && $e );
		}
		public function updateLastActivityTimestamp($timestamp = null){
			$timestamp = ($timestamp == null) ? time() : $timestamp;
			if(function_exists('session_regenerate_id')){ session_regenerate_id(); }
			$timestamp = ($timestamp instanceof \DateTime) ? strtotime($timestamp) : $timestamp;
			return $this->_session->store(self::SESS_KEY_LAST_ACTIVITY, $timestamp);
		}
		public function clearSession($clearEntireSession = false){
			$a = $this->_session->remove(self::SESS_KEY_USER); 
			$b = $this->_session->remove(self::SESS_KEY_ACCESS_TOKEN);
			$c = $this->_session->remove(self::SESS_KEY_IV);
			$d = $this->_session->remove(self::SESS_KEY_AUTH_KEY);
			$e = $this->_session->remove(self::SESS_KEY_ENC_KEY);
			$f = $this->_session->remove(self::SESS_KEY_TIMESTAMP);
			$g = $this->_session->remove(self::SESS_KEY_LAST_ACTIVITY);
			if($clearEntireSession){
				$this->_session->shutdown();
			}
			return ( $a && $b && $c && $d && $e );
		}
		private function getAccountSessions($account_id){
			if($account_id == null || !is_numeric($account_id)){ return false; }
			return $this->accountSessionFactory->create()
				->addAttributeToFilter('account_id',$account_id)
				->addAttributeToFilter('status','watchable')
				->addAttributeToSort('id','desc')
				->load()
				->getItems();
		}
		private function getActiveAccessTokens(){
			$active_tokens 	= array();
			$accountId		= $this->_getAccount()->getId();
			$sessions 		= $this->getAccountSessions($accountId);
			if(!empty($sessions) && is_array($sessions)){
				foreach ($sessions as $s) {
					if(!$this->_session->isExpired(strtotime($s->getDateUpdated()))){
						$active_tokens[] = $s->getAccessToken();
					}
				}
			}
			return $active_tokens;
		}
		public function getAccount(){
			if($this->isLoggedIn()){
				return $this->_getAccount();
			}			
			return new \Lenkenith\Framework\Api\DataObject(array());
		}
		private function _getAccount($account_id = null){			
			$accountFactory = $this->accountFactory->create();
			if(is_numeric($account_id) && !is_null($account_id)){
				$accountFactory->addAttributeToFilter('id', $account_id);
			}
			else{
				$accountFactory->addAttributeToFilter(
					'username', 
					$this->decryptUsername(
						$this->_session->get(self::SESS_KEY_USER),
						$this->_session->get(self::SESS_KEY_IV),
						$this->_session->get(self::SESS_KEY_AUTH_KEY)
					)
				);
			}
			return $accountFactory->setPageSize(1)->load()->getFirstItem();
		}
	}
?>