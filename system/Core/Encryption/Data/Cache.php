<?php
	namespace Lenkenith\Core\Encryption\Data;

	class Cache implements \Lenkenith\Core\Api\LocalizedStorageInterface{

		private $initial, $_dataList;

		public function __construct(Array $initial){
			$this->_dataList 	= $initial;
			$this->initial  	= $initial;
		}

		private function createKey($key){
			return preg_replace('{\\\}', '--', $key);
		}

		public function has($key){
			return (isset($this->_dataList[$this->createKey($key)])) ? true : false;
		}

		public function store($key, $instance){
			$this->_dataList[$this->createKey($key)] = $instance;
			return true;
		}

		public function get($key){
			if($this->has($key)){
				return $this->_dataList[$this->createKey($key)];
			}
			return false;
		}

		public function remove($key){
			if($this->has($key)){
				unset($this->_dataList[$this->createKey($key)]);
				return true;
			}
			return false;
		}

	}


?>