<?php
	namespace Lenkenith\Core\Api\Security;

	class FormSession {

		private $_session, $_hashManager, $_form;

		public function __construct(\Lenkenith\Core\Data\Session $session,
			\Lenkenith\Core\Encryption\HashManager $hashManager){
			$this->_hashManager = $hashManager;
			$this->_session 	= $session;
			$this->_form 		= null;
		}

		public function load($form){
			$this->_form = $form;
			return $this;
		}

		public function getName(){
			return $this->_form;
		}

		public function generate(){
			$token =  $this->_hashManager->generateRandomToken();
			$this->_session->store($this->_form, $token);
			return $token;
		}

		public function compare($token){
			$_token = $this->getToken();
			return ($_token === $token);
		}

		public function getToken(){
			return $this->_session->get($this->_form);
		}

		public function remove(){
			$this->_session->remove($this->_form);
			$this->_form = null;
			return true;
		}

	}
?>