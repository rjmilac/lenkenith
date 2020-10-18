<?php
	
	namespace Lenkenith\Framework\Api;

	interface DataObjectInterface{
		public function addData(Array $array);
		public function setData($key,$value);
		public function getData($key);
		public function getInitial();
		public function unsetData($key);
	}

?>