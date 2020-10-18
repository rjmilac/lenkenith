<?php
	
	namespace Lenkenith\Core\Encryption;

	class HashManager{

		private $salt, $salts, $cache, $_algos;

		const DEFAULT_HASH_ALGO = 'sha256';

		public function __construct(){
			$this->cache 	= new \Lenkenith\Core\Encryption\Data\Cache(array());
			$this->salt 	= $this->generateRandomToken();			
			$this->salts    = array($this->salt);
			$this->_algos 	= hash_algos();
		}

		public function setSalt($salt){
			array_push($this->salts, $salt);
			$this->salt = $salt;
			return $this;
		}

		public function getActiveSalt(){
			return $this->salt;
		}

		public function resetSalt($index = 0){
			$this->salt = ((isset($this->salts[$index])) ? $this->salts[$index] : $this->salts[0]);
			return $this;
		}

		private function cryptoRandSecure($min, $max){
			$range = $max - $min;
		    if ($range < 1) return $min;
		    $log = ceil(log($range, 2));
		    $bytes = (int) ($log / 8) + 1;
		    $bits = (int) $log + 1;
		    $filter = (int) (1 << $bits) - 1;
		    do {
		        $rnd = hexdec(bin2hex(openssl_random_pseudo_bytes($bytes)));
		        $rnd = $rnd & $filter;
		    } while ($rnd >= $range);
		    return $min + $rnd;
		}

		public function generateRandomToken($length = 32){
			$token 			= "";
			$codeAlphabet 	= "ABCDEFGHIJKLMNOPQRSTUVWXYZ";
			$codeAlphabet	.= "abcdefghijklmnopqrstuvwxyz";
			$codeAlphabet	.= "0123456789";
			$max 			= strlen($codeAlphabet); 
			for ($i=0; $i < $length; $i++) {
			    $token .= $codeAlphabet[$this->cryptoRandSecure(0, $max)];
			}
			if($this->cache->has($token)){
				return $this->generateRandomToken($length);
			}
			$this->cache->store($token, true);
			return $token;
		}

		public function getHash($string,$hashAlgo = self::DEFAULT_HASH_ALGO){
			if(!is_string($string) && !is_numeric($string)){
				throw new \Lenkenith\Exception\InvalidArgumentsException('Unable to hash passed input. Invalid data type.',5003);
			}
			return hash(((!in_array($hashAlgo, $this->_algos)) ? self::DEFAULT_HASH_ALGO : $hashAlgo), $string);			
		}

		public function getMergedSaltHash($string,$hashAlgo = self::DEFAULT_HASH_ALGO){
			return array('string' => $this->getHash($this->salt.$string,$hashAlgo) , 'salt' => $this->salt);
		}

		public function hashArray(Array $array,$hashBoth=false,$hashAlgo = self::DEFAULT_HASH_ALGO){
			if(is_array($array)){
				foreach ($array as $key => $value) {
					$hashedValue = (is_array($value)) ? $this->hashArray($value,$hashBoth,$hashAlgo) : $this->getMergedSaltHash($value,$hashAlgo)['string'];
					if($hashBoth){
						$array[$this->getMergedSaltHash($key,$hashAlgo)['string']] = $hashedValue;
						unset($array[$key]);
					}
					else{
						$array[$key] = $hashedValue;
					}
				}
			}
			return $array;
		}

		public function validateHash($input,$hash,$merge_salt=true,$hashAlgo = self::DEFAULT_HASH_ALGO){
			$rehashed = ($merge_salt) ? $this->getMergedSaltHash($input,$hashAlgo)['string'] : $this->getHash($input,$hashAlgo); 
			return ($rehashed === $hash);
		}

		public function getPasswordHash($password, $algo = null, $options = array()){
			$algo = ($algo == null) ? PASSWORD_DEFAULT : $algo;
			return password_hash($password,$algo,$options);
		}

		public function verifyPasswordHash($password, $hash){
			return password_verify($password,$hash);
		}

	}

?>