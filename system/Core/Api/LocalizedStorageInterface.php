<?php	
	namespace Lenkenith\Core\Api;
	interface LocalizedStorageInterface{
		public function get($key);
		public function has($key);
		public function store($key, $value);
		public function remove($key);
	}
?>