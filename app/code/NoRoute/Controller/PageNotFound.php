<?php
	namespace NoRoute\Controller;
	class PageNotFound extends \Lenkenith\Framework\Controller\BaseController {
		public function index(){
			header('HTTP/1.1 404 Page Not Found');
			header('Status: 404 Page Not Found');
			$this->getLayout()->loadTemplateFile('NoRoute::default-no-route.phtml','NoRoute\Block\NoRoute');
		}
	}