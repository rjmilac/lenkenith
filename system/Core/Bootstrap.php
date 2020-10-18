<?php
	namespace Lenkenith\Core;
	use \Lenkenith\Base\Configuration AS Configuration;
	class Bootstrap {
		private $configuration, $_loader, $_router, $request, $_registry;
		public $configs, $logger;
		private function configure(){	
			$this->configuration 	= new Configuration(new \Lenkenith\Base\DatabaseConfiguration());
			$this->configs    	 	= $this->configuration->getConfigs();							
			$this->_loader 			= $this->initLoader();
			if(!defined('ENV_MODE')){ define('ENV_MODE',$this->configs->env_mode); } 
			return $this;
		}
		private function startSession(){
			$session = $this->_loader->get('\Lenkenith\Core\Data\Session',array($this->configs->session));
			$session->start();
			return $this;
		}
		private function initLoader(){
			require_once $this->configs->directories->system.'/Core/Loader.php';
			return new \Lenkenith\Core\Loader($this->configuration);
		}
		private function initLogger(){
			$this->logger = $this->_loader->create('\Lenkenith\Core\Logger');
			return $this;
		}
		private function autoloadExceptionDefinitions(){
			$exceptions = scandir($this->configs->directories->system.'/Exception');
			foreach ($exceptions as $e) {
				if (strpos($e, 'Exception.php') !== false) {
				   	$this->_loader->prepare('\Lenkenith\Exception\\'.rtrim($e,'.php'));
				}
			}
			return $this;
		}
		private function prepareCoreResources(){	
			$this->_loader->prepare('\Lenkenith\Core\Registry');		
			$this->_loader->prepare('\Lenkenith\Core\CacheManager');			
			$this->_loader->prepare('\Lenkenith\Core\Api\ResourceRequestInterface');
			$this->_loader->prepare('\Lenkenith\Core\Data\ResourceRequest');
			$this->_loader->prepare('\Lenkenith\Core\UrlRewriteManager');
			$this->_loader->prepare('\Lenkenith\Core\Encryption\Data\Cache');
			$this->_loader->prepare('\Lenkenith\Core\Encryption\Api\AuthenticatedEncryptionDataInterface');
			$this->_loader->prepare('\Lenkenith\Core\Encryption\Api\AuthenticatedEncryptionResult');
			return $this;
		}
		private function prepareFrameworkResources(){	
			$this->_loader->prepare('\Lenkenith\Framework\Api\DataObjectInterface'); 	
			$this->_loader->prepare('\Lenkenith\Framework\Api\DataObject');
			$this->_loader->prepare('\Lenkenith\Framework\Model\Api\ResourceModelInterface');
			$this->_loader->prepare('\Lenkenith\Framework\Model\ResourceModel');
			$this->_loader->prepare('\Lenkenith\Framework\Model\Api\FactoryInterface');
			$this->_loader->prepare('\Lenkenith\Framework\Model\Api\Factory');
			$this->_loader->prepare('\Lenkenith\Framework\Model\Api\CollectionInterface');
			$this->_loader->prepare('\Lenkenith\Framework\Model\Api\Collection');
			$this->_loader->prepare('\Lenkenith\Framework\Controller\BaseController');
			$this->_loader->prepare('\Lenkenith\Framework\View\Block');
			$this->_loader->prepare('\Lenkenith\Framework\View\Data\OnPageSeo');
			return $this;
		}
		private function setRoutes($request_segments = array()){
			$this->_router 	= $this->_loader->create('\Lenkenith\Core\Router');
			if(!empty($request_segments)){
				$this->request 	= new \Lenkenith\Core\Data\ResourceRequest($this->_router->getRequest($request_segments)); 
			}
			else{
				$this->request 	= new \Lenkenith\Core\Data\ResourceRequest($this->_router->getRequest()); 	
			}			
			return $this;
		}
		private function reRoute($route_code = 'no-route'){
			$this->request 	= new \Lenkenith\Core\Data\ResourceRequest($this->_router->getRequest($route_code)); 
			$this->runRequest();
			return $this;
		}
		private function runRequest(){
			try{
				$controller 			= $this->request->getController();
				$controller_instance 	= $this->_loader->create($controller);
			} catch (\Lenkenith\Exception\ClassNotFoundException $e){
				return $this->reRoute('no-route');
			}	
			$this->_loader->get('\Lenkenith\Core\Registry')->store('current_request',$this->request,true);
			if(method_exists($controller_instance, $this->request->getValidAction())){
				$client_ip = (isset($_SERVER['REMOTE_ADDR'])) ? $_SERVER['REMOTE_ADDR'] : 'undetected';
				if(!in_array($client_ip, $this->configs->env_ip_exemption) && $this->configs->env_mode == 'MAINTENANCE'){
					$this->request 	= new \Lenkenith\Core\Data\ResourceRequest($this->_router->getRequest('maintenance')); 
					unset($controller); unset($controller_instance);
				}
				define('BASE_ROUTING_COMPLETE',true);		
			}
			else{
				$this->reRoute('no-route');
			}
			return $this;
		}
		private function open(){
			if(defined('BOOTSTRAP_EXECUTED')) { 
				throw new \Exception("Bootstrap already executed. Directly rebooting the request is strongly discouraged.", 1000); 
			}
			require_once('system/Base/DatabaseConfiguration.php');
			require_once('system/Base/Configuration.php');	
			return $this;
		}
		private function endBoot($exit = false){
			define('BOOTSTRAP_EXECUTED',true);
			if($exit){ exit(); }
			return $this;
		}
		private function createApplication(){
			return $this->_loader->create('\Lenkenith\Core\Application',array($this->request,$this->_loader,$this->configuration));
		}
		public function run($application = array()){	
			if(!empty($application) && !is_array($application)){
				$application = explode('%\%', $application);
			}	
			return $this->open()
				->configure()
				->startSession()
				->autoloadExceptionDefinitions()
				->initLogger()
				->prepareCoreResources()
				->prepareFrameworkResources()
				->setRoutes($application)
				->runRequest()
				->endBoot()
				->createApplication();
		}
		public function runConsoleCli(){
			$this->open()
				->configure()
				->startSession()
				->autoloadExceptionDefinitions()
				->initLogger()
				->prepareCoreResources()
				->prepareFrameworkResources();
			return $this->_loader->create('\Lenkenith\Console\Cli');
		}
	}
?>