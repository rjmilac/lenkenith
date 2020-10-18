<?php
	namespace Admin\Block\Account;
	class Login extends \Lenkenith\Framework\View\Block {

		private $_formSession;

		const FORM_SIGNIN = 'form:adm:signin';

		public function __construct(\Lenkenith\Framework\View\Context $context,
			\Lenkenith\Core\Api\Security\FormSession $formSession,
		 	Array $data){
			$this->_formSession = $formSession;
			parent::__construct($context,$data);	
		}

		public function getFinalOnPageSeoTags(){
			$meta = array(
					'title' => 'Admin | '.$this->context->getConfig()->website->name,
					'url' 	=> $this->context->getConfig()->baseUrl.'admin/login'
				);
			return $this->helper->getOnPageSeo()->getMetaHtml($meta);
		}

		public function getContinueRedirect(){
			$params = $this->getRequest()->getParams();
			return (isset($params['get']['continue'])) ? urldecode($params['get']['continue']) : 'na';
		}

		public function getFormSession($form = self::FORM_SIGNIN){
			return $this->_formSession->load($form);
		}

	}
?>