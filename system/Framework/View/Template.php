<?php
	namespace Lenkenith\Framework\View;
	class Template{
		protected $file_path, $layout, $block;
		public $helper;
		public function __construct($file_path, 
			\Lenkenith\Framework\View\Layout $layout,
			\Lenkenith\Framework\View\Block $block){
			$this->block 		= $block;
			$this->file_path 	= $file_path;
			$this->_layout 		= $layout;
			$this->helper 		= $this->_layout->helper;
		}
		public function getLayout(){
			return $this->_layout;
		}
		public function getRegistry(){
			return $this->_layout->getRegistry();
		}
		public function getConfig(){
			return $this->_layout->getConfig();
		}
		public function toHtml(){
			if(file_exists($this->file_path)){
				$block = $this->block;
				ob_start();	
				include $this->file_path;
				return ob_get_clean();
			}
			return false;
		}
		public function load(){
			if(file_exists($this->file_path)){
				$block = $this->block;
				include $this->file_path;
			}
			return file_exists($this->file_path);
		}
	}
?>