<?php
	namespace Lenkenith\Core;
	class Logger{
		private $configs, $logFile;
		public function __construct(\Lenkenith\Base\Configuration $configuration){
			$this->configs = $configuration->getConfigs();
			if(!is_dir('var')){ mkdir('var'); }
			if(!is_dir('var/logs')){ mkdir('var/logs'); }
			$this->logFile['system'] = "var/logs/system.txt";
			$this->logFile['errors'] = "var/logs/errors.txt";
			$this->logFile['exception'] = "var/logs/exceptions.txt";
		}
		public function log($message,$code=1){
			$type = 'errors';
			switch ($code) {
				case 2:					
					$type = 'system';
					break;
				case 3:
					$type = 'exception';
					break;				
				default:
					$type = 'errors';
					break;
			}
			$file = $this->logFile[$type];
			$message = '['.date('F d, Y H:i:s').'] : '.$message.'---'."\n";
			file_put_contents($file, $message, FILE_APPEND | LOCK_EX);
			return true;
		}
	}
?>