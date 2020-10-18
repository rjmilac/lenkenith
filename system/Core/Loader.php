<?php
	namespace Lenkenith\Core;
	class Loader {
		private $_objectCache, $_configuration;
		public function __construct(\Lenkenith\Base\Configuration $configuration){
			$this->_configuration 	= $configuration;
			$this->setObjectCache();
			$this->_objectCache->store('\Lenkenith\Core\Loader', $this);
		}
		private function setObjectCache(){			
			$this->prepare('\Lenkenith\Core\Api\LocalizedStorageInterface');
			$this->prepare('\Lenkenith\Core\Data\ObjectCache');
			$this->_objectCache = new \Lenkenith\Core\Data\ObjectCache(array());
			$this->_objectCache->store('\Lenkenith\Core\Data\ObjectCache', $this->_objectCache);
		}
		public function __toString(){	
			return 'Lenkenith\Core\Loader class instance';
		}
		public function prepare($class){
			$class_frags = explode('\\', ltrim($class,'\\'));
			$real_file 	= ltrim($class,'\\');
			if($class_frags[0] == 'Lenkenith'){			
				$real_file 	= preg_replace('%Lenkenith%', $this->_configuration->getConfigs()->directories->system,  ltrim($class,'\\'));
			}
			else{
				$real_file 	= $this->_configuration->getConfigs()->directories->app.'\\'.$this->_configuration->getConfigs()->directories->code.'\\'.ltrim($class,'\\');
			}
			if(!class_exists($class) || interface_exists($class)){
				if(file_exists($real_file.'.php')){					
					require_once $real_file.'.php';
				}
				else{
					throw new \Lenkenith\Exception\ClassNotFoundException($class." class not found. Target file was : ".$real_file.'.php', 1002);		
				}
			}
			return $real_file.'.php';
		}
		public function create($class, $constructor_args = null){
			$real_file 		= $this->prepare($class);	
			$compare_class 	= ltrim($class,'\\');
			$class 	  		= '\\'.$compare_class;
			if(class_exists($class) || interface_exists($class)){
				$reflector 			= new \ReflectionClass($class);
				$constructor_args 	= (!is_array($constructor_args) && $constructor_args != null) ? array($constructor_args) : $constructor_args;
				if($reflector->getConstructor() && $reflector->getConstructor() != null && $reflector->isInstantiable()){
					$try_args = $constructor_args;				
					if(empty($constructor_args) || $constructor_args == null){
						$try_args 	= array();
						$params 	= $reflector->getConstructor()->getParameters();
						foreach ($params AS $param) {
							$ins = null;
							try{
								if($param->getClass() != null){
								    if(class_exists($param->getClass()->name) || interface_exists($param->getClass()->name)){
								    	$ins = $this->get($param->getClass()->name);
								    }
								}
							}
							catch(\ReflectionException $e){
								$retry_frags = preg_split('/\s+/', $e->getMessage());
								if(isset($retry_frags[1])){
									$ins = $this->get($retry_frags[1]);
								}
								else{
									throw new \Lenkenith\Exception\ClassNotFoundException($e->getMessage(), 1002);
								}
							}
							if($param->isArray()){
								$ins = array();
							}
							array_push($try_args, $ins);
						}	
					}
					$instance = $reflector->newInstanceArgs($try_args);		
				}
				else{
					if($reflector->isInstantiable()) {
						$instance =  $reflector->newInstanceArgs();
					} else {
						throw new \Lenkenith\Exception\ClassNotFoundException($class." is not instantiable", 1005);	
					}
				}
				$this->_objectCache->store($class, $instance);
				return $instance;					
			}
			else{
				throw new \Lenkenith\Exception\ClassNotFoundException($class." not found. No class exists with that name", 1002);					
			}
		}
		public function get($class, $construct = null){	
			$class = ltrim($class,'\\');
			$class = '\\'.$class;
			if($this->_objectCache->has($class)){
				return $this->_objectCache->get($class);
			}
			else{
				return $this->create($class,$construct);
			}
		}
	}
?>