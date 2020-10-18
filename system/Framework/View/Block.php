<?php
	namespace Lenkenith\Framework\View;
	class Block {
		protected $__layout, $data, $context;
		public $helper;
		public function __construct(\Lenkenith\Framework\View\Context $context, Array $data){
			$this->context  = $context;
			$this->helper 	= $context->getHelper();
			$this->data 	= $data;
		}
		protected function getRequest(){
			return $this->context->getRequest();
		}
		protected function getLayout(){
			return $this->context->getLayout();
		}
		public function getRegistry(){
			return $this->context->getRegistry();
		}
		public function setData($name,$value){
			$this->data[$name] = $value;
		}
		public function getData($name){
			if(isset($this->data[$name])){
				return $this->data[$name];
			}
			return false;
		}
	}
?>