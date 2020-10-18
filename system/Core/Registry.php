<?php
	namespace Lenkenith\Core;
	class Registry implements \Lenkenith\Core\Api\LocalizedStorageInterface{
		private $registry;
		public function __construct(){
			$this->registry = array();
		}
		public function get($name){
			if(isset($this->registry[$name])){
				return $this->registry[$name];
			}
			return false;
		}
		public function has($name){
			return (isset($this->registry[$name])) ? true : false;
		}
		public function store($name,$value = null,$overwrite = false){
			if($this->has($name) && !$overwrite){
				$name = $name.'_'.(sizeOf($this->registry[$name]) + 1);
			}
			$this->registry[$name] = $value;
			return true;
		}
		public function remove($name){
			if($this->has($name)){
				unset($this->registry[$name]);
			}
			return false;
		}
	}
?>