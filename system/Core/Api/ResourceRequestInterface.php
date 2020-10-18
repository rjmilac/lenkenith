<?php	
	namespace Lenkenith\Core\Api;
	interface ResourceRequestInterface{
		public function getModule();
		public function getController();
		public function getAction();
		public function getArguments();
		public function getParams();
		public function getRawRequestPath();
	}
?>