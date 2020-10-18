<?php
	namespace Lenkenith\Core;
	class CacheManager{		
		private $config, $registry; 		
		public function __construct(\Lenkenith\Base\Configuration $configuration, \Lenkenith\Core\Registry $registry){
			$this->config 		= $configuration->getConfigs();
			$this->registry 	= $registry;
		}
		private function createCacheId($name){
			return 'cache_'.substr(md5($name), 0, 10).'_'.substr($name, -10);
		}
		private function storeToLocal($name,$value,$overwrite = true){
			return $this->registry->store($name,$value,$overwrite);
		}
		private function getFromLocal($name){
			return $this->registry->get($name);
		}
		public function recurseCopy($src,$dest,$sub="static"){
			$publicDir 	= $this->config->directories->pub;
			$dst 		= $publicDir.'/'.$sub.'/'.preg_replace('%'.$publicDir.'/'.$sub.'/'.'%', '', $dest);
			$dir 		= opendir($src); 
		    @mkdir($dst); 
		    while(false !== ( $file = readdir($dir)) ) { 
		        if (( $file != '.' ) && ( $file != '..' )) { 
		            if ( is_dir($src . '/' . $file) ) { 
		                $this->recurseCopy($src . '/' . $file,$dst . '/' . $file); 
		            } 
		            else { 
		                copy($src . '/' . $file,$dst . '/' . $file); 
		            } 
		        } 
		    } 
		    closedir($dir); 
		}
		public function load($cached_resource,$destination){				
			$resourceId = $this->createCacheId($cached_resource);		
			if($this->getFromLocal($resourceId)){
				return $this->getFromLocal($resourceId);
			}
			return $this->store($cached_resource,$destination);
		}
		public function store($resource,$destination,$sub="static"){
			$publicDir 	= $this->config->directories->pub;
			$cachedPath	= $resource;
			if(file_exists($publicDir.'/'.$sub.'/'.$destination)){				
				$cachedPath = $publicDir.'/'.$sub.'/'.$destination;
			}
			else{
				$dirs = explode('/', $sub.'/'.$destination); $concat_dir = ''; $base_dir = $publicDir.'/';
				array_pop($dirs);
				foreach ($dirs as $dir) {
					$concat_dir .= $dir.'/';
					if(!is_dir($base_dir.$concat_dir) && !is_file($base_dir.$concat_dir)){
						mkdir($base_dir.rtrim($concat_dir,'/'));
					}
				}
				if(copy($resource, $publicDir.'/'.$sub.'/'.$destination)){
					$cachedPath = $publicDir.'/'.$sub.'/'.$destination;
				}
			}
			$this->storeToLocal($this->createCacheId($resource), $cachedPath);
			return $cachedPath;
		}
	}
?>