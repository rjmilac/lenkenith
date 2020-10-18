<?php
	namespace Lenkenith\Console;
	class CommandList {
		private $commands, $_consoleConfig;
		public function __construct(\Lenkenith\Console\Config $consoleConfig){	
			$this->_consoleConfig 	= $consoleConfig;				
			$this->commands 		= $consoleConfig->getConfig('command_list');
		}
		public function getCommandClass($command){
			if($this->commandExists($command)){
				return (isset($this->commands[$command]['class'])) ? $this->commands[$command]['class'] : false;
			}
			return false;
		}
		public function commandExists($command){
			return isset($this->commands[$command]);
		}
		public function commandActionExists($command, $action){
			return isset($this->commands[$command]['action'][$action]);
		}
		public function getCommandList($parent = null){
			if($parent != null){
				return (isset($this->commands[$parent])) ? $this->commands[$parent] : $this->commands;
			}	
			return $this->commands;
		}
		public function getListOutput($message = '',$command = null){
	   		$cli_commands = $this->getCommandList($command);
	   		$output = "\n";
	   		$output .= $message;   		
	    	$output .= "\n";
	    	$output .= "\n";
			foreach ($cli_commands as $method_key => $method_value) {
				if(is_array($method_value)){
					if(isset($method_value['action']) && is_array($method_value['action'])){
						foreach ($method_value['action'] as $param_key => $param_value) {
							$output .= (!empty($command)) ? $command."::" : '';
		    				$output .= $method_key."::".((is_numeric($param_key)) ? $param_value : $param_key);
		    				if(isset($param_value['params'])){
		    					foreach ($param_value['params'] as $p_key => $p_value) {
		    						$output .= "::".((is_numeric($p_key)) ? $p_value : $p_key);
		    					}
		    				}
		    				$output .= "\n";
		    			}
					}
					else{
						foreach ($method_value as $param_key => $param_value) {
							$output .= (!empty($command)) ? $command."::" : '';
		    				$output .= $method_key."::".((is_numeric($param_key)) ? $param_value : $param_key)."\n";
		    			}
					}					
				} 				
			}	
	    	$output .= "\n";
	   		return $output;
	   	}
	}

?>