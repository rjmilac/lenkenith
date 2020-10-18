<?php
	namespace Lenkenith\Framework\Model;
	class Context {

		protected $context;
		private $_dbDriver, $_loader, $_configuration;
		private $_dbConfig, $_config;

		public function __construct(\Lenkenith\Database\Driver $dbDriver, 
			\Lenkenith\Core\Loader $loader, 
			\Lenkenith\Base\Configuration $configuration){
			$this->_configuration 	= $configuration;
			$this->_loader 			= $loader;
			$this->_dbDriver 		= $dbDriver;
		}

		public function getConfig(){
			return $this->_configuration;
		}

		public function getConnection($key = 'default'){
			return $this->_dbDriver->get($key);
		}

		public function closeConnection($key){
			return $this->_dbDriver->close($key);
		}

	}
?>