<?php
	namespace Lenkenith\Framework\View;
	class Context{
		private $__layout;
		public function __construct(\Lenkenith\Framework\View\Layout $layout){
			$this->__layout = $layout;
		}		
		public function getHelper(){
			return $this->getLayout()->helper;
		}
		public function getRequest(){
			return $this->getRegistry()->get('current_request');
		}
		public function getRegistry(){
			return $this->getLayout()->getRegistry();
		}
		public function getConfig(){
			return $this->getLayout()->getConfig();
		}
		public function getLoader(){
			return $this->getLayout()->getLoader();
		}
		public function getLayout(){
			return $this->__layout;
		}
	}
?>