<?php
	namespace Lenkenith\Base;

	class DatabaseConfiguration{
		public $server,$user,$pass,$database,$port,$prefix;
		private $savedSettings;
		public function __construct(){			
			$defaults = array(
				'driver' 	=> "pdo",
				'server' 	=> "localhost",
				'user'		=> "root",
				'pass' 		=> "",
				'database' 	=> "base_skel",
				'port' 		=> "",
				'prefix'   	=> ""
			);
			$config 				= array();
			$this->savedSettings 	= array();
			if(file_exists('config/database.config.php')){
				include 'config/database.config.php';
				if(isset($_DB)){
					$config = array_merge($defaults,$_DB);
				}
			}
			$this->set($config);
			$this->saveCurrentSettingAs('default');
		}
		public function set($config){
			$this->server 	= $config['server'];
			$this->user 	= $config['user'];
			$this->pass 	= $config['pass'];
			$this->database = $config['database'];
			$this->port 	= $config['port'];
			$this->driver 	= $config['driver'];
			$this->prefix   = $config['prefix'];
		}
		public function saveCurrentSettingAs($name){
			if(!isset($this->savedSettings[$name])){
				$settings = array(
						'server' => $this->server,
						'user' => $this->user,
						'pass' => $this->pass,
						'database' => $this->database,
						'port' => $this->port,
						'driver' => $this->driver,
						'prefix' => $this->prefix
					);
				$this->savedSettings[$name] = (json_decode(json_encode($settings), FALSE));
				return true;
			}
			else{
				return false;
			}
		}
		public function getSavedSetting($name){
			if(isset($this->savedSettings[$name])){
				return $this->savedSettings[$name];
			}
			else{
				return false;
			}
		}
		public function loadSavedSetting($name){
			$settings = $this->getSavedSetting($name);
			if($settings){
				$this->set($settings->server,$settings->user,$settings->pass,$settings->database,$settings->port,$settings->driver="pdo",$settings->prefix="");
			}
			else{
				return false;
			}
		}
	}	
?>