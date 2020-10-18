<?php
	namespace Lenkenith\Console;
	class Config {
		protected $configuration;
		public function __construct(){
			$this->configuration = array();
			if(file_exists(__DIR__.'/../../config/console.config.php') && !isset($_CONSOLE)){
				include(__DIR__.'/../../config/console.config.php');
				if(isset($_CONSOLE)){
					$this->configuration = $_CONSOLE;
				}
			}
		}
		public function getConfig($scope = 'all'){
			if(isset($this->configuration[$scope])){
				return $this->configuration[$scope];
			}
			else{
				return $this->configuration;	
			}
		}
	}
?>