<?php
	
	namespace Lenkenith\Core\Encryption\Api;

	interface AuthenticatedEncryptionDataInterface{
		public function getIv();
		public function getHash();
		public function getAuth();
		public function getEncryptionKey();
		public function formatPreDecryption();
	}

?>