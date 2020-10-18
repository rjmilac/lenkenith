<?php
	namespace Lenkenith\Core;
	class Router {
		private $registry, $request, $magic_methods, $_configuration, $configs, $depth, $request_segments, $db_config, $request_execution_iteration;
		private $pointer, $raw_request_path, $request_depth, $_rewriteManager;
		public function __construct(\Lenkenith\Base\Configuration $configuration, \Lenkenith\Core\UrlRewriteManager $url_rewrite_manager){
			$this->_configuration 		= $configuration;
			$this->configs 			 	= $this->_configuration->getConfigs();
			$this->db_config 		 	= $configuration->getDatabaseConfiguration();
			$this->_rewriteManager 	 	= $url_rewrite_manager;
			$this->request_depth		= $this->configs->dirDepth;
			$this->pointer 				= 0;
			$this->request_execution_iteration = 0;
		}
		public function __toString(){	
			return 'Lenkenith\Core\Router class instance';
		}
		private function ensureTrailingSlash($url){
			return rtrim($url,"/")."/";
		}
		private function filterUri($uri){
			$bad	= array('$',		'(',		')',		'%28',		'%29');
			$good	= array('&#36;',	'&#40;',	'&#41;',	'&#40;',	'&#41;');
			return str_replace($bad, $good, $uri);
		}
		private function parseRequestFromUri($path = null){
			$path 			= ($path == null) ? rawurldecode(trim(parse_url(getenv('REQUEST_URI'), PHP_URL_PATH), '/')) : $path;
			$filtered_path 	= $this->filterUri($path);
			$filtered_path 	= $this->ensureTrailingSlash($filtered_path);			
			$path 			= preg_replace("%\.\./%", "", $filtered_path);	
			$segments       = explode('/', $path);
			if($this->request_depth > 0){
				for($i=1;$i<=$this->request_depth;$i++){			
					array_shift($segments);				
				}
			}						
			$this->raw_request_path = implode('/',$segments);
			$rewritten = $this->_rewriteManager->getRewrite($this->raw_request_path);
			if($rewritten != false){ 
				if(strtolower($rewritten['type']) == 'external'){
					if(!headers_sent()){
						header('HTTP/1.1 '.$rewritten['code']);
						header('Location:'.$this->configs->baseUrl.$rewritten['path']);
						exit;
					}
				}
				$rewritten = $rewritten['segments']; 
			}
			return (!$rewritten) ? $segments : $rewritten;
		}
		private function getDefaultRequest(){
			return $this->configs->default_modules->default;
		}
		private function getNoRouteRequest(){
			return $this->configs->default_modules->no_route;
		}
		private function getMaintenanceRequest(){
			return $this->configs->default_modules->maintenance;
		}
		private function getModuleRoute($module){
			try{
				$dbh = new \PDO('mysql:host='.$this->db_config->server.';dbname='.$this->db_config->database.'', $this->db_config->user, $this->db_config->pass);
				$moduleQuery = $dbh->prepare('SELECT * from '.$this->db_config->prefix.'modules WHERE LOWER(name) = ? OR LOWER(route) = ? ORDER BY id ASC LIMIT 1 ');
				$moduleQuery->execute(array(strtolower($module),strtolower($module)));
				$mResult = $moduleQuery->fetchAll(\PDO::FETCH_OBJ);
				if($mResult){
					foreach ($mResult as $m) {
						if( strtolower($m->status) == 'active' ){
							return $m;
						}	
						else if( strtolower($m->status) == 'maintenance' ){
							return 'maintenance';
						}	
						else{
							return 'disabled';
						}
					}									
				}
				$dbh = null;
				unset($dbh);
			}
			catch(\Exception $e){
				throw new \Lenkenith\Exception\DataConnectionFailureException('Router : '.$e->getMessage(), 1003);			
			}		
			return 'disabled';
		}
		private function getControllerRoute($module_dir){
			$controller = ((isset($this->request_segments[1])) ? ((!empty($this->request_segments[1])) ? ucwords($this->request_segments[1]) : 'Index') : 'Index');
			$this->pointer = 1; $front_dirs = '';
			while($this->pointer <= sizeOf($this->request_segments) && is_dir($module_dir.'/'.$this->configs->directories->controller.'/'.ucwords($controller))){
				$controller = $this->request_segments[$this->pointer];
				$front_dirs  .= ucwords($controller).'\\';
				$this->pointer++;
			}
			if(file_exists($module_dir.'/routes_config.xml')){				
				if($route_config = @simplexml_load_file($module_dir.'/routes_config.xml')){
					foreach ($route_config->controllerRoute as $c) {
						$route = $c->attributes();
						if(strtolower($route['name']) == strtolower($controller)){
							if(isset($route['target'])){
								$controller = $route['target'];
							}
						}
					}
				}
			}
			return (!empty($front_dirs)) ? rtrim(ucwords($front_dirs),'\.php') : rtrim(ucwords($controller),'.php');
		}
		private function getActionRoute($module_dir,$controller,$action_index = 2){
			$action_index = ($action_index < 2) ? 2 : $action_index;
			$action = ((isset($this->request_segments[$action_index])) ? ((!empty($this->request_segments[$action_index])) ? $this->request_segments[$action_index] : 'index') : 'index');
			if(file_exists($module_dir.'/routes_config.xml')){
				if($route_config = @simplexml_load_file($module_dir.'/routes_config.xml')){			
					foreach ($route_config->controllerRoute as $c) {
						$route = $c->attributes();
						if(strtolower($route['target']) == strtolower($controller) || strtolower($route['name']) == strtolower($controller)){
							foreach ($c->actionRoute as $a) {
								$a_route = $a->attributes();
								if(strtolower($a_route['name']) == strtolower($action)){
									$action = strtolower($a_route['target']);
								}
							}
						}
					}
				}
			}	
			return $action;
		}
		private function validateRequest($request = null){
			$request 	= ($request == null) ? $this->getDefaultRequest() : $request;
			$request 	= (!is_array($request)) ? json_decode(json_encode($request), true) : $request;
			$module_dir = $this->configs->directories->app.'/'.$this->configs->directories->code.'/'.$request['module'];	
			$controller_file = $module_dir.'/'.$this->configs->directories->controller.'/'.$request['controller'].'.php';
			if($this->request_execution_iteration > 3){
				throw new \Lenkenith\Exception\FileNotFoundException("Controller file not found. (".$controller_file.")", 1003);				
			}
			if(!file_exists($controller_file)){
				$this->request_execution_iteration = $this->request_execution_iteration + 1;
				return $this->validateRequest($this->getNoRouteRequest());				
			}
			require_once $controller_file;
			$class 		= $request['module'].'\\Controller\\'.$request['controller'];
			$action 	= ((!isset($request['action'])) ? 'index' : $request['action'] );			
			$reflector  = new \ReflectionClass($class);
			if(!$reflector->hasMethod($action)){ $action = 'index'; }
			else{ $this->pointer++;	}		
			unset($reflector);	
			$args_arr 	 = array();
			for($pointer = $this->pointer; $pointer < sizeOf($this->request_segments); $pointer++){
				if(isset($this->request_segments[$pointer])){
					if(!empty($this->request_segments[$pointer])){
						array_push($args_arr, $this->request_segments[$pointer]);
					}
					$this->pointer++;
				}
			}		
			return array('module'=> $request['module'], 'controller' => $class, 'action' => $action, 'arguments' => $args_arr, 'raw_request_path' => $this->raw_request_path);
		}
		public function getRequest($request_segments = null){
			if(defined('BASE_ROUTING_COMPLETE')){ throw new \Lenkenith\Exception\RequestDirectExecutionException("Router Error : Executing request directly is prohibited.", 1003); }
			if($request_segments == 'no-route'){ 
				return $this->validateRequest($this->getNoRouteRequest()); 
			}
			else if($request_segments == 'maintenance'){ 
				return $this->validateRequest($this->getMaintenanceRequest()); 
			}
			else if(!is_array($request_segments)){ 
				$this->request_segments = $this->parseRequestFromUri(); 
			}
			else{ 
				$this->request_segments = $request_segments; 
			}
			$request 					= $this->getDefaultRequest();
			$this->request_segments 	= ($request_segments == null) ? $this->parseRequestFromUri() : $request_segments;
			if(is_array($this->request_segments) && !empty($this->request_segments)){
				if(!empty($this->request_segments[0])){
					$module_route 	= $this->getModuleRoute($this->request_segments[0]);
					$module 		= (!$module_route || !isset($module_route->name)) ? $this->request_segments[0] : $module_route->name;
					$module_dir  	= $this->configs->directories->app.'/'.$this->configs->directories->code.'/'.ucwords($module);
					if(is_dir($module_dir) && $module_route != 'disabled' && $module_route != 'maintenance'){
						$controller = $this->getControllerRoute($module_dir);
						if(!empty($controller) && file_exists($module_dir.'/'.$this->configs->directories->controller.'/'.$controller.'.php')){	
							$action_index  	= $this->pointer;
							$action 		= $this->getActionRoute($module_dir,$controller,$action_index);								
							$request 		= array('module' => $module, 'controller' => $controller, 'action' => $action);
						}	
						else{
							$request = $this->getNoRouteRequest();
						}
					}
					else if(is_dir($module_dir) && $module_route == 'maintenance'){
						$request = $this->getMaintenanceRequest();
					}
					else{
						$request = $this->getNoRouteRequest();
					}
				}
				else{
					$request = $this->getDefaultRequest();
				}
			}
			$module_route 	= $this->getModuleRoute( (isset($request->module)) ? $request->module : $request['module'] );
			if($module_route == 'disabled'){ $request = $this->getNoRouteRequest(); }
			if($module_route == 'maintenance'){ $request = $this->getMaintenanceRequest(); }
			return $this->validateRequest($request);
		}
	}
?>