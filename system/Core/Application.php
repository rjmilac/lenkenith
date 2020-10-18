<?php
	namespace Lenkenith\Core;
	class Application {
		private $request, $loader, $configuration;
		public function __construct(
			\Lenkenith\Core\Api\ResourceRequestInterface $request,
			\Lenkenith\Core\Loader $loader,
			\Lenkenith\Base\Configuration $configuration
		){
			$this->loader 			= $loader;
			$this->request 			= $request;
			$this->configuration  	= $configuration;	
		}
		public function setRequest(\Lenkenith\Core\Api\ResourceRequestInterface $request){
			$this->request = $request;
			return $this;
		}
		public function getActiveRequest(){
			return $this->request;
		}
		public function execute(){
			$controllerInstance = $this->loader->get($this->request->getController());
			$action 			= $this->request->getValidAction();
			$methodReflector 	= new \ReflectionMethod($controllerInstance, $action);
			if(!$methodReflector->isPublic()){
				throw new \Lenkenith\Exception\AccessModifierException($action.' action is restricted. '.$action.'() method is not public.' );
			}
			$controllerInstance->$action($this->request->getArguments());		
			return $this->request;
		}
	}