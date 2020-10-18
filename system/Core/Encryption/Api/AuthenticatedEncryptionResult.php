<?php
	
	namespace Lenkenith\Core\Encryption\Api;

	use \Lenkenith\Core\Encryption\Api\AuthenticatedEncryptionDataInterface as AuthenticatedEncryptionDataInterface;

	class AuthenticatedEncryptionResult implements AuthenticatedEncryptionDataInterface{

		private $iv, $auth, $hash, $raw, $ae, $encryption_key;

		public function __construct(Array $ae){		
			$this->ae 	= $ae;
			$this->setValues();
		}

		private function setValues(){			
			$this->iv 	= $this->ae['iv'];
			$this->auth = $this->ae['auth'];
			$this->hash = $this->ae['hash'];
			$this->encryption_key = $this->ae['encryption_key'];
			return true;
		}

		public function formatPreDecryption(){	
			foreach ($this->ae as $key => $value) {
				if (ctype_xdigit($value)) {
					$this->ae[$key] = hex2bin($value);
				}
			}
			$this->setValues();
			return true;
		}

		public function getIv(){
			return $this->iv;
		}

		public function getAuth(){
			return $this->auth;
		}

		public function getHash(){
			return $this->hash;
		}

		public function getEncryptionKey(){
			return $this->encryption_key;
		}

	}

?>