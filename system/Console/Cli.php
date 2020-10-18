<?php
	namespace Lenkenith\Console;

	class Cli {

		private $_loader, $argv, $_commandList;

		private $target_class, $target_action, $target_command, $target_params;

		public function __construct(\Lenkenith\Core\Loader $loader,
			\Lenkenith\Console\CommandList $commandList){
			$this->_loader 			= $loader;
			$this->argv 			= array();
			$this->parent_command 	= null;
			$this->target_class 	= false;
			$this->target_command 	= null;
			$this->target_action 	= null;
			$this->target_params 	= array();
			$this->_commandList 	= $commandList;
		}
		public function execute($argv = array()){
			$this->argv = $argv;
			$is_command_valid = $this->parseCommand();
			if($is_command_valid) {
				if($this->target_class){
					$app = $this->_loader->create($this->target_class);
					$action = $this->target_action;
					if(!empty($this->target_params) && is_array($this->target_params)){
						call_user_func_array(array($app,$action), $this->target_params);
					}
					else{
						$app->$action();
					}
				}
			}
			else{
				echo $this->_commandList->getListOutput("Invalid command \"".$this->target_command."\". Here is the list of available commands:",$this->parent_command);
			}			
		}
		private function parseCommand(){
			if(isset($this->argv[1])){
				$command_frags 			= explode('::', $this->argv[1]);
				$this->target_command 	= $command_frags[0];
				$this->parent_command 	= ($this->_commandList->commandExists($command_frags[0])) ?$command_frags[0] : null;
				if($this->_commandList->commandExists($command_frags[0])){
					if(isset($command_frags[1])){
						if($this->_commandList->commandActionExists($this->target_command, $command_frags[1])){		
							$this->target_class 	= $this->_commandList->getCommandClass($this->target_command);			
							$this->target_action 	= $command_frags[1];
							for($i = 2; $i < sizeOf($command_frags); $i++){
								if(isset($command_frags[$i])){
									$this->target_params[] = $command_frags[$i];
								}
							}
							return true;
						} else {
							return false;
						}
					}
					return false;
				}				
			}
			return false;
		}
	}
?>