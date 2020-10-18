<?php
	namespace Lenkenith\Core\Data;
	class ResourceRequest implements \Lenkenith\Core\Api\ResourceRequestInterface{
		private $module,$controller,$action,$arguments,$data;
		public function __construct(Array $request){
			$this->module 			= isset($request['module']) ? $request['module'] : null;
			$this->controller 		= isset($request['controller']) ? $request['controller'] : 'Index';
			$this->action 			= isset($request['action']) ? $request['action'] : 'index';
			$this->arguments 		= isset($request['arguments']) ? $request['arguments'] : array();
			$this->raw_request_path	= isset($request['raw_request_path']) ? $request['raw_request_path'] : array();
			$this->magic_methods 	= array( '__construct', '__destruct', '__call', '__callStatic', '__get', '__set', '__isset', '__unset', '__sleep', '__wakeup', '__toString', '__invoke', '__set_state', '__clone',  '__debugInfo' );
		}
		public function getModule(){
			return $this->module;
		}
		public function getController(){
			return $this->controller;
		}
		public function getAction(){
			return $this->action;
		}
		public function getArguments(){
			return $this->arguments;
		}
		public function getRawRequestPath(){
			return $this->raw_request_path;
		}
		public function getPost(){
			$params = $this->getParams();
			return $params['post'];
		}
		public function getParams(){
			$post 	= isset($_POST) ? $_POST : array();
			$get 	= isset($_GET) ? $_GET : array();
			return array('post' => $post , 'get' => $get);
		}
		private function isActionValid($action = null){
			$action = ($action == null) ? $this->action : $action ;
			return (in_array($action, $this->magic_methods) ? false : true);
		}
		public function getValidAction(){
			return ((!$this->isActionValid()) ? 'index' : $this->action );
		}
	}
?>