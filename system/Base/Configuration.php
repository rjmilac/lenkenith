<?php
	namespace Lenkenith\Base;
	class Configuration{		
		private $databaseConfig, $defaults, $configs, $default_base, $installation_dir_from_base;
		public $baseConfig;
		public function __construct(\Lenkenith\Base\DatabaseConfiguration $databaseConfig){
			$this->databaseConfig 				= $databaseConfig;
			$this->default_base   				= 'http://localhost/';
			$this->installation_dir_from_base	= '';
			$this->defaults = array(
				'env_mode' 			=> 'DEVELOPMENT',
				'env_ip_exemption'  => array('::1'),
				'work_offline' 		=> true,
				'installation_dir' 	=> '',
				'theme' 			=> 'default',
				'directories'  		=> array(
						// if you changed the next line from 'system', 
						// edit also the directories with name 'system' on system/Core/Bootstrap.php
						'system' 		=> 'system',
						'pub' 			=> 'pub',
						'utils' 		=> 'utils',
						'uploads' 		=> 'uploads',
						'skin' 			=> 'skin',
						'app' 			=> 'app',
						'code' 			=> 'code',
						'skin' 			=> 'skin',
						'controller' 	=> 'Controller',
						'model' 		=> 'Model',
						'block' 		=> 'Block',
						'helper' 		=> 'Helper',
						'plugin' 		=> 'Plugin',
						'view' 			=> 'View',
						'library' 		=> 'Library'
					),
				'website' 			=> array(
						'name' 			=> 'My Web Application',
						'title' 		=> 'My Web Application',
						'description' 	=> 'Lorem Ipsum',
						'image' 		=> 'favicon.ico',
						'tags' 			=> 'website, about'
					),
				'security' 			=> array(
						'encryption_key'=> '1T2OjrnqfuwKjEUdCf7Exd9MUXjxXIPa'
					),
				'session' 			=> array(
						'name' 			=> 'default_session',
						'clear_cookies_on_destroy'  => true,
						'token_invalidation_after_idle_time' => 1800 // (in ms) 30 minutes 
					),
				'default_modules' 	=> array(
						'default' 		=> array('module' => 'Home', 'controller' => 'Index', 'action' => 'index'),
						'no_route' 		=> array('module' => 'NoRoute', 'controller' => 'PageNotFound', 'action' => 'index'),
						'maintenance' 	=> array('module' => 'NoRoute', 'controller' => 'Maintenance', 'action' => 'index')
					),
				'caching' 			=> array(
						'enable_skin_caching' => true
					),
				'baseUrl' 			=> $this->getBaseUrlFromRequest(),
				'dirDepth' 			=> (!empty($this->installation_dir_from_base)) ? substr_count(rtrim($this->installation_dir_from_base,'/').'/', '/') : 0
			);
			if(file_exists('config/application.config.php')){
				include 'config/application.config.php';
				if(isset($_CONFIGS)){
					$this->defaults = array_merge($this->defaults,$_CONFIGS); 
					$this->defaults['dirDepth'] = (!empty($this->defaults['installation_dir'])) ? substr_count(rtrim($this->defaults['installation_dir'],'/').'/', '/') : 0;
					$this->defaults['baseUrl'] = $this->getBaseUrlFromRequest();
				}
			}
			$this->configs 		= $this->defaults;
			$this->configureEnv();
			$this->baseConfig 	= $this->configs;
		}
		public function configureEnv(){
			switch ($this->configs['env_mode']) {
				case 'DEVELOPMENT':
					error_reporting(E_ALL);
					ini_set('display_errors', '1');
					break;
				
				default:
					ini_set('display_errors', '0');
					break;
			}
		}
		public function getBaseUrlFromRequest(){
			$protocol 	= (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? "https://" : "http://";
			$host 		=  isset($_SERVER['HTTP_HOST'])? $_SERVER['HTTP_HOST'] : $this->default_base;
			$host 		= rtrim($host,'/').'/';	
			$host 		= preg_replace('%http://%', '', $host);
			$host 		= preg_replace('%https://%', '', $host);
			return $protocol.$host.$this->defaults['installation_dir'];	
		}
		public function getDatabaseConfiguration(){
			return $this->databaseConfig;
		}
		public function setConfiguration($config = array()){
			$this->configs = array_merge($config, $this->defaults);
			return true;
		}
		public function getConfigs($stdFormat = true){
			return ($stdFormat) ? (json_decode(json_encode($this->configs), FALSE)) : $this->configs;
		}
	}	
?>
