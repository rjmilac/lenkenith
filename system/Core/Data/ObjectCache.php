<?php
	namespace Lenkenith\Core\Data;
	class ObjectCache implements \Lenkenith\Core\Api\LocalizedStorageInterface{
		private $initial, $_objList;
		public function __construct(Array $initial){
			$this->_objList 	= $initial;
			$this->initial  	= $initial;
		}
		private function createKey($key){
			return preg_replace('{\\\}', '--', $key);
		}
		public function has($className){
			return (isset($this->_objList[$this->createKey($className)])) ? true : false;
		}
		public function store($className, $instance){
			$this->_objList[$this->createKey($className)] = $instance;
			return true;
		}
		public function get($className){
			if($this->has($className)){
				return $this->_objList[$this->createKey($className)];
			}
			return false;
		}
		public function remove($className){
			if($this->has($className)){
				unset($this->_objList[$this->createKey($className)]);
				return true;
			}
			return false;
		}
	}
?>