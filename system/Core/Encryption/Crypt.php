<?php
	
	namespace Lenkenith\Core\Encryption;

	class Crypt{

		private $security_config, $encryption_key, $cache;

		const DEFAULT_CIPHER_MODE = 'AES-256-CBC';

		public function __construct(\Lenkenith\Base\Configuration $configuration){
			$this->cache 			= new \Lenkenith\Core\Encryption\Data\Cache(array());
			$this->security_config 	= $configuration->getConfigs()->security;
			$this->encryption_key 	= (!empty($this->security_config->encryption_key)) ? $this->security_config->encryption_key : $this->generateEncryptionKey();
		}

		private function pkcs7Pad($data, $size){
			$length = $size - strlen($data) % $size;
   			return $data . str_repeat(chr($length), $length);
		}

		private function pkcs7Unpad($data){
   			return substr($data, 0, -ord($data[strlen($data) - 1]));
		}

		private function generateRandomBytes($size){
			$generated = openssl_random_pseudo_bytes($size, $strong);
			if($this->cache->has($generated)){
				return $this->generateRandomBytes($size);
			}
			$this->cache->store($generated, true);
			return $generated;
		}

		private function generateIniVector(){
			return $this->generateRandomBytes(16);
		}

		private function generateEncryptionKey(){
			return $this->generateRandomBytes(32);
		}

		public function resetEncryptionKey(){
			$this->encryption_key = (!empty($this->security_config->encryption_key)) ? $this->security_config->encryption_key : $this->generateEncryptionKey();
			return true;
		}

		public function setEncryptionKey($key){
			$this->encryption_key 	= $key;
			return true;
		}

		private function formatKey($key){
			return substr($key, 0, 32);
		}

		public function encryptData($data){
			$iv 		= $this->generateIniVector();
			$enc_data 	= openssl_encrypt(
			    $this->pkcs7Pad($data, 16), // padded data
			    self::DEFAULT_CIPHER_MODE,  // cipher and mode
			    $this->formatKey($this->encryption_key),      // secret key
			    0,                    		// options (not used)
			    $iv  						// initialisation vector
			);
			$encrypted  = $enc_data;
			$auth_key 	= $this->generateRandomBytes(32);
			$auth 		= hash_hmac('sha256', $enc_data, $auth_key, true);
			$encrypted 	= $auth . $enc_data;
			$result = array(
					'hash'	=> $encrypted,
					'iv'	=> $iv,
					'auth'	=> ((isset($auth_key)) ? $auth_key : false),
					'encryption_key' => $this->formatKey($this->encryption_key)
				);
			return new \Lenkenith\Core\Encryption\Api\AuthenticatedEncryptionResult($result);
		}

		public function decryptData(\Lenkenith\Core\Encryption\Api\AuthenticatedEncryptionDataInterface $aeData){			
			$continue = false; $decrypted = false;
			$aeData->formatPreDecryption();
			$encrypted_data = $aeData->getHash();
			$iv 			= $aeData->getIv();
			$auth_key 		= $aeData->getAuth();
			if($auth_key){
				$auth 			= substr($encrypted_data, 0, 32);
				$enc_data 		= substr($encrypted_data, 32);
				$actual_auth 	= hash_hmac('sha256', $enc_data, $auth_key, true);	
				if(hash_equals($auth, $actual_auth)) {
				    $continue = true;
				}
			}
			else{
				$enc_data = $encrypted_data;
				$continue = true;
			}
			if($continue === true){
				$decrypted = $this->pkcs7Unpad(openssl_decrypt(
				    $enc_data,
				    self::DEFAULT_CIPHER_MODE,
				    $this->formatKey($aeData->getEncryptionKey()),
				    0,
				    $iv
				));
			}
			return $decrypted;
		}

		public function validateEncryptedData($raw_data,$encrypted_data){
			$decrypted = $this->decryptData($encrypted_data);
			return ($raw_data === $decrypted);
		}
		
	}

?>