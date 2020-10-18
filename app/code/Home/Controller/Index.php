<?php
	namespace Home\Controller;
	class Index extends \Lenkenith\Framework\Controller\BaseController {
		public function index(){
			$this->getLayout()->loadTemplateFile('Home::home.phtml','Home\Block\Home');
		}
	}