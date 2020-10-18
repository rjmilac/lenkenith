<?php
	namespace NoRoute\Controller;
	class Maintenance extends \Lenkenith\Framework\Controller\BaseController {
		public function index(){
			header('HTTP/1.1 503 Service Temporarily Unavailable');
			header('Status: 503 Service Temporarily Unavailable');
			$this->getLayout()->loadTemplateFile('NoRoute::maintenance.phtml','NoRoute\Block\Maintenance');
		}
	}